<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionDocente extends Model
{
    protected $table = 'asignacion_docentes';

    protected $fillable = [
        'docente_id',
        'materia_id',
        'periodo_id',
        'paralelo_id',
        'file_contrato',
    ];

    /**
     * Docente asignado
     */
    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Materia asignada
     */
    /* public function materia()
    {
        return $this->belongsTo(Materia::class);
    } */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    /**
     * Período de la asignación
     */
    /* public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    } */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    /**
     * Paralelo asignado
     */
    /* public function paralelo()
    {
        return $this->belongsTo(Paralelo::class);
    } */
    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'paralelo_id');
    }

    /**
     * Horarios de la asignación
     */
    /* public function horarios()
    {
        return $this->hasMany(Horario::class);
    } */
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'asignacion_docente_id');
    }
}
