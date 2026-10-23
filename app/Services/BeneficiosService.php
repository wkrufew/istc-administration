<?php

namespace App\Services;

use App\Models\BecaAplicada;
use App\Models\ConvenioAplicado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Punto único para desactivar becas y convenios. Nunca se borran: quedan inactivos con
 * fecha, usuario y motivo, para el historial de beneficios y auditorías.
 */
class BeneficiosService
{
    public const MOTIVOS = [
        'revocacion_manual'   => 'Revocación manual',
        'cierre_periodo'      => 'Cierre de período',
        'retiro'              => 'Retiro',
        'egreso'              => 'Egreso',
        'anulacion_matricula' => 'Anulación de matrícula',
        'aspirante_rechazado' => 'Aspirante rechazado',
        'aspirante_eliminado' => 'Aspirante eliminado',
        'reemplazo'           => 'Reemplazado por otro',
        'retirado_en_edicion' => 'Retirado al editar el aspirante',
        'renovacion'          => 'Renovación',
    ];

    public static function desactivar(BecaAplicada|ConvenioAplicado $beneficio, string $motivo, ?string $detalle = null): bool
    {
        if (! $beneficio->is_active) return false;

        $beneficio->update([
            'is_active'             => false,
            'desactivado_at'        => now(),
            'desactivado_por'       => Auth::id(),
            'motivo_desactivacion'  => $motivo,
            'detalle_desactivacion' => $detalle,
        ]);

        return true;
    }

    /** Desactiva las becas y convenios activos de un estudiante. Devuelve cuántos desactivó. */
    public static function desactivarTodos(int $userId, string $motivo, ?string $detalle = null): int
    {
        $total = 0;
        foreach (BecaAplicada::where('user_id', $userId)->where('is_active', true)->get() as $beca) {
            $total += (int) self::desactivar($beca, $motivo, $detalle);
        }
        foreach (ConvenioAplicado::where('user_id', $userId)->where('is_active', true)->get() as $convenio) {
            $total += (int) self::desactivar($convenio, $motivo, $detalle);
        }
        return $total;
    }

    /**
     * Ahorro en colegiaturas atribuible a becas ('beca') o a convenios ('convenio').
     * Cada cuota guarda un solo descuento combinado; cuando tuvo beca y convenio a la vez se
     * reparte según sus porcentajes. Las cuotas anteriores a convenio_aplicado_id cuentan
     * enteras para la beca. $filtro recibe la consulta (alias "o" = obligaciones).
     */
    public static function ahorro(string $tipo, ?callable $filtro = null): float
    {
        $columna = $tipo === 'convenio' ? 'o.convenio_aplicado_id' : 'o.beca_aplicada_id';
        $parte   = $tipo === 'convenio' ? 'ca.porcentaje_aplicado' : 'ba.porcentaje_aplicado';

        $consulta = DB::table('obligaciones_financieras as o')
            ->leftJoin('becas_aplicadas as ba', 'ba.id', '=', 'o.beca_aplicada_id')
            ->leftJoin('convenios_aplicados as ca', 'ca.id', '=', 'o.convenio_aplicado_id')
            ->where('o.tipo', 'COLEGIATURA')
            ->where('o.estado', '!=', 'Invalidado')
            ->whereNotNull($columna);

        if ($filtro) $filtro($consulta);

        $expresion = "CASE WHEN o.beca_aplicada_id IS NOT NULL AND o.convenio_aplicado_id IS NOT NULL
                            AND (ba.porcentaje_aplicado + ca.porcentaje_aplicado) > 0
                       THEN o.descuento * {$parte} / (ba.porcentaje_aplicado + ca.porcentaje_aplicado)
                       ELSE o.descuento END";

        return round((float) $consulta->sum(DB::raw($expresion)), 2);
    }

    public static function etiquetaMotivo(?string $motivo): string
    {
        return self::MOTIVOS[$motivo] ?? ($motivo ? ucfirst(str_replace('_', ' ', $motivo)) : '—');
    }
}
