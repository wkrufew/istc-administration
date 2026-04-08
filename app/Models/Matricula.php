<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $fillable = [
        'fecha_matricula',
        'code',
        'tipo',
        'estado',
        /* 'total_creditos',
        'costo_total',
        'descuento',
        'total_pagar', */
        'observaciones',
        'periodo_id',
        'carrera_id',
        'user_id'
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
        /* 'total_creditos' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total_pagar' => 'decimal:2', */
    ];

    /**
     * Estudiante matriculado
     */
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Período académico
     */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    /**
     * Carrera
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    /**
     * Detalles de la matrícula
     */
    public function detalles()
    {
        return $this->hasMany(DetalleMatricula::class);
    }

    /**
     * Pagos de la matrícula
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function obligacionesFinancieras()
    {
        return $this->hasMany(ObligacionesFinanciera::class);
    }
}
