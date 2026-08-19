<?php

namespace App\Mail;

use App\Models\Matricula;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RetiroRegistrado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Matricula $matricula,
    ) {}

    public function envelope(): Envelope
    {
        $periodo = $this->matricula->periodo?->code ?? '';

        return new Envelope(
            subject: "Notificación de Retiro — {$periodo}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.retiro.notificacion',
            with: [
                'estudiante' => $this->matricula->estudiante,
                'matricula'  => $this->matricula,
                'detalles'   => $this->matricula->detalles->load('materia'),
                'retiro'     => $this->matricula->retiro,
                'instituto'  => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'web'          => SettingService::get('instituto.web', ''),
                    'direccion'    => SettingService::get('instituto.direccion', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                ],
            ],
        );
    }
}
