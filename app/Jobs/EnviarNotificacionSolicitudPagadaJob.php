<?php

namespace App\Jobs;

use App\Mail\SolicitudPagadaConfirmada;
use App\Models\Solicitud;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionSolicitudPagadaJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(private readonly int $solicitudId) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') return;

        $solicitud = Solicitud::with([
            'estudiante',
            'tipoSolicitud',
            'obligacion.pagos',
        ])->find($this->solicitudId);

        if (! $solicitud || ! $solicitud->estudiante?->email) {
            Log::warning('SolicitudPagadaJob: solicitud o email no encontrado', ['id' => $this->solicitudId]);
            return;
        }

        try {
            SettingService::buildMailer()
                ->to($solicitud->estudiante->email)
                ->send(new SolicitudPagadaConfirmada($solicitud));
        } catch (\Throwable $e) {
            Log::warning('SolicitudPagadaJob: email falló', [
                'id'    => $this->solicitudId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SolicitudPagadaJob falló', [
            'id'    => $this->solicitudId,
            'error' => $exception->getMessage(),
        ]);
    }
}
