<?php

namespace App\Mail;

use App\Models\User;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReenvioCredencialesAcceso extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $usuario,
        public readonly string $plainPassword,
        public readonly string $tipoAcceso,  // 'administrativo' | 'docente'
        public readonly string $nombreRol,
    ) {}

    public function envelope(): Envelope
    {
        $nombreCorto = SettingService::get('instituto.nombre_corto', 'Instituto');

        return new Envelope(
            subject: "Actualización de credenciales de acceso | {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.credenciales.reenvio-acceso',
            with: [
                'instituto' => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'direccion'    => SettingService::get('instituto.direccion', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                    'url_portal'   => url('/login'),
                ],
                'tipoAcceso' => $this->tipoAcceso,
            ],
        );
    }
}
