<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarEmailBienvenidaAspirante;
use App\Jobs\NotificarEstadoAspirante;
use App\Jobs\NotificarObservacionDocumentoAspirante;
use App\Models\Aspirante;
use App\Models\Carrera;
use App\Models\Cohorte;
use App\Models\ConvenioAplicado;
use App\Models\TipoConvenio;
use App\Services\BeneficiosService;
use App\Services\CredencialesService;
use App\Services\PagoAdmisionService;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GestionAspirantes extends Component
{
    use WithAuthorization, WithPagination, WithFileUploads;

    // ── Filtros (sincronizados con la URL) ────────────────────────────────
    #[Url(as: 'q')]
    public string $buscar       = '';

    #[Url(as: 'cohorte')]
    public string $filtroCohorte = '';

    #[Url(as: 'estado')]
    public string $filtroEstado = '';

    #[Url(as: 'tipo')]
    public string $filtroTipoProceso = '';

    #[Url(as: 'devueltos')]
    public bool $filtroConDevueltos = false;

    // ── Modal detalle / acción ────────────────────────────────────────────
    public bool    $showDetalle    = false;
    public ?int    $aspiranteId    = null;
    public string  $motivoRechazo  = '';
    public bool    $showRechazar   = false;

    // ── Modal cambiar estado ──────────────────────────────────────────────
    public bool    $showCambiarEstado = false;
    public string  $nuevoEstado       = '';
    public string  $observacion       = '';

    // ── Indicadores ──────────────────────────────────────────────────────
    public bool   $showIndicadores   = false;

    // ── Reenviar credenciales ─────────────────────────────────────────────
    public bool    $showReenvio        = false;
    public ?int    $reenvioAspiranteId = null;
    public ?string $reenvioResultado   = null; // null (sin enviar) | 'ok' | 'error'
    public string  $reenvioMensaje     = '';
    public string  $reenvioAviso       = ''; // por qué se abrió solo (se editaron cédula o correo)

    // ── Editar datos básicos del aspirante ────────────────────────────────
    public bool   $showEditar        = false;
    public string $editFirstName     = '';
    public string $editLastName      = '';
    public string $editCedula        = '';
    public string $editEmail         = '';
    public string $editCarreraId     = '';
    public string $editCohorteId     = '';
    public string $editTipoConvenioId     = '';
    public string $editPorcentajeConvenio = '';

    // ── Registrar / corregir el pago de admisión ──────────────────────────
    public bool   $showPago    = false;
    public string $pagoMonto   = '';
    public string $pagoMetodo  = '';
    public string $pagoFecha   = '';
    public string $pagoMotivo  = '';
    public $pagoComprobante    = null;

    public function mount(): void
    {
        $this->requierePermiso('gestionar_aspirantes');
    }

    public function updatedBuscar(): void             { $this->resetPage(); }
    public function updatedFiltroCohorte(): void      { $this->resetPage(); }
    public function updatedFiltroEstado(): void       { $this->resetPage(); }
    public function updatedFiltroTipoProceso(): void  { $this->resetPage(); }
    public function updatedFiltroConDevueltos(): void { $this->resetPage(); }

    #[Computed]
    public function cohortes()
    {
        return Cohorte::orderByDesc('created_at')->get(['id', 'nombre', 'estado']);
    }

    #[Computed]
    public function carreras()
    {
        return Carrera::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function aspirantes()
    {
        return Aspirante::with(['user', 'carrera', 'cohorte', 'registradoPor'])
            ->when($this->buscar, function ($q) {
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$this->buscar}%")
                      ->orWhere('cedula', 'like', "%{$this->buscar}%")
                      ->orWhere('email', 'like', "%{$this->buscar}%")
                );
            })
            ->when($this->filtroCohorte, fn ($q) => $q->where('cohorte_id', $this->filtroCohorte))
            ->when($this->filtroEstado, fn ($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroTipoProceso, fn ($q) => $q->where('tipo_proceso', $this->filtroTipoProceso))
            ->when($this->filtroConDevueltos, fn ($q) => $q->where(fn ($inner) =>
                $inner->where('cedula_estado',          'rechazado')
                      ->orWhere('bachiller_estado',     'rechazado')
                      ->orWhere('habilitante_estado',   'rechazado')
                      ->orWhere('pago_estado',          'rechazado')
                      ->orWhere('hoja_vida_estado',     'rechazado')
                      ->orWhere('cert_laborales_estado','rechazado')
                      ->orWhere('cert_cursos_estado',   'rechazado')
            ))
            ->orderByDesc('created_at')
            ->paginate(25);
    }

    #[Computed]
    public function aspiranteSeleccionado(): ?Aspirante
    {
        if (! $this->aspiranteId) return null;
        return Aspirante::with(['user', 'carrera', 'cohorte', 'registradoPor'])->find($this->aspiranteId);
    }

    #[Computed]
    public function tiposConvenio()
    {
        return TipoConvenio::where('is_active', true)->orderBy('nombre')->get(['id', 'nombre', 'porcentaje_defecto', 'tipo_alcance']);
    }

    #[Computed]
    public function convenioAspirante(): ?ConvenioAplicado
    {
        $userId = $this->aspiranteSeleccionado?->user_id;
        return $userId ? $this->convenioActivoDe($userId) : null;
    }

    private function convenioActivoDe(int $userId): ?ConvenioAplicado
    {
        return ConvenioAplicado::where('user_id', $userId)
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
            ->with(['tipoConvenio:id,nombre,tipo_alcance', 'periodo:id,code'])
            ->latest('id')
            ->first();
    }

    /** Un aspirante rechazado o eliminado no conserva convenio activo (queda en su historial). */
    private function desactivarConvenios(int $userId, string $motivo): void
    {
        ConvenioAplicado::where('user_id', $userId)->where('is_active', true)
            ->get()
            ->each(fn($c) => BeneficiosService::desactivar($c, $motivo));
    }

    private function sincronizarConvenio(int $userId): void
    {
        $actual    = $this->convenioActivoDe($userId);
        $nuevoTipo = $this->editTipoConvenioId !== '' ? (int) $this->editTipoConvenioId : null;

        if (! $nuevoTipo) {
            if ($actual) $this->desactivarConvenios($userId, 'retirado_en_edicion');
            return;
        }

        if ($actual && (int) $actual->tipo_convenio_id === $nuevoTipo) {
            $actual->update(['porcentaje_aplicado' => $this->editPorcentajeConvenio]);
            return;
        }

        if ($actual) $this->desactivarConvenios($userId, 'reemplazo');

        ConvenioAplicado::create([
            'user_id'             => $userId,
            'tipo_convenio_id'    => $nuevoTipo,
            'porcentaje_aplicado' => $this->editPorcentajeConvenio,
            'motivo'              => 'Asignado desde la gestión de admisión',
            'fecha_inicio'        => now()->toDateString(),
            'fecha_fin'           => null,
            'is_active'           => true,
            'registrado_por'      => auth()->id(),
        ]);
    }

    public function updatedEditTipoConvenioId($value): void
    {
        $tipo = $value ? TipoConvenio::find($value) : null;
        $this->editPorcentajeConvenio = $tipo ? (string) (float) $tipo->porcentaje_defecto : '';
    }

    #[Computed]
    public function verificacionCount(): int
    {
        return Aspirante::where('estado', 'verificacion')->count();
    }

    #[Computed]
    public function indicadores(): array
    {
        $porEstado = Aspirante::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $total      = array_sum($porEstado);
        $aprobados  = $porEstado['aprobado']  ?? 0;
        $rechazados = $porEstado['rechazado'] ?? 0;
        $resueltos  = $aprobados + $rechazados;

        $porCarrera = Aspirante::with('carrera:id,name')
            ->select('carrera_id', 'estado', DB::raw('count(*) as total'))
            ->groupBy('carrera_id', 'estado')
            ->get()
            ->groupBy('carrera_id')
            ->map(fn ($rows) => [
                'nombre'       => $rows->first()->carrera?->name ?? 'Sin carrera asignada',
                'total'        => $rows->sum('total'),
                'pendiente'    => $rows->where('estado', 'pendiente')->sum('total'),
                'proceso'      => $rows->where('estado', 'proceso')->sum('total'),
                'verificacion' => $rows->where('estado', 'verificacion')->sum('total'),
                'aprobado'     => $rows->where('estado', 'aprobado')->sum('total'),
                'rechazado'    => $rows->where('estado', 'rechazado')->sum('total'),
            ])
            ->sortByDesc('total')
            ->values();

        $porTipo = Aspirante::select('tipo_proceso', DB::raw('count(*) as total'))
            ->groupBy('tipo_proceso')
            ->pluck('total', 'tipo_proceso')
            ->toArray();

        $cedula    = $total > 0 ? Aspirante::whereNotNull('cedula_path')->where('cedula_estado', 'aprobado')->count() : 0;
        $bachiller = $total > 0 ? Aspirante::where(fn ($q) => $q->whereNotNull('bachiller_path')->where('bachiller_estado', 'aprobado')
                                                                 ->orWhereNotNull('habilitante_path')->where('habilitante_estado', 'aprobado'))->count() : 0;
        $pago      = $total > 0 ? Aspirante::whereNotNull('pago_comprobante_path')->where('pago_estado', 'verificado')->count() : 0;

        return [
            'total'              => $total,
            'activos'            => ($porEstado['pendiente'] ?? 0) + ($porEstado['proceso'] ?? 0) + ($porEstado['verificacion'] ?? 0),
            'resueltos'          => $resueltos,
            'aprobados'          => $aprobados,
            'rechazados'         => $rechazados,
            'tasa_aprobacion'    => $resueltos > 0 ? round($aprobados / $resueltos * 100) : null,
            'por_carrera'        => $porCarrera,
            'regular'            => $porTipo['regular'] ?? 0,
            'validacion'         => $porTipo['validacion_conocimientos'] ?? 0,
            'docs_cedula_pct'    => $total > 0 ? round($cedula    / $total * 100) : 0,
            'docs_bachiller_pct' => $total > 0 ? round($bachiller / $total * 100) : 0,
            'docs_pago_pct'      => $total > 0 ? round($pago      / $total * 100) : 0,
            'ultimos_7_dias'     => Aspirante::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.gestion-aspirantes', [
            'aspirantes'        => $this->aspirantes,
            'cohortes'          => $this->cohortes,
            'carreras'          => $this->carreras,
            'aspiranteDetalle'  => $this->aspiranteSeleccionado,
            'verificacionCount' => $this->verificacionCount,
            'tiposConvenio'     => $this->tiposConvenio,
            'convenioAspirante' => $this->convenioAspirante,
        ]);
    }

    // ── Ver detalle ───────────────────────────────────────────────────────

    public function verDetalle(int $id): void
    {
        $this->aspiranteId   = $id;
        $this->showDetalle   = true;
        $this->showRechazar  = false;
        $this->showCambiarEstado = false;
        $this->cerrarPago();
        unset($this->aspiranteSeleccionado);
    }

    public function cerrarDetalle(): void
    {
        $this->showDetalle   = false;
        $this->aspiranteId   = null;
        $this->showRechazar  = false;
        $this->showCambiarEstado = false;
        $this->motivoRechazo = '';
        $this->observacion   = '';
        $this->nuevoEstado   = '';
        $this->cerrarPago();
        unset($this->aspiranteSeleccionado);
    }

    // ── Cambiar estado ─────────────────────────────────────────────────────

    public function abrirCambiarEstado(string $estado): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;
        $this->nuevoEstado   = $estado;
        $this->observacion   = '';
        $this->motivoRechazo = '';
        $this->showCambiarEstado = true;
        $this->showRechazar  = ($estado === 'rechazado');
    }

    public function confirmarCambioEstado(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        if (! array_key_exists($this->nuevoEstado, Aspirante::ESTADOS)) {
            return;
        }

        $aspirante = Aspirante::with('user')->findOrFail($this->aspiranteId);
        $estadoActual = $aspirante->estado;

        // Las mismas reglas que muestra la pantalla, verificadas aquí: un aprobado o matriculado
        // no vuelve atrás (pasar a 'proceso' le cambiaría la contraseña) y aprobar exige requisitos
        $rechazo = match (true) {
            ! $aspirante->puedeCambiarA($this->nuevoEstado) =>
                'No se puede pasar de ' . $aspirante->estadoLabel() . ' a ' . (Aspirante::ESTADOS[$this->nuevoEstado] ?? $this->nuevoEstado) . '.',
            $this->nuevoEstado === 'aprobado' && $aspirante->razonesNoPuedeAprobar() =>
                'Aún no se puede aprobar: ' . implode(' · ', $aspirante->razonesNoPuedeAprobar()) . '.',
            default => null,
        };
        if ($rechazo) {
            $this->showCambiarEstado = false;
            $this->showRechazar      = false;
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Cambio no permitido', 'text' => $rechazo]);
            return;
        }

        if ($this->nuevoEstado === 'rechazado') {
            $this->validate(['motivoRechazo' => 'required|string|max:500'],
                ['motivoRechazo.required' => 'Indica el motivo del rechazo.']);
            $aspirante->update([
                'estado'         => 'rechazado',
                'motivo_rechazo' => $this->motivoRechazo,
            ]);
            if ($estadoActual !== 'matriculado') {
                $this->desactivarConvenios($aspirante->user_id, 'aspirante_rechazado');
            }
        } else {
            $aspirante->update([
                'estado'               => $this->nuevoEstado,
                'observacion_general'  => $this->observacion ?: $aspirante->observacion_general,
            ]);
        }

        if ($this->nuevoEstado === 'proceso') {
            // Misma regla en todo el sistema: la contraseña inicial es la cédula
            $password = $aspirante->user->cedula ?: Str::random(10);
            $aspirante->user->update(['password' => Hash::make($password)]);
            // El "último envío" lo marca el propio trabajo cuando el correo sale de verdad
            EnviarEmailBienvenidaAspirante::dispatch($aspirante->user_id, $password, auth()->id());
        } elseif (in_array($this->nuevoEstado, ['aprobado', 'rechazado', 'matriculado'])) {
            NotificarEstadoAspirante::dispatch($aspirante->id, $this->nuevoEstado);
        }

        unset($this->aspirantes, $this->aspiranteSeleccionado);
        $this->showCambiarEstado = false;
        $this->showRechazar = false;
        $this->motivoRechazo = '';
        $this->observacion   = '';

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Estado actualizado',
            'text'  => 'Aspirante pasó de ' . (Aspirante::ESTADOS[$estadoActual] ?? $estadoActual) . ' a ' . (Aspirante::ESTADOS[$this->nuevoEstado] ?? $this->nuevoEstado) . '.',
            'timer' => 3000,
        ]);

        $this->nuevoEstado = '';
        // Refrescar detalle
        $this->aspiranteId = $aspirante->id;
        unset($this->aspiranteSeleccionado);
    }

    // ── Reenviar credenciales ─────────────────────────────────────────────

    #[Computed]
    public function aspiranteReenvio(): ?Aspirante
    {
        return $this->reenvioAspiranteId
            ? Aspirante::with(['user', 'carrera', 'credencialesEnviadasPor'])->find($this->reenvioAspiranteId)
            : null;
    }

    public function abrirReenvio(int $id): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $this->reenvioAspiranteId = $id;
        $this->reenvioResultado   = null;
        $this->reenvioMensaje     = '';
        $this->reenvioAviso       = '';
        $this->showReenvio        = true;
        unset($this->aspiranteReenvio);
    }

    public function cerrarReenvio(): void
    {
        $this->reset(['showReenvio', 'reenvioAspiranteId', 'reenvioResultado', 'reenvioMensaje', 'reenvioAviso']);
        unset($this->aspiranteReenvio, $this->aspirantes, $this->aspiranteSeleccionado);
    }

    /** Restablece la contraseña a la cédula y envía las credenciales en el momento (ver CredencialesService). */
    public function reenviarCredenciales(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $aspirante = Aspirante::with('user')->find($this->reenvioAspiranteId);
        if (! $aspirante) {
            $this->cerrarReenvio();
            return;
        }

        $user = $aspirante->user;

        try {
            CredencialesService::reenviarAspirante($aspirante);
        } catch (\DomainException | \RuntimeException $e) {
            $this->reenvioResultado = 'error';
            $this->reenvioMensaje   = $e->getMessage();
            unset($this->aspiranteReenvio);
            return;
        }

        $this->reenvioAviso     = '';
        $this->reenvioResultado = 'ok';
        $this->reenvioMensaje   = 'Credenciales enviadas a ' . $user->email . '. Su contraseña ahora es su número de cédula.';
        unset($this->aspiranteReenvio);
    }

    // ── Eliminar aspirante (soft delete) ─────────────────────────────────

    public function eliminar(int $id): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $aspirante = Aspirante::findOrFail($id);
        $aspirante->delete();
        if ($aspirante->estado !== 'matriculado') {
            $this->desactivarConvenios($aspirante->user_id, 'aspirante_eliminado');
        }

        unset($this->aspirantes);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Aspirante eliminado',
            'text'  => 'Movido a la papelera. Puedes restaurarlo desde allí.',
            'timer' => 3000,
        ]);
    }

    // ── Editar datos básicos del aspirante ────────────────────────────────

    public function abrirEditar(): void
    {
        $asp = $this->aspiranteSeleccionado;
        if (! $asp || $asp->estado === 'matriculado') return;

        $this->editFirstName  = $asp->user->first_name ?? '';
        $this->editLastName   = $asp->user->last_name  ?? '';
        $this->editCedula     = $asp->user->cedula     ?? '';
        $this->editEmail      = $asp->user->email      ?? '';
        $this->editCarreraId  = (string) ($asp->carrera_id ?? '');
        $this->editCohorteId  = (string) ($asp->cohorte_id ?? '');

        $convenio = $this->convenioActivoDe($asp->user_id);
        $this->editTipoConvenioId     = (string) ($convenio?->tipo_convenio_id ?? '');
        $this->editPorcentajeConvenio = $convenio ? (string) (float) $convenio->porcentaje_aplicado : '';

        $this->showEditar     = true;
        $this->resetErrorBag();
    }

    public function cerrarEditar(): void
    {
        $this->showEditar = false;
        $this->resetErrorBag();
    }

    public function guardarEdicion(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $asp = Aspirante::with('user')->findOrFail($this->aspiranteId);
        if ($asp->estado === 'matriculado') return;

        $userId = $asp->user_id;

        $this->validate([
            'editFirstName' => 'required|string|max:255',
            'editLastName'  => 'required|string|max:255',
            'editCedula'    => "required|string|max:20|unique:users,cedula,{$userId}",
            'editEmail'     => "required|email|max:255|unique:users,email,{$userId}",
            'editCohorteId' => 'required|exists:cohortes,id',
            'editCarreraId' => 'required|exists:carreras,id',
            'editTipoConvenioId'     => 'nullable|exists:tipos_convenio,id',
            'editPorcentajeConvenio' => 'nullable|required_with:editTipoConvenioId|numeric|min:0|max:100',
        ], [
            'editPorcentajeConvenio.required_with' => 'Indica el porcentaje de descuento del convenio.',
            'editFirstName.required' => 'El nombre es obligatorio.',
            'editLastName.required'  => 'El apellido es obligatorio.',
            'editCedula.unique'      => 'Esta cédula ya está en uso.',
            'editEmail.unique'       => 'Este correo ya está en uso.',
            'editCohorteId.required' => 'Selecciona una cohorte.',
            'editCarreraId.required' => 'Selecciona una carrera.',
        ]);

        $cambioCedula = $this->editCedula !== (string) $asp->user->cedula;
        $cambioEmail  = $this->editEmail  !== (string) $asp->user->email;

        $asp->user->update([
            'first_name' => $this->editFirstName,
            'last_name'  => $this->editLastName,
            'name'       => $this->editFirstName . ' ' . $this->editLastName,
            'cedula'     => $this->editCedula,
            'email'      => $this->editEmail,
        ]);

        $asp->update([
            'cohorte_id' => $this->editCohorteId,
            'carrera_id' => $this->editCarreraId,
        ]);

        $this->sincronizarConvenio($userId);

        unset($this->aspiranteSeleccionado, $this->aspirantes, $this->convenioAspirante);
        $this->showEditar = false;

        // Si cambió su cédula o su correo y ya tiene acceso al portal, sus credenciales quedaron
        // desactualizadas: se ofrece reenviarlas en el momento
        if (($cambioCedula || $cambioEmail) && in_array($asp->estado, Aspirante::ESTADOS_CON_ACCESO, true)) {
            $this->abrirReenvio($asp->id);
            $this->reenvioAviso = collect([
                $cambioCedula ? 'Cambiaste su cédula: si aún usa la contraseña inicial, esa sigue siendo la cédula anterior.' : null,
                $cambioEmail  ? 'Cambiaste su correo: ahora ingresa al portal con ' . $this->editEmail . ' y todavía no lo sabe.' : null,
            ])->filter()->implode(' ') . ' ¿Le reenvías las credenciales ahora?';
            return;
        }

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Datos actualizados',
            'timer' => 2500,
        ]);
    }

    // ── Pago de matrícula (admisión): verificar, registrar o corregir ─────

    /**
     * Abre el formulario del pago. El monto viene sugerido (lo que declaró el aspirante o el valor
     * de la matrícula); método y fecha van vacíos para que se tomen del comprobante. Al corregir
     * un pago ya confirmado se cargan los datos guardados.
     */
    public function abrirPago(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $aspirante = $this->aspiranteSeleccionado;
        if (! $aspirante) return;

        if ($motivo = PagoAdmisionService::motivoNoEditable($aspirante)) {
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'No se puede editar el pago', 'text' => $motivo]);
            return;
        }

        $monto = (float) $aspirante->pago_monto ?: $aspirante->valorMatriculaAdmision();
        $this->pagoMonto = $monto > 0 ? number_format($monto, 2, '.', '') : '';
        if ($aspirante->pagoConfirmado()) {
            $this->pagoMetodo = (string) $aspirante->pago_metodo;
            $this->pagoFecha  = ($aspirante->pago_fecha ?? $aspirante->pago_verificado_at)->format('Y-m-d');
        } else {
            $this->pagoMetodo = '';
            $this->pagoFecha  = '';
        }
        $this->pagoMotivo = '';
        $this->pagoComprobante = null;
        $this->resetErrorBag();
        $this->showPago = true;
    }

    public function cerrarPago(): void
    {
        $this->showPago = false;
        $this->reset(['pagoMonto', 'pagoMetodo', 'pagoFecha', 'pagoMotivo', 'pagoComprobante']);
        $this->resetErrorBag(['pagoMonto', 'pagoMetodo', 'pagoFecha', 'pagoMotivo', 'pagoComprobante']);
    }

    public function guardarPago(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $aspirante = $this->aspiranteSeleccionado;
        if (! $aspirante) return;

        $esCorreccion = $aspirante->pagoConfirmado();
        $this->validate([
            'pagoMonto'       => 'required|numeric|min:0.01|max:99999',
            'pagoMetodo'      => 'required|in:' . implode(',', array_keys(PagoAdmisionService::METODOS)),
            'pagoFecha'       => 'required|date|before_or_equal:today|after_or_equal:' . PagoAdmisionService::fechaMinima($aspirante),
            'pagoComprobante' => ($aspirante->pago_comprobante_path ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pagoMotivo'      => $esCorreccion ? 'required|string|min:5|max:500' : 'nullable|string|max:500',
        ], [
            'pagoMonto.required'       => 'Indica el monto que consta en el comprobante.',
            'pagoMonto.min'            => 'El monto debe ser mayor a cero.',
            'pagoMetodo.*'             => 'Selecciona el método de pago.',
            'pagoFecha.required'       => 'Indica la fecha del pago.',
            'pagoFecha.before_or_equal'=> 'La fecha del pago no puede ser futura.',
            'pagoFecha.after_or_equal' => 'Revisa la fecha del pago: no puede ser anterior al ' . \Illuminate\Support\Carbon::parse(PagoAdmisionService::fechaMinima($aspirante))->format('d/m/Y') . '.',
            'pagoComprobante.required' => 'Sube el comprobante del pago.',
            'pagoComprobante.mimes'    => 'Solo PDF, JPG o PNG.',
            'pagoComprobante.max'      => 'Máx. 5 MB.',
            'pagoMotivo.required'      => 'Indica el motivo de la corrección.',
            'pagoMotivo.min'           => 'Describe el motivo (mínimo 5 caracteres).',
        ]);

        try {
            PagoAdmisionService::registrar($aspirante, (float) $this->pagoMonto, $this->pagoMetodo, $this->pagoFecha,
                $this->pagoComprobante, $this->pagoMotivo);
        } catch (\DomainException $e) {
            $this->addError('pagoMonto', $e->getMessage());
            return;
        }

        $texto = '$' . number_format((float) $this->pagoMonto, 2) . ' · ' . (PagoAdmisionService::METODOS[$this->pagoMetodo] ?? $this->pagoMetodo);
        $this->cerrarPago();
        unset($this->aspiranteSeleccionado);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => $esCorreccion ? 'Pago corregido' : 'Pago verificado',
            'text'  => $texto . '. Se aplicará automáticamente al matricularlo.',
            'timer' => 3500,
        ]);
    }

    // ── Actualizar estado de documento ────────────────────────────────────

    public function actualizarDocumento(string $campo, string $nuevoEstado, string $observacion = ''): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $camposValidos = ['cedula', 'bachiller', 'habilitante', 'pago', 'hoja_vida', 'cert_laborales', 'cert_cursos'];
        if (! in_array($campo, $camposValidos, true)) return;

        $estadosPermitidos = $campo === 'pago' ? Aspirante::PAGO_ESTADOS : Aspirante::DOC_ESTADOS;
        if (! in_array($nuevoEstado, $estadosPermitidos, true)) return;

        // El pago se verifica con monto, método y fecha: abrirPago() / guardarPago()
        if ($campo === 'pago' && $nuevoEstado === 'verificado') return;

        $aspirante = Aspirante::findOrFail($this->aspiranteId);

        // Los documentos se revisan solo con el aspirante en Verificación (la pantalla ya lo limita; aquí lo exige el servidor)
        if ($aspirante->estado !== 'verificacion') {
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'No se puede cambiar el documento',
                'text' => 'Los documentos se revisan cuando el aspirante está en Verificación.']);
            return;
        }
        // Rechazar exige motivo (se le envía al aspirante por correo)
        if ($nuevoEstado === 'rechazado' && trim($observacion) === '') {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'Falta el motivo', 'text' => 'Indica el motivo del rechazo.']);
            return;
        }

        if ($campo === 'pago') {
            // El pago solo se rechaza aquí (verificar/corregir va por guardarPago); queda en auditoría
            if ($nuevoEstado !== 'rechazado') return;
            try {
                PagoAdmisionService::rechazar($aspirante, $observacion);
            } catch (\DomainException $e) {
                $this->dispatch('swal', ['icon' => 'info', 'title' => 'No se puede rechazar el pago', 'text' => $e->getMessage()]);
                return;
            }
        } else {
            $data = ["{$campo}_estado" => $nuevoEstado];
            if ($observacion !== '') {
                $data["{$campo}_observacion"] = trim($observacion);
            } elseif ($nuevoEstado !== 'rechazado') {
                // Limpiar la observación previa si se aprueba
                $data["{$campo}_observacion"] = null;
            }
            $aspirante->update($data);
        }

        // Enviar correo al estudiante cuando se rechaza con observación
        if ($nuevoEstado === 'rechazado' && trim($observacion) !== '') {
            $labelDocumentos = [
                'cedula'        => 'Cédula de identidad y papeleta de votación',
                'bachiller'     => 'Título de bachiller',
                'habilitante'   => 'Documento habilitante',
                'pago'          => 'Comprobante de pago de matrícula',
                'hoja_vida'     => 'Hoja de vida',
                'cert_laborales'=> 'Certificados laborales',
                'cert_cursos'   => 'Certificados de cursos o capacitaciones',
            ];
            NotificarObservacionDocumentoAspirante::dispatch(
                $aspirante->id,
                $labelDocumentos[$campo] ?? $campo,
                trim($observacion),
            );
        }

        unset($this->aspiranteSeleccionado);

        $icono = match($nuevoEstado) {
            'aprobado', 'verificado' => 'success',
            'rechazado'              => 'warning',
            default                  => 'success',
        };

        $this->dispatch('swal', [
            'icon'  => $icono,
            'title' => $nuevoEstado === 'rechazado' ? 'Documento rechazado' : 'Documento aprobado',
            'text'  => $nuevoEstado === 'rechazado' && trim($observacion) !== '' ? 'Se notificó al estudiante por correo.' : '',
            'timer' => 3000,
        ]);
    }
}
