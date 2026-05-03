<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\AsignacionDocente;
use App\Models\DetalleMatricula;
use App\Models\Horario;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportesCarrerasMaterias extends Component
{
    // =========================================================================
    // FILTROS PRINCIPALES
    // =========================================================================
    public ?int    $periodoId    = null;
    public string  $tipoReporte  = ''; // 'carrera' | 'materia'
    public ?int    $carreraId    = null;
    public ?int    $materiaId    = null;

    public function mount(): void
    {
        // Sin pre-selección: el admin elige carrera primero
    }

    public function updatedCarreraId(): void
    {
        $this->periodoId = null;
        $this->materiaId = null;

        if ($this->carreraId) {
            $carrera = Carrera::find($this->carreraId);
            $this->periodoId = $carrera?->periodoActual()?->id
                ?? Periodo::whereHas('carreras', fn($q) => $q->where('carreras.id', $this->carreraId))
                    ->orderByDesc('fecha_inicio')->first()?->id;
        }
    }

    // Reset selección secundaria al cambiar tipo
    public function updatedTipoReporte(): void
    {
        $this->materiaId = null;
    }

    public function updatedPeriodoId(): void
    {
        $this->materiaId = null;
    }

    // =========================================================================
    // COMPUTED — SELECTS
    // =========================================================================
    #[Computed]
    public function periodos()
    {
        if (! $this->carreraId) return collect();

        return Periodo::whereHas('carreras', fn($q) => $q->where('carreras.id', $this->carreraId))
            ->orderByDesc('fecha_inicio')->get();
    }

    #[Computed]
    public function carreras()
    {
        return Carrera::where('is_active', true)->orderBy('name')->get();
    }

    #[Computed]
    public function materias()
    {
        if (! $this->periodoId) return collect();

        return Materia::whereHas(
            'asignacionesDocentes',
            fn($q) =>
            $q->where('periodo_id', $this->periodoId)
        )
            ->with('semestre.carrera')
            ->orderBy('name')
            ->get();
    }

    // =========================================================================
    // COMPUTED — REPORTE POR CARRERA
    // =========================================================================
    #[Computed]
    public function reporteCarrera(): array
    {
        if (! $this->periodoId || ! $this->carreraId) return [];

        $carrera  = Carrera::with('semestres.materias')->find($this->carreraId);
        $periodoId = $this->periodoId;

        if (! $carrera) return [];

        $semestresData        = [];
        $totalEstudiantesGlobal = 0;
        $totalMateriasGlobal  = 0;
        $totalHorasGlobal     = 0;

        foreach ($carrera->semestres->sortBy('order') as $semestre) {
            $materiasData         = [];
            $totalEstSemestre     = 0;
            $totalHorasSemestre   = 0;

            foreach ($semestre->materias->sortBy('name') as $materia) {

                // Asignaciones del periodo para esta materia
                $asignaciones = AsignacionDocente::with(['docente', 'paralelo'])
                    ->where('materia_id',  $materia->id)
                    ->where('periodo_id',  $periodoId)
                    ->get();

                $paralelosData = [];

                foreach ($asignaciones as $asig) {
                    // Horarios del paralelo
                    $horarios = Horario::where('materia_id',  $materia->id)
                        ->where('paralelo_id', $asig->paralelo_id)
                        ->where('periodo_id',  $periodoId)
                        ->where('is_active',   true)
                        ->orderBy('dia_semana')
                        ->orderBy('hora_inicio')
                        ->get();

                    // Estudiantes matriculados en este paralelo/materia/periodo
                    $estudiantesCount = DetalleMatricula::where('materia_id',  $materia->id)
                        ->where('paralelo_id', $asig->paralelo_id)
                        ->whereHas(
                            'matricula',
                            fn($q) =>
                            $q->where('periodo_id', $periodoId)
                                ->where('estado', 'Habilitada')
                        )
                        ->count();

                    // Promedio de notas finales del grupo
                    $promedioGrupo = DetalleMatricula::where('materia_id',  $materia->id)
                        ->where('paralelo_id', $asig->paralelo_id)
                        ->whereHas(
                            'matricula',
                            fn($q) =>
                            $q->where('periodo_id', $periodoId)
                        )
                        ->whereHas(
                            'calificaciones',
                            fn($q) =>
                            $q->whereNotNull('nota_final')
                        )
                        ->with('calificaciones')
                        ->get()
                        ->map(fn($d) => $d->calificaciones->first()?->nota_final)
                        ->filter()
                        ->avg();

                    $totalHorasParalelo = $horarios->sum(
                        fn($h) => (strtotime($h->hora_fin) - strtotime($h->hora_inicio)) / 3600
                    );

                    $totalEstSemestre   += $estudiantesCount;
                    $totalHorasSemestre += $totalHorasParalelo;

                    $paralelosData[] = [
                        'paralelo'          => $asig->paralelo,
                        'docente'           => $asig->docente,
                        'horarios'          => $horarios,
                        'estudiantes'       => $estudiantesCount,
                        'cupo_maximo'       => $asig->paralelo?->cupo_maximo,
                        'cupo_actual'       => $asig->paralelo?->cupo_actual,
                        'promedio_grupo'    => $promedioGrupo ? round($promedioGrupo, 2) : null,
                        'horas_semana'      => round($totalHorasParalelo, 1),
                    ];
                }

                $materiasData[] = [
                    'materia'         => $materia,
                    'paralelos'       => $paralelosData,
                    'total_estudiantes' => $totalEstSemestre,
                    'sin_asignacion'  => $asignaciones->isEmpty(),
                ];

                $totalEstudiantesGlobal += $totalEstSemestre;
                $totalMateriasGlobal++;
            }

            $totalHorasGlobal += $totalHorasSemestre;

            $semestresData[] = [
                'semestre'           => $semestre,
                'materias'           => $materiasData,
                'total_materias'     => count($materiasData),
                'total_estudiantes'  => $totalEstSemestre,
                'total_horas_semana' => round($totalHorasSemestre, 1),
            ];
        }

        return [
            'carrera'              => $carrera,
            'periodo'              => Periodo::find($periodoId),
            'semestres'            => $semestresData,
            'total_materias'       => $totalMateriasGlobal,
            'total_estudiantes'    => $totalEstudiantesGlobal,
            'total_horas_semana'   => round($totalHorasGlobal, 1),
        ];
    }

    // =========================================================================
    // COMPUTED — REPORTE POR MATERIA
    // =========================================================================
    #[Computed]
    public function reporteMateria(): array
    {
        if (! $this->periodoId || ! $this->materiaId) return [];

        $materia   = Materia::with('semestre.carrera')->find($this->materiaId);
        $periodoId = $this->periodoId;

        if (! $materia) return [];

        $asignaciones = AsignacionDocente::with(['docente', 'paralelo'])
            ->where('materia_id', $materia->id)
            ->where('periodo_id', $periodoId)
            ->get();

        $paralelosData    = [];
        $totalInscritos   = 0;
        $totalAprobados   = 0;
        $totalReprobados  = 0;
        $todasNotas       = [];

        foreach ($asignaciones as $asig) {

            $horarios = Horario::where('materia_id',  $materia->id)
                ->where('paralelo_id', $asig->paralelo_id)
                ->where('periodo_id',  $periodoId)
                ->where('is_active',   true)
                ->orderBy('dia_semana')
                ->orderBy('hora_inicio')
                ->get();

            $detalles = DetalleMatricula::with([
                'matricula.estudiante',
                'calificaciones' => fn($q) => $q->orderByDesc('numero_intento'),
            ])
                ->where('materia_id',  $materia->id)
                ->where('paralelo_id', $asig->paralelo_id)
                ->whereHas(
                    'matricula',
                    fn($q) =>
                    $q->where('periodo_id', $periodoId)
                        ->where('estado', 'Habilitada')
                )
                ->get();

            $estudiantesData = [];

            foreach ($detalles as $det) {
                $cal = $det->calificaciones->first();

                $notaFinal   = $cal?->nota_final;
                $estadoFinal = $cal?->estado_final;

                if ($notaFinal !== null) $todasNotas[] = (float) $notaFinal;
                if ($estadoFinal === 'Aprobado')  $totalAprobados++;
                if ($estadoFinal === 'Reprobado') $totalReprobados++;

                $estudiantesData[] = [
                    'nombre'         => $det->matricula?->estudiante?->name ?? '—',
                    'cedula'         => $det->matricula?->estudiante?->cedula ?? '—',
                    'matricula_num'  => $det->matricula?->estudiante?->matricula_numero ?? '—',
                    'tipo'           => $det->tipo,
                    'promedio_insumos' => $cal?->promedio_insumos,
                    'examen_parcial' => $cal?->examen_parcial,
                    'examen_final'   => $cal?->examen_final,
                    'nota_final'     => $notaFinal,
                    'nota_suspenso'  => $cal?->nota_suspenso,
                    'estado_final'   => $estadoFinal,
                ];
            }

            // Ordenar por nombre
            usort($estudiantesData, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));

            $totalInscritos += count($estudiantesData);

            $promedioParalelo = ! empty($todasNotas)
                ? round(array_sum($todasNotas) / count($todasNotas), 2)
                : null;

            $paralelosData[] = [
                'paralelo'    => $asig->paralelo,
                'docente'     => $asig->docente,
                'horarios'    => $horarios,
                'estudiantes' => $estudiantesData,
                'inscritos'   => count($estudiantesData),
                'aprobados'   => collect($estudiantesData)->where('estado_final', 'Aprobado')->count(),
                'reprobados'  => collect($estudiantesData)->where('estado_final', 'Reprobado')->count(),
                'promedio'    => $promedioParalelo,
            ];
        }

        $promedioGeneral = ! empty($todasNotas)
            ? round(array_sum($todasNotas) / count($todasNotas), 2)
            : null;

        return [
            'materia'          => $materia,
            'periodo'          => Periodo::find($periodoId),
            'paralelos'        => $paralelosData,
            'total_inscritos'  => $totalInscritos,
            'total_aprobados'  => $totalAprobados,
            'total_reprobados' => $totalReprobados,
            'promedio_general' => $promedioGeneral,
            'sin_asignacion'   => $asignaciones->isEmpty(),
        ];
    }

    public function render()
    {
        return view('livewire.administration.reportes-carreras-materias');
    }

    public function exportarPDF()
    {
        if ($this->tipoReporte === 'carrera') {
            $data = $this->reporteCarrera;

            $pdf = Pdf::loadView('pdf.reporte-carrera', [
                'data' => $data
            ])->setPaper('a4', 'landscape');

            return response()->streamDownload(
                fn() => print($pdf->output()),
                'reporte-carrera.pdf'
            );
        }

        if ($this->tipoReporte === 'materia') {
            $data = $this->reporteMateria;

            $pdf = Pdf::loadView('pdf.reporte-materia', [
                'data' => $data
            ])->setPaper('a4', 'portrait');

            return response()->streamDownload(
                fn() => print($pdf->output()),
                'reporte-materia.pdf'
            );
        }
    }
}
