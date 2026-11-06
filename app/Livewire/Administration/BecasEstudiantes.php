<?php

namespace App\Livewire\Administration;

use App\Jobs\EnviarNotificacionBecaAsignada;
use App\Jobs\EnviarNotificacionBecaRevocada;
use App\Models\BecaAplicada;
use App\Models\Carrera;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\TipoBeca;
use App\Models\User;
use App\Services\BeneficiosService;
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
class BecasEstudiantes extends Component
{
    use WithPagination, WithAuthorization, WithFileUploads;

    public string $search = '';

    // Modal asignar
    public bool   $showModal    = false;
    public ?int   $becaId       = null;
    public ?int   $estudianteId = null;

    // Campos del formulario
    public ?int   $tipoBecaId             = null;
    public string $porcentajeAplicado     = '';
    public ?int   $porcentajeDiscapacidad = null;
    public        $documento              = null;
    public string $observacion            = '';
    public string $fechaAsignacion        = '';

    // Modal revocar
    public bool   $showRevocarModal    = false;
    public bool   $confirmarRevocacion = false;
    public ?int   $becaRevocarId       = null;
    public string $motivo              = '';

    // Modal indicadores
    public bool   $showIndicadores  = false;
    public string $modoPeriodoInd   = 'actual';

    // Filtros de la barra
    public string $filtroPeriodo = '';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_becas');
        $this->fechaAsignacion = now()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroPeriodo(): void
    {
        $this->resetPage();
    }

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
                'becasAplicadas' => fn($q) => $q->where('is_active', true)->with('tipoBeca'),
                'matriculas'     => fn($q) => $periodo
                    ? $q->where('estado', 'Habilitada')->where('periodo_id', $periodo->id)->with('carrera:id,name')
                    : $q->where('estado', 'Habilitada')->with('carrera:id,name'),
            ])
            ->orderBy('name')
            ->paginate(20);
    }

    #[Computed]
    public function statsResumen(): array
    {
        $periodo = $this->periodoEfectivo;
        if (! $periodo) {
            return ['matriculados' => 0, 'con_beca' => 0, 'sin_beca' => 0, 'cobertura' => 0, 'descuento_total' => 0];
        }

        $matriculados = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('matriculas', 'model_has_roles.model_id', '=', 'matriculas.user_id')
            ->where('roles.name', 'Estudiante')
            ->where('matriculas.estado', 'Habilitada')
            ->where('matriculas.periodo_id', $periodo->id)
            ->distinct('model_has_roles.model_id')
            ->count('model_has_roles.model_id');

        $conBeca = DB::table('becas_aplicadas')
            ->join('matriculas', 'becas_aplicadas.user_id', '=', 'matriculas.user_id')
            ->where('becas_aplicadas.is_active', true)
            ->where('matriculas.estado', 'Habilitada')
            ->where('matriculas.periodo_id', $periodo->id)
            ->distinct('becas_aplicadas.user_id')
            ->count('becas_aplicadas.user_id');

        $descuentoTotal = BeneficiosService::ahorro('beca', fn($q) => $q->where('o.periodo_id', $periodo->id));

        $cobertura = $matriculados > 0 ? round($conBeca / $matriculados * 100, 1) : 0;

        return [
            'matriculados'   => $matriculados,
            'con_beca'       => $conBeca,
            'sin_beca'       => max(0, $matriculados - $conBeca),
            'cobertura'      => $cobertura,
            'descuento_total' => (float) $descuentoTotal,
        ];
    }

    #[Computed]
    public function indicadores(): array
    {
        $periodo = $this->modoPeriodoInd === 'global' ? null : $this->periodoEfectivo;

        // ── Totales globales ──────────────────────────────────────────────
        $totalMatriculados = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('matriculas', 'model_has_roles.model_id', '=', 'matriculas.user_id')
            ->where('roles.name', 'Estudiante')
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->distinct('model_has_roles.model_id')
            ->count('model_has_roles.model_id');

        $totalConBeca = DB::table('becas_aplicadas')
            ->join('matriculas', 'becas_aplicadas.user_id', '=', 'matriculas.user_id')
            ->where('becas_aplicadas.is_active', true)
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->distinct('becas_aplicadas.user_id')
            ->count('becas_aplicadas.user_id');

        $descuentoTotal = BeneficiosService::ahorro('beca', fn($q) => $q->when($periodo, fn($q2) => $q2->where('o.periodo_id', $periodo->id)));

        $montoOriginalTotal = ObligacionesFinanciera::where('tipo', 'COLEGIATURA')
            ->whereNotNull('beca_aplicada_id')
            ->where('estado', '!=', 'Invalidado')
            ->when($periodo, fn($q) => $q->where('periodo_id', $periodo->id))
            ->sum('monto_original');

        $cobertura = $totalMatriculados > 0 ? round($totalConBeca / $totalMatriculados * 100, 1) : 0;
        $ahorroProm = $totalConBeca > 0 ? round($descuentoTotal / $totalConBeca, 2) : 0;

        // ── Por tipo de beca ───────────────────────────────────────────────
        $porTipo = DB::table('becas_aplicadas')
            ->join('tipos_beca', 'becas_aplicadas.tipo_beca_id', '=', 'tipos_beca.id')
            ->join('matriculas', 'becas_aplicadas.user_id', '=', 'matriculas.user_id')
            ->where('becas_aplicadas.is_active', true)
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->select(
                'tipos_beca.id',
                'tipos_beca.nombre',
                'tipos_beca.categoria',
                'tipos_beca.porcentaje_descuento',
                DB::raw('COUNT(DISTINCT becas_aplicadas.user_id) as cantidad')
            )
            ->groupBy('tipos_beca.id', 'tipos_beca.nombre', 'tipos_beca.categoria', 'tipos_beca.porcentaje_descuento')
            ->orderByDesc('cantidad')
            ->get()
            ->map(function ($row) use ($descuentoTotal, $totalConBeca) {
                $pct = $totalConBeca > 0 ? round($row->cantidad / $totalConBeca * 100, 1) : 0;
                return [
                    'nombre'    => $row->nombre,
                    'categoria' => $row->categoria,
                    'porcentaje' => $row->porcentaje_descuento,
                    'cantidad'  => $row->cantidad,
                    'pct_dist'  => $pct,
                ];
            })
            ->toArray();

        // ── Por carrera (período actual) ───────────────────────────────────
        $porCarrera = DB::table('carreras')
            ->join('matriculas', 'carreras.id', '=', 'matriculas.carrera_id')
            ->join('model_has_roles', 'matriculas.user_id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'Estudiante')
            ->where('matriculas.estado', 'Habilitada')
            ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
            ->select(
                'carreras.id',
                'carreras.name',
                DB::raw('COUNT(DISTINCT matriculas.user_id) as total_matriculados')
            )
            ->groupBy('carreras.id', 'carreras.name')
            ->orderByDesc('total_matriculados')
            ->get()
            ->map(function ($row) use ($periodo) {
                $conBecaCarrera = DB::table('becas_aplicadas')
                    ->join('matriculas', 'becas_aplicadas.user_id', '=', 'matriculas.user_id')
                    ->where('becas_aplicadas.is_active', true)
                    ->where('matriculas.carrera_id', $row->id)
                    ->where('matriculas.estado', 'Habilitada')
                    ->when($periodo, fn($q) => $q->where('matriculas.periodo_id', $periodo->id))
                    ->distinct('becas_aplicadas.user_id')
                    ->count('becas_aplicadas.user_id');

                $descCarrera = BeneficiosService::ahorro('beca', fn($q) => $q
                    ->when($periodo, fn($q2) => $q2->where('o.periodo_id', $periodo->id))
                    ->whereIn('o.user_id', function ($sq) use ($row, $periodo) {
                        $sq->select('user_id')->from('matriculas')
                           ->where('carrera_id', $row->id)
                           ->where('estado', 'Habilitada')
                           ->when($periodo, fn($q2) => $q2->where('periodo_id', $periodo->id));
                    }));

                $cobertura = $row->total_matriculados > 0
                    ? round($conBecaCarrera / $row->total_matriculados * 100, 1)
                    : 0;

                return [
                    'nombre'         => $row->name,
                    'matriculados'   => $row->total_matriculados,
                    'con_beca'       => $conBecaCarrera,
                    'sin_beca'       => max(0, $row->total_matriculados - $conBecaCarrera),
                    'cobertura'      => $cobertura,
                    'descuento'      => (float) $descCarrera,
                ];
            })
            ->toArray();

        // ── Histórico por período ─────────────────────────────────────────
        $historico = DB::table('periodos')
            ->join('matriculas', 'periodos.id', '=', 'matriculas.periodo_id')
            ->where('matriculas.estado', 'Habilitada')
            ->select(
                'periodos.id',
                'periodos.code',
                DB::raw('COUNT(DISTINCT matriculas.user_id) as total_matriculados')
            )
            ->groupBy('periodos.id', 'periodos.code')
            ->orderByDesc('periodos.id')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $conBecaPer = DB::table('becas_aplicadas')
                    ->join('matriculas', 'becas_aplicadas.user_id', '=', 'matriculas.user_id')
                    ->where('becas_aplicadas.is_active', true)
                    ->where('matriculas.periodo_id', $row->id)
                    ->where('matriculas.estado', 'Habilitada')
                    ->distinct('becas_aplicadas.user_id')
                    ->count('becas_aplicadas.user_id');

                $descPer = BeneficiosService::ahorro('beca', fn($q) => $q->where('o.periodo_id', $row->id));

                $cobertura = $row->total_matriculados > 0
                    ? round($conBecaPer / $row->total_matriculados * 100, 1)
                    : 0;

                return [
                    'periodo'      => $row->code,
                    'matriculados' => $row->total_matriculados,
                    'con_beca'     => $conBecaPer,
                    'cobertura'    => $cobertura,
                    'descuento'    => (float) $descPer,
                ];
            })
            ->toArray();

        return [
            'total_matriculados' => $totalMatriculados,
            'con_beca'           => $totalConBeca,
            'sin_beca'           => max(0, $totalMatriculados - $totalConBeca),
            'cobertura'          => $cobertura,
            'descuento_total'    => (float) $descuentoTotal,
            'monto_original'     => (float) $montoOriginalTotal,
            'ahorro_promedio'    => $ahorroProm,
            'periodo_code'       => $this->modoPeriodoInd === 'global' ? 'Todos los períodos' : ($periodo?->code ?? '—'),
            'es_global'          => $this->modoPeriodoInd === 'global',
            'por_tipo'           => $porTipo,
            'por_carrera'        => $porCarrera,
            'historico'          => $historico,
        ];
    }

    #[Computed]
    public function tiposBeca()
    {
        return TipoBeca::where('is_active', true)->orderBy('categoria')->orderBy('nombre')->get();
    }

    public function updatedTipoBecaId(): void
    {
        $tipo = TipoBeca::find($this->tipoBecaId);
        if ($tipo) {
            $this->porcentajeAplicado = (string) $tipo->porcentaje_descuento;
            if ($tipo->categoria !== 'Discapacidad') {
                $this->porcentajeDiscapacidad = null;
            }
        } else {
            $this->porcentajeAplicado     = '';
            $this->porcentajeDiscapacidad = null;
        }
    }

    public function updatedPorcentajeDiscapacidad(): void
    {
        $pct = (int) $this->porcentajeDiscapacidad;
        if ($pct >= 75) {
            $this->porcentajeAplicado = '100';
        } elseif ($pct >= 50) {
            $this->porcentajeAplicado = '75';
        } elseif ($pct >= 35) {
            $this->porcentajeAplicado = '50';
        }
    }

    public function abrirModalAsignar(int $estudianteId): void
    {
        $this->reset(['becaId', 'tipoBecaId', 'porcentajeAplicado', 'porcentajeDiscapacidad', 'documento', 'observacion', 'motivo']);
        $this->estudianteId    = $estudianteId;
        $this->fechaAsignacion = now()->toDateString();
        $this->showModal       = true;
    }

    public function abrirModalEditar(int $becaId): void
    {
        $beca = BecaAplicada::with('tipoBeca')->findOrFail($becaId);
        $this->becaId                 = $beca->id;
        $this->estudianteId           = $beca->user_id;
        $this->tipoBecaId             = $beca->tipo_beca_id;
        $this->porcentajeAplicado     = (string) $beca->porcentaje_aplicado;
        $this->porcentajeDiscapacidad = $beca->porcentaje_discapacidad;
        $this->observacion            = $beca->observacion ?? '';
        $this->fechaAsignacion        = $beca->fecha_asignacion->toDateString();
        $this->showModal              = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['becaId', 'estudianteId', 'tipoBecaId', 'porcentajeAplicado', 'porcentajeDiscapacidad', 'documento', 'observacion']);
        $this->fechaAsignacion = now()->toDateString();
    }

    public function guardar(): void
    {
        if ($this->sinPermiso('gestionar_becas')) return;

        $tipo = TipoBeca::find($this->tipoBecaId);

        $this->validate([
            'estudianteId'           => 'required|exists:users,id',
            'tipoBecaId'             => 'required|exists:tipos_beca,id',
            'porcentajeAplicado'     => 'required|numeric|min:0|max:100',
            'porcentajeDiscapacidad' => $tipo && $tipo->categoria === 'Discapacidad'
                                         ? 'required|integer|min:35|max:100'
                                         : 'nullable|integer',
            'documento'              => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'observacion'            => 'nullable|string|max:500',
            'fechaAsignacion'        => 'required|date',
        ]);

        if ($this->becaId) {
            // Una beca revocada no se reactiva editándola: queda en el historial
            if (! BecaAplicada::whereKey($this->becaId)->where('is_active', true)->exists()) {
                $this->addError('tipoBecaId', 'Esta beca ya no está activa. Asigna una nueva si corresponde.');
                return;
            }
        } elseif (BecaAplicada::where('user_id', $this->estudianteId)->where('is_active', true)->exists()) {
            $this->addError('tipoBecaId', 'Este estudiante ya tiene una beca activa. Revoca la actual antes de asignar otra.');
            return;
        }

        $documentoPath = null;
        if ($this->documento) {
            $documentoPath = $this->documento->store('becas/documentos', 'public');
        }

        $data = [
            'user_id'                => $this->estudianteId,
            'tipo_beca_id'           => $this->tipoBecaId,
            'porcentaje_aplicado'    => $this->porcentajeAplicado,
            'porcentaje_discapacidad' => $this->porcentajeDiscapacidad,
            'observacion'            => trim($this->observacion) ?: null,
            'fecha_asignacion'       => $this->fechaAsignacion,
            'fecha_ultima_revision'  => now()->toDateString(),
        ];

        if ($documentoPath) {
            $data['documento_path'] = $documentoPath;
        }

        // Todo o nada: la beca y el recálculo de las cuotas aún sin pagar del período en curso
        $resultado = DB::transaction(function () use ($data) {
            if ($this->becaId) {
                $beca = BecaAplicada::lockForUpdate()->findOrFail($this->becaId);
                if (! $beca->is_active) return null;
                // Editar nunca cambia de estudiante ni quién la asignó
                $beca->update(collect($data)->except('user_id')->all());
            } else {
                // Bloquea al estudiante: dos clics seguidos no deben crear dos becas activas
                User::whereKey($this->estudianteId)->lockForUpdate()->first();
                if (BecaAplicada::where('user_id', $this->estudianteId)->where('is_active', true)->exists()) return null;

                $beca = BecaAplicada::create($data + ['is_active' => true, 'asignado_por' => Auth::id()]);
            }

            return [$beca, BeneficiosService::recalcularCuotasPendientes($beca->user_id)];
        });

        if (! $resultado) {
            if ($documentoPath) Storage::disk('public')->delete($documentoPath);
            $this->addError('tipoBecaId', $this->becaId
                ? 'Esta beca ya no está activa. Asigna una nueva si corresponde.'
                : 'Este estudiante ya tiene una beca activa. Revoca la actual antes de asignar otra.');
            return;
        }
        [$beca, $cambiadas] = $resultado;
        $msg = $this->becaId ? 'Beca actualizada correctamente.' : 'Beca asignada correctamente.';

        if (! $this->becaId && $cambiadas > 0) {
            dispatch(new EnviarNotificacionBecaAsignada($beca->id));
        }

        unset($this->estudiantes);
        $this->cerrarModal();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => $msg]);
    }

    public function abrirRevocar(int $becaId): void
    {
        $this->becaRevocarId       = $becaId;
        $this->motivo              = '';
        $this->confirmarRevocacion = false;
        $this->showRevocarModal    = true;
    }

    public function cerrarRevocar(): void
    {
        $this->showRevocarModal    = false;
        $this->confirmarRevocacion = false;
        $this->reset(['becaRevocarId', 'motivo']);
    }

    public function pedirConfirmacionRevocar(): void
    {
        $this->validate(['motivo' => 'nullable|string|max:500']);
        $this->confirmarRevocacion = true;
    }

    public function revocar(): void
    {
        if ($this->sinPermiso('gestionar_becas')) return;

        $beca    = BecaAplicada::with(['estudiante', 'tipoBeca'])->findOrFail($this->becaRevocarId);
        $periodo = Periodo::periodoActivoGlobal();

        // Totales del período para el correo (sin cuotas invalidadas)
        $totalOriginal = ObligacionesFinanciera::where('user_id', $beca->user_id)
            ->where('tipo', 'COLEGIATURA')
            ->where('estado', '!=', 'Invalidado')
            ->when($periodo, fn($q) => $q->where('periodo_id', $periodo->id))
            ->sum('monto_original');

        $totalPagado = DB::table('pagos')
            ->join('obligaciones_financieras', 'pagos.obligacion_id', '=', 'obligaciones_financieras.id')
            ->where('obligaciones_financieras.user_id', $beca->user_id)
            ->where('obligaciones_financieras.tipo', 'COLEGIATURA')
            ->where('obligaciones_financieras.estado', '!=', 'Invalidado')
            ->when($periodo, fn($q) => $q->where('obligaciones_financieras.periodo_id', $periodo->id))
            ->where('pagos.estado', Pago::ESTADO_APROBADO)
            ->sum('pagos.monto');

        // Se revoca (queda en el historial con fecha, usuario y motivo) y las cuotas sin pagar
        // vuelven a calcularse solo con lo que quede vigente (el convenio, si lo tiene)
        DB::transaction(function () use ($beca) {
            $beca->update(['fecha_ultima_revision' => now()->toDateString()]);
            BeneficiosService::desactivar($beca, 'revocacion_manual', trim($this->motivo) ?: null);
            BeneficiosService::recalcularCuotasPendientes($beca->user_id);
        });

        dispatch(new EnviarNotificacionBecaRevocada(
            $beca->id,
            (float) $totalOriginal,
            (float) $totalPagado,
        ));

        unset($this->estudiantes);
        $this->cerrarRevocar();
        $this->dispatch('swal', ['toast' => true, 'icon' => 'success', 'title' => 'Beca revocada. Obligaciones ajustadas.']);
    }

    public function render()
    {
        return view('livewire.administration.becas-estudiantes', [
            'estudiantes'     => $this->estudiantes,
            'tiposBeca'       => $this->tiposBeca,
            'statsResumen'    => $this->statsResumen,
            'periodoEfectivo' => $this->periodoEfectivo,
            'periodos'        => $this->periodos,
        ]);
    }
}
