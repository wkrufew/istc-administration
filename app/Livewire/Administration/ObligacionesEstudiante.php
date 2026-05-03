<?php

namespace App\Livewire\Administration;

use Livewire\Attributes\Layout;
use App\Jobs\EnviarNotificacionPago;
use App\Models\User;
use App\Models\Pago;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Periodo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Traits\WithAuthorization;

class ObligacionesEstudiante extends Component
{
    use WithPagination, WithFileUploads, WithAuthorization;

    // -------------------------------------------------------------------------
    // FILTROS
    // -------------------------------------------------------------------------
    public $filtroEstudiante       = '';
    public $filtroTipo             = '';
    public $filtroEstado           = '';
    public $filtroMatricula        = null;

    // Buscador predictivo del filtro de estudiante
    public $filtroBusquedaTexto    = '';
    public $filtroEstudianteNombre = '';
    public $showDropdownFiltro     = false;

    // -------------------------------------------------------------------------
    // MODAL CREAR OBLIGACIÓN MANUAL (MULTA / OTROS)
    // -------------------------------------------------------------------------
    public $showModalObligacion    = false;
    public $busquedaEstudiante     = '';
    public $estudianteSeleccionado = null;
    public $nombreEstudiante       = '';
    public $showDropdown           = false;

    public $obligacionTipo        = 'MULTA';
    public $obligacionMonto       = '';
    public $obligacionDescripcion = '';
    public $obligacionVencimiento = '';

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
    public $showModalHistorial        = false;
    public $obligacionHistorial       = null; // ObligacionesFinanciera con pagos cargados

    // -------------------------------------------------------------------------
    // MODAL VERIFICAR PAGO (ADMIN)
    // -------------------------------------------------------------------------
    public $showModalVerificacion   = false;
    public $pagoSeleccionado        = null;
    public $observacionVerificacion = '';

