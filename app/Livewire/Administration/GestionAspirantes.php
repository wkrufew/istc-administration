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
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class GestionAspirantes extends Component
{
    use WithAuthorization, WithPagination;

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
            ->with('tipoConvenio:id,nombre')
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
            $password = Str::random(10);
            $aspirante->user->update(['password' => $password]);
            EnviarEmailBienvenidaAspirante::dispatch($aspirante->user_id, $password);
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

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Datos actualizados',
            'timer' => 2500,
        ]);
    }

    // ── Verificar pago de matrícula (admisión) ────────────────────────────

    /**
     * El monto y método confirmados aquí son los que se aplican a la obligación
     * de matrícula cuando el aspirante se matricula.
     */
    public function verificarPagoAdmision($monto, $metodo): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $aspirante = Aspirante::findOrFail($this->aspiranteId);
        if ($aspirante->estado !== 'verificacion' || ! $aspirante->pago_comprobante_path) return;

        $validator = validator(
            ['monto' => $monto, 'metodo' => $metodo],
            [
                'monto'  => 'required|numeric|min:0.01|max:99999',
                'metodo' => 'required|in:Efectivo,Tarjeta,Transferencia,Deposito,Payphone',
            ],
            [
                'monto.required'  => 'Indica el monto verificado.',
                'monto.min'       => 'El monto debe ser mayor a cero.',
                'metodo.required' => 'Selecciona el método de pago.',
            ]
        );

        if ($validator->fails()) {
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Revisa los datos del pago',
                'text'  => $validator->errors()->first(),
            ]);
            return;
        }

        $aspirante->update([
            'pago_estado'        => 'verificado',
            'pago_monto'         => round((float) $monto, 2),
            'pago_metodo'        => $metodo,
            'pago_verificado_at' => now(),
            'pago_observacion'   => null,
        ]);

        unset($this->aspiranteSeleccionado);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Pago verificado',
            'text'  => '$' . number_format((float) $monto, 2) . ' · ' . $metodo . '. Se aplicará automáticamente al matricularlo.',
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

        // El pago se verifica con monto y método: verificarPagoAdmision()
        if ($campo === 'pago' && $nuevoEstado === 'verificado') return;

        $aspirante = Aspirante::findOrFail($this->aspiranteId);

        $data = ["{$campo}_estado" => $nuevoEstado];
        if ($observacion !== '') {
            $data["{$campo}_observacion"] = $observacion;
        } elseif ($nuevoEstado !== 'rechazado') {
            // Limpiar la observación previa si se aprueba/verifica
            $data["{$campo}_observacion"] = null;
        }
        $aspirante->update($data);

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
