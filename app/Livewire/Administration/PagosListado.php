<?php

namespace App\Livewire\Administration;

use App\Models\Matricula;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Pago;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\WithAuthorization;

class PagosListado extends Component
{
    use WithPagination, WithFileUploads, WithAuthorization;

    // Filtros
    public $busqueda = '';
    public $estado_filtro = 'Pendiente';
    public $por_pagina = 10;

    // Modal de pago
    public $mostrarModal = false;
    public $matriculaSeleccionada = null;

    // Campos del pago
    public $monto;
    public $numero_comprobante;
    public $concepto = 'MATRICULA';
    public $tipo_pago = 'Semestral';
    public $metodo_pago = 'Transferencia';
    public $numero_cuota;
    public $descripcion;
    public $comprobante;

    // Datos calculados
    public $saldo_pendiente = 0;
    public $total_pagado = 0;

    protected $rules = [
        'numero_comprobante' => 'required',
        'monto' => 'required|numeric|min:1',
        'concepto' => 'required|string',
        'tipo_pago' => 'required|string',
        'metodo_pago' => 'required|string',
        'numero_cuota' => 'nullable|integer|min:1',
        'descripcion' => 'nullable|string|max:500',
        'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:2048',
    ];

    public function updatedBusqueda()
    {
        $this->resetPage();
    }

    public function render()
    {
        $matriculas = Matricula::with(['estudiante', 'pagos'])
            ->where(function ($query) {
                $query->where('estado', 'Pendiente_Pago')
                    ->orWhere('estado', 'Pagada');
            })
            ->when($this->busqueda, function ($query) {
                $query->whereHas('estudiante', function ($q) {
                    $q->where('name', 'like', '%' . $this->busqueda . '%')
                        ->orWhere('email', 'like', '%' . $this->busqueda . '%');
                })->orWhere('code', 'like', '%' . $this->busqueda . '%');
            })
            ->paginate($this->por_pagina);

        return view('livewire.administration.pagos-listado', [
            'matriculas' => $matriculas
        ]);
    }

    public function abrirModalPago($matricula_id)
    {
        $this->reset(['monto', 'concepto', 'tipo_pago', 'metodo_pago', 'numero_cuota', 'descripcion', 'comprobante']);
        $this->matriculaSeleccionada = Matricula::with(['estudiante', 'pagos'])->findOrFail($matricula_id);

        $this->total_pagado = $this->matriculaSeleccionada->pagos()->where('estado', Pago::ESTADO_APROBADO)->sum('monto');
        $this->saldo_pendiente = $this->matriculaSeleccionada->total_pagar - $this->total_pagado;

        $this->mostrarModal = true;
    }

    public function guardarPago()
    {
        if ($this->sinPermiso('gestionar_pagos')) return;

        $this->validate();

        //dd($this->validate());

        if (!$this->matriculaSeleccionada) {
            $this->dispatch('alerta', ['type' => 'error', 'message' => 'No se ha seleccionado ninguna matrícula.']);
            return;
        }

        // Calcular saldo pendiente
        $saldo = $this->matriculaSeleccionada->total_pagar -
            $this->matriculaSeleccionada->pagos()->where('estado', Pago::ESTADO_APROBADO)->sum('monto');

        if ($this->monto > $saldo) {
            $this->addError('monto', 'El monto supera el saldo pendiente.');
            return;
        }

        DB::beginTransaction();

        try {
            $rutaComprobante = null;

            // 📸 Subida del comprobante
            if ($this->comprobante) {

                $nombreArchivo = 'comprobante_pago_' . $this->matriculaSeleccionada->id . '_' . now()->format('Ymd_His') . '.' . $this->comprobante->getClientOriginalExtension();

                $rutaComprobante = $this->comprobante->storeAs('pagos/comprobantes', $nombreArchivo, 'public');
            }

            // 💾 Crear registro del pago
            $pago = Pago::create([
                'numero_comprobante' => $this->numero_comprobante,
                'monto' => $this->monto,
                'concepto' => $this->concepto,
                'tipo_pago' => $this->tipo_pago,
                'metodo_pago' => $this->metodo_pago,
                'numero_cuota' => $this->numero_cuota,
                'descripcion' => $this->descripcion,
                'comprobante_path' => $rutaComprobante,
                'estado' => Pago::ESTADO_APROBADO, // directo aprobado (puedes cambiar a Pendiente)
                'fecha_pago' => now(),
                'fecha_vencimiento' => $this->matriculaSeleccionada->periodo?->fecha_fin ?? now()->endOfMonth(),
                'matricula_id' => $this->matriculaSeleccionada->id,
                'user_id' => $this->matriculaSeleccionada->user_id,
            ]);

            // 🧮 Recalcular el saldo y actualizar estado de matrícula
            $totalPagado = $this->matriculaSeleccionada->pagos()->where('estado', Pago::ESTADO_APROBADO)->sum('monto');
            $saldoPendiente = $this->matriculaSeleccionada->total_pagar - $totalPagado;

            $nuevoEstado = $saldoPendiente <= 0 ? 'Pagada' : 'Pendiente_Pago';
            $this->matriculaSeleccionada->update(['status' => $nuevoEstado]);

            DB::commit();

            $this->mostrarModal = false;

            $this->reset(['monto', 'concepto', 'tipo_pago', 'metodo_pago', 'numero_comprobante', 'numero_cuota', 'descripcion', 'comprobante']);
            $this->dispatch('alerta', ['type' => 'success', 'message' => 'Pago registrado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();

            // Eliminar archivo subido si algo falla
            if (isset($rutaComprobante) && Storage::disk('public')->exists($rutaComprobante)) {
                Storage::disk('public')->delete($rutaComprobante);
            }
            //dd($e->getMessage());
            $this->dispatch('alerta', ['type' => 'error', 'message' => 'Error al guardar el pago: ' . $e->getMessage()]);
        }
    }
    /* public function render()
    {
        return view('livewire.administration.pagos-listado');
    } */
}
