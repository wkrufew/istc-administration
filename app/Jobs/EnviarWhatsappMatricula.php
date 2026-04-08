<?php

namespace App\Jobs;

use App\Models\Matricula;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarWhatsappMatricula implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int  $tries   = 2;        // reintentos si falla
    public int  $timeout = 30;       // segundos máximo

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int  $matriculaId,
        private readonly bool $esPrimerMatricula
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsappService $whatsapp): void
    {
        // Cargar matrícula con todas las relaciones necesarias
        $matricula = Matricula::with([
            'estudiante',
            'carrera',
            'periodo',
            'obligaciones' => fn($q) => $q->where('tipo', 'MATRICULA')->latest(),
        ])->find($this->matriculaId);

        if (! $matricula) {
            Log::warning('WhatsApp Job: matrícula no encontrada', [
                'matricula_id' => $this->matriculaId,
            ]);
            return;
        }

        $estudiante = $matricula->estudiante;
        $telefono   = $estudiante->telefono ?? $estudiante->phone ?? null;

        if (! $telefono) {
            Log::warning('WhatsApp Job: estudiante sin teléfono', [
                'estudiante_id' => $estudiante->id,
                'matricula_id'  => $this->matriculaId,
            ]);
            return;
        }

        // Datos comunes
        $nombre          = $estudiante->name ?? $estudiante->nombre_completo ?? '—';
        $cedula          = $estudiante->cedula ?? '—';
        $correo          = $estudiante->email ?? '—';
        $codigoMatricula = $matricula->code ?? '—';
        $carrera         = $matricula->carrera?->name ?? '—';
        $periodo         = $matricula->periodo?->code ?? '—';

        // Monto de la obligación de matrícula
        $obligacion      = $matricula->obligaciones->first();
        $monto           = $obligacion
            ? '$' . number_format($obligacion->monto_final, 2)
            : '—';
        $fechaLimite     = $obligacion?->fecha_vencimiento
            ? Carbon::parse($obligacion->fecha_vencimiento)->format('d/m/Y')
            : '—';

        // ── Elegir template según si es primera matrícula ─────────────────────
        if ($this->esPrimerMatricula) {
            $whatsapp->enviarBienvenidaConCredenciales(
                telefono: $telefono,
                nombre: $nombre,
                correo: $correo,
                cedula: $cedula,
                codigoMatricula: $codigoMatricula,
                carrera: $carrera,
                periodo: $periodo,
                monto: $monto,
                fechaLimite: $fechaLimite,
            );
        } else {
            $whatsapp->enviarConfirmacionMatricula(
                telefono: $telefono,
                nombre: $nombre,
                codigoMatricula: $codigoMatricula,
                carrera: $carrera,
                periodo: $periodo,
                cedula: $cedula,
                monto: $monto,
                fechaLimite: $fechaLimite,
            );
        }
    }

    // Si el job falla después de los reintentos, solo loguea — no afecta la matrícula
    public function failed(\Throwable $exception): void
    {
        Log::error('WhatsApp Job falló definitivamente', [
            'matricula_id'       => $this->matriculaId,
            'es_primer_matricula' => $this->esPrimerMatricula,
            'error'              => $exception->getMessage(),
        ]);
    }
}
