<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketMensajeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Ticket        $ticket,
        private readonly TicketMessage $mensaje,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket  = $this->ticket;
        $mensaje = $this->mensaje;
        $autor   = $mensaje->autor;

        return (new MailMessage)
            ->subject("Nueva respuesta en ticket #{$ticket->numero} — {$ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}!")
            ->line("Hay una nueva respuesta en tu ticket de soporte.")
            ->line("**Ticket:** {$ticket->numero} — {$ticket->titulo}")
            ->line("**Respondido por:** {$autor?->name}")
            ->line("**Estado actual:** " . ucfirst($ticket->estado))
            ->action('Ver respuesta', url("/administracion/tickets/{$ticket->id}"))
            ->line("Ingresa al sistema para ver el mensaje completo.")
            ->salutation("Instituto Superior Tecnológico Cumandá");
    }

    /**
     * Enviar WhatsApp (llamar manualmente, no es canal nativo).
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
            return $ws->enviarRespuestaTicket(
                telefono: $telefono,
                nombre: $notifiable->name,
                numero: $this->ticket->numero,
                titulo: $this->ticket->titulo,
                respondidoPor: $this->mensaje->autor?->name ?? 'Sistema',
            );
        } catch (\Throwable $e) {
            Log::warning('WhatsApp ticket_respuesta falló', [
                'ticket_id' => $this->ticket->id,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
