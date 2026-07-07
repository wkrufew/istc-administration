<?php

namespace App\Livewire\Docente;

use App\Models\DocumentoInstitucional;
use App\Models\DetalleMatricula;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public int|null $periodo_id = null;
    public int $totalMaterias   = 0;
    public int $totalEstudiantes = 0;
    public array $detalleMaterias = [];

    public function mount(): void
    {
        $periodos = Periodo::orderByDesc('fecha_inicio')->get();
        $actual   = Periodo::periodoActivoGlobal();

        $this->periodo_id = $actual?->id ?? $periodos->first()?->id;

        $this->cargarDatos();
    }

    public function updatedPeriodoId(): void
    {
        $this->cargarDatos();
    }

    public function cargarDatos(): void
    {
        if (! $this->periodo_id) return;

        $periodo = Periodo::find($this->periodo_id);
        $user    = Auth::user();

        $asignaciones = $user->asignacionesDocente()
            ->where('periodo_id', $periodo->id)
            ->with(['materia', 'paralelo'])
            ->get();

        $this->totalMaterias = $asignaciones->unique('materia_id')->count();

        $materiaIds  = $asignaciones->pluck('materia_id');
        $paraleloIds = $asignaciones->pluck('paralelo_id');

        $this->totalEstudiantes = DetalleMatricula::whereHas('matricula', fn($q) => $q->where('periodo_id', $periodo->id))
            ->whereIn('materia_id', $materiaIds)
            ->whereIn('paralelo_id', $paraleloIds)
            ->where('estado', 'Inscrito')
            ->distinct('user_id')
            ->count('user_id');

        $this->detalleMaterias = $asignaciones->map(function ($asig) use ($periodo) {
            $totalEst = DetalleMatricula::whereHas('matricula', fn($q) => $q->where('periodo_id', $periodo->id))
                ->where('materia_id', $asig->materia_id)
                ->where('paralelo_id', $asig->paralelo_id)
                ->where('estado', 'Inscrito')
                ->distinct('user_id')
                ->count('user_id');

            return [
                'materia'      => $asig->materia->name,
                'codigo'       => $asig->materia->code,
                'paralelo'     => $asig->paralelo->name,
                'estudiantes'  => $totalEst,
            ];
        })->values()->all();
    }

    public function render()
    {
        $periodos = Periodo::orderByDesc('fecha_inicio')->get();
        $recursos = DocumentoInstitucional::orderByRaw("FIELD(tipo,'silabo','rubrica','acta','guia','otro')")->get();

        return view('livewire.docente.dashboard', compact('periodos', 'recursos'));
    }
}
