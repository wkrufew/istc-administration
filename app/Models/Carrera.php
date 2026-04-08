<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'costo_credito',
        'costo_carrera',
        'duracion_semestres',
        'modalidad',
        'is_active'
    ];

    protected $casts = [
        'costo_credito' => 'decimal:2',
        'costo_carrera' => 'decimal:2',
        'duracion_semestres' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Semestres de la carrera
     */
    public function semestres()
    {
        return $this->hasMany(Semestre::class)->orderBy('order');
    }

    /**
     * Matrículas en esta carrera
     */
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Scope para carreras activas
     */
    public function scopeActivas($query)
    {
        return $query->where('is_active', true);
    }


    ///para titulacion y practicas preprofesionales
    public function practicasPreprofesionales()
    {
        return $this->hasMany(PracticaPreprofesional::class, 'carrera_id');
    }

    public function notasTitulacion()
    {
        return $this->hasMany(NotaTitulacion::class, 'carrera_id');
    }
}
