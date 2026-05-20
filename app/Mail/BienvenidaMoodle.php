<?php

namespace App\Mail;

use App\Models\User;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaMoodle extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $usuario,
    ) {}

    public function envelope(): Envelope
    {
        $nombreCorto = SettingService::get('instituto.nombre_corto', 'Instituto');
        return new Envelope(
            subject: "Acceso a la Plataforma Virtual — {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath  = SettingService::get('instituto.logo_path');
        $moodleUrl = SettingService::get('moodle.url', '');

        return new Content(
            view: 'emails.moodle.bienvenida-moodle',
            with: [
                'instituto' => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'direccion'    => SettingService::get('instituto.direccion', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                ],
                'moodle_url' => $moodleUrl,
            ],
        );
    }
}
