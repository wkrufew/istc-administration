<?php

namespace App\Livewire\Administration;

use App\Models\Ticket;
use App\Models\TicketAsignacion;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\TicketAsignadoNotification;
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

    // Respuesta
    #[Validate('required|string')]
    public string $mensaje = '';
    public bool $esNotaInterna = false;

    // Estado
    public string $nuevoEstado = '';

    // Asignación — usuario a agregar
    public ?int $nuevoAsignadoId = null;

    // Edición
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
        $this->ticket          = $ticket;
        $this->nuevoEstado     = $ticket->estado;
        $this->editTitulo      = $ticket->titulo;
        $this->editPrioridad   = $ticket->prioridad;
        $this->editFechaLimite = $ticket->fecha_limite?->format('Y-m-d');
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
    public function asignadosActuales()
    {
        return $this->ticket->asignados()->get();
    }

    #[Computed]
    public function usuariosDisponibles()
    {
        $yaAsignados = $this->ticket->asignados()->pluck('users.id')->toArray();

        return User::permission('acceso_administrativo')
            ->whereNotIn('id', $yaAsignados)
            ->with('roles')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($u) => [
                'id'  => $u->id,
                'name' => $u->name,
                'rol' => $u->roles->first()?->name ?? 'Administrador',
            ]);
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

        if (! $this->ticket->first_response_at) {
            $this->ticket->update(['first_response_at' => now()]);
        }

        // Notificar a todos los asignados excepto quien responde
        if (! $this->esNotaInterna) {
            $notif = new TicketMensajeNotification($this->ticket, $msg);
            foreach ($this->ticket->asignados as $dest) {
                if ($dest->id === Auth::id()) continue;
                try { $dest->notify($notif); } catch (\Throwable) {}
                try { $notif->enviarWhatsapp($dest); } catch (\Throwable) {}
            }
            // También notificar al creador si no está entre los asignados
            $creador = $this->ticket->creador;
            if ($creador && $creador->id !== Auth::id() && ! $this->ticket->asignados->contains($creador->id)) {
                try { $creador->notify($notif); } catch (\Throwable) {}
                try { $notif->enviarWhatsapp($creador); } catch (\Throwable) {}
            }
        }

        $this->reset('mensaje', 'esNotaInterna');
        unset($this->mensajes);

        $this->dispatch('mensaje-enviado');
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Respuesta enviada', 'timer' => 1500]);
    }

    public function cambiarEstado(): void
    {
        if ($this->sinPermiso('cambiar_estado_tickets')) return;

        $estados = ['abierto', 'en_proceso', 'esperando', 'resuelto', 'cerrado'];
        if (! in_array($this->nuevoEstado, $estados)) return;

        $update = ['estado' => $this->nuevoEstado];
        if ($this->nuevoEstado === 'resuelto' && ! $this->ticket->resolved_at) {
            $update['resolved_at'] = now();
        }
        if ($this->nuevoEstado === 'cerrado' && ! $this->ticket->closed_at) {
            $update['closed_at'] = now();
        }

        $this->ticket->update($update);
        $this->ticket->refresh();

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Estado actualizado', 'timer' => 1500]);
    }

    public function agregarAsignado(): void
    {
        if ($this->sinPermiso('asignar_tickets')) return;
        if (! $this->nuevoAsignadoId) return;

        $asignacion = TicketAsignacion::firstOrCreate(
            ['ticket_id' => $this->ticket->id, 'user_id' => $this->nuevoAsignadoId],
            ['assigned_by' => Auth::id(), 'assigned_at' => now()]
        );

        $asignado = User::find($this->nuevoAsignadoId);
        $nombre   = $asignado?->name ?? '—';

        // Registrar en el hilo
        TicketMessage::create([
            'ticket_id'       => $this->ticket->id,
            'user_id'         => Auth::id(),
            'mensaje'         => "<em>Asignado a <strong>{$nombre}</strong></em>",
            'es_nota_interna' => true,
        ]);

        // Notificar al usuario recién asignado (solo si es una asignación nueva)
        if ($asignado && $asignacion->wasRecentlyCreated && $asignado->id !== Auth::id()) {
            $notif = new TicketAsignadoNotification($this->ticket, Auth::user()->name);
            try { $asignado->notify($notif); } catch (\Throwable) {}
            try { $notif->enviarWhatsapp($asignado); } catch (\Throwable) {}
        }

        $this->nuevoAsignadoId = null;
        unset($this->asignadosActuales, $this->usuariosDisponibles, $this->mensajes);

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Asignado correctamente', 'timer' => 1500]);
    }

    public function quitarAsignado(int $userId): void
    {
        if ($this->sinPermiso('asignar_tickets')) return;

        TicketAsignacion::where('ticket_id', $this->ticket->id)
            ->where('user_id', $userId)
            ->delete();

        $nombre = User::find($userId)?->name ?? '—';
        TicketMessage::create([
            'ticket_id'       => $this->ticket->id,
            'user_id'         => Auth::id(),
            'mensaje'         => "<em>Se quitó la asignación de <strong>{$nombre}</strong></em>",
            'es_nota_interna' => true,
        ]);

        unset($this->asignadosActuales, $this->usuariosDisponibles, $this->mensajes);
        $this->dispatch('swal', ['icon' => 'info', 'title' => 'Asignación removida', 'timer' => 1500]);
    }

    public function toggleEdicion(): void
    {
        $this->editandoTicket = ! $this->editandoTicket;
    }

    public function guardarEdicion(): void
    {
        if ($this->sinPermiso('gestionar_tickets')) return;

        $this->validate([
            'editTitulo'        => 'required|string|max:200',
            'editPrioridad'     => 'required|in:baja,media,alta,urgente',
            'editFechaLimite'   => 'nullable|date',
            'editObservaciones' => 'nullable|string|max:500',
        ]);

        $this->ticket->update([
            'titulo'        => $this->editTitulo,
            'prioridad'     => $this->editPrioridad,
            'fecha_limite'  => $this->editFechaLimite ?: null,
            'observaciones' => $this->editObservaciones ?: null,
        ]);

        $this->ticket->refresh();
        $this->editandoTicket = false;

        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Ticket actualizado', 'timer' => 1500]);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.ticket-show');
    }
}
