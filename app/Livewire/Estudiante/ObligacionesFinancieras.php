<?php

namespace App\Livewire\Estudiante;

use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;

class ObligacionesFinancieras extends Component
{
    use WithPagination, WithFileUploads;

    public $idUser;
    // -------------------------------------------------------------------------
    // FILTROS
    // -------------------------------------------------------------------------
    public $filtroTipo    = '';
    public $filtroEstado  = '';
    public $filtroPeriodo = '';

    // -------------------------------------------------------------------------
    // MODAL REGISTRAR PAGO
    // -------------------------------------------------------------------------
    public $showModalPago          = false;
    public $obligacionSeleccionada = null;

    public $metodoPago      = 'Transferencia';
    public $referencia      = '';
    public $comprobante     = null;
    public $montoPago       = 0;
    public $descripcionPago = '';

    // -------------------------------------------------------------------------
    // MODAL HISTORIAL DE CUOTAS
    // -------------------------------------------------------------------------
    public $showModalHistorial  = false;
    public $obligacionHistorial = null;

    // -------------------------------------------------------------------------
    // MOUNT
    // -------------------------------------------------------------------------
    public function mount()
    {
        $this->idUser = Auth::id();
        $this->filtroPeriodo = Periodo::where('is_current', true)->first()?->id ?? '';
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================

    #[Computed]
    public function periodos()
    {
        return Periodo::whereHas(
            'matriculas',
            fn($q) =>
            $q->where('user_id', $this->idUser)
        )
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    #[Computed]
    public function obligaciones()
    {
        return ObligacionesFinanciera::with(['periodo', 'matricula.carrera', 'pagos'])
            ->where('user_id', $this->idUser)
            ->when($this->filtroTipo,    fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroEstado,  fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroPeriodo, fn($q) => $q->where('periodo_id', $this->filtroPeriodo))
            ->orderByRaw("FIELD(estado, 'Pendiente', 'Parcial', 'Vencido', 'Pagado')")
            ->orderBy('fecha_vencimiento')
            ->paginate(10);
    }

    // -------------------------------------------------------------------------
    // MÉTRICAS DEL TABLERO
    // -------------------------------------------------------------------------
    #[Computed]
    public function deudaPeriodoActual()
    {
        $periodoId = $this->filtroPeriodo
            ?: Periodo::where('is_current', true)->first()?->id;

        if (! $periodoId) return 0;

        return ObligacionesFinanciera::where('user_id', $this->idUser)
            ->where('periodo_id', $periodoId)
            ->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
            ->get()
            ->sum(fn($ob) => max(0, $ob->saldo));
    }

    #[Computed]
    public function totalPagadoPeriodo()
    {
        $periodoId = $this->filtroPeriodo
            ?: Periodo::where('is_current', true)->first()?->id;

        if (! $periodoId) return 0;

        return ObligacionesFinanciera::where('user_id', $this->idUser)
            ->where('periodo_id', $periodoId)
            ->get()
            ->sum(fn($ob) => $ob->total_pagado);
    }

    #[Computed]
    public function saldoCarrera()
    {
        $matricula = Matricula::with('carrera')
            ->where('user_id', $this->idUser)
            ->latest()
            ->first();

        if (! $matricula?->carrera) return 0;

        $totalPagadoHistorico = ObligacionesFinanciera::where('user_id', $this->idUser)
            ->get()
            ->sum(fn($ob) => $ob->total_pagado);

        return max(0, $matricula->carrera->costo_carrera - $totalPagadoHistorico);
    }

    // =========================================================================
    // MODAL REGISTRAR PAGO
    // =========================================================================
    public function abrirModalPago($obligacionId)
    {
        $obligacion = ObligacionesFinanciera::with('pagos')
            ->where('user_id', $this->idUser)
            ->find($obligacionId);

        if (! $obligacion || $obligacion->estado === 'Pagado') {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Esta obligación ya está pagada.']);
            return;
        }

        $this->obligacionSeleccionada = $obligacion;
        $this->montoPago              = $obligacion->saldo;
        $this->metodoPago             = 'Transferencia';
        $this->referencia             = '';
        $this->comprobante            = null;
        $this->descripcionPago        = '';
        $this->showModalPago          = true;

        //dd($this->obligacionSeleccionada->tipo);
    }

    public function cerrarModalPago()
    {
        $this->showModalPago = false;
        $this->reset([
            'obligacionSeleccionada',
            'metodoPago',
            'referencia',
            'comprobante',
            'montoPago',
            'descripcionPago',
        ]);
    }

    public function guardarPago()
    {
        $estudiante    = Auth::user();
        dd($estudiante);
        $this->validate([
            'montoPago'       => 'required|numeric|min:0.01|max:' . ($this->obligacionSeleccionada?->saldo ?? 0),
            'metodoPago'      => 'required|in:Efectivo,Tarjeta,Transferencia,Deposito,Payphone',
            'referencia'      => 'nullable|string|max:100',
            'comprobante'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'descripcionPago' => 'nullable|string|max:500',
        ], [
            'comprobante.required' => 'Debe adjuntar el comprobante de pago.',
            'montoPago.max'        => 'El monto no puede superar el saldo pendiente.',
        ]);

        try {
            $numeroCuota = Pago::where('obligacion_id', $this->obligacionSeleccionada->id)
                ->whereIn('estado', [Pago::ESTADO_APROBADO, Pago::ESTADO_PENDIENTE])
                ->count() + 1;


            $matricula     = $this->obligacionSeleccionada->matricula;
            $nombreLimpio  = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $estudiante->name));
            $identificador = $matricula?->code ?? $estudiante->cedula;

            $nombreArchivo = implode('_', [
                $this->obligacionSeleccionada->tipo,
                $identificador,
                $nombreLimpio,
                now()->format('Ymd_His'),
            ]) . '.' . $this->comprobante->getClientOriginalExtension();

            $comprobantePath = $this->comprobante->storeAs(
                'pagos/comprobantes',
                $nombreArchivo,
                'public'
            );

            Pago::create([
                'numero_comprobante' => $this->generarNumeroComprobante(),
                'codigo_referencia'  => $this->referencia ?: null,
                'obligacion_id'      => $this->obligacionSeleccionada->id,
                'monto'              => $this->montoPago,
                'metodo_pago'        => $this->metodoPago,
                'estado'             => Pago::ESTADO_PENDIENTE, // Secretaria verifica
                'numero_cuota'       => $numeroCuota,
                'fecha_pago'         => now(),
                'descripcion'        => $this->descripcionPago
                    ?: 'Cuota ' . $numeroCuota . ' — ' . $this->obligacionSeleccionada->tipo,
                'comprobante_path'   => $comprobantePath,
            ]);

            if ($this->obligacionSeleccionada->estado === 'Pendiente') {
                $this->obligacionSeleccionada->update(['estado' => 'Parcial']);
            }

            $this->cerrarModalPago();
            unset($this->obligaciones, $this->totalPagadoPeriodo, $this->deudaPeriodoActual);

            $this->dispatch('toast', [
                'tipo'    => 'success',
                'mensaje' => 'Pago enviado. La secretaría verificará tu comprobante en breve.',
            ]);
        } catch (\Exception $e) {
            $this->addError('pago_general', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // MODAL HISTORIAL DE CUOTAS
    // =========================================================================
    public function abrirHistorial($obligacionId)
    {
        $this->obligacionHistorial = ObligacionesFinanciera::with([
            'periodo',
            'matricula',
            'pagos' => fn($q) => $q->orderByDesc('numero_cuota')->orderByDesc('fecha_pago'),
        ])
            ->where('user_id', $this->idUser)
            ->find($obligacionId);

        $this->showModalHistorial = true;
    }

    public function cerrarHistorial()
    {
        $this->showModalHistorial  = false;
        $this->obligacionHistorial = null;
    }

    // =========================================================================
    // HELPER
    // =========================================================================
    private function generarNumeroComprobante(): string
    {
        $ultimo = Pago::max('id') + 1;
        return 'ISTC-CP-' . $this->obligacionSeleccionada->tipo . '-' . now()->year . '-' . str_pad($ultimo, 5, '0', STR_PAD_LEFT);
    }

    // =========================================================================
    // RENDER
    // =========================================================================
    public function render()
    {
        return view('livewire.estudiante.obligaciones-financieras', [
            'obligaciones'       => $this->obligaciones,
            'periodos'           => $this->periodos,
            'deudaPeriodoActual' => $this->deudaPeriodoActual,
            'totalPagadoPeriodo' => $this->totalPagadoPeriodo,
            'saldoCarrera'       => $this->saldoCarrera,
        ]);
    }
}
