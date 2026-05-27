<?php

namespace App\Jobs;

use App\Mail\ReenvioCredencialesAcceso;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarReenvioCredencialesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly int    $userId,
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
            Log::warning("EnviarReenvioCredencialesJob: usuario #{$this->userId} no encontrado o sin email.");
            return;
        }

        if (! $user->cedula) {
            Log::warning("EnviarReenvioCredencialesJob: usuario #{$this->userId} no tiene cédula.");
            return;
        }

        if (SettingService::get('smtp.activo', '0') !== '1') {
            return;
        }

        // La contraseña restablecida siempre es la cédula del usuario
        SettingService::buildMailer()
            ->to($user->email)
            ->send(new ReenvioCredencialesAcceso(
                usuario: $user,
                plainPassword: $user->cedula,
                tipoAcceso: $this->tipoAcceso,
                nombreRol: $this->nombreRol,
            ));

        Log::info("EnviarReenvioCredencialesJob: correo enviado a {$user->email}.");
    }
}
