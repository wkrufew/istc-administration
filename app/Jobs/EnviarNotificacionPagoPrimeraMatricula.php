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
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Notifica al estudiante cuando se registra el pago de su primera matrícula
 * e incluye en el mensaje el detalle de la inscripción auto-liquidada.
 */
class EnviarNotificacionPagoPrimeraMatricula implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int $pagoMatriculaId,
        private readonly int $pagoInscripcionId,
    ) {}

    public function handle(WhatsappService $whatsapp): void
    {
        $pagoMatricula = Pago::with([
            'obligacion.matricula.estudiante',
            'obligacion.matricula.carrera',
            'obligacion.matricula.periodo',
        ])->find($this->pagoMatriculaId);

        $pagoInscripcion = Pago::find($this->pagoInscripcionId);

        if (! $pagoMatricula || ! $pagoInscripcion) {
            Log::warning('PagoPrimeraMatricula Job: pago(s) no encontrado(s)', [
                'pago_matricula_id'    => $this->pagoMatriculaId,
                'pago_inscripcion_id'  => $this->pagoInscripcionId,
            ]);
            return;
        }

        $matricula  = $pagoMatricula->obligacion?->matricula;
        $estudiante = $matricula?->estudiante;

        if (! $estudiante) {
            Log::warning('PagoPrimeraMatricula Job: sin estudiante', [
                'pago_matricula_id' => $this->pagoMatriculaId,
            ]);
            return;
        }

        $this->enviarEmail($pagoMatricula, $matricula, $estudiante);
        $this->enviarWhatsapp($pagoMatricula, $pagoInscripcion, $matricula, $estudiante, $whatsapp);
    }

    private function enviarEmail(Pago $pago, Matricula $matricula, User $estudiante): void
    {
        if (SettingService::get('notificaciones.pago_email', '0') !== '1') return;
        if (SettingService::get('smtp.activo', '0') !== '1') return;

        $email = $estudiante->email;
        if (! $email) return;

        try {
            // El email de PagoConfirmado ya detecta la inscripción asociada internamente
            SettingService::buildMailer()
                ->to($email)
                ->send(new PagoConfirmado($pago, $matricula));
        } catch (\Throwable $e) {
            Log::warning('PagoPrimeraMatricula: email falló', [
                'pago_id' => $this->pagoMatriculaId,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function enviarWhatsapp(
        Pago           $pagoMatricula,
        Pago           $pagoInscripcion,
        Matricula      $matricula,
        User           $estudiante,
        WhatsappService $whatsapp,
    ): void {
        if (SettingService::get('notificaciones.pago_whatsapp', '0') !== '1') return;
        if (SettingService::get('whatsapp.activo', '0') !== '1') return;

        $telefono = $estudiante->phone ?? null;
        if (! $telefono) return;

        $nombreInstituto  = SettingService::get('instituto.nombre_largo', config('app.name'));
        $totalCobrado     = (float) $pagoMatricula->monto + (float) $pagoInscripcion->monto;
        $fecha            = Carbon::parse($pagoMatricula->fecha_pago)->format('d/m/Y H:i');

        try {
            $whatsapp->enviarPagoPrimeraMatricula(
                telefono:                $telefono,
                nombre:                  $estudiante->name,
                nombreInstituto:         $nombreInstituto,
                comprobanteMatricula:    $pagoMatricula->numero_comprobante,
                montoMatricula:          '$' . number_format((float) $pagoMatricula->monto, 2),
                comprobanteInscripcion:  $pagoInscripcion->numero_comprobante,
                montoInscripcion:        '$' . number_format((float) $pagoInscripcion->monto, 2),
                totalCobrado:            '$' . number_format($totalCobrado, 2),
                metodoPago:              $pagoMatricula->metodo_pago,
                fechaPago:               $fecha,
            );
        } catch (\Throwable $e) {
            Log::warning('PagoPrimeraMatricula: WhatsApp falló', [
                'pago_matricula_id' => $this->pagoMatriculaId,
                'error'             => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('PagoPrimeraMatricula Job falló definitivamente', [
            'pago_matricula_id'   => $this->pagoMatriculaId,
            'pago_inscripcion_id' => $this->pagoInscripcionId,
            'error'               => $exception->getMessage(),
        ]);
    }
}
