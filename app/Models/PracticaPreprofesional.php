<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PracticaPreprofesional extends Model
{
    protected $table = 'practicas_preprofesionales';

    protected $fillable = [
        'user_id',
        'carrera_id',
        'empresa',
        'sector',
        'direccion_empresa',
        'tutor_empresa',
        'cargo_tutor_empresa',
        'telefono_empresa',
        'email_empresa',
        'cargo_estudiante',
        'actividades_realizadas',
        'fecha_inicio',
        'fecha_fin',
        'total_horas',
        'nota',
        'estado',
        'carta_aceptacion_path',
        'informe_final_path',
        'certificado_empresa_path',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'nota'         => 'decimal:2',
        'total_horas'  => 'integer',
    ];

    // =========================================================================
    // CONSTANTES
    // =========================================================================
    const ESTADO_EN_CURSO   = 'En_Curso';
    const ESTADO_COMPLETADA = 'Completada';
    const ESTADO_REPROBADA  = 'Reprobada';

    const NOTA_MINIMA = 7.00;

    // =========================================================================
    // RELACIONES
    // =========================================================================
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function notaTitulacion(): HasOne
    {
        return $this->hasOne(NotaTitulacion::class, 'practica_id');
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Duración en días de las prácticas
     */
    public function getDuracionDiasAttribute(): ?int
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin) return null;
        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }

    /**
     * Verifica si la nota es aprobatoria
     */
    public function getAprobadaAttribute(): bool
    {
        return $this->nota !== null && $this->nota >= self::NOTA_MINIMA;
    }

    // =========================================================================
    // SCOPES
    // =========================================================================
    public function scopeCompletadas($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADA);
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', self::ESTADO_COMPLETADA)
            ->where('nota', '>=', self::NOTA_MINIMA);
    }
}
