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

class BecaAsignadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly BecaAplicada $beca,
        public readonly Collection   $obligacionesPendientes,
        public readonly float        $totalConDescuento,
        public readonly float        $totalPagado,
    ) {}

    public function envelope(): Envelope
    {
        $nombreCorto = SettingService::get('instituto.nombre_corto', 'Instituto');
        return new Envelope(
            subject: "Beca asignada — {$this->beca->tipoBeca->nombre} | {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.beca.asignada',
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
