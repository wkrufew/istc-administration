<?php

namespace App\Console\Commands;

use App\Jobs\CrearUsuarioMoodleJob;
use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Console\Command;

class SyncMoodlePendientes extends Command
{
    protected $signature = 'moodle:sync-pendientes
                            {--dry-run : Muestra los usuarios a sincronizar sin despachar jobs}';

    protected $description = 'Despacha CrearUsuarioMoodleJob para todos los usuarios sin moodle_id que tengan al menos una matrícula';

    public function handle(): int
    {
        if (! MoodleService::activo()) {
            $this->error('La integración con Moodle no está activa. Verifique MOODLE_MODE en .env y moodle.activo en Settings.');
            return Command::FAILURE;
        }

        $dryRun = $this->option('dry-run');

        $usuarios = User::whereNull('moodle_id')
            ->whereHas('matriculas')
            ->get(['id', 'name', 'email', 'cedula']);

        if ($usuarios->isEmpty()) {
            $this->info('No hay usuarios pendientes de sincronizar con Moodle.');
            return Command::SUCCESS;
        }

        $this->info("Usuarios sin moodle_id con matrícula: {$usuarios->count()}");

        if ($dryRun) {
            $this->table(['ID', 'Nombre', 'Correo', 'Cédula'], $usuarios->map(fn($u) => [
                $u->id, $u->name, $u->email, $u->cedula ?? '—',
            ])->toArray());
            $this->warn('Modo dry-run: no se despacharon jobs.');
            return Command::SUCCESS;
        }

        $confirmacion = $this->confirm("¿Despachar {$usuarios->count()} jobs de sincronización?", true);
        if (! $confirmacion) {
            $this->info('Operación cancelada.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($usuarios->count());
        $bar->start();

        foreach ($usuarios as $index => $usuario) {
            // Delay progresivo: 5 segundos entre cada job para no saturar la API de Moodle
            CrearUsuarioMoodleJob::dispatch($usuario->id)->delay(now()->addSeconds($index * 5));
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✓ {$usuarios->count()} jobs encolados con delay progresivo de 5s entre cada uno.");

        return Command::SUCCESS;
    }
}
