<?php

namespace App\Observers;

use App\Models\Pago;

class PagoObserver
{
    public function creating(Pago $pago)
    {
        // Estado por defecto
        if (empty($pago->estado)) {
            $pago->estado = Pago::ESTADO_PENDIENTE;
        }

        // Método de pago por defecto
        if (empty($pago->metodo_pago)) {
            $pago->metodo_pago = Pago::METODO_TRANSFERENCIA;
        }

        // Fecha de pago por defecto
        if (empty($pago->fecha_pago)) {
            $pago->fecha_pago = now();
        }
    }

    /**
     * Después de crear el pago
     * (Aquí generamos el número de comprobante usando el ID real)
     */
    public function created(Pago $pago)
    {
        if (empty($pago->numero_comprobante)) {

            $pago->numero_comprobante =
                'ISTC-CP-' . now()->year . '-' .
                str_pad($pago->id, 5, '0', STR_PAD_LEFT);

            // saveQuietly evita disparar nuevamente eventos
            $pago->saveQuietly();
        }

        // Si se creó ya aprobado (caso raro pero posible)
        if ($pago->estado === Pago::ESTADO_APROBADO) {
            $pago->obligacion?->actualizarEstado();
        }
    }

    /**
     * Cuando el pago es actualizado
     */
    public function updated(Pago $pago)
    {
        // Solo si cambió el estado
        if ($pago->wasChanged('estado')) {

            // Si fue aprobado o cambió desde aprobado
            $pago->obligacion?->actualizarEstado();
        }
    }

    /**
     * Si se elimina un pago
     * (Recalcular obligación)
     */
    public function deleted(Pago $pago)
    {
        $pago->obligacion?->actualizarEstado();
    }

    /**
     * Handle the Pago "restored" event.
     */
    public function restored(Pago $pago): void
    {
        //
    }

    /**
     * Handle the Pago "force deleted" event.
     */
    public function forceDeleted(Pago $pago): void
    {
        //
    }
}
