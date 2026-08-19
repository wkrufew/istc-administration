<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Matricula extends Model
{
    use SoftDeletes;

    const TIPO_NUEVA      = 'Nueva';
    const TIPO_RENOVACION = 'Renovacion';
    const TIPO_ARRASTRE   = 'Arrastre';
    const TIPO_VALIDACION = 'Validacion';

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
        'user_id',
        'num_cuotas_arancel',
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
     * Pagos de la matrícula (vía obligaciones financieras)
     */
    public function pagos()
    {
        return $this->hasManyThrough(
            Pago::class,
            ObligacionesFinanciera::class,
            'matricula_id',  // FK en obligaciones_financieras
            'obligacion_id', // FK en pagos
            'id',
            'id'
        );
    }

    public function obligacionesFinancieras()
    {
        return $this->hasMany(ObligacionesFinanciera::class);
    }

    public function convalidacion()
    {
        return $this->hasOne(Convalidacion::class);
    }

    public function retiro()
    {
        return $this->hasOne(Retiro::class);
    }

    public function esValidacion(): bool
    {
        return $this->tipo === self::TIPO_VALIDACION;
    }
}
