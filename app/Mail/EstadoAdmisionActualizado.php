<?php

namespace App\Mail;

use App\Models\Aspirante;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EstadoAdmisionActualizado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Aspirante $aspirante,
        public readonly string    $nuevoEstado,
    ) {}

    public function envelope(): Envelope
    {
        $instituto = SettingService::get('instituto.nombre_corto', 'ISTC');

        $subjects = [
            'aprobado'    => "¡Felicitaciones! Has sido aprobado — {$instituto}",
            'rechazado'   => "Resultado de tu solicitud de admisión — {$instituto}",
            'matriculado' => "Tu matrícula ha sido registrada — {$instituto}",
        ];

        return new Envelope(
            subject: $subjects[$this->nuevoEstado]
                ?? "Actualización de tu proceso de admisión — {$instituto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.admision.estado-actualizado',
            with: [
                'aspirante'   => $this->aspirante,
                'nuevoEstado' => $this->nuevoEstado,
                'portalUrl'   => route('administracion.admision.dashboard'),
                'instituto'   => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'web'          => SettingService::get('instituto.web', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                ],
            ],
        );
    }
}
