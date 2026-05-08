<?php

namespace App\Mail;

use App\Models\Matricula;
use App\Models\Pago;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PagoConfirmado extends Mailable
{
    use Queueable, SerializesModels;

    public string $fechaPago;
    public string $montoFormateado;

    public function __construct(
        public readonly Pago      $pago,
        public readonly Matricula $matricula,
    ) {
        $this->fechaPago       = Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i');
        $this->montoFormateado = '$' . number_format((float) $pago->monto, 2);
    }

    public function envelope(): Envelope
    {
        $nombreCorto = SettingService::get('instituto.nombre_corto', 'Instituto');

        return new Envelope(
            subject: "Confirmación de Pago — {$this->pago->numero_comprobante} | {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        // Buscar pago de INSCRIPCION auto-liquidado junto con este pago (misma matrícula)
        $pagoInscripcion = null;
        if ($this->matricula?->id) {
            $pagoInscripcion = Pago::whereHas('obligacion', fn ($q) =>
                $q->where('matricula_id', $this->matricula->id)
                  ->where('tipo', 'INSCRIPCION')
            )
            ->where('estado', Pago::ESTADO_APROBADO)
            ->latest('fecha_pago')
            ->first();
        }

        return new Content(
            view: 'emails.pago.confirmacion',
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
                'pagoInscripcion' => $pagoInscripcion,
            ],
        );
    }
}
