<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Convalidacion extends Model
{
    protected $table = 'convalidaciones';

    protected $fillable = [
        'user_id',
        'carrera_id',
        'periodo_id',
        'registrado_por',
        'matricula_id',
        'documento_path',
        'observaciones',
        'estado',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(ConvalidacionDetalle::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'Confirmada');
    }

    public function scopeBorradores($query)
    {
        return $query->where('estado', 'Borrador');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function esConfirmada(): bool
    {
        return $this->estado === 'Confirmada';
    }

    public function totalAprobadas(): int
    {
        return $this->detalles()->where('estado', 'Aprobado')->count();
    }

    public function totalReprobadas(): int
    {
        return $this->detalles()->where('estado', 'Reprobado')->count();
    }
}
