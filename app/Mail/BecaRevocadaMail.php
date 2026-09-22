<?php

namespace App\Mail;

use App\Models\BecaAplicada;
use App\Models\ObligacionesFinanciera;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BecaRevocadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly BecaAplicada $beca,
        public readonly Collection   $obligacionesPendientes,
        public readonly float        $totalOriginal,
        public readonly float        $totalPagado,
    ) {}

    public function envelope(): Envelope
    {
        $nombreCorto = SettingService::get('instituto.nombre_corto', 'Instituto');
        return new Envelope(
            subject: "Beca revocada — Ajuste de colegiatura | {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.beca.revocada',
            with: [
                'instituto' => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                    'url_portal'   => url('/login'),
                ],
            ],
        );
    }
}
