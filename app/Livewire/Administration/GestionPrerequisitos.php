<?php

namespace App\Livewire\Administration;

use App\Models\Materia;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GestionPrerequisitos extends Component
{
    public int $materia_id;
    public string $busqueda = '';
    public string $prerequisitoSeleccionado = '';
    public bool $esObligatorio = true;

    public array $prerequisitos      = [];
    public array $materiasDisponibles = [];

    public function mount(int $materia_id): void
    {
        $this->materia_id = $materia_id;
        $this->cargarPrerequisitos();
        $this->actualizarDisponibles();
    }

    public function updatedBusqueda(): void
    {
        $this->actualizarDisponibles();
    }

    public function cargarPrerequisitos(): void
    {
        $materia = Materia::with(['prerequisitos.semestre'])->find($this->materia_id);

        $this->prerequisitos = $materia->prerequisitos->map(fn($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'code'          => $p->code,
            'semestre'      => $p->semestre?->name ?? '—',
            'es_obligatorio' => (bool) $p->pivot->es_obligatorio,
        ])->values()->toArray();
    }

    public function actualizarDisponibles(): void
    {
        $materia = Materia::with('semestre')->find($this->materia_id);

        if (! $materia?->semestre) {
            $this->materiasDisponibles = [];
            return;
        }

        $carreraId = DB::table('semestres')->where('id', $materia->semestre_id)->value('carrera_id');

        if (! $carreraId) {
            $this->materiasDisponibles = [];
            return;
        }

        $yaAgregados = collect($this->prerequisitos)->pluck('id')->toArray();

        $this->materiasDisponibles = Materia::whereHas('semestre', fn($q) => $q->where('carrera_id', $carreraId))
            ->where('id', '!=', $this->materia_id)
            ->whereNotIn('id', $yaAgregados)
            ->when($this->busqueda, fn($q) => $q->where('name', 'like', "%{$this->busqueda}%")
                ->orWhere('code', 'like', "%{$this->busqueda}%"))
            ->with('semestre')
            ->orderBy('name')
            ->get()
            ->map(fn($m) => [
                'id'       => $m->id,
                'name'     => $m->name,
                'code'     => $m->code,
                'semestre' => $m->semestre?->name ?? '—',
            ])
            ->values()
            ->toArray();
    }

    public function agregar(): void
    {
        if (! $this->prerequisitoSeleccionado) {
            $this->dispatch('swal', [
                'icon' => 'warning', 'title' => 'Selecciona una materia', 'toast' => true,
                'position' => 'top-end', 'timer' => 3000,
            ]);
            return;
        }

        $seleccionadoId = (int) $this->prerequisitoSeleccionado;

        // Prevenir referencia circular: A→B si ya existe B→A
        $circular = DB::table('prerequisitos')
            ->where('materia_id', $seleccionadoId)
            ->where('prerequisito_id', $this->materia_id)
            ->exists();

        if ($circular) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Referencia circular',
                'text'  => 'Esa materia ya tiene a esta como prerequisito, lo que crearía una dependencia circular.',
                'toast' => false,
            ]);
            return;
        }

        DB::table('prerequisitos')->insertOrIgnore([
            'materia_id'      => $this->materia_id,
            'prerequisito_id' => $seleccionadoId,
            'es_obligatorio'  => $this->esObligatorio,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $this->prerequisitoSeleccionado = '';
        $this->busqueda                 = '';
        $this->cargarPrerequisitos();
        $this->actualizarDisponibles();

        $this->dispatch('swal', [
            'icon' => 'success', 'title' => 'Prerequisito agregado', 'toast' => true,
            'position' => 'top-end', 'timer' => 2500,
        ]);
    }

    public function toggleObligatorio(int $prerequisitoId): void
    {
        $actual = DB::table('prerequisitos')
            ->where('materia_id', $this->materia_id)
            ->where('prerequisito_id', $prerequisitoId)
            ->value('es_obligatorio');

        DB::table('prerequisitos')
            ->where('materia_id', $this->materia_id)
            ->where('prerequisito_id', $prerequisitoId)
            ->update(['es_obligatorio' => ! $actual, 'updated_at' => now()]);

        $this->cargarPrerequisitos();
    }

    public function eliminar(int $prerequisitoId): void
    {
        DB::table('prerequisitos')
            ->where('materia_id', $this->materia_id)
            ->where('prerequisito_id', $prerequisitoId)
            ->delete();

        $this->cargarPrerequisitos();
        $this->actualizarDisponibles();

        $this->dispatch('swal', [
            'icon' => 'success', 'title' => 'Prerequisito eliminado', 'toast' => true,
            'position' => 'top-end', 'timer' => 2000,
        ]);
    }

    public function render()
    {
        return view('livewire.administration.gestion-prerequisitos');
    }
}
