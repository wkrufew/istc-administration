<?php

namespace App\Services;

use App\Models\Carrera;
use App\Models\Convalidacion;
use App\Models\Matricula;
use App\Models\MateriasArrastrada;
use App\Models\ObligacionesFinanciera;

/**
 * Validación de conocimientos: cuántos semestres exoneró, cuántos cursó antes, y el caso
 * especial del "saldo de validación" (cursó semestres normales, validó los restantes y ya no
 * le queda ninguna materia, pero sí la parte de la carrera exonerada por pagar). Lo usan
 * Matriculación (precios y generación del saldo) y Titulación (no marcar Egresado antes).
 */
class SaldoValidacionService
{
    /** Obligaciones que forman el saldo (se asientan sobre la matrícula interna de validación). */
    public const TIPOS_SALDO = ['MATRICULA', 'COLEGIATURA', 'REINTEGRO'];

    /**
     * Regla de negocio — tres casos de validación de conocimientos:
     *
     * Caso 1: el estudiante entra DIRECTO por validación (nunca tuvo matrícula regular
     *         en esta carrera). Aplica pricing de convalidación: costo_convalidacion
     *         dividido entre los semestres restantes (total − exonerados).
     *
     * Caso 2: completó algún semestre con matrícula normal y luego validó semestres
     *         subsiguientes. Ya entró al track normal; el costo_convalidacion no le aplica.
     *
     * Caso 3: igual que Caso 2 — completó S1 normal, validó semestres no consecutivos
     *         y materias parciales. Sigue en el track normal.
     *
     * Diferenciador: matrícula regular (tipo ≠ Validacion) ANTERIOR a la convalidación.
     * Si existe → Casos 2/3: pricing normal con saldo restante (es_convalidacion = false,
     * para que el arancel use costo_carrera). Las matrículas regulares posteriores son los
     * semestres del Caso 1: mantienen el mismo valor por semestre durante toda la carrera.
     *
     * @return array{es_convalidacion: bool, exonerados: int, cursados: int}
     */
    public static function estadoConvalidacion(int $userId, int $carreraId): array
    {
        $sin = ['es_convalidacion' => false, 'exonerados' => 0, 'cursados' => 0];

        $convalidacion = Convalidacion::where('user_id', $userId)
            ->where('carrera_id', $carreraId)
            ->where('estado', 'Confirmada')
            ->with(['detalles' => fn($q) => $q->where('estado', 'Aprobado')])
            ->first();
        if (! $convalidacion) return $sin;

        $exonerados = CalculoMatriculaService::semestresExonerados(
            $carreraId,
            $convalidacion->detalles->pluck('materia_id')->toArray()
        );

        $tieneMatriculaRegular = Matricula::where('user_id', $userId)
            ->where('carrera_id', $carreraId)
            ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
            ->where('created_at', '<', $convalidacion->created_at)
            ->exists();

        if (! $tieneMatriculaRegular) {
            return ['es_convalidacion' => true, 'exonerados' => $exonerados, 'cursados' => 0];
        }

        // semestres exonerados se conservan: los necesita la fórmula del saldo restante
        return [
            'es_convalidacion' => false,
            'exonerados'       => $exonerados,
            'cursados'         => Matricula::where('user_id', $userId)
                ->where('carrera_id', $carreraId)
                ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
                ->where('estado', 'Habilitada')
                ->count(),
        ];
    }

    /**
     * Caso del saldo: cursó y exoneró entre ambos toda la carrera, aún falta una parte por
     * cobrar y no le queda ninguna materia pendiente. Devuelve los datos para la tarjeta del
     * wizard, o null si no aplica. 'generado' indica si sus obligaciones ya existen.
     */
    public static function detectar(int $userId, int $carreraId): ?array
    {
        $carrera = Carrera::find($carreraId);
        if (! $carrera) return null;

        $estado = self::estadoConvalidacion($userId, $carreraId);
        $total  = CalculoMatriculaService::semestresCarrera($carrera);

        $aplica = ! $estado['es_convalidacion']
            && $estado['cursados'] > 0
            && $estado['exonerados'] > 0
            && $estado['cursados'] < $total
            && ($total - $estado['exonerados'] - $estado['cursados']) <= 0;
        if (! $aplica) return null;

        if (MateriasArrastrada::where('user_id', $userId)->where('estado', 'Arrastrada')->exists()) return null;
        if (! EstadoAcademicoService::estadoMalla($userId, $carreraId)['completa']) return null;

        $matriculaValidacion = self::matriculaValidacion($userId, $carreraId);
        if (! $matriculaValidacion) return null;

        return [
            'matricula_id' => $matriculaValidacion->id,
            'codigo'       => $matriculaValidacion->code,
            'total'        => $total,
            'cursados'     => $estado['cursados'],
            'exonerados'   => $estado['exonerados'],
            'generado'     => self::yaGenerado($matriculaValidacion->id),
        ];
    }

    public static function matriculaValidacion(int $userId, int $carreraId): ?Matricula
    {
        return Matricula::where('user_id', $userId)
            ->where('carrera_id', $carreraId)
            ->where('tipo', Matricula::TIPO_VALIDACION)
            ->latest('id')
            ->first();
    }

    /** Obligaciones vigentes del saldo (lo que "Revertir saldo" invalidaría). */
    public static function obligacionesVigentes(int $matriculaValidacionId)
    {
        return ObligacionesFinanciera::where('matricula_id', $matriculaValidacionId)
            ->whereIn('tipo', self::TIPOS_SALDO)
            ->where('estado', '!=', 'Invalidado')
            ->get();
    }

    public static function yaGenerado(int $matriculaValidacionId): bool
    {
        return ObligacionesFinanciera::where('matricula_id', $matriculaValidacionId)
            ->whereIn('tipo', ['MATRICULA', 'COLEGIATURA'])
            ->where('estado', '!=', 'Invalidado')
            ->exists();
    }
}
