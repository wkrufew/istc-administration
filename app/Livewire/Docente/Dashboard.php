<?php

namespace App\Livewire\Docente;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Periodo;
use App\Models\DetalleMatricula;

class Dashboard extends Component
{
    public $periodo_id;
    public $periodos = [];
    public $totalMaterias = 0;
    public $totalEstudiantes = 0;
    public $detalleMaterias = [];

    public function mount()
    {
        $this->periodos = Periodo::orderByDesc('fecha_inicio')->get();

        // Seleccionar el periodo activo por defecto
        $actual = Periodo::periodoActivoGlobal();
        $this->periodo_id = $actual?->id ?? $this->periodos->first()?->id;

        $this->cargarDatos();
    }

    public function updatedPeriodoId()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        if (!$this->periodo_id) return;

        $periodo = Periodo::find($this->periodo_id);
        $user = Auth::user();

        // Asignaciones del docente en el periodo seleccionado
        $asignaciones = $user->asignacionesDocente()
            ->where('periodo_id', $periodo->id)
            ->with(['materia', 'paralelo'])
            ->get();

        $this->totalMaterias = $asignaciones->unique('materia_id')->count();

        $materiaIds = $asignaciones->pluck('materia_id');
        $paraleloIds = $asignaciones->pluck('paralelo_id');

        $this->totalEstudiantes = DetalleMatricula::whereHas('matricula', function ($q) use ($periodo) {
            $q->where('periodo_id', $periodo->id);
        })
            ->whereIn('materia_id', $materiaIds)
            ->whereIn('paralelo_id', $paraleloIds)
            ->where('estado', 'Inscrito')
            ->distinct('user_id')
            ->count('user_id');

        // Detalle por materia
        $this->detalleMaterias = $asignaciones->map(function ($asig) use ($periodo) {
            $totalEst = DetalleMatricula::whereHas('matricula', function ($q) use ($periodo) {
                $q->where('periodo_id', $periodo->id);
            })
                ->where('materia_id', $asig->materia_id)
                ->where('paralelo_id', $asig->paralelo_id)
                ->where('estado', 'Inscrito')
                ->distinct('user_id')
                ->count('user_id');

            return [
                'materia' => $asig->materia->name,
                'codigo' => $asig->materia->code,
                'paralelo' => $asig->paralelo->name,
                'estudiantes' => $totalEst,
            ];
        });
    }

    public function render()
    {
        //dd($this->detalleMaterias);
        return view('livewire.docente.dashboard');
    }
}
