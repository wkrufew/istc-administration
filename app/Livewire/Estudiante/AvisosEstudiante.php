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

    public int  $limitePasados         = 10;
    public bool $seccionPasadosAbierta = false;

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
    public function avisos(): array
    {
        if ($this->asignacionIds->isEmpty()) {
            return ['hoy' => collect(), 'proximo' => collect(), 'pasado' => collect()];
        }

        $userId = Auth::id();
        $ids    = $this->asignacionIds;

        $withRelations = ['asignacionDocente.materia', 'asignacionDocente.paralelo', 'asignacionDocente.docente'];

        $futuros = Aviso::with($withRelations)
            ->whereIn('asignacion_docente_id', $ids)
            ->withExists(['lecturas as leido' => fn($q) => $q->where('user_id', $userId)])
            ->where('fecha_aviso', '>=', now()->startOfDay())
            ->orderBy('fecha_aviso', 'asc')
            ->get();

        $pasados = Aviso::with($withRelations)
            ->whereIn('asignacion_docente_id', $ids)
            ->withExists(['lecturas as leido' => fn($q) => $q->where('user_id', $userId)])
            ->where('fecha_aviso', '<', now()->startOfDay())
            ->orderBy('fecha_aviso', 'desc')
            ->limit($this->limitePasados)
            ->get();

        return [
            'hoy'     => $futuros->filter(fn($a) => $a->estado === 'hoy'),
            'proximo' => $futuros->filter(fn($a) => $a->estado === 'proximo'),
            'pasado'  => $pasados,
        ];
    }

    #[Computed]
    public function totalPasados(): int
    {
        if ($this->asignacionIds->isEmpty()) {
            return 0;
        }

        return Aviso::whereIn('asignacion_docente_id', $this->asignacionIds)
            ->where('fecha_aviso', '<', now()->startOfDay())
            ->count();
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

    public function toggleSeccionPasados(): void
    {
        $this->seccionPasadosAbierta = !$this->seccionPasadosAbierta;
    }

    public function cargarMasPasados(): void
    {
        $this->limitePasados += 10;
        $this->seccionPasadosAbierta = true;
        unset($this->avisos);
    }

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
