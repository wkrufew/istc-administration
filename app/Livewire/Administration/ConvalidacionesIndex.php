<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\Convalidacion;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ConvalidacionesIndex extends Component
{
    use WithPagination;

    // Modal de búsqueda
    public bool   $modalAbierto = false;
    public string $busqueda     = '';

    // Filtros del listado
    public string $filtroEstado   = '';
    public string $filtroCarrera  = '';

    public function updatedBusqueda(): void
    {
        // Resetear paginación del modal al escribir
    }

    public function updatedFiltroEstado(): void  { $this->resetPage(); }
    public function updatedFiltroCarrera(): void { $this->resetPage(); }

    public function abrirModal(): void
    {
        $this->busqueda    = '';
        $this->modalAbierto = true;
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->busqueda    = '';
    }

    public function irAConvalidacion(int $userId): void
    {
        $this->cerrarModal();
        $this->redirect(
            route('administracion.administrativa.convalidacion.conocimiento', ['userId' => $userId]),
            navigate: true
        );
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function resultadosBusqueda()
    {
        if (strlen(trim($this->busqueda)) < 2) return collect();

        return User::permission('acceso_estudiantil')
            ->where(fn($q) => $q
                ->where('name',    'like', "%{$this->busqueda}%")
                ->orWhere('email', 'like', "%{$this->busqueda}%")
                ->orWhere('cedula','like', "%{$this->busqueda}%")
            )
            ->with([
                'matriculas' => fn($q) => $q
                    ->whereIn('tipo', ['Nueva', 'Renovacion', 'Arrastre'])
                    ->where('estado', 'Habilitada')
                    ->latest()
                    ->limit(1),
                'matriculas.carrera:id,name',
                'convalidaciones' => fn($q) => $q->where('estado', 'Confirmada')->limit(1),
            ])
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function convalidaciones()
    {
        return Convalidacion::with([
            'estudiante:id,name,cedula,email',
            'carrera:id,name',
            'periodo:id,code',
            'registradoPor:id,name',
            'detalles',
        ])
            ->when($this->filtroEstado,  fn($q) => $q->where('estado',     $this->filtroEstado))
            ->when($this->filtroCarrera, fn($q) => $q->where('carrera_id', $this->filtroCarrera))
            ->orderByDesc('created_at')
            ->paginate(12);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'       => Convalidacion::count(),
            'confirmadas' => Convalidacion::where('estado', 'Confirmada')->count(),
            'borradores'  => Convalidacion::where('estado', 'Borrador')->count(),
        ];
    }

    #[Computed]
    public function carreras()
    {
        return Carrera::where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.convalidaciones-index');
    }
}
