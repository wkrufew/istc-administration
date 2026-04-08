<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleMatricula extends Model
{
    protected $fillable = [
        'asignacion',
        'code',
        'tipo',
        'estado',
        'costo_materia',
        'es_repeticion',
        'matricula_id',
        'materia_id',
        'paralelo_id',
        'user_id'
    ];

    protected $casts = [
        'asignacion' => 'date',
        'costo_materia' => 'decimal:2',
        'es_repeticion' => 'boolean',
    ];

    /**
     * Matrícula principal
     */
    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    /**
     * Estudiante
     */
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
     * Calificaciones
     */
    /* public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    } */

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'detalle_matricula_id');
    }

    //para le acta de calificaciones del estudiante
    public function calificacion()
    {
        return $this->hasOne(Calificacion::class, 'detalle_matricula_id');
    }
    /**
     * Asistencias
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    // Método para obtener horarios de esta materia (VER SI ES FACTIBLE ESTO PILAS)
    public function horarios()
    {
        return Horario::where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->get();
    }
}
