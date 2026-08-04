<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvalidacionDetalle extends Model
{
    protected $table = 'convalidacion_detalles';

    protected $fillable = [
        'convalidacion_id',
        'materia_id',
        'nota',
        'estado',
        'detalle_matricula_id',
    ];

    protected $casts = [
        'nota' => 'float',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function convalidacion(): BelongsTo
    {
        return $this->belongsTo(Convalidacion::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function detalleMatricula(): BelongsTo
    {
        return $this->belongsTo(DetalleMatricula::class, 'detalle_matricula_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function esAprobada(): bool
    {
        return $this->estado === 'Aprobado';
    }
}
