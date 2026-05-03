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

    public string $nombreInstituto;
    public string $fechaPago;
    public string $montoFormateado;

    public function __construct(
        public readonly Pago      $pago,
        public readonly Matricula $matricula,
    ) {
        $this->nombreInstituto = SettingService::get('institucion.nombre', 'Instituto Superior Tecnológico');
        $this->fechaPago       = Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i');
        $this->montoFormateado = '$' . number_format((float) $pago->monto, 2);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Pago — ' . $this->pago->numero_comprobante,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pago.confirmacion',
        );
    }
}
