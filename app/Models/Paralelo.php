<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paralelo extends Model
{
    protected $fillable = [
        'name',
        'code',
        'cupo_maximo',
        'cupo_actual',
        'is_active'
    ];

    protected $casts = [
        'cupo_maximo' => 'integer',
        'cupo_actual' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Asignaciones de docentes
     */
    /* public function asignacionDocentes()
    {
        return $this->hasMany(AsignacionDocente::class, 'paralelo_id');
    } */
    public function asignacionesDocentes()
    {
        return $this->hasMany(AsignacionDocente::class, 'paralelo_id');
    }

    /**
     * Detalles de matrícula
     */
    public function detalleMatriculas()
    {
        return $this->hasMany(DetalleMatricula::class);
    }

    /**
     * Horarios del paralelo
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Verificar si tiene cupo disponible
     */
    public function tieneCupoDisponible(): bool
    {
        return $this->cupo_actual < $this->cupo_maximo;
    }

    /**
     * Scope para paralelos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }

    public function materiasPeriodos()
    {
        return $this->hasMany(MateriaPeriodoParalelo::class);
    }
}
