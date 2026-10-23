<?php

namespace App\Services;

use App\Models\Carrera;
use App\Models\Convalidacion;
use App\Models\Semestre;

/**
 * Fuente única del valor de matrícula. La matrícula de toda la carrera es el 10% de su costo
 * y se reparte entre los semestres que el estudiante realmente cursa.
 */
class CalculoMatriculaService
{
    public const PORCENTAJE_MATRICULA = 0.10;

    public static function semestresCarrera(Carrera $carrera): int
    {
        return $carrera->duracion_semestres > 0 ? (int) $carrera->duracion_semestres : 1;
    }

    public static function matriculaTotalCarrera(Carrera $carrera): float
    {
        return round((float) $carrera->costo_carrera * self::PORCENTAJE_MATRICULA, 2);
    }

    /**
     * @param float $fraccionRestante Parte de la matrícula total que aún falta cobrar
     *                                (1.0 salvo quien cursó semestres regulares antes de validar).
     */
    public static function matriculaPorSemestre(Carrera $carrera, int $semestresACursar, float $fraccionRestante = 1.0): float
    {
        return round(((float) $carrera->costo_carrera * self::PORCENTAJE_MATRICULA * $fraccionRestante) / max(1, $semestresACursar), 2);
    }

    /**
     * Valor de matrícula según cuántos semestres exonere en la validación de conocimientos,
     * y cuánto le faltaría completar respecto al pago inicial hecho en admisión.
     *
     * @return array<int, array{exonera:int, cursa:int, valor:float, diferencia:float}>
     */
    public static function escenariosValidacion(Carrera $carrera, float $pagoInicial): array
    {
        $total = self::semestresCarrera($carrera);
        $escenarios = [];

        for ($exonera = 0; $exonera < $total; $exonera++) {
            $cursa = $total - $exonera;
            $valor = self::matriculaPorSemestre($carrera, $cursa);
            $escenarios[] = [
                'exonera'    => $exonera,
                'cursa'      => $cursa,
                'valor'      => $valor,
                'diferencia' => max(0, round($valor - $pagoInicial, 2)),
            ];
        }

        return $escenarios;
    }

    /**
     * Semestres completos exonerados: todas sus materias activas aprobadas en la validación.
     *
     * @param array<int> $materiasAprobadas
     */
    public static function semestresExonerados(int $carreraId, array $materiasAprobadas): int
    {
        return Semestre::with(['materias' => fn($q) => $q->where('is_active', true)])
            ->where('carrera_id', $carreraId)
            ->where('is_active', true)
            ->get()
            ->filter(fn($semestre) => $semestre->materias->isNotEmpty()
                && $semestre->materias->every(fn($m) => in_array($m->id, $materiasAprobadas)))
            ->count();
    }

    public static function semestresExoneradosDeEstudiante(int $userId, int $carreraId): int
    {
        $convalidacion = Convalidacion::where('user_id', $userId)
            ->where('carrera_id', $carreraId)
            ->where('estado', 'Confirmada')
            ->with(['detalles' => fn($q) => $q->where('estado', 'Aprobado')])
            ->first();

        if (! $convalidacion) return 0;

        return self::semestresExonerados($carreraId, $convalidacion->detalles->pluck('materia_id')->all());
    }
}
