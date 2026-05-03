<?php

namespace App\Jobs;

use App\Mail\PagoConfirmado;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\User;
use App\Services\SettingService;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionPago implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(private readonly int $pagoId) {}

    public function handle(WhatsappService $whatsapp): void
    {
        $pago = Pago::with([
            'obligacion.matricula.estudiante',
            'obligacion.matricula.carrera',
            'obligacion.matricula.periodo',
        ])->find($this->pagoId);

        if (! $pago) {
            Log::warning('NotificacionPago Job: pago no encontrado', ['pago_id' => $this->pagoId]);
            return;
        }

        $matricula  = $pago->obligacion?->matricula;
        $estudiante = $matricula?->estudiante;

        if (! $estudiante) {
            Log::warning('NotificacionPago Job: sin estudiante', ['pago_id' => $this->pagoId]);
            return;
        }

        $this->enviarEmail($pago, $matricula, $estudiante);
        $this->enviarWhatsapp($pago, $matricula, $estudiante, $whatsapp);
    }

    private function enviarEmail(Pago $pago, Matricula $matricula, User $estudiante): void
    {
        if (SettingService::get('notificaciones.pago_email', '0') !== '1') return;
        if (SettingService::get('smtp.activo', '0') !== '1') return;

        $email = $estudiante->email;
        if (! $email) return;

        try {
            SettingService::buildMailer()
                ->to($email)
                ->send(new PagoConfirmado($pago, $matricula));
        } catch (\Throwable $e) {
            Log::warning('NotificacionPago: email falló', [
                'pago_id' => $this->pagoId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function enviarWhatsapp(Pago $pago, Matricula $matricula, User $estudiante, WhatsappService $whatsapp): void
    {
        if (SettingService::get('notificaciones.pago_whatsapp', '0') !== '1') return;
        if (SettingService::get('whatsapp.activo', '0') !== '1') return;

        $telefono = $estudiante->phone ?? null;
        if (! $telefono) return;

        try {
            $whatsapp->enviarConfirmacionPago(
                telefono:          $telefono,
                nombre:            $estudiante->name,
                numeroComprobante: $pago->numero_comprobante,
                monto:             '$' . number_format((float) $pago->monto, 2),
                metodoPago:        $pago->metodo_pago,
                fechaPago:         Carbon::parse($pago->fecha_pago)->format('d/m/Y'),
                codigoMatricula:   $matricula->code,
            );
        } catch (\Throwable $e) {
            Log::warning('NotificacionPago: WhatsApp falló', [
                'pago_id' => $this->pagoId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('NotificacionPago Job falló', [
            'pago_id' => $this->pagoId,
            'error'   => $exception->getMessage(),
        ]);
    }
}
