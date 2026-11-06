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

    /**
     * Quién lo puso en proceso, para el "último envío" del aspirante. Propiedad con valor por
     * defecto (no promovida) para que los trabajos ya encolados antes de este cambio sigan funcionando.
     */
    private ?int $enviadoPor = null;

    public function __construct(
        private readonly int    $userId,
        private readonly string $password,
        ?int $enviadoPor = null,
    ) {
        $this->enviadoPor = $enviadoPor;
    }

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

        // Solo un correo que realmente salió cuenta como envío de credenciales
        $user->aspirante?->update([
            'credenciales_enviadas_at'  => now(),
            'credenciales_enviadas_por' => $this->enviadoPor,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('EnviarEmailBienvenidaAspirante falló definitivamente', [
            'user_id' => $this->userId,
            'error'   => $exception->getMessage(),
        ]);
    }
}
