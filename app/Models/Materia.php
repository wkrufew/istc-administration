<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materia extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'credits',
        'horas_teoricas',
        'horas_practicas',
        'nota_minima_aprobacion',
        'tipo',
        'semestre_id',
        'is_active',
    ];

    protected $casts = [
        'credits' => 'decimal:2',
        'horas_teoricas' => 'integer',
        'horas_practicas' => 'integer',
        'nota_minima_aprobacion' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Semestre al que pertenece
     */
    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    /**
     * Prerequisitos de esta materia
     */
    public function prerequisitos()
    {
        return $this->belongsToMany(
            Materia::class,
            'prerequisitos',
            'materia_id',
            'prerequisito_id'
        )->withPivot('es_obligatorio')->withTimestamps();
    }

    /**
     * Materias que tienen esta como prerequisito
     */
    public function esPrerrequisitoDeMateria()
    {
        return $this->belongsToMany(
            Materia::class,
            'prerequisitos',
            'prerequisito_id',
            'materia_id'
        )->withPivot('es_obligatorio')->withTimestamps();
    }

    /**
     * Asignaciones de docentes
     */
    public function asignacionesDocentes()
    {
        return $this->hasMany(AsignacionDocente::class);
    }

    /**
     * Detalles de matrícula
     */
    public function detalleMatriculas()
    {
        return $this->hasMany(DetalleMatricula::class);
    }

    /**
     * Horarios de la materia
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Materias arrastradas
     */
    public function materiasArrastradas()
    {
        return $this->hasMany(MateriasArrastrada::class);
    }

    /**
     * Scope para materias activas
     */
    public function scopeActivas($query)
    {
        return $query->where('is_active', true);
    }

    public function periodosParalelos()
    {
        return $this->hasMany(MateriaPeriodoParalelo::class);
    }
}
