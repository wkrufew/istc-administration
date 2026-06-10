<?php

namespace App\Jobs;

use App\Mail\NotificacionDocenteSolicitud;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionDocenteSolicitudJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int $solicitudId,
        private readonly int $docenteId,
    ) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') return;

        $solicitud = Solicitud::with(['estudiante', 'tipoSolicitud'])->find($this->solicitudId);
        $docente   = User::find($this->docenteId);

        if (! $solicitud || ! $docente || ! $docente->email) {
            Log::warning('DocenteSolicitudJob: datos incompletos', [
                'solicitud_id' => $this->solicitudId,
                'docente_id'   => $this->docenteId,
            ]);
            return;
        }

        try {
            SettingService::buildMailer()
                ->to($docente->email, $docente->name)
                ->send(new NotificacionDocenteSolicitud($solicitud, $docente));
        } catch (\Throwable $e) {
            Log::warning('DocenteSolicitudJob: email falló', [
                'solicitud_id' => $this->solicitudId,
                'error'        => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('DocenteSolicitudJob falló definitivamente', [
            'solicitud_id' => $this->solicitudId,
            'error'        => $exception->getMessage(),
        ]);
    }
}
