<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ObligacionesFinanciera extends Model
{
    use HasFactory;

    protected $table = 'obligaciones_financieras';

    protected $fillable = [
        'user_id',
        'periodo_id',
        'matricula_id',
        'tipo',
        'monto_original',
        'descuento',
        'monto_final',
        'estado',
        'fecha_vencimiento',
        'descripcion',
        'solicitud_id',
    ];

    protected $casts = [
        'monto_original'     => 'decimal:2',
        'descuento'          => 'decimal:2',
        'monto_final'        => 'decimal:2',
        'fecha_vencimiento'  => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    // 🔹 Pertenece a un estudiante
    /* public function user()
    {
        return $this->belongsTo(User::class);
    } */
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔹 Pertenece a un periodo académico
    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    // 🔹 Puede estar asociada a una matrícula
    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    // 🔹 Tiene muchos pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'obligacion_id');
    }

    // 🔹 Puede estar ligada a una solicitud
    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS DINÁMICOS
    |--------------------------------------------------------------------------
    */

    public function getTotalPagadoAttribute()
    {
        return $this->pagos()
            ->where('estado', Pago::ESTADO_APROBADO)
            ->sum('monto');
    }

    public function getSaldoAttribute()
    {
        return $this->monto_final - $this->total_pagado;
    }

    public function getEstaPagadaAttribute()
    {
        return $this->saldo <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODO PARA ACTUALIZAR ESTADO
    |--------------------------------------------------------------------------
    */

    public function actualizarEstado()
    {
        if ($this->saldo <= 0) {
            $this->estado = 'Pagado';
        } elseif ($this->total_pagado > 0) {
            $this->estado = 'Parcial';
        } else {
            $this->estado = 'Pendiente';
        }

        $this->save();
    }
}
