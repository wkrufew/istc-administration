<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarNotificacionPago;
use App\Jobs\EnviarNotificacionPagoPrimeraMatricula;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class PagoMatricula extends Component
{
    use WithFileUploads, WithAuthorization;

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
    public $montoMatricula   = 0;
    public $montoArrastres   = 0;
    public $descuento        = 0;
    public $montoFinal       = 0;
    public $materiasArrastre = [];
    public $montoInscripcion = 0;
    public $obligInscripcion = null;

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
            return redirect()->route('administracion.administrativa.obligaciones.index');

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

        // Obligación de inscripción — si existe, se auto-liquidará al pagar la matrícula
        $this->obligInscripcion = $this->matricula->obligacionesFinancieras()
            ->where('tipo', 'INSCRIPCION')
            ->whereIn('estado', ['Pendiente', 'Parcial'])
            ->first();
        $this->montoInscripcion = $this->obligInscripcion?->monto_final ?? 0;
    }

    // =========================================================================
    // GUARDAR PAGO
    // =========================================================================
    public function guardarPago()
    {
        if ($this->sinPermiso('gestionar_matriculas')) return;

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

            $pago = Pago::create([
                'numero_comprobante' => 'TEMP',
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

            $pago->update(['numero_comprobante' => $this->generarNumeroComprobante($pago->id)]);

            $this->obligacion->update(['estado' => 'Pagado']);

            // Auto-liquidar INSCRIPCION
            if ($this->obligInscripcion) {
                $pagoInscripcion = Pago::create([
                    'numero_comprobante' => 'TEMP',
                    'codigo_referencia'  => $this->referencia ?: null,
                    'obligacion_id'      => $this->obligInscripcion->id,
                    'monto'              => $this->obligInscripcion->monto_final,
                    'metodo_pago'        => $this->metodoPago,
                    'estado'             => Pago::ESTADO_APROBADO,
                    'fecha_pago'         => now(),
                    'numero_cuota'       => 1,
                    'comprobante_path'   => $comprobantePath,
                    'descripcion'        => 'Inscripción liquidada con pago de matrícula ' . $this->matricula->code,
                ]);
                $pagoInscripcion->update([
                    'numero_comprobante' => 'ISTC-CP-INSCRIPCION-' . now()->year . '-' . str_pad($pagoInscripcion->id, 5, '0', STR_PAD_LEFT),
                ]);
                $this->obligInscripcion->update(['estado' => 'Pagado']);
            }

            $this->matricula->update(['estado' => 'Habilitada']);

            DB::commit();

            try {
                if (isset($pagoInscripcion)) {
                    EnviarNotificacionPagoPrimeraMatricula::dispatch($pago->id, $pagoInscripcion->id);
                } else {
                    EnviarNotificacionPago::dispatch($pago->id);
                }
            } catch (\Throwable $e) {
                Log::warning('PagoMatricula: no se pudo despachar notificación', [
                    'pago_id' => $pago->id,
                    'error'   => $e->getMessage(),
                ]);
            }

            session()->flash('success', 'Pago de matrícula registrado correctamente.');

            return redirect()->route('administracion.administrativa.obligaciones.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // HELPER
    // =========================================================================
    private function generarNumeroComprobante(int $pagoId): string
    {
        return 'ISTC-CP-MATRICULA-' . now()->year . '-' . str_pad($pagoId, 5, '0', STR_PAD_LEFT);
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
