<?php

namespace App\Jobs;

use App\Mail\SolicitudListaEnviada;
use App\Models\Solicitud;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionSolicitudListaJob implements ShouldQueue
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
        ])->find($this->solicitudId);

        if (! $solicitud || ! $solicitud->estudiante?->email) {
            Log::warning('SolicitudListaJob: solicitud o email no encontrado', ['id' => $this->solicitudId]);
            return;
        }

        try {
            SettingService::buildMailer()
                ->to($solicitud->estudiante->email)
                ->send(new SolicitudListaEnviada($solicitud));
        } catch (\Throwable $e) {
            Log::warning('SolicitudListaJob: email falló', [
                'id'    => $this->solicitudId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SolicitudListaJob falló', [
            'id'    => $this->solicitudId,
            'error' => $exception->getMessage(),
        ]);
    }
}
