<?php

namespace App\Jobs;

use App\Mail\FelicitacionCumpleanos;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionCumpleanosJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(private readonly int $userId) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') return;

        $user = User::with([
            'roles',
            'matriculas' => fn($q) => $q->where('estado', 'Habilitada')
                                        ->with('carrera', 'periodo')
                                        ->latest()
                                        ->limit(1),
        ])->find($this->userId);

        if (! $user || ! $user->email) {
            Log::warning('CumpleanosJob: usuario o email no encontrado', ['id' => $this->userId]);
            return;
        }

        try {
            SettingService::buildMailer()
                ->to($user->email, $user->name)
                ->send(new FelicitacionCumpleanos($user));
        } catch (\Throwable $e) {
            Log::warning('CumpleanosJob: email falló', [
                'id'    => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CumpleanosJob falló definitivamente', [
            'id'    => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
