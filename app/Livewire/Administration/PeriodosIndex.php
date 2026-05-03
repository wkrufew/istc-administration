<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PeriodosIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $expandedId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->expandedId = null;
    }

    public function toggle(int $periodoId): void
    {
        $this->expandedId = $this->expandedId === $periodoId ? null : $periodoId;
    }

    public function cerrar(int $periodoId, int $carreraId): void
    {
        $periodo = Periodo::find($periodoId);
        if (! $periodo) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Periodo no encontrado.']);
            return;
        }

        $cpActual = DB::table('carrera_periodo')
            ->where('carrera_id', $carreraId)
            ->where('periodo_id', $periodo->id)
            ->where('is_current', true)
            ->first();

        if (! $cpActual) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Este periodo no está activo para la carrera seleccionada.']);
            return;
        }

        $cpSiguiente = DB::table('carrera_periodo as cp')
            ->join('periodos as p', 'p.id', '=', 'cp.periodo_id')
            ->where('cp.carrera_id', $carreraId)
            ->where('cp.periodo_id', '!=', $periodo->id)
            ->where('cp.is_current', false)
            ->where('p.fecha_inicio', '>', $periodo->fecha_fin)
            ->orderBy('p.fecha_inicio')
            ->select('cp.*', 'p.code as p_code', 'p.fecha_limite_pago as p_fecha_limite_pago')
            ->first();

        if (! $cpSiguiente) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'No existe un periodo siguiente vinculado a esta carrera. Cree el próximo periodo y vincúlelo primero.']);
            return;
        }

        DB::beginTransaction();
        try {
            $fechaLimitePago = $cpSiguiente->fecha_limite_pago ?? $cpSiguiente->p_fecha_limite_pago;
            $codeSiguiente   = $cpSiguiente->p_code;

            $obligacionesPendientes = ObligacionesFinanciera::where('periodo_id', $periodo->id)
                ->where('tipo', 'COLEGIATURA')
                ->where('estado', '!=', 'Pagado')
                ->with('matricula.carrera')
                ->get();

            foreach ($obligacionesPendientes as $obligacion) {
                $saldoPendiente = $obligacion->saldo;
                if ($saldoPendiente <= 0) continue;

                $matricula = $obligacion->matricula;
                $carrera   = $matricula?->carrera;
                if (! $carrera) continue;

                $semestres        = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;
                $nuevaColegiatura = round($carrera->costo_carrera / $semestres, 2);

                ObligacionesFinanciera::create([
                    'user_id'           => $obligacion->user_id,
                    'periodo_id'        => $cpSiguiente->periodo_id,
                    'matricula_id'      => $obligacion->matricula_id,
                    'tipo'              => 'COLEGIATURA',
                    'monto_original'    => $nuevaColegiatura + $saldoPendiente,
                    'descuento'         => 0,
                    'monto_final'       => $nuevaColegiatura + $saldoPendiente,
                    'estado'            => 'Pendiente',
                    'fecha_vencimiento' => $fechaLimitePago,
                    'descripcion'       => "Colegiatura {$codeSiguiente} + \${$saldoPendiente} pendiente de {$periodo->code}",
                ]);

                $obligacion->update(['estado' => 'Vencido']);
            }

            DB::table('carrera_periodo')
                ->where('carrera_id', $carreraId)
                ->where('periodo_id', $periodo->id)
                ->update(['is_current' => false]);

            DB::table('carrera_periodo')
                ->where('id', $cpSiguiente->id)
                ->update(['is_current' => true]);

            DB::commit();

            $carreraObj = Carrera::find($carreraId);
            $this->dispatch('toast', [
                'tipo'    => 'success',
                'mensaje' => "Periodo {$periodo->code} cerrado para {$carreraObj->name}. Saldos arrastrados al período {$codeSiguiente}.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Error al cerrar el periodo: ' . $e->getMessage()]);
        }
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $periodos = Periodo::with(['carreras'])
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                $q->where('code', 'like', $term)
                  ->orWhere('description', 'like', $term);
            })
            ->orderByDesc('fecha_inicio')
            ->paginate(10);

        return view('livewire.administration.periodos-index', compact('periodos'));
    }
}
