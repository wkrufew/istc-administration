<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarNotificacionPago;
use App\Jobs\EnviarNotificacionPagoPrimeraMatricula;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Services\HabilitacionMatriculaService;
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
    public $montoReintegro   = 0;
    public $abonado          = 0; // pagos ya aprobados (p. ej. abono de admisión)

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

        // Conceptos que se cobran juntos para habilitar: matrícula, arrastres y reintegro
        $requeridas = $this->obligacionesRequeridas();
        $urlObligaciones = route('administracion.administrativa.obligaciones.index', ['estudiante' => $this->matricula->user_id]);

        if (Pago::whereIn('obligacion_id', $requeridas->pluck('id'))->where('estado', Pago::ESTADO_PENDIENTE)->exists()) {
            session()->flash('info', 'El estudiante subió un comprobante que está pendiente de verificación. Revísalo antes de registrar otro pago.');
            return redirect()->to($urlObligaciones);
        }

        if ($requeridas->every(fn($ob) => (float) $ob->saldo <= 0)) {
            session()->flash('info', 'La matrícula ya no tiene valores pendientes.');
            return redirect()->to($urlObligaciones);
        }

        $obligArrastre  = $requeridas->firstWhere('tipo', 'ARRASTRE');
        $obligReintegro = $requeridas->firstWhere('tipo', 'REINTEGRO');

        $this->descuento      = round($requeridas->sum(fn($ob) => (float) $ob->descuento), 2);
        $this->abonado        = round($requeridas->sum(fn($ob) => (float) $ob->total_pagado), 2);
        $this->montoReintegro = (float) ($obligReintegro?->monto_original ?? 0);
        $this->montoFinal     = round($requeridas->sum(fn($ob) => max(0, (float) $ob->saldo)), 2);

        $this->materiasArrastre = $this->matricula->detalles
            ->where('tipo', 'Arrastre')
            ->map(fn($d) => [
                'nombre'         => $d->materia->name,
                'creditos'       => $d->materia->credits,
                'costo_adicional' => $d->costo_materia,
            ])
            ->values()
            ->toArray();

        // Base tomada de lo facturado (ya contempla semestres exonerados y cursados).
        // Matrículas antiguas: los arrastres van sumados dentro de la obligación MATRICULA.
        if ($obligArrastre) {
            $this->montoArrastres = (float) $obligArrastre->monto_original;
            $this->montoMatricula = (float) $this->obligacion->monto_original;
        } else {
            $this->montoArrastres = collect($this->materiasArrastre)->sum('costo_adicional');
            $this->montoMatricula = max(0, round((float) $this->obligacion->monto_original - $this->montoArrastres, 2));
        }

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

        try {
            DB::beginTransaction();

            // Re-consultar dentro de la transacción: no confiar en montos calculados al cargar la página
            $pendientes = $this->obligacionesRequeridas()->filter(fn($ob) => (float) $ob->saldo > 0);
            if ($pendientes->isEmpty()) {
                DB::rollBack();
                $this->addError('general', 'La matrícula ya no tiene valores pendientes.');
                return;
            }

            $comprobantePath = null;
            if ($this->comprobante) {
                $nombreArchivo = 'comprobante_pago_' . $this->obligacion->tipo . '_' . $this->matricula->id . '_' . now()->format('Ymd_His') . '.' . $this->comprobante->getClientOriginalExtension();

                $comprobantePath = $this->comprobante->storeAs('pagos/comprobantes', $nombreArchivo, 'public');
                //$comprobantePath = $this->comprobante->store('pagos/comprobantes', 'public');
            }

            $conceptos = ['MATRICULA' => 'Pago de matrícula', 'ARRASTRE' => 'Pago de arrastres', 'REINTEGRO' => 'Pago de reintegro'];
            $pago = null;

            // Un pago por concepto, por su saldo: PagoObserver recalcula el estado de cada obligación
            foreach ($pendientes as $ob) {
                $numeroCuota = Pago::where('obligacion_id', $ob->id)
                    ->whereIn('estado', [Pago::ESTADO_APROBADO, Pago::ESTADO_PENDIENTE])
                    ->count() + 1;

                $pagoConcepto = Pago::create([
                    'numero_comprobante' => 'TEMP',
                    'codigo_referencia'  => $this->referencia ?: null,
                    'obligacion_id'      => $ob->id,
                    'monto'              => round((float) $ob->saldo, 2),
                    'metodo_pago'        => $this->metodoPago,
                    'estado'             => Pago::ESTADO_APROBADO, // Aprobado directo por secretaria
                    'fecha_pago'         => now(),
                    'descripcion'        => $this->descripcion ?: ($conceptos[$ob->tipo] ?? 'Pago') . ' ' . $this->matricula->code,
                    'numero_cuota'       => $numeroCuota,
                    'comprobante_path'   => $comprobantePath,
                ]);
                $pagoConcepto->update(['numero_comprobante' => $this->generarNumeroComprobante($pagoConcepto->id, $ob->tipo)]);

                // El aviso al estudiante se envía sobre el pago de matrícula (o el primero si ya estaba pagada)
                if (! $pago || $ob->tipo === 'MATRICULA') {
                    $pago = $pagoConcepto;
                }
            }

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
            }

            HabilitacionMatriculaService::verificar($this->matricula->fresh());

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
    private function generarNumeroComprobante(int $pagoId, string $tipo = 'MATRICULA'): string
    {
        return 'ISTC-CP-' . $tipo . '-' . now()->year . '-' . str_pad($pagoId, 5, '0', STR_PAD_LEFT);
    }

    private function obligacionesRequeridas()
    {
        return ObligacionesFinanciera::with('pagos')
            ->where('matricula_id', $this->matricula->id)
            ->whereIn('tipo', ObligacionesFinanciera::TIPOS_HABILITACION)
            ->where('estado', '!=', 'Invalidado')
            ->get();
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
