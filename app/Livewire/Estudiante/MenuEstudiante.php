<?php

namespace App\Livewire\Estudiante;

use App\Models\Aviso;
use App\Models\AsignacionDocente;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MenuEstudiante extends Component
{
    protected $listeners = ['aviso-leido' => 'refrescarBadge'];

    public function refrescarBadge(): void
    {
        unset($this->avisosNoLeidos);
    }

    #[Computed]
    public function avisosNoLeidos(): int
    {
        $periodo = Periodo::periodoActivoGlobal();
        if (! $periodo || ! Auth::check()) {
            return 0;
        }

        $userId = Auth::id();

        $asignacionIds = AsignacionDocente::where('periodo_id', $periodo->id)
            ->whereExists(function ($query) use ($userId, $periodo) {
                $query->select(DB::raw(1))
                    ->from('detalle_matriculas')
                    ->join('matriculas', 'matriculas.id', '=', 'detalle_matriculas.matricula_id')
                    ->whereColumn('detalle_matriculas.materia_id', 'asignacion_docentes.materia_id')
                    ->whereColumn('detalle_matriculas.paralelo_id', 'asignacion_docentes.paralelo_id')
                    ->where('matriculas.user_id', $userId)
                    ->where('matriculas.periodo_id', $periodo->id);
            })
            ->pluck('id');

        if ($asignacionIds->isEmpty()) {
            return 0;
        }

        return Aviso::whereIn('asignacion_docente_id', $asignacionIds)
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->whereDoesntHave('lecturas', fn($q) => $q->where('user_id', $userId))
            ->count();
    }

    public function render()
    {
        return view('livewire.estudiante.menu-estudiante');
    }
}
