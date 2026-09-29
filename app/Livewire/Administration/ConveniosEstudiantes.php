<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\ConvenioAplicado;
use App\Models\Periodo;
use App\Models\TipoConvenio;
use App\Models\User;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ConveniosEstudiantes extends Component
{
    use WithPagination, WithAuthorization, WithFileUploads;

    public string $search = '';

    // Modal asignar / editar
    public bool   $showModal     = false;
    public ?int   $convenioId    = null;
    public ?int   $estudianteId  = null;

    // Campos del formulario
    public ?int   $tipoConvenioId      = null;
    public string $porcentajeAplicado  = '';
    public string $motivo              = '';
    public        $documento           = null;
    public string $observacion         = '';
    public string $fechaInicio         = '';
    public string $fechaFin            = '';

    // Modal revocar
    public bool   $showRevocarModal   = false;
    public ?int   $convenioRevocarId  = null;
    public string $motivoRevocacion   = '';

    // Filtros e indicadores
    public string $filtroPeriodo   = '';
    public bool   $showIndicadores = false;
    public string $modoPeriodoInd  = 'actual';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_convenios');
        $this->fechaInicio = now()->toDateString();
    }

    public function updatedSearch(): void        { $this->resetPage(); }
    public function updatedFiltroPeriodo(): void { $this->resetPage(); }

    public function limpiarFiltros(): void
    {
        $this->filtroPeriodo = '';
        $this->search        = '';
        $this->resetPage();
    }

    #[Computed]
    public function periodos()
    {
        return Periodo::whereHas('matriculas', fn($q) => $q->where('estado', 'Habilitada'))
            ->orderByDesc('id')
            ->get(['id', 'code']);
    }

    #[Computed]
    public function periodoEfectivo(): ?Periodo
    {
        if ($this->filtroPeriodo !== '') {
            return Periodo::find((int) $this->filtroPeriodo);
        }
        return Periodo::periodoActivoGlobal();
    }

    #[Computed]
    public function statsResumen(): array
    {
        $periodo = $this->periodoEfectivo;
        if (! $periodo) {
            return ['matriculados' => 0, 'con_convenio' => 0, 'sin_convenio' => 0, 'cobertura' => 0];
        }

        $matriculados = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('matriculas', 'model_has_roles.model_id', '=', 'matriculas.user_id')
            ->where('roles.name', 'Estudiante')
            ->where('matriculas.estado', 'Habilitada')
            ->where('matriculas.periodo_id', $periodo->id)
            ->distinct('model_has_roles.model_id')
            ->count('model_has_roles.model_id');

        $conConvenio = DB::table('convenios_aplicados')
            ->join('matriculas', 'convenios_aplicados.user_id', '=', 'matriculas.user_id')
            ->where('convenios_aplicados.is_active', true)
            ->where(fn($q) => $q->whereNull('convenios_aplicados.fecha_fin')
                ->orWhere('convenios_aplicados.fecha_fin', '>=', now()->toDateString()))
            ->where('matriculas.estado', 'Habilitada')
            ->where('matriculas.periodo_id', $periodo->id)
            ->distinct('convenios_aplicados.user_id')
            ->count('convenios_aplicados.user_id');

        $cobertura = $matriculados > 0 ? round($conConvenio / $matriculados * 100, 1) : 0;

        return [
            'matriculados' => $matriculados,
            'con_convenio' => $conConvenio,
            'sin_convenio' => max(0, $matriculados - $conConvenio),
            'cobertura'    => $cobertura,
        ];
    }

    #[Computed]
    public function indicadores(): array
    {
        $periodo = $this->modoPeriodoInd === 'global' ? null : $this->periodoEfectivo;

        $totalMatriculados = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('matriculas', 'model_has_roles.model_id', '=', 'matriculas.user_id')
            ->where('roles.name', 'Estudiante')
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->distinct('model_has_roles.model_id')
            ->count('model_has_roles.model_id');

        $totalConConvenio = DB::table('convenios_aplicados')
            ->join('matriculas', 'convenios_aplicados.user_id', '=', 'matriculas.user_id')
            ->where('convenios_aplicados.is_active', true)
            ->where(fn($q) => $q->whereNull('convenios_aplicados.fecha_fin')
                ->orWhere('convenios_aplicados.fecha_fin', '>=', now()->toDateString()))
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->distinct('convenios_aplicados.user_id')
            ->count('convenios_aplicados.user_id');

        $cobertura = $totalMatriculados > 0 ? round($totalConConvenio / $totalMatriculados * 100, 1) : 0;

        $porTipo = DB::table('convenios_aplicados')
            ->join('tipos_convenio', 'convenios_aplicados.tipo_convenio_id', '=', 'tipos_convenio.id')
            ->join('matriculas', 'convenios_aplicados.user_id', '=', 'matriculas.user_id')
            ->where('convenios_aplicados.is_active', true)
            ->where(fn($q) => $q->whereNull('convenios_aplicados.fecha_fin')
                ->orWhere('convenios_aplicados.fecha_fin', '>=', now()->toDateString()))
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->select(
                'tipos_convenio.id',
                'tipos_convenio.nombre',
                'tipos_convenio.tipo_alcance',
                'tipos_convenio.porcentaje_defecto',
                DB::raw('COUNT(DISTINCT convenios_aplicados.user_id) as cantidad')
            )
            ->groupBy('tipos_convenio.id', 'tipos_convenio.nombre', 'tipos_convenio.tipo_alcance', 'tipos_convenio.porcentaje_defecto')
            ->orderByDesc('cantidad')
            ->get()
            ->map(function ($row) use ($totalConConvenio) {
                return [
                    'nombre'     => $row->nombre,
                    'alcance'    => $row->tipo_alcance,
                    'porcentaje' => $row->porcentaje_defecto,
                    'cantidad'   => $row->cantidad,
                    'pct_dist'   => $totalConConvenio > 0 ? round($row->cantidad / $totalConConvenio * 100, 1) : 0,
                ];
            })
            ->toArray();

        $porCarrera = DB::table('carreras')
            ->join('matriculas', 'carreras.id', '=', 'matriculas.carrera_id')
            ->join('model_has_roles', 'matriculas.user_id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'Estudiante')
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->select('carreras.id', 'carreras.name', DB::raw('COUNT(DISTINCT matriculas.user_id) as total_matriculados'))
            ->groupBy('carreras.id', 'carreras.name')
            ->orderByDesc('total_matriculados')
            ->get()
            ->map(function ($row) use ($periodo) {
                $conConvenioCarrera = DB::table('convenios_aplicados')
                    ->join('matriculas', 'convenios_aplicados.user_id', '=', 'matriculas.user_id')
                    ->where('convenios_aplicados.is_active', true)
                    ->where(fn($q) => $q->whereNull('convenios_aplicados.fecha_fin')
                        ->orWhere('convenios_aplicados.fecha_fin', '>=', now()->toDateString()))
                    ->where('matriculas.carrera_id', $row->id)
                    ->where('matriculas.estado', 'Habilitada')
                    ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
                    ->distinct('convenios_aplicados.user_id')
                    ->count('convenios_aplicados.user_id');

                $cobertura = $row->total_matriculados > 0
                    ? round($conConvenioCarrera / $row->total_matriculados * 100, 1)
                    : 0;

                return [
                    'nombre'       => $row->name,
                    'matriculados' => $row->total_matriculados,
                    'con_convenio' => $conConvenioCarrera,
                    'sin_convenio' => max(0, $row->total_matriculados - $conConvenioCarrera),
                    'cobertura'    => $cobertura,
                ];
            })
            ->toArray();

        $historico = DB::table('periodos')
            ->join('matriculas', 'periodos.id', '=', 'matriculas.periodo_id')
            ->where('matriculas.estado', 'Habilitada')
            ->select('periodos.id', 'periodos.code', DB::raw('COUNT(DISTINCT matriculas.user_id) as total_matriculados'))
            ->groupBy('periodos.id', 'periodos.code')
            ->orderByDesc('periodos.id')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $conConvenioPer = DB::table('convenios_aplicados')
                    ->join('matriculas', 'convenios_aplicados.user_id', '=', 'matriculas.user_id')
                    ->where('convenios_aplicados.is_active', true)
                    ->where('matriculas.periodo_id', $row->id)
                    ->where('matriculas.estado', 'Habilitada')
                    ->distinct('convenios_aplicados.user_id')
                    ->count('convenios_aplicados.user_id');

                $cobertura = $row->total_matriculados > 0
                    ? round($conConvenioPer / $row->total_matriculados * 100, 1)
                    : 0;

                return [
                    'periodo'      => $row->code,
                    'matriculados' => $row->total_matriculados,
                    'con_convenio' => $conConvenioPer,
                    'cobertura'    => $cobertura,
                ];
            })
            ->toArray();

        return [
            'total_matriculados' => $totalMatriculados,
            'con_convenio'       => $totalConConvenio,
            'sin_convenio'       => max(0, $totalMatriculados - $totalConConvenio),
            'cobertura'          => $cobertura,
            'periodo_code'       => $this->modoPeriodoInd === 'global' ? 'Todos los períodos' : ($periodo?->code ?? '—'),
            'es_global'          => $this->modoPeriodoInd === 'global',
            'por_tipo'           => $porTipo,
            'por_carrera'        => $porCarrera,
            'historico'          => $historico,
        ];
    }

    #[Computed]
    public function estudiantes()
    {
        $periodo = $this->periodoEfectivo;

        return User::role('Estudiante')
            ->where('estado_academico', 'Activo')
            ->when($periodo, fn($q) =>
                $q->whereHas('matriculas', fn($mq) =>
                    $mq->where('estado', 'Habilitada')->where('periodo_id', $periodo->id)
                )
            )
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('cedula', 'like', '%' . $this->search . '%')
                       ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->with([
                'conveniosAplicados' => fn($q) => $q->where('is_active', true)
                    ->where(fn($q2) => $q2->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
                    ->with('tipoConvenio'),
                'matriculas' => fn($q) => $periodo
                    ? $q->where('estado', 'Habilitada')->where('periodo_id', $periodo->id)->with('carrera:id,name')
                    : $q->where('estado', 'Habilitada')->with('carrera:id,name'),
            ])
            ->orderBy('name')
            ->paginate(20);
    }

    #[Computed]
    public function tiposConvenio()
    {
        return TipoConvenio::where('is_active', true)->orderBy('nombre')->get();
    }

    public function updatedTipoConvenioId(): void
    {
        $tipo = TipoConvenio::find($this->tipoConvenioId);
        if ($tipo) {
            $this->porcentajeAplicado = (string) $tipo->porcentaje_defecto;
            if ($tipo->tipo_alcance === 'semestral') {
                $this->fechaFin = '';
            }
        } else {
            $this->porcentajeAplicado = '';
        }
    }

    public function abrirModalAsignar(int $estudianteId): void
    {
        $this->reset(['convenioId', 'tipoConvenioId', 'porcentajeAplicado', 'motivo', 'documento', 'observacion', 'fechaFin', 'motivoRevocacion']);
        $this->estudianteId = $estudianteId;
        $this->fechaInicio  = now()->toDateString();
        $this->showModal    = true;
    }

    public function abrirModalEditar(int $convenioId): void
    {
        $convenio = ConvenioAplicado::with('tipoConvenio')->findOrFail($convenioId);
        $this->convenioId         = $convenio->id;
        $this->estudianteId       = $convenio->user_id;
        $this->tipoConvenioId     = $convenio->tipo_convenio_id;
        $this->porcentajeAplicado = (string) $convenio->porcentaje_aplicado;
        $this->motivo             = $convenio->motivo ?? '';
        $this->observacion        = $convenio->observacion ?? '';
        $this->fechaInicio        = $convenio->fecha_inicio->toDateString();
        $this->fechaFin           = $convenio->fecha_fin?->toDateString() ?? '';
        $this->showModal          = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['convenioId', 'estudianteId', 'tipoConvenioId', 'porcentajeAplicado', 'motivo', 'documento', 'observacion', 'fechaFin']);
        $this->fechaInicio = now()->toDateString();
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_convenios')) return;

        $this->validate([
            'estudianteId'        => 'required|exists:users,id',
            'tipoConvenioId'      => 'required|exists:tipos_convenio,id',
            'porcentajeAplicado'  => 'required|numeric|min:0|max:100',
            'motivo'              => 'nullable|string|max:500',
            'documento'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'observacion'         => 'nullable|string|max:500',
            'fechaInicio'         => 'required|date',
            'fechaFin'            => 'nullable|date|after_or_equal:fechaInicio',
        ]);

        if (!$this->convenioId) {
            $yaActivo = ConvenioAplicado::where('user_id', $this->estudianteId)
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
                ->exists();

            if ($yaActivo) {
                $this->addError('tipoConvenioId', 'Este estudiante ya tiene un convenio activo. Revoca el actual antes de asignar otro.');
                return;
            }
        }

        $documentoPath = null;
        if ($this->documento) {
            $documentoPath = $this->documento->store('convenios/documentos', 'public');
        }

        $data = [
            'user_id'             => $this->estudianteId,
            'tipo_convenio_id'    => $this->tipoConvenioId,
            'porcentaje_aplicado' => $this->porcentajeAplicado,
            'motivo'              => trim($this->motivo) ?: null,
            'observacion'         => trim($this->observacion) ?: null,
            'fecha_inicio'        => $this->fechaInicio,
            'fecha_fin'           => $this->fechaFin ?: null,
            'is_active'           => true,
            'registrado_por'      => Auth::id(),
        ];

        if ($documentoPath) {
            $data['documento_path'] = $documentoPath;
        }

        if ($this->convenioId) {
            ConvenioAplicado::findOrFail($this->convenioId)->update($data);
            $msg = 'Convenio actualizado correctamente.';
        } else {
            ConvenioAplicado::create($data);
            $msg = 'Convenio asignado correctamente.';
        }

        unset($this->estudiantes);
        $this->cerrarModal();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    public function abrirRevocar(int $convenioId): void
    {
        $this->convenioRevocarId = $convenioId;
        $this->motivoRevocacion  = '';
        $this->showRevocarModal  = true;
    }

    public function cerrarRevocar(): void
    {
        $this->showRevocarModal = false;
        $this->reset(['convenioRevocarId', 'motivoRevocacion']);
    }

    public function revocar(): void
    {
        if ($this->sinPermiso('gestionar_convenios')) return;

        $convenio = ConvenioAplicado::findOrFail($this->convenioRevocarId);
        $convenio->update([
            'is_active'  => false,
            'observacion' => $convenio->observacion
                ? $convenio->observacion . ' | REVOCADO: ' . trim($this->motivoRevocacion)
                : 'REVOCADO: ' . trim($this->motivoRevocacion),
        ]);

        unset($this->estudiantes);
        $this->cerrarRevocar();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => 'Convenio revocado.']);
    }

    public function render()
    {
        return view('livewire.administration.convenios-estudiantes', [
            'estudiantes'     => $this->estudiantes,
            'tiposConvenio'   => $this->tiposConvenio,
            'statsResumen'    => $this->statsResumen,
            'periodoEfectivo' => $this->periodoEfectivo,
            'periodos'        => $this->periodos,
        ]);
    }
}
