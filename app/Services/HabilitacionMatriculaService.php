<?php

namespace App\Services;

use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;

/**
 * Regla única para habilitar una matrícula: deben estar pagadas su matrícula,
 * sus arrastres y su reintegro (los que existan). La colegiatura no bloquea.
 */
class HabilitacionMatriculaService
{
    /**
     * Habilita la matrícula si ya no le queda nada pendiente de los conceptos requeridos.
     * Solo actúa sobre matrículas en Pendiente_Pago. Devuelve true si quedó habilitada.
     */
    public static function verificar(?Matricula $matricula): bool
    {
        if (! $matricula) return false;
        if ($matricula->estado === 'Habilitada') return true;
        if ($matricula->estado !== 'Pendiente_Pago') return false;

        if (self::saldoPendiente($matricula->id) > 0) return false;

        $tieneRequeridas = ObligacionesFinanciera::where('matricula_id', $matricula->id)
            ->whereIn('tipo', ObligacionesFinanciera::TIPOS_HABILITACION)
            ->where('estado', '!=', 'Invalidado')
            ->exists();
        if (! $tieneRequeridas) return false;

        $matricula->update(['estado' => 'Habilitada']);
        return true;
    }

    public static function verificarPorObligacion(ObligacionesFinanciera $obligacion): bool
    {
        if (! in_array($obligacion->tipo, ObligacionesFinanciera::TIPOS_HABILITACION, true)) return false;

        return self::verificar($obligacion->matricula_id ? Matricula::find($obligacion->matricula_id) : null);
    }

    /** Lo que falta pagar para habilitar (pagos aprobados únicamente). */
    public static function saldoPendiente(int $matriculaId): float
    {
        return round(
            ObligacionesFinanciera::where('matricula_id', $matriculaId)
                ->whereIn('tipo', ObligacionesFinanciera::TIPOS_HABILITACION)
                ->where('estado', '!=', 'Invalidado')
                ->get()
                ->sum(fn($ob) => max(0, (float) $ob->saldo)),
            2
        );
    }
}
