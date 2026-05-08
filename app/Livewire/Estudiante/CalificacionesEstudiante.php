<?php

namespace App\Livewire\Estudiante;

use App\Models\DetalleMatricula;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Periodo;
use App\Models\Matricula;

class CalificacionesEstudiante extends Component
{
    public $periodos = [];
    public $periodo_id = null;

    public $matricula = null;

    public $filas = [];

    public $resumen = [
        'total_materias' => 0,
        'aprobadas' => 0,
        'reprobadas' => 0,
        'promedio_general' => 0,
    ];

    public function mount()
    {
        // Periodos activos (puedes ajustar el filtro si deseas)
        $this->periodos = Periodo::orderBy('id', 'desc')->get();

        // Período activo para la carrera del estudiante
        $ultimaMatricula  = Auth::user()->matriculas()->with('carrera')->latest()->first();
        $periodoDeCarrera = $ultimaMatricula?->carrera?->periodoActual();
        $this->periodo_id = $periodoDeCarrera?->id ?? $this->periodos->first()?->id;

        $this->cargarActa();
    }

    public function updatedPeriodoId()
    {
        $this->cargarActa();
    }

    public function cargarActa()
    {
        $this->filas = [];
        $this->matricula = null;

        $this->resumen = [
            'total_materias' => 0,
            'aprobadas' => 0,
            'reprobadas' => 0,
            'promedio_general' => 0,
        ];

        if (!$this->periodo_id) return;

        $userId = Auth::id();

        // 1) Buscar la matrícula del estudiante en ese periodo
        $this->matricula = Matricula::where('user_id', $userId)
            ->where('periodo_id', $this->periodo_id)
            ->latest('id')
            ->first();

        if (!$this->matricula) {
            return;
        }

        // 2) Traer los detalles con materia, paralelo y calificacion
        $detalles = DetalleMatricula::with([
            'materia:id,name,code,nota_minima_aprobacion',
            'paralelo:id,code',
            'calificacion' => function ($q) {
                $q->with('docente:id,name');
            }
            /*  'calificaciones' => function ($q) {
    $q->with('docente:id,name');
} */
        ])
            ->where('matricula_id', $this->matricula->id)
            ->where('estado', 'Inscrito')
            ->orderBy('materia_id')
            ->get();

        // 3) Formatear filas para la tabla
        $this->filas = $detalles->map(function ($detalle) {

            $cal = $detalle->calificacion; // puede ser null

            $notaFinal = $cal?->nota_final;
            $estado = $cal?->estado_final ?? 'Pendiente';

            return [
                'detalle_id' => $detalle->id,
                'materia' => $detalle->materia?->name ?? '---',
                'materia_code' => $detalle->materia?->code ?? '',
                'paralelo' => $detalle->paralelo?->code ?? '---',

                'tipo' => $detalle->tipo,
                'es_repeticion' => $detalle->es_repeticion ? 'Sí' : 'No',

                // notas
                'insumo1' => $cal?->insumo1,
                'insumo2' => $cal?->insumo2,
                'insumo3' => $cal?->insumo3,
                'insumo4' => $cal?->insumo4,
                'insumo5' => $cal?->insumo5,
                'promedio_insumos' => $cal?->promedio_insumos,

                'examen_parcial' => $cal?->examen_parcial,
                'examen_final' => $cal?->examen_final,

                'nota_final' => $notaFinal,
                'nota_suspenso' => $cal?->nota_suspenso,

                'estado_final' => $estado,
                'es_borrador' => $cal?->es_borrador ?? false,

                'docente' => $cal?->docente?->name ?? '---',
                'fecha_calificada' => $cal?->updated_at?->format('d/m/Y H:i') ?? null,
            ];
        })->values()->toArray();

        // 4) Resumen
        $total = count($this->filas);

        $aprobadas = collect($this->filas)->where('estado_final', 'Aprobado')->count();
        $reprobadas = collect($this->filas)->where('estado_final', 'Reprobado')->count();

        $notasValidas = collect($this->filas)
            ->pluck('nota_final')
            ->filter(fn($n) => $n !== null);

        $promedioGeneral = $notasValidas->count() > 0 ? $notasValidas->avg() : 0;

        $this->resumen = [
            'total_materias' => $total,
            'aprobadas' => $aprobadas,
            'reprobadas' => $reprobadas,
            'promedio_general' => round($promedioGeneral, 2),
        ];
    }

    public function render()
    {
        return view('livewire.estudiante.calificaciones-estudiante');
    }
}
