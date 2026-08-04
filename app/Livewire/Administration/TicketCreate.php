<?php

namespace App\Livewire\Administration;

use App\Models\Ticket;
use App\Models\TicketAsignacion;
use App\Models\User;
use App\Notifications\TicketCreadoNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
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

    // Destinatarios — al menos 1 obligatorio
    public array $asignadosIds = [];

    #[Computed]
    public function usuariosAdmin()
    {
        return User::permission('acceso_administrativo')
            ->where('id', '!=', Auth::id())
            ->with('roles')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($u) => [
                'id'  => $u->id,
                'name' => $u->name,
                'rol' => $u->roles->first()?->name ?? 'Administrador',
            ]);
    }

    public function toggleAsignado(int $userId): void
    {
        if (in_array($userId, $this->asignadosIds)) {
            $this->asignadosIds = array_values(array_diff($this->asignadosIds, [$userId]));
        } else {
            $this->asignadosIds[] = $userId;
        }
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('crear_tickets')) return;

        $this->validate();

        if (empty($this->asignadosIds)) {
            $this->addError('asignadosIds', 'Debes seleccionar al menos un destinatario.');
            return;
        }

        $ticket = Ticket::create([
            'numero'        => Ticket::generarNumero(),
            'titulo'        => $this->titulo,
            'descripcion'   => $this->descripcion,
            'prioridad'     => $this->prioridad,
            'estado'        => 'abierto',
            'created_by'    => Auth::id(),
            'assigned_at'   => now(),
            'fecha_limite'  => $this->fecha_limite ?: null,
            'observaciones' => $this->observaciones ?: null,
        ]);

        // Crear asignaciones múltiples
        foreach ($this->asignadosIds as $userId) {
            TicketAsignacion::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => (int) $userId,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);
        }

        // Notificar a cada destinatario
        $notif = new TicketCreadoNotification($ticket);
        foreach ($ticket->asignados as $destinatario) {
            try {
                $destinatario->notify($notif);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Email ticket_creado falló', [
                    'ticket_id' => $ticket->id,
                    'user_id'   => $destinatario->id,
                    'error'     => $e->getMessage(),
                ]);
            }
            try {
                $notif->enviarWhatsapp($destinatario);
            } catch (\Throwable $e) {
                // No crítico
            }
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
