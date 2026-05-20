<?php

namespace App\Jobs;

use App\Mail\ReenvioCredencialesAcceso;
use App\Models\User;
use App\Services\MoodleService;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RestablecerCredencialesMoodleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;
    public array $backoff = [30, 60, 120];

    public function __construct(private readonly int $userId) {}

    public function handle(MoodleService $moodle): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            Log::warning('RestablecerCredencialesMoodleJob: usuario no encontrado', ['user_id' => $this->userId]);
            return;
        }

        if (! $user->moodle_id) {
            Log::warning('RestablecerCredencialesMoodleJob: usuario sin moodle_id', ['user_id' => $user->id]);
            return;
        }

        if (! $user->cedula) {
            Log::warning('RestablecerCredencialesMoodleJob: usuario sin cédula', ['user_id' => $user->id]);
            return;
        }

        // Restablecer en Moodle (password = cédula)
        $moodle->restablecerPassword($user);

        Log::info('RestablecerCredencialesMoodleJob: contraseña restablecida en Moodle', [
            'user_id'   => $user->id,
            'moodle_id' => $user->moodle_id,
        ]);

        // Enviar email con las nuevas credenciales si SMTP activo
        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        $mailer = SettingService::buildMailer();
        $rol    = $user->roles->first();

        $mailer->to($user->email)->send(new ReenvioCredencialesAcceso(
            usuario:    $user,
            plainPassword: $user->cedula,
            tipoAcceso: 'moodle',
            nombreRol:  $rol?->name ?? 'Estudiante',
        ));
    }

    public function failed(\Throwable $e): void
    {
        Log::error('RestablecerCredencialesMoodleJob: falló definitivamente', [
            'user_id' => $this->userId,
            'error'   => $e->getMessage(),
        ]);
    }
}
