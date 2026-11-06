<?php

namespace App\Services;

use App\Models\BecaAplicada;
use App\Models\ConvenioAplicado;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
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
     * Recalcula con la beca y el convenio vigentes las cuotas de colegiatura que aún no tienen
     * ningún pago (ni aprobado ni en revisión) del período en curso de su carrera. Las pagadas,
     * con abono o invalidadas no se tocan; una cuota en $0 por gratuidad vuelve a cobrarse si
     * el descuento baja. Cada cuota se calcula sobre su propio valor original (beca + convenio,
     * máx. 100%). Un convenio semestral que llega a descontar queda usado en ese período.
     * Devuelve cuántas cuotas cambiaron.
     */
    public static function recalcularCuotasPendientes(int $userId): int
    {
        $cuotas = ObligacionesFinanciera::where('user_id', $userId)
            ->where('tipo', 'COLEGIATURA')
            ->where(fn($q) => $q
                ->whereIn('estado', ['Pendiente', 'Vencido'])
                ->orWhere(fn($q2) => $q2->where('estado', 'Pagado')->where('monto_final', '<=', 0)))
            ->whereDoesntHave('pagos', fn($p) => $p->whereIn('estado', [Pago::ESTADO_APROBADO, Pago::ESTADO_PENDIENTE, Pago::ESTADO_PROCESANDO]))
            // Solo el período vigente de la carrera de su matrícula
            ->whereExists(fn($q) => $q->selectRaw(1)
                ->from('matriculas as m')
                ->join('carrera_periodo as cp', 'cp.carrera_id', '=', 'm.carrera_id')
                ->whereColumn('m.id', 'obligaciones_financieras.matricula_id')
                ->whereColumn('cp.periodo_id', 'obligaciones_financieras.periodo_id')
                ->where('cp.is_current', true)
                ->where('cp.is_active', true))
            ->get();

        if ($cuotas->isEmpty()) return 0;

        $beca      = BecaAplicada::where('user_id', $userId)->where('is_active', true)->first();
        $pctBeca   = $beca ? (float) $beca->porcentaje_aplicado : 0;
        $convenios = [];
        $cambiadas = 0;

        foreach ($cuotas as $cuota) {
            $convenio = $convenios[$cuota->periodo_id] ??= ConvenioAplicado::aplicablesEnPeriodo($userId, (int) $cuota->periodo_id)
                ->with('tipoConvenio')->first() ?? false;
            $pctConvenio = $convenio ? (float) $convenio->porcentaje_aplicado : 0;

            $original  = (float) $cuota->monto_original;
            $descuento = round($original * min(100, $pctBeca + $pctConvenio) / 100, 2);
            $final     = max(0, round($original - $descuento, 2));

            $estado = match (true) {
                $final <= 0                => 'Pagado',
                $cuota->estado === 'Pagado' => 'Pendiente',
                default                    => $cuota->estado,
            };

            $datos = [
                'descuento'            => $descuento,
                'monto_final'          => $final,
                'estado'               => $estado,
                'beca_aplicada_id'     => $beca && $pctBeca > 0 ? $beca->id : null,
                'convenio_aplicado_id' => $convenio && $pctConvenio > 0 ? $convenio->id : null,
                'descripcion'          => self::descripcionConDescuentos($cuota->descripcion, $pctBeca > 0, $pctConvenio > 0),
            ];

            $cuota->fill($datos);
            if ($cuota->isDirty()) {
                $cuota->save(); // uno por uno: cada cambio queda en la auditoría
                $cambiadas++;
            }

            if ($convenio && $pctConvenio > 0 && $convenio->esSemestral() && ! $convenio->periodo_id) {
                $convenio->update(['periodo_id' => $cuota->periodo_id]);
            }
        }

        return $cambiadas;
    }

    /** "Arancel semestral con beca - Período X (Cuota n/5)": actualiza solo el "con …". */
    private static function descripcionConDescuentos(?string $descripcion, bool $beca, bool $convenio): ?string
    {
        if (! $descripcion || ! preg_match('/^(.*?)(?: con (?:beca y convenio|beca|convenio))?( - Período .*)$/u', $descripcion, $m)) {
            return $descripcion;
        }

        $partes = array_filter([$beca ? 'beca' : null, $convenio ? 'convenio' : null]);
        return $m[1] . (count($partes) ? ' con ' . implode(' y ', $partes) : '') . $m[2];
    }

    /**
     * Reactiva las becas y convenios que se desactivaron juntos por $motivo (el último lote:
     * los de un egreso anterior revertido sin reactivar no vuelven). Se salta lo que chocaría
     * con un beneficio activo y los convenios ya vencidos por fecha. Devuelve cuántos reactivó.
     */
    public static function reactivarDesactivadosPor(int $userId, string $motivo): int
    {
        $ultimo = collect([
            BecaAplicada::where('user_id', $userId)->where('is_active', false)->where('motivo_desactivacion', $motivo)->max('desactivado_at'),
            ConvenioAplicado::where('user_id', $userId)->where('is_active', false)->where('motivo_desactivacion', $motivo)->max('desactivado_at'),
        ])->filter()->max();
        if (! $ultimo) return 0;

        // Mismo lote = desactivados en la misma operación (unos segundos de margen)
        $desde = \Illuminate\Support\Carbon::parse($ultimo)->subMinute();
        $lote  = fn($q) => $q->where('user_id', $userId)->where('is_active', false)
            ->where('motivo_desactivacion', $motivo)->where('desactivado_at', '>=', $desde);

        $limpiar = [
            'is_active'             => true,
            'desactivado_at'        => null,
            'desactivado_por'       => null,
            'motivo_desactivacion'  => null,
            'detalle_desactivacion' => null,
        ];

        $total = 0;

        // Solo una beca activa a la vez
        $beca = BecaAplicada::where($lote)->latest('desactivado_at')->first();
        if ($beca && ! BecaAplicada::where('user_id', $userId)->where('is_active', true)->exists()) {
            $beca->update($limpiar);
            $total++;
        }

        if (! ConvenioAplicado::where('user_id', $userId)->where('is_active', true)->exists()) {
            $convenio = ConvenioAplicado::where($lote)
                ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
                ->latest('desactivado_at')->first();
            if ($convenio) {
                $convenio->update($limpiar);
                $total++;
            }
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