    // -------------------------------------------------------------------------
    // MOUNT
    // -------------------------------------------------------------------------
    public function mount($matricula = null, $user = null)
    {
        $this->filtroMatricula = $matricula;
        if ($user) {
            $this->filtroEstudiante = $user;
            $estudiante = User::find($user);
            if ($estudiante) {
                $this->filtroEstudianteNombre = $estudiante->name . ' — ' . $estudiante->cedula;
                $this->filtroBusquedaTexto    = $this->filtroEstudianteNombre;
            }
        }
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================
    #[Computed]
    public function obligaciones()
    {
        return ObligacionesFinanciera::with([
            'estudiante',
            'periodo',
            'matricula.carrera',
            'pagos',
        ])
            ->when($this->filtroEstudiante, fn($q) => $q->where('user_id', $this->filtroEstudiante))
            ->when($this->filtroTipo,       fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroEstado,     fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroMatricula,  fn($q) => $q->where('matricula_id', $this->filtroMatricula))
            ->orderByRaw("FIELD(estado, 'Pendiente', 'Parcial', 'Vencido', 'Pagado')")
            ->orderBy('fecha_vencimiento')
            ->paginate(15);
    }

    #[Computed]
    public function estudiantes()
    {
        return User::role('estudiante')->orderBy('name')->get(['id', 'name', 'cedula']);
    }

    // Buscador predictivo del filtro de estudiante
    #[Computed]
    public function resultadosFiltro()
    {
        if (strlen($this->filtroBusquedaTexto) < 3) {
            return collect();
        }

        return User::role('estudiante')
            ->whereHas('matriculas')
            ->where(function ($q) {
                $q->where('name',    'like', '%' . $this->filtroBusquedaTexto . '%')
                  ->orWhere('cedula', 'like', '%' . $this->filtroBusquedaTexto . '%');
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'cedula']);
    }

    // Buscador predictivo del modal — activo con 3+ caracteres
    #[Computed]
    public function resultadosBusqueda()
    {
        if (strlen($this->busquedaEstudiante) < 3) {
            return collect();
        }

        return User::role('estudiante')
            ->whereHas('matriculas')
            ->where(function ($q) {
                $q->where('name',    'like', '%' . $this->busquedaEstudiante . '%')
                    ->orWhere('cedula', 'like', '%' . $this->busquedaEstudiante . '%');
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'cedula']);
    }

    // =========================================================================
    // MODAL CREAR OBLIGACIÓN MANUAL
    // =========================================================================
    public function abrirModalObligacion()
    {
        $this->resetObligacionForm();
        $this->obligacionVencimiento = now()->addDays(15)->format('Y-m-d');
        $this->showModalObligacion   = true;
        $this->dispatch('modal-opened');
    }

    public function cerrarModalObligacion()
    {
        $this->dispatch('modal-closed');
        $this->showModalObligacion = false;
        $this->resetObligacionForm();
    }

    public function seleccionarEstudiante($id, $nombre, $cedula)
    {
        $this->estudianteSeleccionado = $id;
        $this->nombreEstudiante       = $nombre . ' — ' . $cedula;
        $this->busquedaEstudiante     = $nombre . ' — ' . $cedula;
        $this->showDropdown           = false;
        unset($this->resultadosBusqueda);
    }

    public function updatedBusquedaEstudiante()
    {
        if ($this->busquedaEstudiante !== $this->nombreEstudiante) {
            $this->estudianteSeleccionado = null;
        }
        $this->showDropdown = strlen($this->busquedaEstudiante) >= 3;
        unset($this->resultadosBusqueda);
    }

    public function guardarObligacion()
    {
        if ($this->sinPermiso('gestionar_obligaciones_financieras')) return;

        $this->validate([
            'estudianteSeleccionado' => 'required|exists:users,id',
            'obligacionTipo'         => 'required|in:MULTA,OTROS',
            'obligacionMonto'        => 'required|numeric|min:0.01',
            'obligacionDescripcion'  => 'required|string|max:500',
            'obligacionVencimiento'  => 'required|date|after_or_equal:today',
        ], [
            'estudianteSeleccionado.required'      => 'Debe seleccionar un estudiante.',
            'obligacionMonto.required'             => 'El monto es requerido.',
            'obligacionMonto.min'                  => 'El monto debe ser mayor a 0.',
            'obligacionDescripcion.required'       => 'La descripción es requerida.',
            'obligacionVencimiento.required'       => 'La fecha de vencimiento es requerida.',
            'obligacionVencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser en el pasado.',
        ]);

        try {
            DB::beginTransaction();

            $periodo = Periodo::periodoActivoGlobal()
                ?? throw new \RuntimeException('No hay un período activo configurado.');

            ObligacionesFinanciera::create([
                'user_id'          => $this->estudianteSeleccionado,
                'periodo_id'       => $periodo->id,
                'matricula_id'     => null,
                'tipo'             => $this->obligacionTipo,
                'monto_original'   => $this->obligacionMonto,
                'descuento'        => 0,
                'monto_final'      => $this->obligacionMonto,
                'estado'           => 'Pendiente',
                'fecha_vencimiento' => $this->obligacionVencimiento,
                'descripcion'      => $this->obligacionDescripcion,
            ]);

            DB::commit();

            $this->cerrarModalObligacion();
            unset($this->obligaciones);
            $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Obligación registrada correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('obligacion_general', 'Error al crear la obligación: ' . $e->getMessage());
        }
    }

    private function resetObligacionForm()
    {
        $this->reset([
            'busquedaEstudiante',
            'estudianteSeleccionado',
            'nombreEstudiante',
            'showDropdown',
            'obligacionTipo',
            'obligacionMonto',
            'obligacionDescripcion',
            'obligacionVencimiento',
        ]);
        unset($this->resultadosBusqueda);
    }

    // =========================================================================
    // MODAL REGISTRAR PAGO
    // =========================================================================
    public function abrirModalPago($obligacionId)
    {
        $obligacion = ObligacionesFinanciera::with('pagos', 'estudiante')->find($obligacionId);

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
        $this->dispatch('modal-opened');
    }

    public function cerrarModalPago()
    {
        $this->dispatch('modal-closed');
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
        if ($this->sinPermiso('gestionar_obligaciones_financieras')) return;

        $this->validate([
            'montoPago'       => 'required|numeric|min:0.01|max:' . ($this->obligacionSeleccionada?->saldo ?? 0),
            'metodoPago'      => 'required|in:Efectivo,Tarjeta,Transferencia,Deposito,Payphone',
            'referencia'      => 'nullable|string|max:100',
            'comprobante'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'descripcionPago' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $comprobantePath = null;
            if ($this->comprobante) {
                /* $comprobantePath = $this->comprobante->store('comprobantes/obligaciones', 'public'); */
                $estudiante    = $this->obligacionSeleccionada->estudiante;
                $matricula     = $this->obligacionSeleccionada->matricula;

                $nombreLimpio  = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '_', $estudiante->name));

                $nombreArchivo = implode('_', [
                    $this->obligacionSeleccionada->tipo,  // MULTA, COLEGIATURA, etc
                    $matricula->code,                      // ISTC-0001
                    $nombreLimpio,                         // Juan_Perez
                    now()->format('Ymd_His'),              // 20260226_143022
                ]) . '.' . $this->comprobante->getClientOriginalExtension();

                $comprobantePath = $this->comprobante->storeAs(
                    'pagos/comprobantes',
                    $nombreArchivo,
                    'public'
                );
            }

            // Número de cuota automático
            $numeroCuota = Pago::where('obligacion_id', $this->obligacionSeleccionada->id)
                ->whereIn('estado', [Pago::ESTADO_APROBADO, Pago::ESTADO_PENDIENTE])
                ->count() + 1;

            $pago = Pago::create([
                'numero_comprobante' => 'TEMP',
                'codigo_referencia'  => $this->referencia ?: null,
                'obligacion_id'      => $this->obligacionSeleccionada->id,
                'monto'              => $this->montoPago,
                'metodo_pago'        => $this->metodoPago,
                'estado'             => Pago::ESTADO_APROBADO, // Secretaria aprueba directo
                'numero_cuota'       => $numeroCuota,
                'fecha_pago'         => now(),
                'descripcion'        => $this->descripcionPago
                    ?: 'Cuota ' . $numeroCuota . ' — ' . $this->obligacionSeleccionada->tipo,
                'comprobante_path'   => $comprobantePath,
            ]);
            $pago->update(['numero_comprobante' => $this->generarNumeroComprobante($pago->id)]);

            // Actualizar estado de la obligación (Parcial o Pagado)
            $this->obligacionSeleccionada->actualizarEstado();

            // Si era la obligación de matrícula y quedó pagada, habilitar la matrícula
            if ($this->obligacionSeleccionada->tipo === 'MATRICULA'
                && $this->obligacionSeleccionada->estado === 'Pagado'
                && $this->obligacionSeleccionada->matricula_id) {
                Matricula::find($this->obligacionSeleccionada->matricula_id)
                    ?->update(['estado' => 'Habilitada']);
            }

            DB::commit();

            $this->cerrarModalPago();
            unset($this->obligaciones);
            $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Pago registrado correctamente.']);

            try {
                EnviarNotificacionPago::dispatch($pago->id);
            } catch (\Throwable $e) {
                Log::warning('ObligacionesEstudiante: no se pudo despachar notificación de pago', [
                    'pago_id' => $pago->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('pago_general', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // MODAL HISTORIAL DE CUOTAS
    // =========================================================================
    public function abrirHistorial($obligacionId)
    {
        $this->obligacionHistorial = ObligacionesFinanciera::with([
            'estudiante',
            'periodo',
            'matricula',
            'pagos' => fn($q) => $q->orderByDesc('numero_cuota')->orderByDesc('fecha_pago'),
        ])->find($obligacionId);

        $this->showModalHistorial = true;
        $this->dispatch('modal-opened');
    }

    public function cerrarHistorial()
    {
        $this->dispatch('modal-closed');
        $this->showModalHistorial  = false;
        $this->obligacionHistorial = null;
    }

    // =========================================================================
    // MODAL VERIFICAR PAGO (pagos subidos por estudiante desde su portal)
    // =========================================================================
    public function abrirVerificacion($pagoId)
    {
        $this->pagoSeleccionado        = Pago::with('obligacion.estudiante')->find($pagoId);
        $this->observacionVerificacion = '';
        $this->showModalVerificacion   = true;
        $this->dispatch('modal-opened');
    }

    public function cerrarVerificacion()
    {
        $this->dispatch('modal-closed');
        $this->showModalVerificacion = false;
        $this->reset(['pagoSeleccionado', 'observacionVerificacion']);
    }

    public function aprobarPago()
    {
        if ($this->sinPermiso('gestionar_obligaciones_financieras')) return;
        if (! $this->pagoSeleccionado) return;

        try {
            DB::beginTransaction();

            $this->pagoSeleccionado->update([
                'estado'      => Pago::ESTADO_APROBADO,
                'descripcion' => ($this->pagoSeleccionado->descripcion ?? '')
                    . ($this->observacionVerificacion ? ' | Obs: ' . $this->observacionVerificacion : ''),
            ]);

            $this->pagoSeleccionado->obligacion->actualizarEstado();

            $obligacion = $this->pagoSeleccionado->obligacion;
            if ($obligacion->tipo === 'MATRICULA' && $obligacion->estado === 'Pagado') {
                Matricula::find($obligacion->matricula_id)?->update(['estado' => 'Habilitada']);
            }

            DB::commit();

            $pagoId = $this->pagoSeleccionado->id;

            $this->cerrarVerificacion();
            unset($this->obligaciones);
            $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Pago aprobado correctamente.']);

            try {
                EnviarNotificacionPago::dispatch($pagoId);
            } catch (\Throwable $e) {
                Log::warning('ObligacionesEstudiante: no se pudo despachar notificación de aprobación', [
                    'pago_id' => $pagoId,
                    'error'   => $e->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function rechazarPago()
    {
        if ($this->sinPermiso('gestionar_obligaciones_financieras')) return;

        $this->validate([
            'observacionVerificacion' => 'required|min:10',
        ], [
            'observacionVerificacion.required' => 'Debe ingresar el motivo del rechazo.',
            'observacionVerificacion.min'      => 'El motivo debe tener al menos 10 caracteres.',
        ]);

        if (! $this->pagoSeleccionado) return;

        try {
            DB::beginTransaction();

            $this->pagoSeleccionado->update([
                'estado'      => Pago::ESTADO_RECHAZADO,
                'descripcion' => ($this->pagoSeleccionado->descripcion ?? '')
                    . ' | Rechazado: ' . $this->observacionVerificacion,
            ]);

            $this->pagoSeleccionado->obligacion->actualizarEstado();

            DB::commit();

            $this->cerrarVerificacion();
            unset($this->obligaciones);
            $this->dispatch('toast', ['tipo' => 'warning', 'mensaje' => 'Pago rechazado.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function generarNumeroComprobante(int $pagoId): string
    {
        return 'ISTC-CP-' . $this->obligacionSeleccionada->tipo . '-' . now()->year . '-' . str_pad($pagoId, 5, '0', STR_PAD_LEFT);
    }

    public function seleccionarFiltroEstudiante($id, $nombre, $cedula)
    {
        $this->filtroEstudiante       = $id;
        $this->filtroEstudianteNombre = $nombre . ' — ' . $cedula;
        $this->filtroBusquedaTexto    = $nombre . ' — ' . $cedula;
        $this->showDropdownFiltro     = false;
        unset($this->resultadosFiltro);
        $this->resetPage();
    }

    public function updatedFiltroBusquedaTexto()
    {
        if ($this->filtroBusquedaTexto !== $this->filtroEstudianteNombre) {
            $this->filtroEstudiante = '';
        }
        $this->showDropdownFiltro = strlen($this->filtroBusquedaTexto) >= 3;
        unset($this->resultadosFiltro);
    }

    public function limpiarFiltroEstudiante()
    {
        $this->filtroEstudiante       = '';
        $this->filtroEstudianteNombre = '';
        $this->filtroBusquedaTexto    = '';
        $this->showDropdownFiltro     = false;
        unset($this->resultadosFiltro);
        $this->resetPage();
    }

    public function limpiarFiltroMatricula()
    {
        $this->filtroMatricula = null;
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.obligaciones-estudiante', [
            'obligaciones'      => $this->obligaciones,
            'estudiantes'       => $this->estudiantes,
            'resultadosBusqueda' => $this->resultadosBusqueda,
            'resultadosFiltro'  => $this->resultadosFiltro,
        ]);
    }
}
