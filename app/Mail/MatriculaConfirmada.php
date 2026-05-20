<?php

namespace App\Mail;

use App\Models\Matricula;
use App\Services\MoodleService;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MatriculaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Matricula $matricula,
        public readonly bool $esPrimerMatricula,
        public readonly bool $esEdicion = false,
    ) {}

    public function envelope(): Envelope
    {
        $instituto = SettingService::get('instituto.nombre_corto', 'Instituto');
        $periodo   = $this->matricula->periodo?->code ?? '';

        $subject = match (true) {
            $this->esEdicion         => "Actualización de Matrícula — {$periodo}",
            $this->esPrimerMatricula => "Bienvenido al {$instituto} — Matrícula Confirmada",
            default                  => "Detalles de tu Matrícula — {$periodo}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $moodleActivo = MoodleService::activo();
        $moodleUrl    = $moodleActivo ? SettingService::get('moodle.url', '') : null;

        $obligMatricula   = $this->matricula->obligacionesFinancieras
            ->firstWhere('tipo', 'MATRICULA');
        $obligColegiatura = $this->matricula->obligacionesFinancieras
            ->firstWhere('tipo', 'COLEGIATURA');
        $obligInscripcion = $this->matricula->obligacionesFinancieras
            ->firstWhere('tipo', 'INSCRIPCION');
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.matricula.confirmacion',
            with: [
                'estudiante'        => $this->matricula->estudiante,
                'matricula'         => $this->matricula,
                'obligacion'        => $obligMatricula,
                'obligColegiatura'  => $obligColegiatura,
                'esPrimerMatricula' => $this->esPrimerMatricula,
                'esEdicion'         => $this->esEdicion,
                'instituto'         => [
                    'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto'),
                    'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
                    'email'        => SettingService::get('instituto.email', ''),
                    'telefono'     => SettingService::get('instituto.telefono', ''),
                    'web'          => SettingService::get('instituto.web', ''),
                    'direccion'    => SettingService::get('instituto.direccion', ''),
                    'logo_url'     => $logoPath ? url('storage/' . $logoPath) : null,
                    'url_portal'   => url('/login'),
                ],
                'moodle_activo' => $moodleActivo,
                'moodle_url'    => $moodleUrl,
                'monto'              => $obligMatricula
                    ? number_format((float) $obligMatricula->monto_final, 2) : null,
                'fechaLimite'        => $obligMatricula?->fecha_vencimiento
                    ? Carbon::parse($obligMatricula->fecha_vencimiento)->format('d/m/Y') : null,
                'montoArancel'       => $obligColegiatura
                    ? number_format((float) $obligColegiatura->monto_final, 2) : null,
                'fechaArancel'       => $obligColegiatura?->fecha_vencimiento
                    ? Carbon::parse($obligColegiatura->fecha_vencimiento)->format('d/m/Y') : null,
                'montoInscripcion'   => $obligInscripcion
                    ? number_format((float) $obligInscripcion->monto_final, 2) : null,
                'fechaInscripcion'   => $obligInscripcion?->fecha_vencimiento
                    ? Carbon::parse($obligInscripcion->fecha_vencimiento)->format('d/m/Y') : null,
            ],
        );
    }
}
