<?php

namespace App\Jobs;

use App\Mail\BienvenidaAspirante;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarEmailBienvenidaAspirante implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int    $userId,
        private readonly string $password,
    ) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        $user = User::find($this->userId);

        if (! $user || ! $user->email) {
            Log::warning('EnviarEmailBienvenidaAspirante: usuario no encontrado o sin correo', [
                'user_id' => $this->userId,
            ]);
            return;
        }

        $mailer = SettingService::buildMailer();
        $mailer->to($user->email)->send(new BienvenidaAspirante($user, $this->password));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('EnviarEmailBienvenidaAspirante falló definitivamente', [
            'user_id' => $this->userId,
            'error'   => $exception->getMessage(),
        ]);
    }
}
