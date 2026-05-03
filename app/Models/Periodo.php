<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = [
        'code',
        'description',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite_matricula',
        'fecha_limite_pago',
    ];

    protected $casts = [
        'fecha_inicio'           => 'date',
        'fecha_fin'              => 'date',
        'fecha_limite_matricula' => 'date',
        'fecha_limite_pago'      => 'date',
    ];

    // -------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------

    /**
     * Retorna cualquier período activo en alguna carrera (is_current = true en carrera_periodo).
     * Útil como valor por defecto en dashboards y filtros genéricos.
     */
    public static function periodoActivoGlobal(): ?self
    {
        return static::whereExists(function ($query) {
            $query->selectRaw(1)
                  ->from('carrera_periodo')
                  ->whereColumn('carrera_periodo.periodo_id', 'periodos.id')
                  ->where('carrera_periodo.is_current', true);
        })->first();
    }

    /**
     * Carreras vinculadas a este período (con sus fechas y estado propios).
     */
    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_periodo')
                    ->using(CarreraPeriodo::class)
                    ->withPivot([
                        'id',
                        'fecha_inicio',
                        'fecha_fin',
                        'fecha_limite_matricula',
                        'fecha_limite_pago',
                        'is_current',
                        'is_active',
                    ])
                    ->withTimestamps();
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
     * Relación con materia-período-paralelo
     */
    public function materiasParalelos()
    {
        return $this->hasMany(MateriaPeriodoParalelo::class);
    }

    /**
     * Obligaciones financieras del período
     */
    public function obligacionesFinancieras()
    {
        return $this->hasMany(ObligacionesFinanciera::class);
    }
}
