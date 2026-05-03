<?php

namespace App\Livewire\Administration;

use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ConsolidadoCohortes extends Component
{
    public ?int $periodoId = null;

    public function mount(): void
    {
        // Sin pre-selección: el admin elige el cohorte
    }

    public function updatedPeriodoId(): void
    {
        // Forzar re-cómputo al cambiar el cohorte
        unset($this->resumenCohorte);
    }

    // =========================================================================
    // COMPUTED — SELECTS
    // =========================================================================
    #[Computed]
    public function periodos()
    {
        return Periodo::orderByDesc('fecha_inicio')->get();
    }

    // =========================================================================
    // COMPUTED — RESUMEN POR CARRERA
    // =========================================================================
    #[Computed]
    public function resumenCohorte(): array
    {
        if (! $this->periodoId) return [];

        $periodo = Periodo::with(['carreras'])->find($this->periodoId);
        if (! $periodo) return [];

        $resultado = [];

        foreach ($periodo->carreras as $carrera) {

            // ── Financiero ────────────────────────────────────────────────────
            $obligaciones = ObligacionesFinanciera::where('periodo_id', $this->periodoId)
                ->whereHas('matricula', fn($q) => $q->where('carrera_id', $carrera->id))
                ->get();

            $montoTotal = $obligaciones->sum('monto_final');
            $recaudado  = $obligaciones->where('estado', 'Pagado')->sum('monto_final');
            $pendiente  = $obligaciones->whereIn('estado', ['Pendiente', 'Parcial'])->sum('monto_final');
            $mora       = $obligaciones->where('estado', 'Vencido')->sum('monto_final');

            // ── Académico ─────────────────────────────────────────────────────
            $estudiantes = Matricula::where('periodo_id', $this->periodoId)
                ->where('carrera_id', $carrera->id)
                ->where('estado', 'Habilitada')
                ->count();

            $detalles = DetalleMatricula::whereHas(
                'matricula',
                fn($q) => $q->where('periodo_id', $this->periodoId)
                    ->where('carrera_id', $carrera->id)
                    ->where('estado', 'Habilitada')
            )
                ->with('calificacion')
                ->get();

            $aprobados  = $detalles->filter(fn($d) => $d->calificacion?->estado_final === 'Aprobado')->count();
            $reprobados = $detalles->filter(fn($d) => $d->calificacion?->estado_final === 'Reprobado')->count();

            $resultado[] = [
                'carrera'    => $carrera,
                'es_activa'  => (bool) $carrera->pivot->is_current,
                'estudiantes' => $estudiantes,
                'aprobados'  => $aprobados,
                'reprobados' => $reprobados,
                'monto_total' => $montoTotal,
                'recaudado'  => $recaudado,
                'pendiente'  => $pendiente,
                'mora'       => $mora,
            ];
        }

        return $resultado;
    }

    // =========================================================================
    // COMPUTED — TOTALES GENERALES
    // =========================================================================
    #[Computed]
    public function totales(): array
    {
        if (empty($this->resumenCohorte)) return [];

        $items = collect($this->resumenCohorte);

        return [
            'carreras'    => $items->count(),
            'estudiantes' => $items->sum('estudiantes'),
            'aprobados'   => $items->sum('aprobados'),
            'reprobados'  => $items->sum('reprobados'),
            'monto_total' => $items->sum('monto_total'),
            'recaudado'   => $items->sum('recaudado'),
            'pendiente'   => $items->sum('pendiente'),
            'mora'        => $items->sum('mora'),
        ];
    }

    public function render()
    {
        return view('livewire.administration.consolidado-cohortes');
    }
}
