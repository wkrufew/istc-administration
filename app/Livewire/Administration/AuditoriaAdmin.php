<?php

namespace App\Livewire\Administration;

use App\Models\AuditoriaCalificacion;
use App\Models\AuditoriaGeneral;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class AuditoriaAdmin extends Component
{
    use WithPagination;

    public string $tab = 'calificaciones';

    // ── Filtros: calificaciones ───────────────────────────────────────────────
    public string $busquedaCali   = '';
    public string $filtroCampo    = '';
    public string $fechaDesdeCali = '';
    public string $fechaHastaCali = '';

    // ── Filtros: general ──────────────────────────────────────────────────────
    public string $busquedaGeneral = '';
    public string $filtroModelo    = '';
    public string $filtroEvento    = '';
    public string $fechaDesdeGen   = '';
    public string $fechaHastaGen   = '';

    // ── Paginación se resetea al cambiar cualquier filtro ─────────────────────

    public function updatedTab(): void            { $this->resetPage(); }
    public function updatedBusquedaCali(): void   { $this->resetPage(); }
    public function updatedFiltroCampo(): void    { $this->resetPage(); }
    public function updatedFechaDesdeCali(): void { $this->resetPage(); }
    public function updatedFechaHastaCali(): void { $this->resetPage(); }
    public function updatedBusquedaGeneral(): void { $this->resetPage(); }
    public function updatedFiltroModelo(): void   { $this->resetPage(); }
    public function updatedFiltroEvento(): void   { $this->resetPage(); }
    public function updatedFechaDesdeGen(): void  { $this->resetPage(); }
    public function updatedFechaHastaGen(): void  { $this->resetPage(); }

    // ── Datos: calificaciones ─────────────────────────────────────────────────

    #[Computed]
    public function auditoriaCalificaciones()
    {
        $busqueda = $this->busquedaCali;

        return AuditoriaCalificacion::with([
            'docente:id,name,cedula',
            'calificacion.detalleMatricula.estudiante:id,name,cedula',
            'calificacion.detalleMatricula.materia:id,name,code',
        ])
        ->when($busqueda, function ($q) use ($busqueda) {
            $q->where(function ($sub) use ($busqueda) {
                $sub->whereHas('docente', fn($d) =>
                    $d->where('name',    'like', "%{$busqueda}%")
                      ->orWhere('cedula', 'like', "%{$busqueda}%")
                )
                ->orWhereHas('calificacion.detalleMatricula.estudiante', fn($e) =>
                    $e->where('name',    'like', "%{$busqueda}%")
                      ->orWhere('cedula', 'like', "%{$busqueda}%")
                );
            });
        })
        ->when($this->filtroCampo,    fn($q) => $q->where('campo_modificado', $this->filtroCampo))
        ->when($this->fechaDesdeCali, fn($q) => $q->whereDate('fecha_modificacion', '>=', $this->fechaDesdeCali))
        ->when($this->fechaHastaCali, fn($q) => $q->whereDate('fecha_modificacion', '<=', $this->fechaHastaCali))
        ->orderByDesc('fecha_modificacion')
        ->paginate(20);
    }

    // ── Datos: general ────────────────────────────────────────────────────────

    #[Computed]
    public function auditoriaGeneral()
    {
        $busqueda = $this->busquedaGeneral;

        return AuditoriaGeneral::with('usuario:id,name,cedula')
        ->when($busqueda, function ($q) use ($busqueda) {
            $q->whereHas('usuario', fn($u) =>
                $u->where('name',    'like', "%{$busqueda}%")
                  ->orWhere('cedula', 'like', "%{$busqueda}%")
            );
        })
        ->when($this->filtroModelo, fn($q) => $q->where('auditable_type', $this->filtroModelo))
        ->when($this->filtroEvento, fn($q) => $q->where('evento', $this->filtroEvento))
        ->when($this->fechaDesdeGen, fn($q) => $q->whereDate('created_at', '>=', $this->fechaDesdeGen))
        ->when($this->fechaHastaGen, fn($q) => $q->whereDate('created_at', '<=', $this->fechaHastaGen))
        ->orderByDesc('created_at')
        ->paginate(20);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    #[Computed]
    public function camposDisponibles(): array
    {
        return AuditoriaCalificacion::select('campo_modificado')
            ->whereNotNull('campo_modificado')
            ->distinct()
            ->orderBy('campo_modificado')
            ->pluck('campo_modificado')
            ->toArray();
    }

    public function modeloLabel(string $class): string
    {
        return match ($class) {
            'App\\Models\\Pago'                   => 'Pago',
            'App\\Models\\Matricula'              => 'Matrícula',
            'App\\Models\\Carrera'                => 'Carrera',
            'App\\Models\\Semestre'               => 'Semestre',
            'App\\Models\\Materia'                => 'Materia',
            'App\\Models\\ObligacionesFinanciera'  => 'Obligación Fin.',
            default                               => class_basename($class),
        };
    }

    public function resetFiltrosCali(): void
    {
        $this->reset(['busquedaCali', 'filtroCampo', 'fechaDesdeCali', 'fechaHastaCali']);
        $this->resetPage();
    }

    public function resetFiltrosGeneral(): void
    {
        $this->reset(['busquedaGeneral', 'filtroModelo', 'filtroEvento', 'fechaDesdeGen', 'fechaHastaGen']);
        $this->resetPage();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.auditoria-admin', [
            'camposDisponibles' => $this->camposDisponibles,
        ]);
    }
}
