<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Services\SettingService;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketCerradoNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Ticket $ticket,
        private readonly string $cerradoPorNombre,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket;

        return (new MailMessage)
            ->subject("Ticket #{$ticket->numero} cerrado — {$ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}!")
            ->line("Tu ticket de soporte ha sido **cerrado** por **{$this->cerradoPorNombre}**.")
            ->line("**Número:** {$ticket->numero}")
            ->line("**Título:** {$ticket->titulo}")
            ->action('Ver ticket', url("/administracion/tickets/{$ticket->id}"))
            ->line("Si el problema persiste, puedes abrir un nuevo ticket.")
            ->salutation("Instituto Superior Tecnológico Cumandá");
    }

    public function enviarWhatsapp(object $notifiable): bool
    {
        if (SettingService::get('whatsapp.activo', '0') !== '1') return false;

        $telefono = $notifiable->phone ?? null;
        if (! $telefono) return false;

        try {
            /** @var WhatsappService $ws */
            $ws = app(WhatsappService::class);
            return $ws->enviarRespuestaTicket(
                telefono: $telefono,
                nombre: $notifiable->name,
                numero: $this->ticket->numero,
                titulo: $this->ticket->titulo,
                respondidoPor: $this->cerradoPorNombre,
            );
        } catch (\Throwable $e) {
            Log::warning('WhatsApp ticket_cerrado falló', [
                'ticket_id' => $this->ticket->id,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
