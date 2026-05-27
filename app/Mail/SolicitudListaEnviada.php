<?php

namespace App\Mail;

use App\Models\Solicitud;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudListaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Solicitud $solicitud) {}

    public function envelope(): Envelope
    {
        $nombreCorto = SettingService::get('instituto.nombre_corto', 'Instituto');
        $tipoNombre  = $this->solicitud->tipoSolicitud?->nombre ?? 'Documento';

        return new Envelope(
            subject: "Tu {$tipoNombre} está listo — {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.solicitud.lista-enviada',
            with: [
                'instituto' => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'web'          => SettingService::get('instituto.web', ''),
                    'direccion'    => SettingService::get('instituto.direccion', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                    'url_portal'   => url('/login'),
                ],
            ],
        );
    }
}
