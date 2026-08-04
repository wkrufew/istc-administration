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

    #[Url]
    public string $vista = 'lista'; // 'lista' | 'kanban'

    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingFiltroEstado(): void  { $this->resetPage(); }
    public function updatingFiltroPrioridad(): void { $this->resetPage(); }

    public function setVista(string $v): void
    {
        $this->vista = in_array($v, ['lista', 'kanban']) ? $v : 'lista';
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function tickets()
    {
        return Ticket::query()
            ->with(['creador', 'asignados'])
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
    public function kanbanColumnas(): array
    {
        $base = Ticket::query()
            ->with(['asignados'])
            ->when($this->search, fn($q) =>
                $q->where('titulo', 'like', "%{$this->search}%")
                  ->orWhere('numero', 'like', "%{$this->search}%")
            )
            ->when($this->filtroPrioridad, fn($q) =>
                $q->where('prioridad', $this->filtroPrioridad)
            )
            ->latest()
            ->get();

        $columnas = [
            'pendiente'  => ['label' => 'Pendiente',   'color' => 'yellow', 'items' => collect()],
            'en_proceso' => ['label' => 'En proceso',  'color' => 'blue',   'items' => collect()],
            'cerrado'    => ['label' => 'Cerrado',      'color' => 'gray',   'items' => collect()],
        ];

        foreach ($base as $ticket) {
            if (isset($columnas[$ticket->estado])) {
                $columnas[$ticket->estado]['items']->push($ticket);
            }
        }

        return $columnas;
    }

    #[Computed]
    public function stats(): array
    {
        $counts = Ticket::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(estado = 'pendiente') as pendientes,
                SUM(estado = 'en_proceso') as en_proceso,
                SUM(estado = 'cerrado') as cerrados
            ")
            ->first();

        return [
            'total'      => $counts->total ?? 0,
            'pendientes' => $counts->pendientes ?? 0,
            'en_proceso' => $counts->en_proceso ?? 0,
            'cerrados'   => $counts->cerrados ?? 0,
        ];
    }

    // ── Acción rápida desde Kanban ────────────────────────────────────────────

    public function moverEstado(int $ticketId, string $estado): void
    {
        $estados = ['pendiente', 'en_proceso', 'cerrado'];
        if (! in_array($estado, $estados)) return;

        $ticket = Ticket::find($ticketId);
        if (! $ticket) return;

        $update = ['estado' => $estado];
        if ($estado === 'cerrado' && ! $ticket->closed_at) $update['closed_at'] = now();

        $ticket->update($update);
        unset($this->kanbanColumnas, $this->stats);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.ticket-list');
    }
}
