<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\AsignacionDocente;
use App\Models\DetalleMatricula;
use App\Models\Horario;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\CarreraPeriodo;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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

        $semestresData       = [];
        $totalMateriasGlobal = 0;
        $totalHorasGlobal    = 0;

        foreach ($carrera->semestres->sortBy('order') as $semestre) {
            $materiasData       = [];
            $totalHorasSemestre = 0;

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

                    // Inscritos en este paralelo/materia (para mostrar en la tabla por materia)
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

                    $totalHorasParalelo  = $horarios->sum(
                        fn($h) => (strtotime($h->hora_fin) - strtotime($h->hora_inicio)) / 3600
                    );
                    $totalHorasSemestre += $totalHorasParalelo;

                    $paralelosData[] = [
                        'paralelo'       => $asig->paralelo,
                        'docente'        => $asig->docente,
                        'horarios'       => $horarios,
                        'estudiantes'    => $estudiantesCount,
                        'cupo_maximo'    => $asig->paralelo?->cupo_maximo,
                        'cupo_actual'    => $asig->paralelo?->cupo_actual,
                        'promedio_grupo' => $promedioGrupo ? round($promedioGrupo, 2) : null,
                        'horas_semana'   => round($totalHorasParalelo, 1),
                    ];
                }

                $materiasData[] = [
                    'materia'        => $materia,
                    'paralelos'      => $paralelosData,
                    'sin_asignacion' => $asignaciones->isEmpty(),
                ];

                $totalMateriasGlobal++;
            }

            $totalHorasGlobal += $totalHorasSemestre;

            // Estudiantes únicos inscritos en al menos una materia de este semestre
            $estudiantesSemestre = DetalleMatricula::whereIn('materia_id', $semestre->materias->pluck('id'))
                ->whereHas('matricula', fn($q) => $q->where('periodo_id', $periodoId)->where('estado', 'Habilitada'))
                ->distinct('matricula_id')
                ->count('matricula_id');

            $semestresData[] = [
                'semestre'           => $semestre,
                'materias'           => $materiasData,
                'total_materias'     => count($materiasData),
                'total_estudiantes'  => $estudiantesSemestre,
                'total_horas_semana' => round($totalHorasSemestre, 1),
            ];
        }

        // Estudiantes únicos matriculados en esta carrera/período (no sumar por materia)
        $totalEstudiantesGlobal = Matricula::where('carrera_id', $this->carreraId)
            ->where('periodo_id', $periodoId)
            ->where('estado', 'Habilitada')
            ->count();

        // Paralelos únicos con asignación docente en este período
        $totalParalelossGlobal = AsignacionDocente::where('periodo_id', $periodoId)
            ->whereHas('materia.semestre', fn($q) => $q->where('carrera_id', $this->carreraId))
            ->distinct('paralelo_id')
            ->count('paralelo_id');

        $pivotPeriodo = CarreraPeriodo::where('carrera_id', $this->carreraId)
            ->where('periodo_id', $periodoId)
            ->first();

        return [
            'carrera'              => $carrera,
            'periodo'              => Periodo::find($periodoId),
            'periodo_pivot'        => $pivotPeriodo,
            'semestres'            => $semestresData,
            'total_materias'       => $totalMateriasGlobal,
            'total_estudiantes'    => $totalEstudiantesGlobal,
            'total_paralelos'      => $totalParalelossGlobal,
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

        $carreraId    = $materia->semestre?->carrera_id;
        $pivotPeriodo = $carreraId
            ? CarreraPeriodo::where('carrera_id', $carreraId)
                ->where('periodo_id', $periodoId)
                ->first()
            : null;

        return [
            'materia'          => $materia,
            'periodo'          => Periodo::find($periodoId),
            'periodo_pivot'    => $pivotPeriodo,
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
        $piePagina = SettingService::get(
            'documentos.pie_pagina',
            'Documento generado por el Sistema Académico del ISTC. Válido solo con firma y sello institucional.'
        );

        if ($this->tipoReporte === 'carrera') {
            $data = $this->reporteCarrera;

            $pdf = Pdf::loadView('pdf.reporte-carrera', ['data' => $data])
                ->setPaper('a4', 'landscape')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
                ->setOption('dpi', 96);

            $pdf->render();
            $this->agregarFooterCanvas($pdf->getDomPDF()->getCanvas(), $piePagina);

            $nombre = 'reporte-carrera_'
                . Str::slug($data['carrera']->name ?? 'carrera') . '_'
                . ($data['periodo']->code ?? now()->format('Ymd'))
                . '.pdf';

            return response()->streamDownload(
                fn() => print($pdf->output()),
                $nombre
            );
        }

        if ($this->tipoReporte === 'materia') {
            $data = $this->reporteMateria;

            $pdf = Pdf::loadView('pdf.reporte-materia', ['data' => $data])
                ->setPaper('a4', 'portrait')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
                ->setOption('dpi', 96);

            $pdf->render();
            $this->agregarFooterCanvas($pdf->getDomPDF()->getCanvas(), $piePagina);

            $nombre = 'reporte-materia_'
                . Str::slug($data['materia']->name ?? 'materia') . '_'
                . ($data['periodo']->code ?? now()->format('Ymd'))
                . '.pdf';

            return response()->streamDownload(
                fn() => print($pdf->output()),
                $nombre
            );
        }
    }

    // Añade pie de página + número de página en el canvas de DomPDF (funciona en todas las páginas)
    private function agregarFooterCanvas(\Dompdf\Canvas $canvas, string $piePagina): void
    {
        $font  = $canvas->get_dompdf()->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $w     = $canvas->get_width();
        $h     = $canvas->get_height();

        // y=0 is the TOP of the page in DomPDF canvas; place footer near the bottom
        $yLine = $h - 28;
        $yTxt  = $h - 20;

        // Línea separadora verde
        $canvas->page_line(10, $yLine, $w - 10, $yLine, [0.08, 0.27, 0.10], 0.5);

        // Texto institucional (izquierda)
        $canvas->page_text(10, $yTxt, $piePagina, $font, 5.5, [0.58, 0.64, 0.71]);

        // Número de página (derecha) — {PAGE_NUM} y {PAGE_COUNT} son tokens nativos de DomPDF
        $canvas->page_text($w - 68, $yTxt, 'Pág. {PAGE_NUM} / {PAGE_COUNT}', $font, 7, [0.08, 0.27, 0.10]);
    }
}
