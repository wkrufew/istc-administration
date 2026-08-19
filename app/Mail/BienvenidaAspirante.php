<?php

namespace App\Mail;

use App\Models\User;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaAspirante extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $password,
    ) {}

    public function envelope(): Envelope
    {
        $instituto = SettingService::get('instituto.nombre_corto', 'ISTC');

        return new Envelope(
            subject: "Bienvenido al proceso de admisión — {$instituto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.admision.bienvenida',
            with: [
                'user'      => $this->user,
                'password'  => $this->password,
                'portalUrl' => route('administracion.admision.dashboard'),
                'instituto' => [
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
