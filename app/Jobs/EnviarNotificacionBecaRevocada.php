<?php

namespace App\Jobs;

use App\Mail\BecaRevocadaMail;
use App\Models\BecaAplicada;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionBecaRevocada implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        private readonly int   $becaId,
        private readonly float $totalOriginal,
        private readonly float $totalPagado,
    ) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') return;

        $beca = BecaAplicada::with(['estudiante', 'tipoBeca'])->find($this->becaId);
        if (! $beca || ! $beca->estudiante?->email) return;

        $periodo = Periodo::periodoActivoGlobal();

        $pendientes = ObligacionesFinanciera::where('user_id', $beca->user_id)
            ->where('tipo', 'COLEGIATURA')
            ->whereIn('estado', ['Pendiente', 'Parcial'])
            ->when($periodo, fn($q) => $q->where('periodo_id', $periodo->id))
            ->orderBy('fecha_vencimiento')
            ->get();

        try {
            SettingService::buildMailer()
                ->to($beca->estudiante->email)
                ->send(new BecaRevocadaMail($beca, $pendientes, $this->totalOriginal, $this->totalPagado));
        } catch (\Throwable $e) {
            Log::warning('BecaRevocada: email falló', ['beca_id' => $this->becaId, 'error' => $e->getMessage()]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('EnviarNotificacionBecaRevocada falló', ['beca_id' => $this->becaId, 'error' => $e->getMessage()]);
    }
}
