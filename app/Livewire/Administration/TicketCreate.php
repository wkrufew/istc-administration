<?php

namespace App\Livewire\Administration;

use App\Models\Ticket;
use App\Notifications\TicketCreadoNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Traits\WithAuthorization;

class TicketCreate extends Component
{
    use WithAuthorization;
    #[Validate('required|string|max:200')]
    public string $titulo = '';

    #[Validate('required|string')]
    public string $descripcion = '';

    #[Validate('required|in:baja,media,alta,urgente')]
    public string $prioridad = 'media';

    #[Validate('nullable|date|after_or_equal:today')]
    public ?string $fecha_limite = null;

    #[Validate('nullable|string|max:500')]
    public ?string $observaciones = null;

    public function guardar(): void
    {
        if ($this->sinPermiso('crear_tickets')) return;

        $this->validate();

        $ticket = Ticket::create([
            'numero'        => Ticket::generarNumero(),
            'titulo'        => $this->titulo,
            'descripcion'   => $this->descripcion,
            'prioridad'     => $this->prioridad,
            'estado'        => 'abierto',
            'created_by'    => Auth::id(),
            'fecha_limite'  => $this->fecha_limite ?: null,
            'observaciones' => $this->observaciones ?: null,
        ]);

        // Notificar al creador (email + WhatsApp opcional)
        $notif = new TicketCreadoNotification($ticket);
        $user  = Auth::user();

        try {
            $user->notify($notif);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email ticket_creado falló', [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
            ]);
        }

        try {
            $notif->enviarWhatsapp($user);
        } catch (\Throwable $e) {
            // No crítico
        }

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => '¡Ticket creado!',
            'text'  => "Tu ticket {$ticket->numero} ha sido registrado.",
        ]);

        $this->redirect(route('administracion.administrativa.tickets.show', $ticket));
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.ticket-create');
    }
}
