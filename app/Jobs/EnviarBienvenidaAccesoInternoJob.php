<?php

namespace App\Jobs;

use App\Mail\BienvenidaAccesoInterno;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarBienvenidaAccesoInternoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly int    $userId,
        public readonly string $plainPassword,
        public readonly string $tipoAcceso,
        public readonly string $nombreRol,
    ) {}

    public function backoff(): array
    {
        return [30, 60, 120];
    }

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user || ! $user->email) {
            Log::warning("EnviarBienvenidaAccesoInternoJob: usuario #{$this->userId} no encontrado o sin email.");
            return;
        }

        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        SettingService::buildMailer()
            ->to($user->email)
            ->send(new BienvenidaAccesoInterno($user, $this->plainPassword, $this->tipoAcceso, $this->nombreRol));

        Log::info("EnviarBienvenidaAccesoInternoJob: correo enviado a {$user->email}.");
    }
}
