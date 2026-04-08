<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ReportesFinancieros extends Component
{
    use WithPagination;

    // =========================================================================
    // FILTROS
    // =========================================================================
    public ?int    $periodoId    = null;
    public string  $busqueda     = '';
    public string  $filtroEstado = '';
    public string  $filtroTipo   = '';

    // Fila expandida para ver pagos
    public ?int    $expandedId   = null;

    // Tab activo: 'obligaciones' | 'mora'
    public string  $tab          = 'obligaciones';

    public function mount(): void
    {
        $actual = Periodo::where('is_current', true)->first();
        $this->periodoId = $actual?->id ?? Periodo::latest()->first()?->id;
    }

    public function updatedBusqueda(): void
    {
        $this->resetPage();
    }
    public function updatedFiltroEstado(): void
    {
        $this->resetPage();
    }
    public function updatedFiltroTipo(): void
    {
        $this->resetPage();
    }
    public function updatedPeriodoId(): void
    {
        $this->resetPage();
        $this->expandedId = null;
    }

    public function toggleExpand(int $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->resetPage();
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
    // COMPUTED — STATS FINANCIEROS DEL PERIODO
    // =========================================================================
    #[Computed]
    public function stats(): array
    {
        if (! $this->periodoId) return [];

        $pid = $this->periodoId;

        $base = ObligacionesFinanciera::where('periodo_id', $pid);

        $totalObligaciones = (clone $base)->count();
        $montoTotal        = (clone $base)->sum('monto_final');
        $montoRecaudado    = (clone $base)->where('estado', 'Pagado')->sum('monto_final');
        $montoPendiente    = (clone $base)->whereIn('estado', ['Pendiente', 'Parcial'])->sum('monto_final');
        $montoVencido      = (clone $base)->where('estado', 'Vencido')->sum('monto_final');

        $countPagado    = (clone $base)->where('estado', 'Pagado')->count();
        $countPendiente = (clone $base)->whereIn('estado', ['Pendiente', 'Parcial'])->count();
        $countVencido   = (clone $base)->where('estado', 'Vencido')->count();
        $countParcial   = (clone $base)->where('estado', 'Parcial')->count();

        // Recaudado por tipo
        $porTipo = (clone $base)
            ->selectRaw('tipo, SUM(monto_final) as total, COUNT(*) as cantidad')
            ->groupBy('tipo')
            ->get()
            ->keyBy('tipo');

        $pctRecaudado = $montoTotal > 0
            ? round(($montoRecaudado / $montoTotal) * 100, 1)
            : 0;

        // Estudiantes con alguna obligación vencida
        $estudiantesEnMora = (clone $base)
            ->where('estado', 'Vencido')
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total_obligaciones'   => $totalObligaciones,
            'monto_total'          => $montoTotal,
            'monto_recaudado'      => $montoRecaudado,
            'monto_pendiente'      => $montoPendiente,
            'monto_vencido'        => $montoVencido,
            'count_pagado'         => $countPagado,
            'count_pendiente'      => $countPendiente,
            'count_vencido'        => $countVencido,
            'count_parcial'        => $countParcial,
            'pct_recaudado'        => $pctRecaudado,
            'estudiantes_en_mora'  => $estudiantesEnMora,
            'por_tipo'             => $porTipo,
        ];
    }

    // =========================================================================
    // COMPUTED — TABLA OBLIGACIONES
    // =========================================================================
    #[Computed]
    public function obligaciones()
    {
        return ObligacionesFinanciera::with([
            'estudiante',
            'matricula.carrera',
            'pagos' => fn($q) => $q->orderByDesc('fecha_pago'),
        ])
            ->when($this->periodoId, fn($q) => $q->where('periodo_id', $this->periodoId))
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroTipo,   fn($q) => $q->where('tipo',   $this->filtroTipo))
            ->when(
                $this->busqueda,
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('name',    'like', '%' . $this->busqueda . '%')
                        ->orWhere('cedula', 'like', '%' . $this->busqueda . '%')
                )
            )
            ->orderByRaw("FIELD(estado, 'Vencido', 'Pendiente', 'Parcial', 'Pagado')")
            ->orderBy('fecha_vencimiento')
            ->paginate(15);
    }

    // =========================================================================
    // COMPUTED — PANEL DE MORA
    // =========================================================================
    #[Computed]
    public function estudiantesEnMora()
    {
        if (! $this->periodoId) return collect();

        return ObligacionesFinanciera::with('user')
            ->where('periodo_id', $this->periodoId)
            ->where('estado', 'Vencido')
            ->get()
            ->groupBy('user_id')
            ->map(function ($obligaciones, $userId) {
                $user              = $obligaciones->first()->user;
                $totalAdeudado     = $obligaciones->sum('monto_final');
                $cantidadVencidas  = $obligaciones->count();

                // Días de mora desde la obligación vencida más antigua
                $masAntigua = $obligaciones
                    ->whereNotNull('fecha_vencimiento')
                    ->sortBy('fecha_vencimiento')
                    ->first();

                $diasMora = $masAntigua?->fecha_vencimiento
                    ? now()->diffInDays($masAntigua->fecha_vencimiento)
                    : null;

                // Tipos de obligaciones vencidas
                $tipos = $obligaciones->pluck('tipo')->unique()->values();

                return [
                    'user'              => $user,
                    'total_adeudado'    => $totalAdeudado,
                    'cantidad_vencidas' => $cantidadVencidas,
                    'dias_mora'         => $diasMora,
                    'tipos'             => $tipos,
                    'obligaciones'      => $obligaciones,
                ];
            })
            ->sortByDesc('total_adeudado')
            ->values();
    }

    public function render()
    {
        return view('livewire.administration.reportes-financieros', [
            'obligaciones'      => $this->obligaciones,
            'estudiantesEnMora' => $this->estudiantesEnMora,
        ]);
    }
}
