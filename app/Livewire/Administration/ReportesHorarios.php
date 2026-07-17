<?php

namespace App\Livewire\Administration;

use App\Exports\HorariosExport;
use App\Models\Carrera;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Periodo;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReportesHorarios extends Component
{
    public ?int $periodoId = null;
    public ?int $carreraId = null;
    public ?int $materiaId = null;

    protected const DIAS = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

    protected const PALETA = [
        '#2563eb', '#16a34a', '#dc2626', '#7c3aed',
        '#ea580c', '#0891b2', '#0f766e', '#ca8a04',
    ];

    public function mount(): void
    {
        $this->periodoId = Periodo::orderByDesc('fecha_inicio')->first()?->id;
    }

    public function updatedPeriodoId(): void
    {
        $this->carreraId = null;
        $this->materiaId = null;
    }

    public function updatedCarreraId(): void
    {
        $this->materiaId = null;
    }

    // =========================================================================
    // SELECTS
    // =========================================================================
    #[Computed]
    public function periodos()
    {
        return Periodo::orderByDesc('fecha_inicio')->get();
    }

    #[Computed]
    public function carreras()
    {
        if (!$this->periodoId) return collect();

        return Carrera::whereHas('periodos', fn($q) => $q->where('periodos.id', $this->periodoId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function materias()
    {
        if (!$this->periodoId) return collect();

        $q = Materia::whereHas(
            'horarios',
            fn($q) => $q->where('periodo_id', $this->periodoId)->where('is_active', true)
        )->with('semestre.carrera')->orderBy('name');

        if ($this->carreraId) {
            $q->whereHas('semestre', fn($q) => $q->where('carrera_id', $this->carreraId));
        }

        return $q->get();
    }

    // =========================================================================
    // HORARIOS CARGADOS (base de todo)
    // =========================================================================
    #[Computed]
    public function horariosCargados()
    {
        if (!$this->periodoId) return collect();

        $q = Horario::with([
            'materia:id,name,code,horas_teoricas,horas_practicas,semestre_id',
            'materia.semestre:id,name,order,carrera_id',
            'materia.semestre.carrera:id,name',
            'paralelo:id,name,code',
            'asignacionDocente:id,docente_id,materia_id,paralelo_id',
            'asignacionDocente.docente:id,name',
        ])
            ->where('periodo_id', $this->periodoId)
            ->where('is_active', true);

        if ($this->carreraId) {
            $q->whereHas('materia.semestre', fn($q) => $q->where('carrera_id', $this->carreraId));
        }

        if ($this->materiaId) {
            $q->where('materia_id', $this->materiaId);
        }

        return $q->orderBy('dia_semana')->orderBy('hora_inicio')->get();
    }

    // =========================================================================
    // CONFLICTOS
    // =========================================================================
    #[Computed]
    public function conflictos(): array
    {
        $horarios = $this->horariosCargados;
        $result   = [];
        $vistos   = [];
        $lista    = $horarios->values();

        for ($i = 0; $i < $lista->count(); $i++) {
            for ($j = $i + 1; $j < $lista->count(); $j++) {
                $h1 = $lista[$i];
                $h2 = $lista[$j];

                if ($h1->dia_semana !== $h2->dia_semana) continue;
                if (!$this->solapan($h1, $h2)) continue;

                $tipo        = null;
                $descripcion = '';

                if ($h1->aula && $h2->aula && $h1->aula === $h2->aula) {
                    $tipo        = 'aula';
                    $descripcion = "Aula {$h1->aula}";
                } elseif (
                    $h1->asignacionDocente?->docente_id &&
                    $h1->asignacionDocente->docente_id === $h2->asignacionDocente?->docente_id
                ) {
                    $tipo        = 'docente';
                    $descripcion = $h1->asignacionDocente->docente?->name ?? 'Docente';
                } elseif ($h1->paralelo_id === $h2->paralelo_id) {
                    $tipo        = 'paralelo';
                    $descripcion = "Paralelo {$h1->paralelo?->name}";
                }

                if (!$tipo) continue;

                $key = "{$tipo}-{$h1->id}-{$h2->id}";
                if (isset($vistos[$key])) continue;
                $vistos[$key] = true;

                $result[] = [
                    'tipo'        => $tipo,
                    'dia'         => $h1->dia_semana,
                    'descripcion' => $descripcion,
                    'horario_a'   => $this->horarioResumen($h1),
                    'horario_b'   => $this->horarioResumen($h2),
                ];
            }
        }

        return $result;
    }

    // =========================================================================
    // STATS
    // =========================================================================
    #[Computed]
    public function stats(): array
    {
        $horarios = $this->horariosCargados;

        if ($horarios->isEmpty()) {
            return [
                'total_clases'     => 0,
                'docentes_unicos'  => 0,
                'paralelos_unicos' => 0,
                'horas_semana'     => 0,
                'total_creditos'   => 0,
                'total_conflictos' => 0,
            ];
        }

        $horasSemana = $horarios->sum(
            fn($h) => (strtotime($h->hora_fin) - strtotime($h->hora_inicio)) / 3600
        );

        $docentesUnicos  = $horarios->pluck('asignacionDocente.docente_id')->filter()->unique()->count();
        $paralelosUnicos = $horarios->pluck('paralelo_id')->unique()->count();

        $totalCreditos = $horarios
            ->pluck('materia')
            ->filter()
            ->unique('id')
            ->sum(fn($m) => ($m->horas_teoricas + $m->horas_practicas) / 48);

        return [
            'total_clases'     => $horarios->count(),
            'docentes_unicos'  => $docentesUnicos,
            'paralelos_unicos' => $paralelosUnicos,
            'horas_semana'     => round($horasSemana, 1),
            'total_creditos'   => round($totalCreditos, 2),
            'total_conflictos' => count($this->conflictos),
        ];
    }

    // =========================================================================
    // GRILLA (carrera → semestre → dia → [cards])
    // =========================================================================
    #[Computed]
    public function grillaData(): array
    {
        $horarios    = $this->horariosCargados;
        $conflictIds = collect($this->conflictos)
            ->flatMap(fn($c) => [$c['horario_a']['id'], $c['horario_b']['id']])
            ->unique()->flip()->toArray();

        $estructura = [];

        foreach ($horarios as $h) {
            $carreraId     = $h->materia?->semestre?->carrera_id ?? 0;
            $carreraNombre = $h->materia?->semestre?->carrera?->name ?? 'Sin carrera';
            $semestreId    = $h->materia?->semestre_id ?? 0;
            $semestreNom   = $h->materia?->semestre?->name ?? 'Sin semestre';
            $semestreOrd   = $h->materia?->semestre?->order ?? 99;
            $dia           = $h->dia_semana;

            if (!isset($estructura[$carreraId])) {
                $estructura[$carreraId] = ['nombre' => $carreraNombre, 'semestres' => []];
            }

            if (!isset($estructura[$carreraId]['semestres'][$semestreId])) {
                $estructura[$carreraId]['semestres'][$semestreId] = [
                    'nombre' => $semestreNom,
                    'order'  => $semestreOrd,
                    'dias'   => array_fill_keys(self::DIAS, []),
                ];
            }

            $estructura[$carreraId]['semestres'][$semestreId]['dias'][$dia][] = [
                'id'              => $h->id,
                'materia'         => $h->materia?->name,
                'materia_code'    => $h->materia?->code,
                'horas_teoricas'  => $h->materia?->horas_teoricas ?? 0,
                'horas_practicas' => $h->materia?->horas_practicas ?? 0,
                'paralelo'        => $h->paralelo?->name,
                'paralelo_code'   => $h->paralelo?->code,
                'docente'         => $h->asignacionDocente?->docente?->name,
                'aula'            => $h->aula,
                'hora_inicio'     => $h->hora_inicio->format('H:i'),
                'hora_fin'        => $h->hora_fin->format('H:i'),
                'modalidad'       => $h->modalidad_clase,
                'color'           => self::PALETA[($h->materia_id ?? 0) % count(self::PALETA)],
                'conflicto'       => isset($conflictIds[$h->id]),
            ];
        }

        foreach ($estructura as &$carrera) {
            uasort($carrera['semestres'], fn($a, $b) => $a['order'] <=> $b['order']);
        }
        unset($carrera);

        return $estructura;
    }

    // =========================================================================
    // EXPORTS
    // =========================================================================
    public function exportarPDF()
    {
        if (!$this->periodoId) return;

        $piePagina = SettingService::get(
            'documentos.pie_pagina',
            'Documento generado por el Sistema Académico del ISTC. Válido solo con firma y sello institucional.'
        );

        $data = [
            'grilla'     => $this->grillaData,
            'stats'      => $this->stats,
            'conflictos' => $this->conflictos,
            'dias'       => self::DIAS,
            'periodo'    => Periodo::find($this->periodoId),
            'carrera'    => $this->carreraId ? Carrera::find($this->carreraId) : null,
            'materia'    => $this->materiaId ? Materia::find($this->materiaId) : null,
        ];

        $pdf = Pdf::loadView('pdf.reporte-horarios', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96);

        $pdf->render();
        $this->agregarFooterCanvas($pdf->getDomPDF()->getCanvas(), $piePagina);

        $nombre = 'horarios_' . Str::slug($data['periodo']?->code ?? now()->format('Ymd')) . '.pdf';

        return response()->streamDownload(fn() => print($pdf->output()), $nombre);
    }

    public function exportarExcel()
    {
        if (!$this->periodoId) return;

        $nombre = 'horarios_' . Str::slug(Periodo::find($this->periodoId)?->code ?? now()->format('Ymd')) . '.xlsx';

        return Excel::download(
            new HorariosExport($this->horariosCargados, $this->conflictos, $this->stats),
            $nombre
        );
    }

    public function render()
    {
        return view('livewire.administration.reportes-horarios');
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================
    private function solapan(Horario $a, Horario $b): bool
    {
        $aIni = strtotime($a->hora_inicio);
        $aFin = strtotime($a->hora_fin);
        $bIni = strtotime($b->hora_inicio);
        $bFin = strtotime($b->hora_fin);

        return $aIni < $bFin && $bIni < $aFin;
    }

    private function horarioResumen(Horario $h): array
    {
        return [
            'id'       => $h->id,
            'materia'  => $h->materia?->name,
            'paralelo' => $h->paralelo?->name,
            'hora_ini' => $h->hora_inicio->format('H:i'),
            'hora_fin' => $h->hora_fin->format('H:i'),
        ];
    }

    private function agregarFooterCanvas(\Dompdf\Canvas $canvas, string $piePagina): void
    {
        $font  = $canvas->get_dompdf()->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $w     = $canvas->get_width();
        $h     = $canvas->get_height();
        $yLine = $h - 28;
        $yTxt  = $h - 20;

        $canvas->page_line(10, $yLine, $w - 10, $yLine, [0.08, 0.27, 0.10], 0.5);
        $canvas->page_text(10, $yTxt, $piePagina, $font, 5.5, [0.58, 0.64, 0.71]);
        $canvas->page_text($w - 68, $yTxt, 'Pág. {PAGE_NUM} / {PAGE_COUNT}', $font, 7, [0.08, 0.27, 0.10]);
    }
}
