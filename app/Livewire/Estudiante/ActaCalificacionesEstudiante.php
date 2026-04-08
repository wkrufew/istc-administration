<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\Carrera;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\NotaTitulacion;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class ActaCalificacionesEstudiante extends Component
{
    public $user;

    public $expandedMateria = null; // ID de la materia con detalle de insumos abierto

    public function mount()
    {
        $this->user = Auth::user();
    }
    // =========================================================================
    // COMPUTED PRINCIPAL — toda la estructura del acta
    // =========================================================================
    #[Computed]
    public function acta(): array
    {
        $user    = $this->user;
        $userId  = $user->id;

        // Matrícula más reciente habilitada para obtener carrera
        $matricula = Matricula::with('carrera.semestres.materias')
            ->where('user_id', $userId)
            ->where('estado', 'Habilitada')
            ->latest()
            ->first();

        if (! $matricula) return [];

        $carrera  = $matricula->carrera;
        $semestres = $carrera->semestres->sortBy('order');

        $semestresData      = [];
        $promediosSemestres = [];

        foreach ($semestres as $semestre) {
            $materiasData = [];
            $notasSemestre = [];
            $semestretieneDatos = false;

            foreach ($semestre->materias->sortBy('name') as $materia) {
                // Buscar el detalle de matrícula del estudiante para esta materia
                $detalle = \App\Models\DetalleMatricula::with([
                    'calificaciones' => fn($q) => $q->orderByDesc('numero_intento'),
                    'paralelo',
                ])
                    ->where('user_id', $userId)
                    ->where('materia_id', $materia->id)
                    ->latest()
                    ->first();

                $calificacion = $detalle?->calificaciones->first();

                if ($calificacion) {
                    $semestretieneDatos = true;
                    if ($calificacion->nota_final !== null) {
                        $notasSemestre[] = (float) $calificacion->nota_final;
                    }
                }

                $materiasData[] = [
                    'materia_id'       => $materia->id,
                    'materia_nombre'   => $materia->name,
                    'materia_code'     => $materia->code,
                    'creditos'         => $materia->credits,
                    'tipo'             => $detalle?->tipo ?? 'Normal',
                    'tiene_calificacion' => $calificacion !== null,
                    // Insumos (para tooltip)
                    'insumo1'          => $calificacion?->insumo1,
                    'insumo2'          => $calificacion?->insumo2,
                    'insumo3'          => $calificacion?->insumo3,
                    'insumo4'          => $calificacion?->insumo4,
                    'insumo5'          => $calificacion?->insumo5,
                    'promedio_insumos' => $calificacion?->promedio_insumos,
                    'examen_parcial'   => $calificacion?->examen_parcial,
                    'examen_final'     => $calificacion?->examen_final,
                    'nota_final'       => $calificacion?->nota_final,
                    'nota_suspenso'    => $calificacion?->nota_suspenso,
                    'estado_final'     => $calificacion?->estado_final,
                    'es_arrastre'      => $calificacion?->es_arrastre ?? false,
                    'numero_intento'   => $calificacion?->numero_intento ?? 1,
                ];
            }

            $promedio = ! empty($notasSemestre)
                ? round(array_sum($notasSemestre) / count($notasSemestre), 2)
                : null;

            if ($promedio !== null) {
                $promediosSemestres[] = $promedio;
            }

            $semestresData[] = [
                'semestre_id'    => $semestre->id,
                'semestre_nombre' => $semestre->name,
                'semestre_order' => $semestre->order,
                'tiene_datos'    => $semestretieneDatos,
                'materias'       => $materiasData,
                'promedio'       => $promedio,
                'total_materias' => count($materiasData),
                'aprobadas'      => collect($materiasData)->where('estado_final', 'Aprobado')->count(),
            ];
        }

        // Promedio de malla acumulado
        $promedioMalla = ! empty($promediosSemestres)
            ? round(array_sum($promediosSemestres) / count($promediosSemestres), 2)
            : null;

        // Titulación si existe
        $titulacion = NotaTitulacion::where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->latest('numero_intento')
            ->first();

        return [
            'estudiante'     => $this->user,
            'carrera'        => $carrera,
            'matricula'      => $matricula,
            'semestres'      => $semestresData,
            'promedio_malla' => $promedioMalla,
            'titulacion'     => $titulacion,
            'malla_completa' => NotaTitulacion::mallaCurricular_Completada($userId, $carrera->id),
        ];
    }

    public function toggleInsumos(int $materiaId): void
    {
        $this->expandedMateria = $this->expandedMateria === $materiaId
            ? null
            : $materiaId;
    }

    public function render()
    {
        return view('livewire.estudiante.acta-calificaciones-estudiante', [
            'acta' => $this->acta,
        ]);
    }
}
