<?php

namespace App\Jobs;

use App\Mail\RetiroRegistrado;
use App\Models\Matricula;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarEmailRetiro implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int $matriculaId,
    ) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        $mailer = SettingService::buildMailer();

        $matricula = Matricula::with([
            'estudiante',
            'carrera',
            'periodo',
            'detalles.materia',
            'retiro',
        ])->find($this->matriculaId);

        if (! $matricula) {
            Log::warning('EnviarEmailRetiro: matrícula no encontrada', ['matricula_id' => $this->matriculaId]);
            return;
        }

        $email = $matricula->estudiante?->email;

        if (! $email) {
            Log::warning('EnviarEmailRetiro: estudiante sin correo', [
                'estudiante_id' => $matricula->estudiante?->id,
                'matricula_id'  => $this->matriculaId,
            ]);
            return;
        }

        $mailer->to($email)->send(new RetiroRegistrado($matricula));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('EnviarEmailRetiro falló definitivamente', [
            'matricula_id' => $this->matriculaId,
            'error'        => $exception->getMessage(),
        ]);
    }
}
