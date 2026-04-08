<?php

namespace App\Livewire\Administration;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class PagoMatricula extends Component
{
    use WithFileUploads;

    // -------------------------------------------------------------------------
    // PROPIEDADES PÚBLICAS
    // -------------------------------------------------------------------------
    public Matricula $matricula;
    public ObligacionesFinanciera $obligacion;

    public $metodoPago   = 'Transferencia';
    public $referencia   = '';
    public $comprobante  = null;
    public $descripcion  = '';

    // Solo lectura — se calculan al mount
    public $montoMatricula  = 0;
    public $montoArrastres  = 0;
    public $descuento        = 0;
    public $montoFinal       = 0;
    public $materiasArrastre = [];

    // -------------------------------------------------------------------------
    // MOUNT
    // -------------------------------------------------------------------------
    public function mount(Matricula $matricula)
    {
        $this->matricula = $matricula->load([
            'carrera',
            'periodo',
            'estudiante',
            'detalles.materia',
            'obligacionesFinancieras',
        ]);

        // Buscar la obligación de tipo MATRICULA asociada
        $this->obligacion = $this->matricula->obligacionesFinancieras()
            ->where('tipo', 'MATRICULA')
            ->firstOrFail();
        //dd($this->obligacion->tipo);
        // Si ya está pagada, redirigir a obligaciones
        if (in_array($this->obligacion->estado, ['Pagado', 'Parcial'])) {
            session()->flash('info', 'La obligación de matrícula ya tiene pagos registrados.');
            $this->redirectRoute('admin.obligaciones.index', ['matricula' => $this->matricula->id]);
            return;
        }

        $this->montoFinal    = $this->obligacion->monto_final;
        $this->descuento     = $this->obligacion->descuento;

        // Calcular desglose matrícula vs arrastres
        $carrera   = $this->matricula->carrera;
        $semestres = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;

        $this->montoMatricula  = round(($carrera->costo_carrera * 0.10) / $semestres, 2);

        $this->materiasArrastre = $this->matricula->detalles
            ->where('tipo', 'Arrastre')
            ->map(fn($d) => [
                'nombre'         => $d->materia->name,
                'creditos'       => $d->materia->credits,
                'costo_adicional' => $d->costo_materia,
            ])
            ->values()
            ->toArray();

        $this->montoArrastres = collect($this->materiasArrastre)->sum('costo_adicional');
    }

    // =========================================================================
    // GUARDAR PAGO
    // =========================================================================
    public function guardarPago()
    {
        $this->validate([
            'metodoPago'  => 'required|in:Efectivo,Tarjeta,Transferencia,Deposito,Payphone',
            'referencia'  => 'nullable|string|max:100',
            'comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $numeroCuota = Pago::where('obligacion_id', $this->obligacion->id)
            ->where('estado', Pago::ESTADO_APROBADO)
            ->count() + 1;

        try {
            DB::beginTransaction();

            $comprobantePath = null;
            if ($this->comprobante) {
                $nombreArchivo = 'comprobante_pago_' . $this->obligacion->tipo . '_' . $this->matricula->id . '_' . now()->format('Ymd_His') . '.' . $this->comprobante->getClientOriginalExtension();

                $comprobantePath = $this->comprobante->storeAs('pagos/comprobantes', $nombreArchivo, 'public');
                //$comprobantePath = $this->comprobante->store('pagos/comprobantes', 'public');
            }

            $numero = $this->generarNumeroComprobante();

            Pago::create([
                'numero_comprobante' => $numero,
                'codigo_referencia'  => $this->referencia ?: null,
                'obligacion_id'      => $this->obligacion->id,
                'monto'              => $this->montoFinal,
                'metodo_pago'        => $this->metodoPago,
                'estado'             => Pago::ESTADO_APROBADO, // Aprobado directo por secretaria
                'fecha_pago'         => now(),
                'descripcion'        => $this->descripcion ?: 'Pago de matrícula ' . $this->matricula->code,
                'numero_cuota' => $numeroCuota,
                'comprobante_path'   => $comprobantePath,
            ]);

            // Actualizar estado de la obligación a Parcial hasta que admin apruebe
            $this->obligacion->update(['estado' => 'Pagado']);

            // 2. Actualizar matrícula a Habilitada
            $this->matricula->update(['estado' => 'Habilitada']);

            DB::commit();

            session()->flash('success', 'Pago registrado correctamente. Pendiente de verificación por administración.');

            // Redirigir a la vista de obligaciones del estudiante
            return $this->redirect(
                route('administracion.administrativa.obligaciones.index', ['user' => $this->matricula->user_id]),
                navigate: true
            );
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // HELPER
    // =========================================================================
    private function generarNumeroComprobante(): string
    {
        $ultimo = Pago::max('id') + 1;
        return 'ISTC-CP-MATRICULA-' . now()->year . '-' . str_pad($ultimo, 5, '0', STR_PAD_LEFT);
    }

    // =========================================================================
    // RENDER
    // =========================================================================
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.pago-matricula');
    }
}
