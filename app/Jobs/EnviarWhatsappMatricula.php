<?php

namespace App\Jobs;

use App\Models\Matricula;
use App\Services\SettingService;
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

    public int  $tries   = 2;
    public int  $timeout = 30;

    public function __construct(
        private readonly int  $matriculaId,
        private readonly bool $esPrimerMatricula
    ) {}

    public function handle(WhatsappService $whatsapp): void
    {
        if (SettingService::get('whatsapp.activo', '0') !== '1') return;
        if (SettingService::get('notificaciones.matricula_whatsapp', '0') !== '1') return;

        $matricula = Matricula::with([
            'estudiante',
            'carrera',
            'periodo',
            'obligacionesFinancieras' => fn($q) => $q->where('tipo', 'MATRICULA')->latest(),
        ])->find($this->matriculaId);

        if (! $matricula) {
            Log::warning('WhatsApp Job: matrícula no encontrada', ['matricula_id' => $this->matriculaId]);
            return;
        }

        $estudiante = $matricula->estudiante;
        $telefono   = $estudiante->phone ?? null;

        if (! $telefono) {
            Log::warning('WhatsApp Job: estudiante sin teléfono', [
                'estudiante_id' => $estudiante->id,
                'matricula_id'  => $this->matriculaId,
            ]);
            return;
        }

        // Datos del estudiante
        $nombre          = $estudiante->name ?? '—';
        $cedula          = $estudiante->cedula ?? '—';
        $correo          = $estudiante->email ?? '—';
        $codigoMatricula = $matricula->code ?? '—';
        $carrera         = $matricula->carrera?->name ?? '—';
        $periodo         = $matricula->periodo?->code ?? '—';

        // Datos del instituto desde settings
        $nombreInstituto = SettingService::get('instituto.nombre_largo', config('app.name'));
        $urlPlataforma   = SettingService::get('instituto.web', '—');

        // Datos de la obligación de matrícula
        $obligacion  = $matricula->obligacionesFinancieras->first();
        $monto       = $obligacion ? '$' . number_format($obligacion->monto_final, 2) : '—';
        $fechaLimite = $obligacion?->fecha_vencimiento
            ? Carbon::parse($obligacion->fecha_vencimiento)->format('d/m/Y')
            : '—';

        if ($this->esPrimerMatricula) {
            $whatsapp->enviarBienvenidaConCredenciales(
                telefono:        $telefono,
                nombre:          $nombre,
                nombreInstituto: $nombreInstituto,
                codigoMatricula: $codigoMatricula,
                carrera:         $carrera,
                periodo:         $periodo,
                monto:           $monto,
                fechaLimite:     $fechaLimite,
                correo:          $correo,
                urlPlataforma:   $urlPlataforma,
            );
        } else {
            $whatsapp->enviarConfirmacionMatricula(
                telefono:        $telefono,
                nombre:          $nombre,
                nombreInstituto: $nombreInstituto,
                codigoMatricula: $codigoMatricula,
                carrera:         $carrera,
                periodo:         $periodo,
                cedula:          $cedula,
                monto:           $monto,
                fechaLimite:     $fechaLimite,
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('WhatsApp Job matrícula falló definitivamente', [
            'matricula_id'        => $this->matriculaId,
            'es_primer_matricula' => $this->esPrimerMatricula,
            'error'               => $exception->getMessage(),
        ]);
    }
}
