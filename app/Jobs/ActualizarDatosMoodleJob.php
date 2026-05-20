<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ActualizarDatosMoodleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;
    public array $backoff = [15, 30, 60];

    public function __construct(private readonly int $userId) {}

    public function handle(MoodleService $moodle): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            Log::warning('ActualizarDatosMoodleJob: usuario no encontrado', ['user_id' => $this->userId]);
            return;
        }

        if (! $user->moodle_id) {
            Log::warning('ActualizarDatosMoodleJob: usuario sin moodle_id', ['user_id' => $user->id]);
            return;
        }

        $moodle->actualizarUsuario($user);

        Log::info('ActualizarDatosMoodleJob: datos actualizados en Moodle', [
            'user_id'   => $user->id,
            'moodle_id' => $user->moodle_id,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ActualizarDatosMoodleJob: falló definitivamente', [
            'user_id' => $this->userId,
            'error'   => $e->getMessage(),
        ]);
    }
}
