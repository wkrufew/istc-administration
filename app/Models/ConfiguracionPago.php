<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionPago extends Model
{
    protected $table = 'configuracion_pagos';

    protected $fillable = [
        'gateway',
        'nombre_mostrar',
        'configuracion',
        'is_active',
        'es_sandbox',
        'comision_porcentaje',
        'comision_fija'
    ];

    protected $casts = [
        'configuracion' => 'array',
        'is_active' => 'boolean',
        'es_sandbox' => 'boolean',
        'comision_porcentaje' => 'decimal:2',
        'comision_fija' => 'decimal:2',
    ];

    /**
     * Scope para configuraciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('is_active', true);
    }
}
