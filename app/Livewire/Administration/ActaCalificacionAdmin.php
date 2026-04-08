<?php

namespace App\Livewire\Administration;

use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\NotaTitulacion;
use App\Models\PracticaPreprofesional;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

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

        $semestresData      = [];
        $promediosSemestres = [];

        foreach ($semestres as $semestre) {
            $materiasData       = [];
            $notasSemestre      = [];
            $semestretieneDatos = false;

            foreach ($semestre->materias->sortBy('name') as $materia) {
                $detalle = DetalleMatricula::with([
                    'calificaciones' => fn($q) => $q->orderByDesc('numero_intento'),
                    'paralelo',
                    'matricula.periodo',
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
                    'materia_id'         => $materia->id,
                    'materia_nombre'     => $materia->name,
                    'materia_code'       => $materia->code,
                    'creditos'           => $materia->credits,
                    'tipo'               => $detalle?->tipo ?? 'Normal',
                    'paralelo'           => $detalle?->paralelo?->name,
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

        $titulacion = NotaTitulacion::with('practica')
            ->where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->latest('numero_intento')
            ->first();

        $practica = PracticaPreprofesional::where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->latest()
            ->first();

        $intentos = NotaTitulacion::where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->orderBy('numero_intento')
            ->get();

        return [
            'carrera'        => $carrera,
            'matricula'      => $matricula,
            'semestres'      => $semestresData,
            'promedio_malla' => $promedioMalla,
            'titulacion'     => $titulacion,
            'practica'       => $practica,
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
        // Reutiliza exactamente el mismo computed $this->acta del componente
        $acta       = $this->acta;
        $estudiante = $this->estudiante;

        if (empty($acta)) {
            // Si no hay acta no hacemos nada
            return response()->streamDownload(fn() => print(''), 'sin_datos.pdf');
        }

        $pdf = Pdf::loadView('pdf.acta-calificaciones-pdf', [
            'acta'       => $acta,
            'estudiante' => $estudiante,
        ])
            ->setPaper('a4', 'landscape')   // horizontal para que quepan todas las columnas
            ->setOptions([
                'defaultFont'   => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'dpi'                  => 150,
                'defaultMediaType'     => 'print',
                /* 'margin_top'           => 15,
                'margin_right'         => 18,
                'margin_bottom'        => 15,
                'margin_left'          => 18, */
            ]);

        $nombreArchivo = 'acta_' . str_replace(' ', '_', strtolower($estudiante->name))
            . '_' . now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $nombreArchivo,
            ['Content-Type' => 'application/pdf']
        );
    }
}
