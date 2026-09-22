<?php

namespace App\Jobs;

use App\Mail\BecaAsignadaMail;
use App\Models\BecaAplicada;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use App\Services\SettingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionBecaAsignada implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(private readonly int $becaId) {}

    public function handle(): void
    {
        if (SettingService::get('smtp.activo', '0') !== '1') return;

        $beca = BecaAplicada::with(['estudiante', 'tipoBeca'])->find($this->becaId);
        if (! $beca || ! $beca->estudiante?->email) return;

        $periodo = Periodo::periodoActivoGlobal();

        $pendientes = ObligacionesFinanciera::where('user_id', $beca->user_id)
            ->where('tipo', 'COLEGIATURA')
            ->where('beca_aplicada_id', $beca->id)
            ->whereIn('estado', ['Pendiente', 'Parcial'])
            ->when($periodo, fn($q) => $q->where('periodo_id', $periodo->id))
            ->orderBy('fecha_vencimiento')
            ->get();

        $totalConDescuento = $pendientes->sum('monto_final')
            + ObligacionesFinanciera::where('user_id', $beca->user_id)
                ->where('tipo', 'COLEGIATURA')
                ->where('beca_aplicada_id', $beca->id)
                ->where('estado', 'Pagado')
                ->when($periodo, fn($q) => $q->where('periodo_id', $periodo->id))
                ->sum('monto_final');

        $totalPagado = ObligacionesFinanciera::where('user_id', $beca->user_id)
            ->where('tipo', 'COLEGIATURA')
            ->where('beca_aplicada_id', $beca->id)
            ->where('estado', 'Pagado')
            ->when($periodo, fn($q) => $q->where('periodo_id', $periodo->id))
            ->sum('monto_final');

        try {
            SettingService::buildMailer()
                ->to($beca->estudiante->email)
                ->send(new BecaAsignadaMail($beca, $pendientes, (float) $totalConDescuento, (float) $totalPagado));
        } catch (\Throwable $e) {
            Log::warning('BecaAsignada: email falló', ['beca_id' => $this->becaId, 'error' => $e->getMessage()]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('EnviarNotificacionBecaAsignada falló', ['beca_id' => $this->becaId, 'error' => $e->getMessage()]);
    }
}
