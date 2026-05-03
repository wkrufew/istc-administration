<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketCreadoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket;

        return (new MailMessage)
            ->subject("Ticket #{$ticket->numero} creado — {$ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}!")
            ->line("Tu ticket de soporte ha sido registrado exitosamente.")
            ->line("**Número:** {$ticket->numero}")
            ->line("**Título:** {$ticket->titulo}")
            ->line("**Prioridad:** " . ucfirst($ticket->prioridad))
            ->line("**Estado:** " . ucfirst($ticket->estado))
            ->action('Ver ticket', url("/administracion/tickets/{$ticket->id}"))
            ->line("Nuestro equipo revisará tu solicitud a la brevedad posible.")
            ->salutation("Instituto Superior Tecnológico Cumandá");
    }

    /**
     * Enviar WhatsApp (llamar manualmente, no es canal nativo).
     * Retorna true/false para loguear si fue necesario.
     */
    public function enviarWhatsapp(object $notifiable): bool
    {
        $telefono = $notifiable->phone ?? null;
        if (! $telefono) {
            return false;
        }

        try {
            /** @var WhatsappService $ws */
            $ws = app(WhatsappService::class);
            return $ws->enviarNuevoTicket(
                telefono: $telefono,
                nombre: $notifiable->name,
                numero: $this->ticket->numero,
                titulo: $this->ticket->titulo,
                prioridad: ucfirst($this->ticket->prioridad),
                estado: ucfirst($this->ticket->estado),
            );
        } catch (\Throwable $e) {
            Log::warning('WhatsApp ticket_nuevo falló', [
                'ticket_id' => $this->ticket->id,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
