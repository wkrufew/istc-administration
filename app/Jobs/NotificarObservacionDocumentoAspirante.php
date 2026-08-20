<?php

namespace App\Jobs;

use App\Mail\ObservacionDocumentoAspirante;
use App\Models\Aspirante;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificarObservacionDocumentoAspirante implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int    $aspiranteId,
        private readonly string $labelDocumento,
        private readonly string $observacion,
    ) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        $aspirante = Aspirante::withTrashed()->with(['user', 'carrera'])->find($this->aspiranteId);

        if (! $aspirante || ! $aspirante->user?->email) {
            Log::warning('NotificarObservacionDocumentoAspirante: aspirante no encontrado o sin correo', [
                'aspirante_id' => $this->aspiranteId,
            ]);
            return;
        }

        $mailer = SettingService::buildMailer();
        $mailer->to($aspirante->user->email)
               ->send(new ObservacionDocumentoAspirante($aspirante, $this->labelDocumento, $this->observacion));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('NotificarObservacionDocumentoAspirante falló definitivamente', [
            'aspirante_id' => $this->aspiranteId,
            'error'        => $exception->getMessage(),
        ]);
    }
}
