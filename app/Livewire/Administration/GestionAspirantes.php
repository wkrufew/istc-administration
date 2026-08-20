<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarEmailBienvenidaAspirante;
use App\Jobs\NotificarEstadoAspirante;
use App\Jobs\NotificarObservacionDocumentoAspirante;
use App\Models\Aspirante;
use App\Models\Carrera;
use App\Models\Cohorte;
use App\Models\User;
use App\Services\SettingService;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    // ── Modal detalle / acción ────────────────────────────────────────────
    public bool    $showDetalle    = false;
    public ?int    $aspiranteId    = null;
    public string  $motivoRechazo  = '';
    public bool    $showRechazar   = false;

    // ── Modal cambiar estado ──────────────────────────────────────────────
    public bool    $showCambiarEstado = false;
    public string  $nuevoEstado       = '';
    public string  $observacion       = '';

    // ── Modal registrar aspirante ─────────────────────────────────────────
    public bool   $showRegistrar    = false;
    public string $regFirstName     = '';
    public string $regLastName      = '';
    public string $regCedula        = '';
    public string $regEmail         = '';
    public string $regCohorteId     = '';
    public string $regCarreraId     = '';
    public string $regGenero        = '';
    public string $regEstadoCivil   = '';
    public string $regFechaNac      = '';
    public string $regNacionalidad  = '';
    public string $regPadre         = '';
    public string $regMadre         = '';

    // Modal cédula (igual que CreateUser)
    public bool   $apiActiva          = false;
    public bool   $showEntryModal     = false;
    public string $cedulaModalInput   = '';
    public array  $cedulaModalData    = [];

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

    public function mount(): void
    {
        $this->requierePermiso('gestionar_aspirantes');
        $this->apiActiva = ! empty(SettingService::get('cedula_api.token', ''));
    }

    public function updatedBuscar(): void       { $this->resetPage(); }
    public function updatedFiltroCohorte(): void { $this->resetPage(); }
    public function updatedFiltroEstado(): void  { $this->resetPage(); }

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

        unset($this->aspirantes);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Aspirante eliminado',
            'text'  => 'Movido a la papelera. Puedes restaurarlo desde allí.',
            'timer' => 3000,
        ]);
    }

    // ── Registrar aspirante ───────────────────────────────────────────────

    public function abrirRegistrar(): void
    {
        $this->showRegistrar    = true;
        $this->showEntryModal   = true;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
        $this->resetErrorBag();
    }

    public function cerrarRegistrar(): void
    {
        $this->showRegistrar   = false;
        $this->showEntryModal  = false;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
        $this->regFirstName     = '';
        $this->regLastName      = '';
        $this->regCedula        = '';
        $this->regEmail         = '';
        $this->regCohorteId     = '';
        $this->regCarreraId     = '';
        $this->regGenero        = '';
        $this->regEstadoCivil   = '';
        $this->regFechaNac      = '';
        $this->regNacionalidad  = '';
        $this->regPadre         = '';
        $this->regMadre         = '';
        $this->resetErrorBag();
    }

    public function consultarCedulaRegistro(): void
    {
        $this->resetErrorBag('cedulaModal');

        if (! trim($this->cedulaModalInput)) {
            $this->addError('cedulaModal', 'Ingrese el número de cédula o documento.');
            return;
        }

        $token = SettingService::get('cedula_api.token', '');
        $url   = SettingService::get('cedula_api.url', '');

        if (! $token || ! $url) {
            $this->addError('cedulaModal', 'API no configurada. Ve a Ajustes → API Cédula.');
            return;
        }

        try {
            $response = Http::withToken($token)
                ->timeout(10)
                ->get(rtrim($url, '/') . '/' . trim($this->cedulaModalInput));

            if ($response->successful() && ! empty($response->json('identificacion'))) {
                $this->cedulaModalData = $response->json();
            } else {
                $this->cedulaModalData = [];
                $this->addError('cedulaModal', 'Cédula no encontrada en el sistema.');
            }
        } catch (\Throwable $e) {
            Log::warning('consultarCedulaRegistro error', ['cedula' => $this->cedulaModalInput, 'error' => $e->getMessage()]);
            $this->addError('cedulaModal', 'No se pudo conectar con el servicio. Intente nuevamente.');
        }
    }

    public function aplicarCedulaRegistro(): void
    {
        if (empty($this->cedulaModalData)) return;

        $d = $this->cedulaModalData;

        $this->regCedula = trim($this->cedulaModalInput);

        $palabras = preg_split('/\s+/', trim($d['nombres'] ?? ''));
        $total    = count($palabras);
        if ($total >= 4) {
            $this->regLastName  = ucwords(strtolower($palabras[0] . ' ' . $palabras[1]));
            $this->regFirstName = ucwords(strtolower(implode(' ', array_slice($palabras, 2))));
        } elseif ($total === 3) {
            $this->regLastName  = ucwords(strtolower($palabras[0] . ' ' . $palabras[1]));
            $this->regFirstName = ucwords(strtolower($palabras[2]));
        } elseif ($total === 2) {
            $this->regLastName  = ucwords(strtolower($palabras[0]));
            $this->regFirstName = ucwords(strtolower($palabras[1]));
        } else {
            $this->regFirstName = ucwords(strtolower($d['nombres'] ?? ''));
        }

        $generoApi         = strtoupper($d['genero'] ?? $d['sexo'] ?? '');
        $this->regGenero   = match(true) {
            in_array($generoApi, ['HOMBRE', 'MASCULINO', 'M']) => 'Masculino',
            in_array($generoApi, ['MUJER', 'FEMENINO', 'F'])   => 'Femenino',
            default                                             => '',
        };

        $ec = strtoupper($d['estadoCivil'] ?? '');
        $this->regEstadoCivil = match(true) {
            str_contains($ec, 'SOLTERO') || str_contains($ec, 'SOLTERA') => 'Soltero/a',
            str_contains($ec, 'CASADO')  || str_contains($ec, 'CASADA')  => 'Casado/a',
            str_contains($ec, 'DIVOR')                                    => 'Divorciado/a',
            str_contains($ec, 'VIUDO')   || str_contains($ec, 'VIUDA')   => 'Viudo/a',
            str_contains($ec, 'UNION')   || str_contains($ec, 'LIBRE')   => 'Unión libre',
            default                                                        => '',
        };

        $this->regFechaNac    = ! empty($d['fechaNacimiento']) ? $d['fechaNacimiento'] : '';
        $this->regNacionalidad = ucfirst(strtolower($d['nacionalidad'] ?? ''));
        $this->regPadre        = ! empty($d['nombrePadre']) ? ucwords(strtolower($d['nombrePadre'])) : '';
        $this->regMadre        = ! empty($d['nombreMadre']) ? ucwords(strtolower($d['nombreMadre'])) : '';

        $this->showEntryModal  = false;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
    }

    public function omitirCedula(): void
    {
        $this->showEntryModal  = false;
        $this->cedulaModalInput = '';
        $this->cedulaModalData  = [];
    }

    public function guardarAspirante(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $this->validate([
            'regFirstName'  => 'required|string|max:255',
            'regLastName'   => 'required|string|max:255',
            'regCedula'     => 'required|string|max:20|unique:users,cedula',
            'regEmail'      => 'required|email|max:255|unique:users,email',
            'regCohorteId'  => 'required|exists:cohortes,id',
            'regCarreraId'  => 'required|exists:carreras,id',
        ], [
            'regFirstName.required'  => 'El nombre es obligatorio.',
            'regLastName.required'   => 'El apellido es obligatorio.',
            'regCedula.required'     => 'La cédula/documento es obligatorio.',
            'regCedula.unique'       => 'Esta cédula ya está registrada.',
            'regEmail.required'      => 'El correo es obligatorio.',
            'regEmail.unique'        => 'Este correo ya está registrado.',
            'regCohorteId.required'  => 'Selecciona una cohorte.',
            'regCarreraId.required'  => 'Selecciona una carrera.',
        ]);

        $user = User::create([
            'name'           => $this->regFirstName . ' ' . $this->regLastName,
            'first_name'     => $this->regFirstName,
            'last_name'      => $this->regLastName,
            'cedula'         => $this->regCedula,
            'email'          => $this->regEmail,
            'password'       => Hash::make(Str::random(16)),
            'genero'         => $this->regGenero ?: null,
            'estado_civil'   => $this->regEstadoCivil ?: null,
            'fecha_nacimiento'=> $this->regFechaNac ?: null,
            'nacionalidad'   => $this->regNacionalidad ?: null,
            'padre'          => $this->regPadre ?: null,
            'madre'          => $this->regMadre ?: null,
            'is_active'      => true,
        ]);

        $user->assignRole('Admision');

        Aspirante::create([
            'user_id'       => $user->id,
            'cohorte_id'    => $this->regCohorteId,
            'carrera_id'    => $this->regCarreraId,
            'estado'        => 'pendiente',
            'registrado_por'=> auth()->id(),
        ]);

        unset($this->aspirantes);
        $this->cerrarRegistrar();

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Aspirante registrado',
            'text'  => $this->regFirstName . ' ' . $this->regLastName . ' fue registrado como aspirante en estado Pendiente.',
            'timer' => 4000,
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
        ], [
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

        unset($this->aspiranteSeleccionado, $this->aspirantes);
        $this->showEditar = false;

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Datos actualizados',
            'timer' => 2500,
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
