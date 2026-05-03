<?php

namespace App\Livewire\Administration;

use App\Models\Ticket;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithAuthorization;

class TicketList extends Component
{
    use WithPagination, WithAuthorization;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $filtroEstado = '';

    #[Url]
    public string $filtroPrioridad = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroPrioridad(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function tickets()
    {
        return Ticket::query()
            ->with(['creador', 'asignado'])
            ->when($this->search, fn($q) =>
                $q->where('titulo', 'like', "%{$this->search}%")
                  ->orWhere('numero', 'like', "%{$this->search}%")
            )
            ->when($this->filtroEstado, fn($q) =>
                $q->where('estado', $this->filtroEstado)
            )
            ->when($this->filtroPrioridad, fn($q) =>
                $q->where('prioridad', $this->filtroPrioridad)
            )
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $counts = Ticket::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(estado = 'abierto') as abiertos,
                SUM(estado = 'en_proceso') as en_proceso,
                SUM(estado = 'esperando') as esperando,
                SUM(estado IN ('resuelto','cerrado')) as cerrados
            ")
            ->first();

        return [
            'total'      => $counts->total ?? 0,
            'abiertos'   => $counts->abiertos ?? 0,
            'en_proceso' => $counts->en_proceso ?? 0,
            'esperando'  => $counts->esperando ?? 0,
            'cerrados'   => $counts->cerrados ?? 0,
        ];
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.ticket-list');
    }
}
