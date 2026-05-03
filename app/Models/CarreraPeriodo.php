<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CarreraPeriodo extends Pivot
{
    protected $table = 'carrera_periodo';

    protected $casts = [
        'fecha_inicio'           => 'date',
        'fecha_fin'              => 'date',
        'fecha_limite_matricula' => 'date',
        'fecha_limite_pago'      => 'date',
        'is_current'             => 'boolean',
        'is_active'              => 'boolean',
    ];

    // -------------------------------------------------------
    // Lógica de unicidad: solo un is_current = true por carrera
    // -------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (CarreraPeriodo $cp) {
            if ($cp->is_current) {
                static::where('carrera_id', $cp->carrera_id)
                       ->where('is_current', true)
                       ->update(['is_current' => false]);
            }
        });

        static::updating(function (CarreraPeriodo $cp) {
            if ($cp->is_current && $cp->isDirty('is_current')) {
                static::where('carrera_id', $cp->carrera_id)
                       ->where('id', '!=', $cp->id)
                       ->where('is_current', true)
                       ->update(['is_current' => false]);
            }
        });
    }

    // -------------------------------------------------------
    // Fechas efectivas: override propio → fallback al período base
    // -------------------------------------------------------

    public function getFechaInicioEfectivaAttribute(): ?Carbon
    {
        return $this->fecha_inicio ?? $this->periodo?->fecha_inicio;
    }

    public function getFechaFinEfectivaAttribute(): ?Carbon
    {
        return $this->fecha_fin ?? $this->periodo?->fecha_fin;
    }

    public function getFechaLimiteMatriculaEfectivaAttribute(): ?Carbon
    {
        return $this->fecha_limite_matricula ?? $this->periodo?->fecha_limite_matricula;
    }

    public function getFechaLimitePagoEfectivaAttribute(): ?Carbon
    {
        return $this->fecha_limite_pago ?? $this->periodo?->fecha_limite_pago;
    }

    // -------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }
}
