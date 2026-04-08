<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriasArrastrada extends Model
{
    protected $table = 'materias_arrastradas';

    protected $fillable = [
        'nota_obtenida',
        'nota_minima_requerida',
        'porcentaje_penalizacion',
        'numero_intento',
        'estado',
        'costo_adicional',
        'user_id',
        'materia_id',
        'periodo_reprobado_id'
    ];

    protected $casts = [
        'nota_obtenida' => 'decimal:2',
        'nota_minima_requerida' => 'decimal:2',
        'porcentaje_penalizacion' => 'decimal:2',
        'numero_intento' => 'integer',
        'costo_adicional' => 'decimal:2',
    ];

    /**
     * Estudiante
     */
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Materia arrastrada
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Período donde se reprobó
     */
    public function periodoReprobado()
    {
        return $this->belongsTo(Periodo::class, 'periodo_reprobado_id');
    }
}
