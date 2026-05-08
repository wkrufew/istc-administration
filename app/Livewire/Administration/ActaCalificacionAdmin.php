<?php

namespace App\Livewire\Administration;

use App\Models\CarreraPeriodo;
use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\NotaTitulacion;
use App\Models\PracticaPreprofesional;
use App\Models\User;
use App\Services\SettingService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ActaCalificacionAdmin extends Component
{

    public int $userId;
    public $expandedMateria = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
        abort_unless(User::where('id', $userId)->exists(), 404);
    }

    #[Computed]
    public function estudiante()
    {
        return User::find($this->userId);
    }

    #[Computed]
    public function acta(): array
    {
        $userId = $this->userId;

        $matricula = Matricula::with('carrera.semestres.materias', 'periodo')
            ->where('user_id', $userId)
            ->where('estado', 'Habilitada')
            ->latest()
            ->first();

        if (! $matricula) return [];

        $carrera   = $matricula->carrera;
        $semestres = $carrera->semestres->sortBy('order');

        // Pre-cargar todos los detalles del estudiante de una sola query (evita N+1)
        $allMateriaIds = $semestres->flatMap(fn($s) => $s->materias)->pluck('id');

        $detallesPorMateria = DetalleMatricula::with([
            'calificaciones' => fn($q) => $q->orderByDesc('numero_intento'),
            'paralelo',
            'matricula.periodo',
        ])
            ->where('user_id', $userId)
            ->whereIn('materia_id', $allMateriaIds)
            ->latest()
            ->get()
            ->groupBy('materia_id')
            ->map(fn($group) => $group->first());

        $semestresData      = [];
        $promediosSemestres = [];

        foreach ($semestres as $semestre) {
            $materiasData       = [];
            $notasSemestre      = [];
            $semestretieneDatos = false;

            foreach ($semestre->materias->sortBy('name') as $materia) {
                $detalle = $detallesPorMateria->get($materia->id);

                $calificacion = $detalle?->calificaciones->first();

                if ($calificacion) {
                    $semestretieneDatos = true;
                    if ($calificacion->nota_final !== null) {
                        $notasSemestre[] = (float) $calificacion->nota_final;
                    }
                }

                $materiasData[] = [
                    'materia_id'         => $materia->id,
                    'materia_nombre'     => $materia->name,
                    'materia_code'       => $materia->code,
                    'creditos'           => $materia->credits,
                    'tipo'               => $detalle?->tipo ?? 'Normal',
                    'paralelo'           => $detalle?->paralelo?->code ?? $detalle?->paralelo?->name,
                    'periodo'            => $detalle?->matricula?->periodo?->code,
                    'tiene_calificacion' => $calificacion !== null,
                    'insumo1'            => $calificacion?->insumo1,
                    'insumo2'            => $calificacion?->insumo2,
                    'insumo3'            => $calificacion?->insumo3,
                    'insumo4'            => $calificacion?->insumo4,
                    'insumo5'            => $calificacion?->insumo5,
                    'promedio_insumos'   => $calificacion?->promedio_insumos,
                    'examen_parcial'     => $calificacion?->examen_parcial,
                    'examen_final'       => $calificacion?->examen_final,
                    'nota_final'         => $calificacion?->nota_final,
                    'nota_suspenso'      => $calificacion?->nota_suspenso,
                    'estado_final'       => $calificacion?->estado_final,
                    'es_arrastre'        => $calificacion?->es_arrastre ?? false,
                    'numero_intento'     => $calificacion?->numero_intento ?? 1,
                ];
            }

            $promedio = ! empty($notasSemestre)
                ? round(array_sum($notasSemestre) / count($notasSemestre), 2)
                : null;

            if ($promedio !== null) {
                $promediosSemestres[] = $promedio;
            }

            $semestresData[] = [
                'semestre_id'     => $semestre->id,
                'semestre_nombre' => $semestre->name,
                'semestre_order'  => $semestre->order,
                'tiene_datos'     => $semestretieneDatos,
                'materias'        => $materiasData,
                'promedio'        => $promedio,
                'total_materias'  => count($materiasData),
                'aprobadas'       => collect($materiasData)->where('estado_final', 'Aprobado')->count(),
                'reprobadas'      => collect($materiasData)->where('estado_final', 'Reprobado')->count(),
            ];
        }

        $promedioMalla = ! empty($promediosSemestres)
            ? round(array_sum($promediosSemestres) / count($promediosSemestres), 2)
            : null;

        $titulacion = NotaTitulacion::with('practica', 'comunitaria')
            ->where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->latest('numero_intento')
            ->first();

        $practica = PracticaPreprofesional::where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->latest()
            ->first();

        $comunitaria = \App\Models\Comunitaria::where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->latest()
            ->first();

        $intentos = NotaTitulacion::where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->orderBy('numero_intento')
            ->get();

        $periodoPivot = CarreraPeriodo::where('carrera_id', $carrera->id)
            ->where('periodo_id', $matricula->periodo_id)
            ->first();

        return [
            'carrera'        => $carrera,
            'matricula'      => $matricula,
            'periodo_pivot'  => $periodoPivot,
            'semestres'      => $semestresData,
            'promedio_malla' => $promedioMalla,
            'titulacion'     => $titulacion,
            'practica'       => $practica,
            'comunitaria'    => $comunitaria,
            'intentos'       => $intentos,
            'malla_completa' => NotaTitulacion::mallaCurricular_Completada($userId, $carrera->id),
        ];
    }

    public function toggleInsumos(int $materiaId): void
    {
        $this->expandedMateria = $this->expandedMateria === $materiaId
            ? null
            : $materiaId;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.acta-calificacion-admin', [
            'acta'       => $this->acta,
            'estudiante' => $this->estudiante,
        ]);
    }

    public function exportarPdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $acta       = $this->acta;
        $estudiante = $this->estudiante;

        if (empty($acta)) {
            return response()->streamDownload(fn() => print(''), 'sin_datos.pdf');
        }

        $piePagina = SettingService::get(
            'documentos.pie_pagina',
            'Documento generado por el Sistema Académico del ISTC. Válido solo con firma y sello institucional.'
        );

        $pdf = Pdf::loadView('pdf.acta-calificaciones-pdf', [
            'acta'       => $acta,
            'estudiante' => $estudiante,
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96);

        $pdf->render();
        $this->agregarFooterCanvas($pdf->getDomPDF()->getCanvas(), $piePagina);

        $nombre = 'acta_'
            . Str::slug($estudiante->name ?? 'estudiante') . '_'
            . now()->format('Ymd')
            . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $nombre,
            ['Content-Type' => 'application/pdf']
        );
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
