<?php

namespace App\Jobs;

use App\Mail\ConfirmacionRegistroAspirante;
use App\Models\Aspirante;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarConfirmacionRegistroAspirante implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int $aspiranteId,
    ) {
        $this->afterCommit = true;
    }

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        $aspirante = Aspirante::withTrashed()->with(['user', 'carrera', 'cohorte'])->find($this->aspiranteId);

        if (! $aspirante || ! $aspirante->user?->email) {
            Log::warning('EnviarConfirmacionRegistroAspirante: aspirante no encontrado o sin correo', [
                'aspirante_id' => $this->aspiranteId,
            ]);
            return;
        }

        $mailer = SettingService::buildMailer();
        $mailer->to($aspirante->user->email)
               ->send(new ConfirmacionRegistroAspirante($aspirante));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('EnviarConfirmacionRegistroAspirante falló definitivamente', [
            'aspirante_id' => $this->aspiranteId,
            'error'        => $exception->getMessage(),
        ]);
    }
}
