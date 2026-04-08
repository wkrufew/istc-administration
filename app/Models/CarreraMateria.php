<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraMateria extends Model
{
    protected $table = 'carrera_materias';

    protected $fillable = [
        'carrera_id',
        'materia_id',
        'semestre_id',
        'periodo_id',
        'is_active'
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}
