<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaTitulacion extends Model
{
    protected $table = 'notas_titulacion';

    protected $fillable = [
        'user_id',
        'carrera_id',
        'practica_id',
        'comunitaria_id',
        'promedio_malla',
        'tipo_titulacion',
        'nota_titulacion',
        'nota_practicas',
        'nota_final_egreso',
        'estado',
        'numero_intento',
        'presidente_tribunal',
        'miembro_tribunal_1',
        'miembro_tribunal_2',
        'fecha_registro',
        'fecha_evaluacion',
        'documento_titulacion_path',
        'observaciones',
    ];

    protected $casts = [
        'promedio_malla'    => 'decimal:2',
        'nota_titulacion'   => 'decimal:2',
        'nota_practicas'    => 'decimal:2',
        'nota_final_egreso' => 'decimal:2',
        'fecha_registro'    => 'date',
        'fecha_evaluacion'  => 'date',
        'numero_intento'    => 'integer',
    ];

    // =========================================================================
    // CONSTANTES
    // =========================================================================
    const TIPO_EXAMEN_COMPLEXIVO     = 'Examen_Complexivo';
    const TIPO_PROYECTO_INVESTIGACION = 'Proyecto_Investigacion';

    const ESTADO_PENDIENTE = 'Pendiente';
    const ESTADO_APROBADO  = 'Aprobado';
    const ESTADO_REPROBADO = 'Reprobado';

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

    public function practica(): BelongsTo
    {
        return $this->belongsTo(PracticaPreprofesional::class, 'practica_id');
    }

    public function comunitaria(): BelongsTo
    {
        return $this->belongsTo(Comunitaria::class, 'comunitaria_id');
    }

    // =========================================================================
    // CÁLCULO AUTOMÁTICO DEL PROMEDIO DE MALLA
    // =========================================================================

    /**
     * Calcula el promedio de malla del estudiante en su carrera.
     *
     * Lógica:
     *  1. Por cada semestre de la carrera → promedio de nota_final de sus materias
     *  2. Promedio de malla = promedio de todos los promedios por semestre
     *
     * Solo considera materias con estado_final = 'Aprobado' o 'Reprobado'
     * (excluye Retirado e Incompleto del cálculo)
     */
    public static function calcularPromedioMalla(int $userId, int $carreraId): ?float
    {
        $carrera = Carrera::with([
            'semestres.materias',
        ])->find($carreraId);

        if (! $carrera) return null;

        $promediosPorSemestre = [];

        foreach ($carrera->semestres as $semestre) {
            $notasSemestre = [];

            foreach ($semestre->materias as $materia) {
                // Buscar la mejor calificación aprobada del estudiante en esta materia
                // Si no aprobó, tomar la última nota final registrada
                $calificacion = \App\Models\Calificacion::whereHas('detalleMatricula', function ($q) use ($userId, $materia) {
                    $q->where('user_id', $userId)
                        ->where('materia_id', $materia->id);
                })
                    ->whereNotNull('nota_final')
                    ->whereIn('estado_final', ['Aprobado', 'Reprobado'])
                    ->orderByDesc('numero_intento')
                    ->first();

                if ($calificacion) {
                    $notasSemestre[] = (float) $calificacion->nota_final;
                }
            }

            if (! empty($notasSemestre)) {
                $promediosPorSemestre[] = array_sum($notasSemestre) / count($notasSemestre);
            }
        }

        if (empty($promediosPorSemestre)) return null;

        $promedio = array_sum($promediosPorSemestre) / count($promediosPorSemestre);

        return round($promedio, 2);
    }

    /**
     * Calcula y actualiza el promedio de malla en el registro actual
     */
    public function recalcularPromedioMalla(): void
    {
        $promedio = self::calcularPromedioMalla($this->user_id, $this->carrera_id);

        if ($promedio !== null) {
            $this->promedio_malla = $promedio;
            $this->recalcularNotaFinal();
        }
    }

    /**
     * Recalcula la nota final de egreso y actualiza el estado
     * nota_final_egreso = (promedio_malla + nota_titulacion + nota_practicas) / 3
     */
    public function recalcularNotaFinal(): void
    {
        // La nota de comunitarias viene de la relación, no es columna directa
        $this->loadMissing('comunitaria');
        $notaComunitaria = $this->comunitaria?->nota;

        if (
            $this->promedio_malla  !== null &&
            $this->nota_titulacion !== null &&
            $this->nota_practicas  !== null &&
            $notaComunitaria       !== null
        ) {
            $promedio_practicas = ((float) $this->nota_practicas + (float) $notaComunitaria) / 2;
            $notaFinal = (
                (float) $this->promedio_malla  +
                (float) $this->nota_titulacion +
                (float) $promedio_practicas
            ) / 3;

            $this->nota_final_egreso = round($notaFinal, 2);
            $this->estado = $notaFinal >= self::NOTA_MINIMA
                ? self::ESTADO_APROBADO
                : self::ESTADO_REPROBADO;
        }

        $this->save();
    }

    // =========================================================================
    // HELPERS ESTÁTICOS
    // =========================================================================

    /**
     * Obtiene el número del próximo intento del estudiante en esa carrera
     */
    public static function proximoIntento(int $userId, int $carreraId): int
    {
        $ultimo = static::where('user_id', $userId)
            ->where('carrera_id', $carreraId)
            ->max('numero_intento');

        return ($ultimo ?? 0) + 1;
    }

    /**
     * Verifica si el estudiante aprobó al menos el 80% de la malla curricular.
     * Se usa para habilitar prácticas preprofesionales y comunitarias.
     */
    public static function mallaAlcanza80Porciento(int $userId, int $carreraId): bool
    {
        $carrera = Carrera::with('semestres.materias')->find($carreraId);
        if (! $carrera) return false;

        $total    = 0;
        $aprobadas = 0;

        foreach ($carrera->semestres as $semestre) {
            foreach ($semestre->materias as $materia) {
                $total++;

                $estaAprobada = \App\Models\Calificacion::whereHas('detalleMatricula', function ($q) use ($userId, $materia) {
                    $q->where('user_id', $userId)
                      ->where('materia_id', $materia->id);
                })
                ->where('estado_final', 'Aprobado')
                ->exists();

                if ($estaAprobada) $aprobadas++;
            }
        }

        return $total > 0 && ($aprobadas / $total) >= 0.80;
    }

    /**
     * Verifica si el estudiante completó todos los semestres de la carrera
     * para habilitar el proceso de titulación
     */
    public static function mallaCurricular_Completada(int $userId, int $carreraId): bool
    {
        $carrera = Carrera::with('semestres.materias')->find($carreraId);

        if (! $carrera) return false;

        foreach ($carrera->semestres as $semestre) {
            foreach ($semestre->materias as $materia) {
                $aprobada = \App\Models\Calificacion::whereHas('detalleMatricula', function ($q) use ($userId, $materia) {
                    $q->where('user_id', $userId)
                        ->where('materia_id', $materia->id);
                })
                    ->where('estado_final', 'Aprobado')
                    ->exists();

                if (! $aprobada) return false;
            }
        }

        return true;
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================
    public function getAprobadoAttribute(): bool
    {
        return $this->estado === self::ESTADO_APROBADO;
    }

    public function getTipoTitulacionLabelAttribute(): string
    {
        return match ($this->tipo_titulacion) {
            self::TIPO_EXAMEN_COMPLEXIVO      => 'Examen Complexivo',
            self::TIPO_PROYECTO_INVESTIGACION => 'Proyecto de Investigación',
            default                           => $this->tipo_titulacion,
        };
    }

    // =========================================================================
    // SCOPES
    // =========================================================================
    public function scopeAprobados($query)
    {
        return $query->where('estado', self::ESTADO_APROBADO);
    }

    public function scopeVigente($query)
    {
        // El intento más reciente por estudiante+carrera
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('notas_titulacion')
                ->groupBy('user_id', 'carrera_id');
        });
    }
}
