<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CrearUsuarioMoodleJob implements ShouldQueue
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
            Log::warning('CrearUsuarioMoodleJob: usuario no encontrado', ['user_id' => $this->userId]);
            return;
        }

        // Si ya tiene moodle_id, solo verificamos que sigue existiendo
        if ($user->moodle_id) {
            Log::info('CrearUsuarioMoodleJob: usuario ya tiene moodle_id, omitiendo', [
                'user_id'   => $user->id,
                'moodle_id' => $user->moodle_id,
            ]);
            return;
        }

        // Buscar si ya existe en Moodle
        $moodleUser = $moodle->buscarUsuario($user);

        if ($moodleUser) {
            $user->moodle_id = $moodleUser['id'];
            $user->saveQuietly();
            Log::info('CrearUsuarioMoodleJob: usuario encontrado en Moodle, moodle_id sincronizado', [
                'user_id'   => $user->id,
                'moodle_id' => $user->moodle_id,
            ]);
            return;
        }

        // Crear en Moodle
        $moodleId = $moodle->crearUsuario($user);
        $user->moodle_id = $moodleId;
        $user->saveQuietly();

        Log::info('CrearUsuarioMoodleJob: usuario creado en Moodle', [
            'user_id'   => $user->id,
            'moodle_id' => $moodleId,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('CrearUsuarioMoodleJob: falló definitivamente', [
            'user_id' => $this->userId,
            'error'   => $e->getMessage(),
        ]);
    }
}
