<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrera extends Model
{
    use SoftDeletes;

    const TIPO_TECNOLOGICA = 'Tecnologica';
    const TIPO_TECNICATURA = 'Tecnicatura';

    // Horas mínimas por tipo de carrera
    const HORAS_MIN_PREPROFESIONAL = [
        'Tecnologica' => 240,
        'Tecnicatura' => 192,
    ];

    const HORAS_MIN_COMUNITARIA = [
        'Tecnologica' => 120,
        'Tecnicatura' => 60,
    ];

    protected $fillable = [
        'name',
        'code',
        'description',
        'costo_credito',
        'costo_carrera',
        'costo_convalidacion',
        'duracion_semestres',
        'modalidad',
        'tipo',
        'is_active',
    ];

    protected $casts = [
        'costo_credito'      => 'decimal:2',
        'costo_carrera'      => 'decimal:2',
        'costo_convalidacion' => 'decimal:2',
        'duracion_semestres' => 'integer',
        'is_active'         => 'boolean',
    ];

    public function horasMinPreprofesional(): int
    {
        return self::HORAS_MIN_PREPROFESIONAL[$this->tipo] ?? 240;
    }

    public function horasMinComunitaria(): int
    {
        return self::HORAS_MIN_COMUNITARIA[$this->tipo] ?? 120;
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            self::TIPO_TECNOLOGICA => 'Tecnológica',
            self::TIPO_TECNICATURA => 'Tecnicatura',
            default                => $this->tipo ?? '—',
        };
    }

    /**
     * Semestres de la carrera
     */
    public function semestres()
    {
        return $this->hasMany(Semestre::class)->orderBy('order');
    }

    /**
     * Períodos lectivos vinculados a esta carrera (con fechas y estado propios).
     */
    public function periodos()
    {
        return $this->belongsToMany(Periodo::class, 'carrera_periodo')
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
     * Retorna el período activo actual de esta carrera, o null si no hay ninguno.
     */
    public function periodoActual(): ?Periodo
    {
        return $this->periodos()
                    ->wherePivot('is_current', true)
                    ->wherePivot('is_active', true)
                    ->first();
    }

    /**
     * Matrículas en esta carrera
     */
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * Scope para carreras activas
     */
    public function scopeActivas($query)
    {
        return $query->where('is_active', true);
    }


    ///para titulacion y practicas preprofesionales
    public function practicasPreprofesionales()
    {
        return $this->hasMany(PracticaPreprofesional::class, 'carrera_id');
    }

    public function notasTitulacion()
    {
        return $this->hasMany(NotaTitulacion::class, 'carrera_id');
    }
}
