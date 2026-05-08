<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificacions';

    protected $fillable = [
        'insumo1',
        'insumo2',
        'insumo3',
        'insumo4',
        'insumo5',
        'promedio_insumos',
        'examen_parcial',
        'examen_final',
        'nota_final',
        'nota_suspenso',
        'estado_final',
        'es_arrastre',
        'numero_intento',
        'es_borrador',
        'detalle_matricula_id',
        'docente_id'
    ];

    protected $casts = [
        'insumo1' => 'decimal:2',
        'insumo2' => 'decimal:2',
        'insumo3' => 'decimal:2',
        'insumo4' => 'decimal:2',
        'insumo5' => 'decimal:2',
        'promedio_insumos' => 'decimal:2',
        'examen_parcial' => 'decimal:2',
        'examen_final' => 'decimal:2',
        'nota_final' => 'decimal:2',
        'es_arrastre' => 'boolean',
        'es_borrador' => 'boolean',
        'numero_intento' => 'integer',
    ];

    /**
     * Detalle de matrícula
     */
    public function detalleMatricula()
    {
        return $this->belongsTo(DetalleMatricula::class);
    }

    /**
     * Docente que registra la calificación
     */
    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Estudiante (a través de detalle_matricula)
     */
    public function estudiante()
    {
        return $this->hasOneThrough(
            User::class,
            DetalleMatricula::class,
            'id',
            'id',
            'detalle_matricula_id',
            'user_id'
        );
    }

    /**
     * Auditorías de la calificación
     */
    public function auditorias()
    {
        return $this->hasMany(AuditoriaCalificacion::class);
    }

    // Métodos auxiliares
    public function calcularPromedioInsumos()
    {
        $insumos = collect([$this->insumo1, $this->insumo2, $this->insumo3, $this->insumo4, $this->insumo5])
            ->filter()
            ->values();

        return $insumos->count() > 0 ? $insumos->avg() : 0;
    }

    public function calcularNotaFinal()
    {
        $promedio = $this->promedio_insumos ?? $this->calcularPromedioInsumos();
        $parcial = $this->examen_parcial ?? 0;
        $final = $this->examen_final ?? 0;

        // Fórmula ejemplo: 60% insumos + 20% parcial + 20% final
        return ($promedio * 0.6) + ($parcial * 0.2) + ($final * 0.2);
    }
}
