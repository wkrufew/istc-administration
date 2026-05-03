<?php

namespace App\Livewire\Administration;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketMensajeNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Traits\WithAuthorization;

class TicketShow extends Component
{
    use WithAuthorization;
    public Ticket $ticket;

    // ── Respuesta ─────────────────────────────────────────────────────────────
    #[Validate('required|string')]
    public string $mensaje = '';

    public bool $esNotaInterna = false;

    // ── Cambio de estado ──────────────────────────────────────────────────────
    public string $nuevoEstado = '';

    // ── Asignación ────────────────────────────────────────────────────────────
    public ?int $nuevoAsignado = null;

    // ── Edición del ticket ────────────────────────────────────────────────────
    public bool $editandoTicket = false;

    #[Validate('required|string|max:200')]
    public string $editTitulo = '';

    #[Validate('required|in:baja,media,alta,urgente')]
    public string $editPrioridad = 'media';

    #[Validate('nullable|date')]
    public ?string $editFechaLimite = null;

    #[Validate('nullable|string|max:500')]
    public ?string $editObservaciones = null;

    public function mount(Ticket $ticket): void
    {
        $this->ticket           = $ticket;
        $this->nuevoEstado      = $ticket->estado;
        $this->nuevoAsignado    = $ticket->assigned_to;
        $this->editTitulo       = $ticket->titulo;
        $this->editPrioridad    = $ticket->prioridad;
        $this->editFechaLimite  = $ticket->fecha_limite?->format('Y-m-d');
        $this->editObservaciones = $ticket->observaciones;
    }

    // ── Computed ──────────────────────────────────────────────────────────────

    #[Computed]
    public function mensajes()
    {
        return TicketMessage::where('ticket_id', $this->ticket->id)
            ->with('autor')
            ->oldest()
            ->get();
    }

    #[Computed]
    public function adminUsers()
    {
        return User::role(['Administrador', 'Secretaria'])->orderBy('name')->get(['id', 'name']);
    }

    // ── Acciones ──────────────────────────────────────────────────────────────

    public function responder(): void
    {
        if ($this->sinPermiso('responder_tickets')) return;

        $this->validateOnly('mensaje');

        if (empty(trim(strip_tags($this->mensaje)))) {
            $this->addError('mensaje', 'El mensaje no puede estar vacío.');
            return;
        }

        $msg = TicketMessage::create([
            'ticket_id'       => $this->ticket->id,
            'user_id'         => Auth::id(),
            'mensaje'         => $this->mensaje,
            'es_nota_interna' => $this->esNotaInterna,
        ]);

        // Marcar primera respuesta si aplica
        if (! $this->ticket->first_response_at) {
            $this->ticket->update(['first_response_at' => now()]);
        }

        // Notificar al creador si no es quien responde y no es nota interna
        if (! $this->esNotaInterna && $this->ticket->created_by !== Auth::id()) {
            $notif   = new TicketMensajeNotification($this->ticket, $msg);
            $creador = $this->ticket->creador;

            try {
                $creador->notify($notif);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Email ticket_respuesta falló', [
                    'ticket_id' => $this->ticket->id,
                    'error'     => $e->getMessage(),
                ]);
            }

            try {
                $notif->enviarWhatsapp($creador);
            } catch (\Throwable $e) {
                // No crítico
            }
        }

        $this->reset('mensaje', 'esNotaInterna');
        unset($this->mensajes);

        $this->dispatch('mensaje-enviado');
        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Respuesta enviada',
            'timer' => 1500,
        ]);
    }

    public function cambiarEstado(): void
    {
        if ($this->sinPermiso('cambiar_estado_tickets')) return;

        $estados = ['abierto', 'en_proceso', 'esperando', 'resuelto', 'cerrado'];
        if (! in_array($this->nuevoEstado, $estados)) {
            return;
        }

        $update = ['estado' => $this->nuevoEstado];

        if ($this->nuevoEstado === 'resuelto' && ! $this->ticket->resolved_at) {
            $update['resolved_at'] = now();
        }
        if ($this->nuevoEstado === 'cerrado' && ! $this->ticket->closed_at) {
            $update['closed_at'] = now();
        }

        $this->ticket->update($update);
        $this->ticket->refresh();

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Estado actualizado',
            'timer' => 1500,
        ]);
    }

    public function asignar(): void
    {
        if ($this->sinPermiso('asignar_tickets')) return;

        $update = ['assigned_to' => $this->nuevoAsignado];
        if ($this->nuevoAsignado && ! $this->ticket->assigned_at) {
            $update['assigned_at'] = now();
        }

        $this->ticket->update($update);
        $this->ticket->refresh();

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Ticket asignado',
            'timer' => 1500,
        ]);
    }

    public function toggleEdicion(): void
    {
        $this->editandoTicket = ! $this->editandoTicket;
    }

    public function guardarEdicion(): void
    {
        if ($this->sinPermiso('gestionar_tickets')) return;

        $validated = $this->validate([
            'editTitulo'       => 'required|string|max:200',
            'editPrioridad'    => 'required|in:baja,media,alta,urgente',
            'editFechaLimite'  => 'nullable|date',
            'editObservaciones'=> 'nullable|string|max:500',
        ]);

        $this->ticket->update([
            'titulo'        => $this->editTitulo,
            'prioridad'     => $this->editPrioridad,
            'fecha_limite'  => $this->editFechaLimite ?: null,
            'observaciones' => $this->editObservaciones ?: null,
        ]);

        $this->ticket->refresh();
        $this->editandoTicket = false;

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Ticket actualizado',
            'timer' => 1500,
        ]);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.ticket-show');
    }
}
