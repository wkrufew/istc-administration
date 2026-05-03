<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\Periodo;
use App\Models\Matricula;
use App\Models\DetalleMatricula;
use App\Models\MateriasArrastrada;
use App\Models\ObligacionesFinanciera;
use App\Models\AsignacionDocente;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class DashboardEstudiante extends Component
{
    public $periodoSeleccionado = '';

    public $idUser;

    public function mount()
    {
        $this->idUser = Auth::id();
        // Período activo para la carrera del estudiante
        $ultimaMatricula  = Auth::user()->matriculas()->with('carrera')->latest()->first();
        $periodoDeCarrera = $ultimaMatricula?->carrera?->periodoActual();
        $this->periodoSeleccionado = $periodoDeCarrera?->id ?? Periodo::periodoActivoGlobal()?->id ?? '';
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================

    #[Computed]
    public function periodos()
    {
        return Periodo::whereHas(
            'matriculas',
            fn($q) =>
            $q->where('user_id', $this->idUser)
        )
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    #[Computed]
    public function matricula()
    {
        if (! $this->periodoSeleccionado) return null;

        return Matricula::with([
            'carrera',
            'periodo',
            'detalles.materia.semestre',
            'detalles.paralelo',
            'detalles.calificaciones',
        ])
            ->where('user_id', $this->idUser)
            ->where('periodo_id', $this->periodoSeleccionado)
            ->where('estado', 'Habilitada')
            ->first();
    }

    // Materias con docente del periodo seleccionado
    #[Computed]
    public function materiasConDocente()
    {
        if (! $this->matricula) return collect();

        return $this->matricula->detalles->map(function ($detalle) {
            $asignacion = AsignacionDocente::with('docente')
                ->where('materia_id', $detalle->materia_id)
                ->where('paralelo_id', $detalle->paralelo_id)
                ->where('periodo_id', $this->periodoSeleccionado)
                ->first();

            return [
                'detalle'       => $detalle,
                'materia'       => $detalle->materia,
                'paralelo'      => $detalle->paralelo,
                'docente'       => $asignacion?->docente,
                'calificacion'  => $detalle->calificaciones->first(),
                'tipo'          => $detalle->tipo,
                'estado'        => $detalle->estado,
            ];
        });
    }

    // Materias arrastradas pendientes (estado Arrastrada)
    #[Computed]
    public function materiasArrastradas()
    {
        return MateriasArrastrada::with(['materia', 'periodoReprobado'])
            ->where('user_id', $this->idUser)
            ->where('estado', 'Arrastrada')
            ->get();
    }

    // Resumen financiero del periodo
    #[Computed]
    public function resumenFinanciero()
    {
        if (! $this->periodoSeleccionado) return null;

        $obligaciones = ObligacionesFinanciera::where('user_id', $this->idUser)
            ->where('periodo_id', $this->periodoSeleccionado)
            ->get();

        return [
            'deuda_total'    => $obligaciones->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])->sum(fn($o) => max(0, $o->saldo)),
            'total_pagado'   => $obligaciones->sum(fn($o) => $o->total_pagado),
            'tiene_vencidas' => $obligaciones->where('estado', 'Vencido')->count() > 0,
            'obligaciones'   => $obligaciones,
        ];
    }

    // Promedio general del periodo (solo notas finales aprobadas/reprobadas)
    #[Computed]
    public function promedioGeneral()
    {
        if (! $this->matricula) return null;

        $notas = $this->matricula->detalles
            ->flatMap(fn($d) => $d->calificaciones)
            ->whereNotNull('nota_final')
            ->pluck('nota_final');

        if ($notas->isEmpty()) return null;

        return round($notas->average(), 2);
    }

    // Stats rápidas
    #[Computed]
    public function stats()
    {
        $detalles = $this->matricula?->detalles ?? collect();
        $cals     = $detalles->flatMap(fn($d) => $d->calificaciones);

        return [
            'total_materias'     => $detalles->where('tipo', 'Normal')->count(),
            'materias_arrastre'  => $detalles->where('tipo', 'Arrastre')->count(),
            'aprobadas'          => $cals->where('estado_final', 'Aprobado')->count(),
            'reprobadas'         => $cals->where('estado_final', 'Reprobado')->count(),
            'pendientes_arrastre' => $this->materiasArrastradas->count(),
            'total_creditos'     => $detalles->sum(fn($d) => $d->materia?->credits ?? 0),
        ];
    }

    public function render()
    {
        return view('livewire.estudiante.dashboard-estudiante', [
            'periodos'           => $this->periodos,
            'matricula'          => $this->matricula,
            'materiasConDocente' => $this->materiasConDocente,
            'materiasArrastradas' => $this->materiasArrastradas,
            'resumenFinanciero'  => $this->resumenFinanciero,
            'promedioGeneral'    => $this->promedioGeneral,
            'stats'              => $this->stats,
            'estudiante'         => Auth::user(),
        ]);
    }
}
