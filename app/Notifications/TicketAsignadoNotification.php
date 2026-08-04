<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Services\SettingService;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketAsignadoNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Ticket $ticket,
        private readonly string $asignadoPorNombre,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket;

        return (new MailMessage)
            ->subject("Se te asignó el ticket #{$ticket->numero} — {$ticket->titulo}")
            ->greeting("Hola, {$notifiable->name}!")
            ->line("**{$this->asignadoPorNombre}** te ha asignado un ticket de soporte.")
            ->line("**Número:** {$ticket->numero}")
            ->line("**Título:** {$ticket->titulo}")
            ->line("**Prioridad:** " . ucfirst($ticket->prioridad))
            ->line("**Estado:** " . ucfirst($ticket->estado))
            ->action('Ver ticket', url("/administracion/tickets/{$ticket->id}"))
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
            return $ws->enviarNuevoTicket(
                telefono: $telefono,
                nombre: $notifiable->name,
                numero: $this->ticket->numero,
                titulo: $this->ticket->titulo,
                prioridad: ucfirst($this->ticket->prioridad),
                estado: ucfirst($this->ticket->estado),
            );
        } catch (\Throwable $e) {
            Log::warning('WhatsApp ticket_asignado falló', [
                'ticket_id' => $this->ticket->id,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
