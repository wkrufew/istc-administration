<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SuspenderAccesoMoodleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly int  $userId,
        public readonly bool $suspender,
    ) {}

    public function backoff(): array
    {
        return [15, 30, 60];
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            Log::warning("SuspenderAccesoMoodleJob: usuario #{$this->userId} no encontrado.");
            return;
        }

        if (! $user->moodle_id) {
            Log::warning("SuspenderAccesoMoodleJob: usuario #{$this->userId} no tiene moodle_id.");
            return;
        }

        $moodle = new MoodleService();
        $moodle->suspenderAcceso($user, $this->suspender);

        $user->moodle_suspended = $this->suspender;
        $user->saveQuietly();

        $accion = $this->suspender ? 'suspendido' : 'reactivado';
        Log::info("SuspenderAccesoMoodleJob: usuario #{$this->userId} {$accion} en Moodle.");
    }
}
