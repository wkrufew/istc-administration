<?php

namespace App\Services;

use App\Models\Carrera;
use App\Models\DetalleMatricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;

/**
 * Regla única de "malla completa" y "saldo pendiente": la usan titulación (para habilitar el
 * registro) y el portal del estudiante (para informarle cómo va), así ambos dicen lo mismo.
 */
class EstadoAcademicoService
{
    /** Obligaciones que todavía tienen algo por pagar. */
    public const ESTADOS_CON_SALDO = ['Pendiente', 'Parcial', 'Vencido'];

    /**
     * Semestres de la carrera con todas sus materias aprobadas (por cualquier vía: cursada,
     * arrastre o validación de conocimientos). Solo cuentan notas publicadas: un borrador del
     * docente puede quedar como "Aprobado" antes de cerrarse.
     *
     * @return array{completa: bool, semestres_ok: int, semestres_total: int}
     */
    public static function estadoMalla(int $userId, int $carreraId): array
    {
        $carrera = Carrera::with('semestres.materias:id,semestre_id')->find($carreraId);
        if (! $carrera) return ['completa' => false, 'semestres_ok' => 0, 'semestres_total' => 0];

        $aprobadas = DetalleMatricula::where('user_id', $userId)
            ->whereHas('calificaciones', fn($q) => $q
                ->where('estado_final', 'Aprobado')
                ->where(fn($b) => $b->where('es_borrador', false)->orWhereNull('es_borrador')))
            ->pluck('materia_id')
            ->flip();

        $total = $carrera->semestres->count();
        $ok    = $carrera->semestres
            ->filter(fn($semestre) => $semestre->materias->every(fn($m) => $aprobadas->has($m->id)))
            ->count();

        return ['completa' => $total > 0 && $ok === $total, 'semestres_ok' => $ok, 'semestres_total' => $total];
    }

    /**
     * Ajustes → Matrícula: si está activo, no se registra la titulación con saldo pendiente.
     * Desactivado por defecto mientras se termina de cargar la información histórica.
     */
    public static function exigeSinSaldoParaTitulacion(): bool
    {
        return (string) SettingService::get('titulacion.exigir_sin_saldo', '0') === '1';
    }

    /** Total que el estudiante aún debe (solo cuentan los pagos aprobados). */
    public static function saldoPendiente(int $userId): float
    {
        return round(
            ObligacionesFinanciera::where('user_id', $userId)
                ->whereIn('estado', self::ESTADOS_CON_SALDO)
                ->withSum(['pagos as pagado' => fn($q) => $q->where('estado', Pago::ESTADO_APROBADO)], 'monto')
                ->get()
                ->sum(fn($ob) => max(0, (float) $ob->monto_final - (float) $ob->pagado)),
            2
        );
    }

    /** Tiene comprobantes subidos que secretaría aún no revisa. */
    public static function pagosEnRevision(int $userId): bool
    {
        return Pago::where('estado', Pago::ESTADO_PENDIENTE)
            ->whereHas('obligacion', fn($q) => $q->where('user_id', $userId))
            ->exists();
    }
}
