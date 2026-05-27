<?php

namespace App\Mail;

use App\Models\Pago;
use App\Models\Solicitud;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudPagadaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public ?Pago   $pago;
    public string  $montoFormateado;
    public string  $fechaPago;

    public function __construct(public readonly Solicitud $solicitud)
    {
        $this->pago = $solicitud->obligacion?->pagos
            ->where('estado', Pago::ESTADO_APROBADO)
            ->sortByDesc('fecha_pago')
            ->first();

        $this->montoFormateado = '$' . number_format((float) $solicitud->precio_aplicado, 2);
        $this->fechaPago       = $this->pago
            ? Carbon::parse($this->pago->fecha_pago)->format('d/m/Y H:i')
            : now()->format('d/m/Y H:i');
    }

    public function envelope(): Envelope
    {
        $nombreCorto  = SettingService::get('instituto.nombre_corto', 'Instituto');
        $tipoNombre   = $this->solicitud->tipoSolicitud?->nombre ?? 'Solicitud';

        return new Envelope(
            subject: "Pago confirmado — {$tipoNombre} en proceso | {$nombreCorto}",
        );
    }

    public function content(): Content
    {
        $logoPath = SettingService::get('instituto.logo_path');

        return new Content(
            view: 'emails.solicitud.pago-confirmado',
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
            ],
        );
    }
}
