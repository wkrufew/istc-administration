<?php

namespace App\Livewire\Estudiante;

use App\Models\AsignacionDocente;
use App\Models\Aviso;
use App\Models\AvisoLectura;
use App\Models\DetalleMatricula;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Traits\WithAuthorization;

class AvisosEstudiante extends Component
{
    use WithAuthorization;
    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function periodoActivo(): ?Periodo
    {
        return Periodo::periodoActivoGlobal();
    }

    #[Computed]
    public function asignacionIds()
    {
        if (! $this->periodoActivo) {
            return collect();
        }

        // Busca las asignaciones de docente que coincidan con
        // las materias + paralelos en los que el estudiante está matriculado
        return AsignacionDocente::where('periodo_id', $this->periodoActivo->id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('detalle_matriculas')
                    ->join('matriculas', 'matriculas.id', '=', 'detalle_matriculas.matricula_id')
                    ->whereColumn('detalle_matriculas.materia_id', 'asignacion_docentes.materia_id')
                    ->whereColumn('detalle_matriculas.paralelo_id', 'asignacion_docentes.paralelo_id')
                    ->where('matriculas.user_id', Auth::id())
                    ->where('matriculas.periodo_id', $this->periodoActivo->id);
            })
            ->pluck('id');
    }

    #[Computed]
    public function avisos()
    {
        if ($this->asignacionIds->isEmpty()) {
            return collect()->groupBy(fn() => 'proximo');
        }

        $userId = Auth::id();

        return Aviso::with([
                'asignacionDocente.materia',
                'asignacionDocente.paralelo',
                'asignacionDocente.docente',
            ])
            ->whereIn('asignacion_docente_id', $this->asignacionIds)
            ->withExists(['lecturas as leido' => fn($q) => $q->where('user_id', $userId)])
            ->orderBy('fecha_aviso', 'asc')
            ->get()
            ->groupBy('estado');
    }

    #[Computed]
    public function totalNoLeidos(): int
    {
        if ($this->asignacionIds->isEmpty()) {
            return 0;
        }

        return Aviso::whereIn('asignacion_docente_id', $this->asignacionIds)
            ->whereIn('estado', ['hoy', 'proximo'])  // solo relevantes
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->whereDoesntHave('lecturas', fn($q) => $q->where('user_id', Auth::id()))
            ->count();
    }

    // ── Acciones ──────────────────────────────────────────────────────────────

    public function marcarLeido(int $avisoId): void
    {
        if ($this->sinPermiso('acceso_estudiantil')) return;

        AvisoLectura::firstOrCreate([
            'aviso_id' => $avisoId,
            'user_id'  => Auth::id(),
        ], [
            'leido_at' => now(),
        ]);

        unset($this->avisos);
        // Notifica al menú para que actualice el badge
        $this->dispatch('aviso-leido');
    }

    public function marcarTodosLeidos(): void
    {
        if ($this->sinPermiso('acceso_estudiantil')) return;
        if ($this->asignacionIds->isEmpty()) return;

        $avisoIds = Aviso::whereIn('asignacion_docente_id', $this->asignacionIds)
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->pluck('id');

        foreach ($avisoIds as $id) {
            AvisoLectura::firstOrCreate(
                ['aviso_id' => $id, 'user_id' => Auth::id()],
                ['leido_at' => now()]
            );
        }

        unset($this->avisos);
        $this->dispatch('aviso-leido');

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Todo marcado como leído',
            'toast' => true,
            'timer' => 1500,
        ]);
    }

    #[Layout('layouts.estudiantil')]
    public function render()
    {
        return view('livewire.estudiante.avisos-estudiante');
    }
}
