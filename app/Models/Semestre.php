<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    protected $fillable = [
        'name',
        'code',
        'order',
        'creditos_minimos',
        'creditos_maximos',
        'is_active',
        'carrera_id'
    ];

    protected $casts = [
        'order' => 'integer',
        'creditos_minimos' => 'integer',
        'creditos_maximos' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Carrera a la que pertenece
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    /**
     * Materias del semestre
     */
    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    /**
     * Scope para semestres activos
     */
    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }
}
