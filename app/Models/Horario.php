<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $fillable = [
        'dia_semana',
        'aula',
        'hora_inicio',
        'hora_fin',
        'is_active',
        'modalidad_clase',
        'enlace_virtual',
        'observaciones',
        'materia_id',
        'paralelo_id',
        'periodo_id',
        'asignacion_docente_id'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Materia
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Paralelo
     */
    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class);
    }

    /**
     * Período
     */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    /**
     * Asignación de docente
     */
    /* public function asignacionDocente()
    {
        return $this->belongsTo(AsignacionDocente::class);
    } */

    public function asignacionDocente()
    {
        return $this->belongsTo(AsignacionDocente::class, 'asignacion_docente_id');
    }

    /**
     * Docente (a través de asignación)
     */
    public function docente()
    {
        return $this->hasOneThrough(
            User::class,
            AsignacionDocente::class,
            'id',
            'id',
            'asignacion_docente_id',
            'docente_id'
        );
    }

    /**
     * Asistencias del horario
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Scope para horarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }
}
