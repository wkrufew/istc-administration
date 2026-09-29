<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\Convalidacion;
use App\Models\Periodo;
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
    public string $filtroEstado          = '';
    public string $filtroCarrera         = '';
    public string $filtroPeriodo         = '';
    public string $busquedaEstudiante    = '';

    public function updatedBusqueda(): void
    {
        // Resetear paginación del modal al escribir
    }

    public function updatedFiltroEstado(): void       { $this->resetPage(); }
    public function updatedFiltroCarrera(): void      { $this->resetPage(); }
    public function updatedFiltroPeriodo(): void      { $this->resetPage(); }
    public function updatedBusquedaEstudiante(): void { $this->resetPage(); }

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

        return User::where(fn($q) => $q
            // Estudiantes registrados directamente o ya matriculados (con o sin is_active)
            ->whereHas('roles', fn($r) => $r->whereHas('permissions', fn($p) => $p->where('name', 'acceso_estudiantil')))
            ->orWhereHas('permissions', fn($p) => $p->where('name', 'acceso_estudiantil'))
            // Aspirantes aprobados con proceso de validación de conocimientos (aún no son estudiantes)
            ->orWhereHas('aspirante', fn($a) => $a
                ->where('tipo_proceso', 'validacion_conocimientos')
                ->whereIn('estado', ['aprobado', 'verificacion', 'proceso'])
            )
        )
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
            'aspirante:id,user_id,tipo_proceso,estado',
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
            ->when($this->filtroPeriodo, fn($q) => $q->where('periodo_id', $this->filtroPeriodo))
            ->when(strlen(trim($this->busquedaEstudiante)) >= 2, fn($q) => $q->whereHas(
                'estudiante',
                fn($s) => $s->where('name',   'like', '%' . trim($this->busquedaEstudiante) . '%')
                             ->orWhere('cedula', 'like', '%' . trim($this->busquedaEstudiante) . '%')
            ))
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

    #[Computed]
    public function periodos()
    {
        return Periodo::orderByDesc('fecha_inicio')->get(['id', 'code', 'description']);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.convalidaciones-index');
    }
}
