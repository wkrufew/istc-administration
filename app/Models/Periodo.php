<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Periodo extends Model
{
    protected $fillable = [
        'code',
        'description',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite_matricula',
        'fecha_limite_pago',
        'is_current'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_limite_matricula' => 'date',
        'fecha_limite_pago' => 'date',
        'is_current' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($periodo) {
            // Al crear uno nuevo como current, desactiva los demás
            if ($periodo->is_current) {
                static::where('is_current', true)->update(['is_current' => false]);
            }
        });

        static::updating(function ($periodo) {
            // Al editar y marcarlo como current, desactiva los demás
            if ($periodo->is_current && $periodo->isDirty('is_current')) {
                static::where('id', '!=', $periodo->id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }
        });
    }

    /**
     * Matrículas del período
     */
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Asignaciones de docentes
     */
    public function asignacionesDocentes()
    {
        return $this->hasMany(AsignacionDocente::class);
    }

    /**
     * Horarios del período
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Materias arrastradas del período
     */
    public function materiasArrastradas()
    {
        return $this->hasMany(MateriasArrastrada::class, 'periodo_reprobado_id');
    }

    /**
     * Scope para período actual
     */
    public function scopeActual($query)
    {
        return $query->where('is_current', true);
    }

    public function materiasParalelos()
    {
        return $this->hasMany(MateriaPeriodoParalelo::class);
    }

    public function obligacionesFinancieras()
    {
        return $this->hasMany(ObligacionesFinanciera::class);
    }
}
