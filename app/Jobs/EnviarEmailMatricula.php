<?php

namespace App\Jobs;

use App\Jobs\CrearUsuarioMoodleJob;
use App\Mail\MatriculaConfirmada;
use App\Models\Matricula;
use App\Services\MoodleService;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarEmailMatricula implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int  $matriculaId,
        private readonly bool $esPrimerMatricula,
        private readonly bool $esEdicion = false
    ) {}

    public function handle(): void
    {
        if (SettingService::get('notificaciones.matricula_email', '0') !== '1') {
            return;
        }

        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        $mailer = SettingService::buildMailer();

        $matricula = Matricula::with([
            'estudiante',
            'carrera',
            'periodo',
            'detalles.materia',
            'obligacionesFinancieras',
        ])->find($this->matriculaId);

        if (! $matricula) {
            Log::warning('Email Job: matrícula no encontrada', ['matricula_id' => $this->matriculaId]);
            return;
        }

        $email = $matricula->estudiante?->email;

        if (! $email) {
            Log::warning('Email Job: estudiante sin correo', [
                'estudiante_id' => $matricula->estudiante?->id,
                'matricula_id'  => $this->matriculaId,
            ]);
            return;
        }

        $mailer->to($email)->send(new MatriculaConfirmada(
            matricula:         $matricula,
            esPrimerMatricula: $this->esPrimerMatricula,
            esEdicion:         $this->esEdicion,
        ));

        // Registrar en Moodle al confirmar la primera matrícula
        if ($this->esPrimerMatricula && ! $this->esEdicion && MoodleService::activo()) {
            $estudiante = $matricula->estudiante;
            if ($estudiante && ! $estudiante->moodle_id) {
                CrearUsuarioMoodleJob::dispatch($estudiante->id);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Email Job falló definitivamente', [
            'matricula_id' => $this->matriculaId,
            'error'        => $exception->getMessage(),
        ]);
    }
}
