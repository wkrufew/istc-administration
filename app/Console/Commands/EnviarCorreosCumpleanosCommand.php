<?php

namespace App\Console\Commands;

use App\Jobs\EnviarNotificacionCumpleanosJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarCorreosCumpleanosCommand extends Command
{
    protected $signature   = 'cumpleanos:notificar';
    protected $description = 'Envía correos de felicitación a los usuarios que cumplen años hoy.';

    public function handle(): void
    {
        $hoy = now();

        $cumpleaneros = User::whereNotNull('fecha_nacimiento')
            ->whereNotNull('email')
            ->where('is_active', true)
            ->whereMonth('fecha_nacimiento', $hoy->month)
            ->whereDay('fecha_nacimiento', $hoy->day)
            ->where(fn($q) =>
                $q->whereNull('cumpleanos_notificado_year')
                  ->orWhere('cumpleanos_notificado_year', '!=', $hoy->year)
            )
            ->get();

        if ($cumpleaneros->isEmpty()) {
            $this->info('Sin cumpleañeros hoy.');
            return;
        }

        foreach ($cumpleaneros as $user) {
            // Marcamos antes de despachar para evitar duplicados en reintento
            $user->updateQuietly(['cumpleanos_notificado_year' => $hoy->year]);
            EnviarNotificacionCumpleanosJob::dispatch($user->id);
        }

        $total = $cumpleaneros->count();
        $this->info("Despachados {$total} correo(s) de cumpleaños.");
        Log::info("cumpleanos:notificar — {$total} job(s) despachados", [
            'fecha' => $hoy->toDateString(),
            'ids'   => $cumpleaneros->pluck('id')->toArray(),
        ]);
    }
}
