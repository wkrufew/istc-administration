<?php

namespace App\Livewire\Administration;

use App\Models\User;
use App\Models\Carrera;
use App\Models\Periodo;
use App\Models\Semestre;
use App\Models\Materia;
use App\Models\Matricula;
use App\Models\DetalleMatricula;
use App\Models\AsignacionDocente;
use App\Models\MateriasArrastrada;
use App\Models\MateriaPeriodoParalelo;
use App\Models\Pago;
use App\Models\ObligacionesFinanciera;
use App\Models\BecaAplicada;
use App\Models\ConvenioAplicado;
use App\Models\Retiro;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use App\Jobs\EnviarEmailMatricula;
use App\Jobs\EnviarEmailRetiro;
use App\Jobs\EnviarWhatsappMatricula;
use App\Services\SettingService;
use App\Traits\WithAuthorization;

class Matriculacion extends Component
{
    use WithPagination, WithAuthorization, WithFileUploads;

    // -------------------------------------------------------------------------
    // PROPIEDADES DE BÚSQUEDA Y FILTROS
    // -------------------------------------------------------------------------
    public $search          = '';
    public $selectedCarrera = '';
    public $selectedPeriodo = '';

    // -------------------------------------------------------------------------
    // PROPIEDADES DEL MODAL
    // -------------------------------------------------------------------------
    public $showModal    = false;
    public $estudiante   = null;
    public $matriculaId  = null;

    // -------------------------------------------------------------------------
    // DATOS DE LA MATRÍCULA
    // -------------------------------------------------------------------------
    public $carrera_id    = '';
    public $periodo_id    = '';
    public $tipo          = 'Nueva';
    public $descuento     = 0;
    public $observaciones = '';

    // -------------------------------------------------------------------------
    // MATERIAS Y PARALELOS
    // -------------------------------------------------------------------------
    public $materiasSeleccionadas  = [];
    public $materiasArrastradas    = [];
    public $materiasDisponibles    = [];
    public $paralelosSeleccionados = [];
    public $paralelosDisponibles   = [];

    // -------------------------------------------------------------------------
    // PASOS DEL WIZARD
    // -------------------------------------------------------------------------
    public $paso       = 1;
    public $totalPasos = 4;

    public ?string $semestreSugerido = null;

    // -------------------------------------------------------------------------
    // CÁLCULOS
    // -------------------------------------------------------------------------
    public $totalCreditos = 0;
    public $costoTotal    = 0;
    public $totalPagar    = 0;

    // Costos calculados automáticamente según carrera
    public $montoMatricula   = 0; // (costo_carrera * 10%) / duracion_semestres
    public $montoArancel     = 0; // arancel neto tras beca y reintegro
    public $montoArancelBruto = 0; // arancel base antes de beca (con reintegro ya sumado)
    public $descuentoBeca    = 0; // descuento de beca aplicado al arancel
    public $montoReintegro   = 0; // recargo 10% costo_carrera por reintegro
    public $tieneReintegro   = false;
    public $infoBeca             = null; // ['nombre' => ..., 'porcentaje' => ...]
    public $infoConvenio         = null; // ['nombre' => ..., 'porcentaje' => ...]
    public $descuentoConvenio    = 0;
    public $porcentajeDescuentoTotal = 0;
    public $esGratuidad          = false;
    public $num_cuotas_arancel = 1;  // cuotas en que se divide el arancel semestral
    public $montoCostoTotal  = 0; // montoMatricula + costoArrastres
    public $costoArrastres   = 0; // suma de costo_adicional de arrastres incluidos
    public $valorInscripcion = 0; // solo primera matrícula — leído de settings

    // -------------------------------------------------------------------------
    // PROPIEDADES DEL MODAL RETIRO
    // -------------------------------------------------------------------------
    public bool   $showRetiroModal      = false;
    public ?int   $retiroMatriculaId    = null;
    public string $retiroEstudianteNombre = '';
    public string $retiroFecha          = '';
    public string $retiroMotivo         = '';
    public        $retiroDocumento      = null;

    protected $listeners = [
        'matricularEstudiante' => 'iniciarMatricula',
        'editarMatricula'      => 'editarMatricula',
    ];

    // =========================================================================
    // MOUNT
    // =========================================================================
    public function mount()
    {
        $this->selectedPeriodo = Periodo::periodoActivoGlobal()?->id ?? '';
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================
    #[Computed]
    public function estudiantes()
    {
        return User::permission('acceso_estudiantil')
            ->when(
                $this->search,
                fn($q) => $q->where(
                    fn($q2) => $q2
                        ->where('name',              'like', "%{$this->search}%")
                        ->orWhere('email',           'like', "%{$this->search}%")
                        ->orWhere('cedula',          'like', "%{$this->search}%")
                        ->orWhere('matricula_numero', 'like', "%{$this->search}%")
                )
            )
            // Solo filtrar por carrera (whereHas): muestra solo estudiantes matriculados en esa carrera.
            // El periodo NO filtra qué estudiantes aparecen — solo afecta qué matrícula se muestra en la columna.
            ->when(
                $this->selectedCarrera,
                fn($q) => $q->whereHas('matriculas', fn($q2) => $q2
                    ->where('carrera_id', $this->selectedCarrera)
                )
            )
            ->with([
                // Matrícula del periodo/carrera seleccionado — puede ser vacía si aún no tiene
                'matriculas' => fn($q) => $q
                    ->when($this->selectedCarrera, fn($q2) => $q2->where('carrera_id', $this->selectedCarrera))
                    ->when($this->selectedPeriodo, fn($q2) => $q2->where('periodo_id', $this->selectedPeriodo))
                    ->with(['periodo', 'carrera'])
                    ->latest(),
                // Última matrícula histórica (para contexto cuando no hay matrícula en el periodo filtrado)
                'ultimaMatricula.periodo',
                'ultimaMatricula.carrera',
            ])
            ->orderBy('name')
            ->paginate(10);
    }

    public function updatedSelectedCarrera(): void { $this->resetPage(); }
    public function updatedSelectedPeriodo(): void  { $this->resetPage(); }
    public function updatedSearch(): void           { $this->resetPage(); }

    #[Computed]
    public function carreras()
    {
        return Carrera::activas()->get();
    }

    #[Computed]
    public function periodos()
    {
        return Periodo::orderBy('fecha_inicio', 'desc')->get();
    }

    // =========================================================================
    // INICIAR / EDITAR MATRÍCULA
    // =========================================================================
    public function iniciarMatricula($estudianteId)
    {
        if ($this->sinPermiso('crear_matriculas')) return;

        $this->resetFormulario();

        $this->estudiante = User::with([
            'materiasArrastradas.materia',
            'matriculas.carrera',
        ])->find($estudianteId);

        $this->periodo_id = Periodo::periodoActivoGlobal()?->id ?? '';

        // Pre-seleccionar carrera más reciente
        $ultimaMatricula = $this->estudiante->matriculas()->latest()->first();
        if ($ultimaMatricula) {
            $this->carrera_id = $ultimaMatricula->carrera_id;
        }

        // Cargar materias arrastradas pendientes
        $this->materiasArrastradas = $this->estudiante
            ->materiasArrastradas()
            ->where('estado', 'Arrastrada')
            ->with('materia')
            ->get()
            ->toArray();

        // Auto-detectar tipo según historial
        if (!empty($this->materiasArrastradas)) {
            $this->tipo = 'Arrastre';
        } elseif ($this->estudiante->matriculas()->count() > 0) {
            $this->tipo = 'Renovacion';
        } else {
            $this->tipo = 'Nueva';
        }

        $this->paso      = 1;
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function editarMatricula($matriculaId)
    {
        if ($this->sinPermiso('editar_matriculas')) return;

        $this->resetFormulario();

        $matricula = Matricula::with([
            'estudiante',
            'detalles.materia',
            'detalles.paralelo',
        ])->find($matriculaId);

        if (! $matricula) return;

        $this->matriculaId  = $matriculaId;
        $this->estudiante   = $matricula->estudiante;
        $this->carrera_id   = $matricula->carrera_id;
        $this->periodo_id   = $matricula->periodo_id;
        $this->tipo         = $matricula->tipo;
        $this->descuento    = $matricula->descuento;
        $this->observaciones = $matricula->observaciones;

        $this->materiasSeleccionadas  = $matricula->detalles->pluck('materia_id')->toArray();
        $this->paralelosSeleccionados = $matricula->detalles->pluck('paralelo_id', 'materia_id')->toArray();

        $this->cargarMateriasDisponibles();
        $this->calcularCostos();

        $this->paso      = 2;
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    // =========================================================================
    // NAVEGACIÓN DE PASOS
    // =========================================================================
    public function siguientePaso()
    {
        if ($this->paso == 1) {
            $this->validate([
                'carrera_id' => 'required|exists:carreras,id',
                'periodo_id' => 'required|exists:periodos,id',
            ]);

            // Validar que no exista ya una matrícula en el mismo periodo (solo en creación)
            if (! $this->matriculaId) {
                $existe = Matricula::where('user_id', $this->estudiante->id)
                    ->where('periodo_id', $this->periodo_id)
                    ->exists();
                if ($existe) {
                    $this->addError('periodo_id', 'Este estudiante ya tiene una matrícula registrada en el período seleccionado.');
                    return;
                }
            }

            $this->cargarMateriasDisponibles();
            $this->calcularMontosPorCarrera();
        }

        if ($this->paso == 2) {
            if (empty($this->materiasSeleccionadas) && empty(array_filter($this->materiasArrastradas, fn($m) => $m['incluir'] ?? false))) {
                $this->addError('materias', 'Debe seleccionar al menos una materia.');
                return;
            }
            $this->cargarParalelos();
        }

        if ($this->paso == 3) {
            if (! $this->validarParalelos()) return;
            $this->calcularCostos();
        }

        $this->paso++;
    }

    public function pasoAnterior()
    {
        $this->paso--;
    }

    public function siguientePasoConParalelos(array $paralelos): void
    {
        $this->paralelosSeleccionados = collect($paralelos)
            ->mapWithKeys(fn($v, $k) => [(int) $k => (int) $v])
            ->toArray();
        $this->siguientePaso();
    }

    // =========================================================================
    // CARGA DE DATOS
    // =========================================================================
    public function cargarMateriasDisponibles()
    {
        if (! $this->carrera_id) return;

        $carrera = Carrera::with(['semestres' => fn($q) => $q->orderBy('order'), 'semestres.materias'])->find($this->carrera_id);

        $materiasAprobadas = DB::table('calificacions')
            ->join('detalle_matriculas', 'calificacions.detalle_matricula_id', '=', 'detalle_matriculas.id')
            ->join('matriculas', 'detalle_matriculas.matricula_id', '=', 'matriculas.id')
            ->where('matriculas.user_id', $this->estudiante->id)
            ->where('calificacions.estado_final', 'Aprobado')
            ->pluck('detalle_matriculas.materia_id')
            ->toArray();

        $this->materiasDisponibles = [];

        foreach ($carrera->semestres as $semestre) {
            $materiasSemestre = [];

            foreach ($semestre->materias as $materia) {
                if (in_array($materia->id, $materiasAprobadas)) continue;

                $prerequisitosCumplidos = $this->verificarPrerequisitos($materia, $materiasAprobadas);
                $creditosCalculados     = ($materia->horas_teoricas + $materia->horas_practicas) / 48;

                $materiasSemestre[] = [
                    'id'                     => $materia->id,
                    'name'                   => $materia->name,
                    'code'                   => $materia->code,
                    'credits'                => $creditosCalculados,
                    'credits_inconsistente'  => abs((float) $materia->credits - $creditosCalculados) > 0.01,
                    'tipo'                   => $materia->tipo,
                    'puede_inscribir'        => $prerequisitosCumplidos,
                    'prerequisitos_faltantes' => $prerequisitosCumplidos
                        ? []
                        : $this->getPrerrequisitosFaltantes($materia, $materiasAprobadas),
                ];
            }

            if (! empty($materiasSemestre)) {
                $this->materiasDisponibles[$semestre->name] = $materiasSemestre;
            }
        }

        // Auto-sugerir el siguiente semestre: primer semestre con materias inscribibles.
        // Solo aplica en creación (no en edición), y no sobreescribe selección previa.
        $this->semestreSugerido = null;
        if (! $this->matriculaId && empty($this->materiasSeleccionadas)) {
            foreach ($carrera->semestres->sortBy('order') as $semestre) {
                if (! isset($this->materiasDisponibles[$semestre->name])) continue;

                $inscribibles = collect($this->materiasDisponibles[$semestre->name])
                    ->filter(fn($m) => $m['puede_inscribir']);

                if ($inscribibles->isNotEmpty()) {
                    $this->semestreSugerido    = $semestre->name;
                    $this->materiasSeleccionadas = $inscribibles->pluck('id')->toArray();
                    break;
                }
            }
        }
    }

    public function cargarParalelos()
    {
        $todasLasMaterias = array_merge(
            $this->materiasSeleccionadas,
            array_column(
                array_filter($this->materiasArrastradas, fn($m) => $m['incluir'] ?? false),
                'materia_id'
            )
        );

        $this->paralelosDisponibles = [];

        if (empty($todasLasMaterias)) return;

        // Secciones habilitadas (MPP) para estas materias en el período actual
        $secciones = MateriaPeriodoParalelo::with('paralelo')
            ->where('periodo_id', $this->periodo_id)
            ->whereIn('materia_id', $todasLasMaterias)
            ->where('is_active', true)
            ->get();

        // Una sola query: cupo ya usado por (materia, paralelo) en este período
        $cuposUsados = DB::table('detalle_matriculas as dm')
            ->join('matriculas as m', 'dm.matricula_id', '=', 'm.id')
            ->where('m.periodo_id', $this->periodo_id)
            ->where('m.estado', '!=', 'Cancelada')
            ->where('dm.estado', '!=', 'Retirado')
            ->whereNull('dm.deleted_at')
            ->whereNotNull('dm.paralelo_id')
            ->whereIn('dm.materia_id', $todasLasMaterias)
            ->select('dm.materia_id', 'dm.paralelo_id', DB::raw('COUNT(*) as usado'))
            ->groupBy('dm.materia_id', 'dm.paralelo_id')
            ->get()
            ->keyBy(fn($row) => $row->materia_id . '_' . $row->paralelo_id);

        foreach ($todasLasMaterias as $materiaId) {
            $this->paralelosDisponibles[$materiaId] = $secciones
                ->where('materia_id', $materiaId)
                ->map(function ($mpp) use ($cuposUsados, $materiaId) {
                    $usado      = $cuposUsados->get($materiaId . '_' . $mpp->paralelo_id)?->usado ?? 0;
                    $disponible = max(0, $mpp->cupo_maximo - $usado);

                    return [
                        'id'              => $mpp->paralelo->id,
                        'name'            => $mpp->paralelo->name,
                        'cupo_disponible' => $disponible,
                        'tiene_cupo'      => $disponible > 0,
                    ];
                })
                ->values();
        }
    }

    // =========================================================================
    // VALIDACIONES
    // =========================================================================
    public function verificarPrerequisitos($materia, $materiasAprobadas): bool
    {
        $prerequisitos = DB::table('prerequisitos')
            ->where('materia_id', $materia->id)
            ->where('es_obligatorio', true)
            ->pluck('prerequisito_id')
            ->toArray();

        return empty(array_diff($prerequisitos, $materiasAprobadas));
    }

    public function getPrerrequisitosFaltantes($materia, $materiasAprobadas): array
    {
        return DB::table('prerequisitos')
            ->join('materias', 'prerequisitos.prerequisito_id', '=', 'materias.id')
            ->where('prerequisitos.materia_id', $materia->id)
            ->where('prerequisitos.es_obligatorio', true)
            ->whereNotIn('prerequisitos.prerequisito_id', $materiasAprobadas)
            ->pluck('materias.name')
            ->toArray();
    }

    public function validarParalelos(): bool
    {
        $todasLasMaterias = array_merge(
            $this->materiasSeleccionadas,
            array_column(
                array_filter($this->materiasArrastradas, fn($m) => $m['incluir'] ?? false),
                'materia_id'
            )
        );

        foreach ($todasLasMaterias as $materiaId) {
            if (empty($this->paralelosSeleccionados[$materiaId])) {
                $this->addError('paralelos', 'Debe seleccionar un paralelo para todas las materias.');
                return false;
            }
        }

        return true;
    }

    // =========================================================================
    // CÁLCULO DE MONTOS
    // =========================================================================

    /**
     * Calcula los montos base según la carrera seleccionada.
     * Se llama al pasar el paso 1.
     *
     * Matrícula  = (costo_carrera × 10%) / duracion_semestres  (siempre, sin beca)
     * Arancel    = base (carrera o convalidación) + reintegro – beca
     */
    public function calcularMontosPorCarrera()
    {
        $carrera = Carrera::find($this->carrera_id);
        if (! $carrera) return;

        $semestres = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;

        $this->montoMatricula = round(($carrera->costo_carrera * 0.10) / $semestres, 2);

        $this->aplicarCalculoArancel($carrera, $semestres);
    }

    /**
     * Helper compartido: calcula base arancel, reintegro y beca,
     * y actualiza las propiedades correspondientes.
     */
    private function aplicarCalculoArancel(Carrera $carrera, int $semestres): void
    {
        // Base arancel: Validación usa costo_convalidacion si está definido
        if ($this->tipo === 'Validacion'
            && $carrera->costo_convalidacion !== null
            && (float) $carrera->costo_convalidacion > 0
        ) {
            $base = round((float) $carrera->costo_convalidacion, 2);
        } else {
            $base = round($carrera->costo_carrera / $semestres, 2);
        }

        // Reintegro: +10% costo_carrera si tiene retiro sin cobrar
        $retiroPendiente      = $this->estudiante
            ? Retiro::where('user_id', $this->estudiante->id)->where('recargo_cobrado', false)->exists()
            : false;
        $this->tieneReintegro = $retiroPendiente;
        $this->montoReintegro = $retiroPendiente ? round($carrera->costo_carrera * 0.10, 2) : 0;

        // Beca activa
        $beca = $this->estudiante
            ? BecaAplicada::where('user_id', $this->estudiante->id)->where('is_active', true)->with('tipoBeca')->first()
            : null;

        $pctBeca = 0;
        if ($beca) {
            $pctBeca        = (float) $beca->porcentaje_aplicado;
            $this->infoBeca = ['nombre' => $beca->tipoBeca->nombre, 'porcentaje' => $pctBeca];
        } else {
            $this->infoBeca      = null;
            $this->descuentoBeca = 0;
        }

        // Convenio activo
        $convenio = $this->estudiante
            ? ConvenioAplicado::where('user_id', $this->estudiante->id)
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
                ->with('tipoConvenio')
                ->first()
            : null;

        $pctConvenio = 0;
        if ($convenio) {
            $pctConvenio          = (float) $convenio->porcentaje_aplicado;
            $this->infoConvenio   = ['nombre' => $convenio->tipoConvenio->nombre, 'porcentaje' => $pctConvenio];
        } else {
            $this->infoConvenio      = null;
            $this->descuentoConvenio = 0;
        }

        // Descuento combinado (máx. 100%)
        $pctTotal                        = min(100, $pctBeca + $pctConvenio);
        $this->porcentajeDescuentoTotal  = $pctTotal;
        $this->esGratuidad               = ($pctTotal >= 100);
        $descuentoTotal                  = round(($base + $this->montoReintegro) * ($pctTotal / 100), 2);

        $this->descuentoBeca     = $beca    ? round(($base + $this->montoReintegro) * ($pctBeca / 100), 2)    : 0;
        $this->descuentoConvenio = $convenio ? round(($base + $this->montoReintegro) * ($pctConvenio / 100), 2) : 0;

        $this->montoArancelBruto = $base;
        $this->montoArancel      = max(0, $base + $this->montoReintegro - $descuentoTotal);
    }

    /**
     * Calcula costos completos: matrícula + arrastres seleccionados.
     * costo_arrastre = (costo_credito × creditos_materia) × 1.10
     */
    public function calcularCostos()
    {
        $carrera = Carrera::find($this->carrera_id);
        if (! $carrera) return;

        $semestres = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;

        $this->montoMatricula = round(($carrera->costo_carrera * 0.10) / $semestres, 2);
        $this->aplicarCalculoArancel($carrera, $semestres);

        // Créditos: acumular horas brutas de TODAS las materias y dividir una sola vez al final
        // Esto evita pérdida de precisión al redondear créditos individuales antes de sumar
        $totalHoras     = 0;
        $costoArrastres = 0;

        if (! empty($this->materiasSeleccionadas)) {
            $materias = Materia::whereIn('id', $this->materiasSeleccionadas)->get();
            foreach ($materias as $materia) {
                $totalHoras += $materia->horas_teoricas + $materia->horas_practicas;
            }
        }

        // Costo de arrastres: usar el valor guardado en materias_arrastradas
        foreach ($this->materiasArrastradas as &$materiaArrastrada) {
            if (! ($materiaArrastrada['incluir'] ?? false)) continue;

            $materia = Materia::find($materiaArrastrada['materia_id']);
            if (! $materia) continue;

            $totalHoras += $materia->horas_teoricas + $materia->horas_practicas;

            // Leer el costo ya calculado y guardado al momento de ingresar la nota
            $costoAdicional = floatval($materiaArrastrada['costo_adicional'] ?? 0);

            // Fallback para registros legacy sin costo guardado
            if ($costoAdicional <= 0) {
                $porcentaje     = floatval($materiaArrastrada['porcentaje_penalizacion'] ?? SettingService::get('matricula.porcentaje_arrastre', 30)) / 100;
                $creditosVivos  = ($materia->horas_teoricas + $materia->horas_practicas) / 48;
                $costoAdicional = round($creditosVivos * $carrera->costo_credito * $porcentaje, 2);
                $materiaArrastrada['costo_adicional'] = $costoAdicional;
            }

            $costoArrastres += $costoAdicional;
        }
        unset($materiaArrastrada);

        // División única al final: suma de horas brutas / 48
        $this->totalCreditos = $totalHoras / 48;

        // El total que aparece en el resumen = matrícula + arrastres
        $this->costoArrastres = $costoArrastres;
        $this->costoTotal     = $this->montoMatricula + $costoArrastres;
        $this->totalPagar  = max(0, $this->costoTotal - $this->descuento);

        // Inscripción solo primera matrícula (Nueva, sin editar)
        $this->valorInscripcion = 0;
        if ($this->tipo === 'Nueva' && ! $this->matriculaId) {
            $this->valorInscripcion = (float) SettingService::get('matricula.valor_inscripcion', '10.00');
        }
    }

    // =========================================================================
    // GUARDAR MATRÍCULA
    // =========================================================================
    public function guardarMatricula()
    {
        $permisoGuardar = $this->matriculaId ? 'editar_matriculas' : 'crear_matriculas';
        if ($this->sinPermiso($permisoGuardar)) return;

        $this->validate([
            'carrera_id'  => 'required|exists:carreras,id',
            'periodo_id'  => 'required|exists:periodos,id',
            'descuento'   => 'numeric|min:0|max:' . $this->costoTotal,
        ]);

        // Red de seguridad: evitar duplicado si el wizard se envía sin pasar por el paso 1
        if (! $this->matriculaId) {
            $existe = Matricula::where('user_id', $this->estudiante->id)
                ->where('periodo_id', $this->periodo_id)
                ->exists();
            if ($existe) {
                $this->addError('periodo_id', 'Este estudiante ya tiene una matrícula en el período seleccionado.');
                return;
            }
        }

        // Verificar cupo disponible al momento de guardar (solo en creación)
        // Esto protege contra el caso en que el cupo se llenó mientras el admin
        // avanzaba los pasos del wizard.
        if (! $this->matriculaId) {
            $materiasParaVerificar = collect($this->materiasSeleccionadas)
                ->map(fn($mid) => [
                    'materia_id'  => $mid,
                    'paralelo_id' => $this->paralelosSeleccionados[$mid] ?? null,
                ])
                ->merge(
                    collect($this->materiasArrastradas)
                        ->filter(fn($m) => $m['incluir'] ?? false)
                        ->map(fn($m) => [
                            'materia_id'  => $m['materia_id'],
                            'paralelo_id' => $this->paralelosSeleccionados[$m['materia_id']] ?? null,
                        ])
                )
                ->filter(fn($item) => ! is_null($item['paralelo_id']));

            foreach ($materiasParaVerificar as $item) {
                $mpp = MateriaPeriodoParalelo::where('materia_id',  $item['materia_id'])
                    ->where('periodo_id',  $this->periodo_id)
                    ->where('paralelo_id', $item['paralelo_id'])
                    ->where('is_active',   true)
                    ->first();

                if (! $mpp) continue;

                $usado = DB::table('detalle_matriculas as dm')
                    ->join('matriculas as m', 'dm.matricula_id', '=', 'm.id')
                    ->where('m.periodo_id',   $this->periodo_id)
                    ->where('m.estado',       '!=', 'Cancelada')
                    ->where('dm.estado',      '!=', 'Retirado')
                    ->whereNull('dm.deleted_at')
                    ->where('dm.materia_id',  $item['materia_id'])
                    ->where('dm.paralelo_id', $item['paralelo_id'])
                    ->count();

                if ($usado >= $mpp->cupo_maximo) {
                    $nombreMateria = Materia::find($item['materia_id'])?->name ?? 'materia';
                    $this->addError('paralelos', "El paralelo seleccionado para \"{$nombreMateria}\" ya no tiene cupo disponible. Regresa al paso anterior y elige otro.");
                    $this->paso = 3;
                    return;
                }
            }
        }

        try {
            DB::beginTransaction();

            $esEdicion = (bool) $this->matriculaId;

            $esPrimeraMatricula = ! $esEdicion && Matricula::where('user_id', $this->estudiante->id)->count() === 0;

            $carrera   = Carrera::find($this->carrera_id);
            $semestres = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;

            // ------------------------------------------------------------------
            // MONTOS
            // ------------------------------------------------------------------
            $montoMatricula = round(($carrera->costo_carrera * 0.10) / $semestres, 2);

            // Base arancel: Validación usa costo_convalidacion si está definido
            if ($this->tipo === 'Validacion'
                && $carrera->costo_convalidacion !== null
                && (float) $carrera->costo_convalidacion > 0
            ) {
                $montoArancelBase = round((float) $carrera->costo_convalidacion, 2);
            } else {
                $montoArancelBase = round($carrera->costo_carrera / $semestres, 2);
            }

            // Reintegro: +10% costo_carrera si hay retiro sin cobrar
            $retiroPendiente = Retiro::where('user_id', $this->estudiante->id)
                ->where('recargo_cobrado', false)
                ->first();
            $montoRecargo = $retiroPendiente ? round($carrera->costo_carrera * 0.10, 2) : 0;

            // Beca activa
            $becaActiva = BecaAplicada::where('user_id', $this->estudiante->id)
                ->where('is_active', true)
                ->first();
            $pctBecaGuardar = $becaActiva ? (float) $becaActiva->porcentaje_aplicado : 0;

            // Convenio activo
            $convenioActivo = ConvenioAplicado::where('user_id', $this->estudiante->id)
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('fecha_fin')->orWhere('fecha_fin', '>=', now()->toDateString()))
                ->first();
            $pctConvenioGuardar = $convenioActivo ? (float) $convenioActivo->porcentaje_aplicado : 0;

            $pctTotalGuardar      = min(100, $pctBecaGuardar + $pctConvenioGuardar);
            $descuentoBecaGuardar = round(($montoArancelBase + $montoRecargo) * ($pctTotalGuardar / 100), 2);

            $montoArancel = max(0, $montoArancelBase + $montoRecargo - $descuentoBecaGuardar);

            $costoArrastres = 0;
            foreach ($this->materiasArrastradas as $ma) {
                if ($ma['incluir'] ?? false) {
                    $costoArrastres += $ma['costo_adicional'] ?? 0;
                }
            }

            $montoFinalMatricula = max(0, ($montoMatricula + $costoArrastres) - (float) $this->descuento);

            // ------------------------------------------------------------------
            // CREAR / ACTUALIZAR MATRÍCULA
            // ------------------------------------------------------------------
            if ($this->matriculaId) {
                $matricula = Matricula::find($this->matriculaId);
                $matricula->update([
                    'tipo'           => $this->tipo,
                    /* 'total_creditos' => $this->totalCreditos,
                    'costo_total'    => $this->costoTotal,
                    'descuento'      => $this->descuento,
                    'total_pagar'    => $this->totalPagar, */
                    'observaciones'  => $this->observaciones,
                ]);
                $matricula->detalles()->delete();
            } else {
                $matricula = Matricula::create([
                    'fecha_matricula'   => now(),
                    'code'              => $this->generarCodigoMatricula(),
                    'tipo'              => $this->tipo,
                    'estado'            => 'Pendiente_Pago',
                    'observaciones'     => $this->observaciones,
                    'periodo_id'        => $this->periodo_id,
                    'carrera_id'        => $this->carrera_id,
                    'user_id'           => $this->estudiante->id,
                    'num_cuotas_arancel' => max(1, (int) $this->num_cuotas_arancel),
                ]);

                // ← AQUÍ: asignar matricula_numero si el estudiante no tiene uno aún
                if (! $this->estudiante->matricula_numero) {
                    $ultimo = User::whereNotNull('matricula_numero')->max('matricula_numero');
                    $siguiente = str_pad(($ultimo ? (int) substr($ultimo, 5) : 0) + 1, 5, '0', STR_PAD_LEFT);
                    $this->estudiante->update(['matricula_numero' => 'ISTC-' . $siguiente]);
                }

                // --------------------------------------------------------------
                // OBLIGACIÓN FINANCIERA: MATRÍCULA (pago inmediato)
                // Se genera aquí; el pago se registra desde la vista de obligaciones
                // --------------------------------------------------------------
                ObligacionesFinanciera::create([
                    'user_id'          => $this->estudiante->id,
                    'periodo_id'       => $this->periodo_id,
                    'matricula_id'     => $matricula->id,
                    'tipo'             => 'MATRICULA',
                    'monto_original'   => $montoMatricula + $costoArrastres,
                    'descuento'        => $this->descuento,
                    'monto_final'      => $montoFinalMatricula,
                    'estado'           => 'Pendiente',
                    'fecha_vencimiento' => now()->addDays(5),
                    'descripcion'      => 'Pago de matrícula - Período ' . $matricula->periodo_id
                        . ($costoArrastres > 0 ? ' (incluye arrastres)' : ''),
                ]);

                // --------------------------------------------------------------
                // OBLIGACIÓN FINANCIERA: ARANCEL/COLEGIATURA del semestre
                // Puede dividirse en N cuotas; incluye reintegro y descuento beca.
                // --------------------------------------------------------------

                // Marcar reintegro cobrado antes de generar las obligaciones
                $retiroPendiente?->update(['recargo_cobrado' => true]);

                $numCuotas      = max(1, (int) $this->num_cuotas_arancel);
                $originalTotal  = $montoArancelBase + $montoRecargo;
                $arancelRestante = $montoArancel;

                for ($cuota = 1; $cuota <= $numCuotas; $cuota++) {
                    $esUltima = ($cuota === $numCuotas);

                    // Reparte proporcionalmente; la última absorbe el residuo de redondeo
                    $montoEsta = $esUltima
                        ? round($arancelRestante, 2)
                        : round($montoArancel / $numCuotas, 2);

                    $arancelRestante -= round($montoArancel / $numCuotas, 2);

                    $sufijo      = $numCuotas > 1 ? " (Cuota {$cuota}/{$numCuotas})" : '';
                    $descPartes  = array_filter([
                        $pctBecaGuardar > 0 ? 'beca' : null,
                        $pctConvenioGuardar > 0 ? 'convenio' : null,
                    ]);
                    $descSufijo    = count($descPartes) ? ' con ' . implode(' y ', $descPartes) : '';
                    $recargoSufijo = $montoRecargo > 0 ? ' + reintegro' : '';

                    ObligacionesFinanciera::create([
                        'user_id'           => $this->estudiante->id,
                        'periodo_id'        => $this->periodo_id,
                        'matricula_id'      => $matricula->id,
                        'tipo'              => 'COLEGIATURA',
                        'monto_original'    => round($originalTotal / $numCuotas, 2),
                        'descuento'         => round($descuentoBecaGuardar / $numCuotas, 2),
                        'monto_final'       => $montoEsta,
                        'estado'            => 'Pendiente',
                        'fecha_vencimiento' => now()->addDays(30 * $cuota),
                        'descripcion'       => 'Arancel semestral' . $recargoSufijo . $descSufijo
                            . ' - Período ' . $matricula->periodo_id . $sufijo,
                    ]);
                }

                // --------------------------------------------------------------
                // OBLIGACIÓN FINANCIERA: INSCRIPCIÓN (solo primera matrícula)
                // Se liquida automáticamente al registrar el pago de matrícula
                // --------------------------------------------------------------
                if ($esPrimeraMatricula) {
                    $montoInscripcion = (float) SettingService::get('matricula.valor_inscripcion', '10.00');
                    if ($montoInscripcion > 0) {
                        ObligacionesFinanciera::create([
                            'user_id'           => $this->estudiante->id,
                            'periodo_id'        => $this->periodo_id,
                            'matricula_id'      => $matricula->id,
                            'tipo'              => 'INSCRIPCION',
                            'monto_original'    => $montoInscripcion,
                            'descuento'         => 0,
                            'monto_final'       => $montoInscripcion,
                            'estado'            => 'Pendiente',
                            'fecha_vencimiento' => now()->addDays(5),
                            'descripcion'       => 'Valor de inscripción — Primera matrícula',
                        ]);
                    }
                }

            }

            // ------------------------------------------------------------------
            // DETALLES: MATERIAS NORMALES
            // ------------------------------------------------------------------
            $paralelosUsados = collect();

            foreach ($this->materiasSeleccionadas as $materiaId) {
                $materia = Materia::find($materiaId);

                DetalleMatricula::create([
                    'asignacion'    => now(),
                    'code'          => $this->generarCodigoDetalle($matricula->code, $materia->code),
                    'tipo'          => 'Normal',
                    'estado'        => 'Inscrito',
                    'costo_materia' => (($materia->horas_teoricas + $materia->horas_practicas) / 48) * $carrera->costo_credito,
                    'es_repeticion' => false,
                    'matricula_id'  => $matricula->id,
                    'materia_id'    => $materiaId,
                    'paralelo_id'   => $this->paralelosSeleccionados[$materiaId],
                    'user_id'       => $this->estudiante->id,
                ]);

                $paralelosUsados->push($this->paralelosSeleccionados[$materiaId]);
            }

            // ------------------------------------------------------------------
            // DETALLES: MATERIAS ARRASTRADAS
            // ------------------------------------------------------------------
            foreach ($this->materiasArrastradas as $materiaArrastrada) {
                if (! ($materiaArrastrada['incluir'] ?? false)) continue;

                $materia = Materia::find($materiaArrastrada['materia_id']);

                DetalleMatricula::create([
                    'asignacion'    => now(),
                    'code'          => $this->generarCodigoDetalle($matricula->code, $materia->code),
                    'tipo'          => 'Arrastre',
                    'estado'        => 'Inscrito',
                    'costo_materia' => $materiaArrastrada['costo_adicional'] ?? 0,
                    'es_repeticion' => true,
                    'matricula_id'  => $matricula->id,
                    'materia_id'    => $materiaArrastrada['materia_id'],
                    'paralelo_id'   => $this->paralelosSeleccionados[$materiaArrastrada['materia_id']],
                    'user_id'       => $this->estudiante->id,
                ]);

                MateriasArrastrada::find($materiaArrastrada['id'])?->update(['estado' => 'Inscrita']);

                $paralelosUsados->push($this->paralelosSeleccionados[$materiaArrastrada['materia_id']]);
            }

            DB::commit();

            // ------------------------------------------------------------------
            // WHATSAPP — solo en matrículas nuevas, no en ediciones
            // El try independiente evita que un fallo del job (sync) rompa la UI
            // ------------------------------------------------------------------
            if (! $this->matriculaId) {
                try {
                    $whatsappActivo = SettingService::get('whatsapp.activo', '0') === '1';
                    $notifWA        = SettingService::get('notificaciones.matricula_whatsapp', '0') === '1';

                    if ($whatsappActivo && $notifWA) {
                        // Después del commit esta matrícula ya existe, contamos ≥1
                        $totalMatriculas   = Matricula::where('user_id', $this->estudiante->id)->count();
                        $esPrimerMatricula = $totalMatriculas === 1;

                        EnviarWhatsappMatricula::dispatch(
                            $matricula->id,
                            $esPrimerMatricula
                        );
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('WhatsApp dispatch falló (no crítico)', [
                        'matricula_id' => $matricula->id,
                        'error'        => $e->getMessage(),
                    ]);
                }
            }

            // ------------------------------------------------------------------
            // EMAIL — nuevas Y ediciones (el Job verifica internamente los settings)
            // ------------------------------------------------------------------
            try {
                $totalMatriculas   = Matricula::where('user_id', $this->estudiante->id)->count();
                $esPrimerMatricula = ! $esEdicion && $totalMatriculas === 1;

                EnviarEmailMatricula::dispatch($matricula->id, $esPrimerMatricula, $esEdicion);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Email dispatch falló (no crítico)', [
                    'matricula_id' => $matricula->id,
                    'error'        => $e->getMessage(),
                ]);
            }

            $this->cerrarModal();

            // Redirigir a la vista de obligaciones filtrando por la matrícula recién creada
            /* $this->dispatch('matricula-guardada', [
                'mensaje'      => $this->matriculaId ? 'Matrícula actualizada exitosamente' : 'Matrícula creada. Proceda a registrar el pago.',
                'matricula_id' => $matricula->id,
                'redirigir'    => ! $this->matriculaId,
            ]); */
            $this->dispatch('matricula-guardada', [
                'mensaje'      => $this->matriculaId ? 'Matrícula actualizada exitosamente' : 'Matrícula creada. Proceda a registrar el pago.',
                'matricula_id' => $matricula->id,
                'redirigir'    => ! $this->matriculaId,
                'url_pago'     => route('administracion.administrativa.matriculas.pago', $matricula->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', ['mensaje' => 'Error al guardar la matrícula: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function generarCodigoMatricula(): string
    {
        $ultimo = Matricula::count();
        return sprintf('ISTC-MATRICULA-%04d', $ultimo + 1);
    }

    private function generarCodigoDetalle(string $codigoMatricula, string $codigoMateria): string
    {
        return $codigoMatricula . '-' . $codigoMateria;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->resetFormulario();
        $this->dispatch('modal-closed');
    }

    private function resetFormulario()
    {
        $this->reset([
            'estudiante',
            'matriculaId',
            'carrera_id',
            'periodo_id',
            'tipo',
            'descuento',
            'observaciones',
            'materiasSeleccionadas',
            'materiasArrastradas',
            'materiasDisponibles',
            'paralelosSeleccionados',
            'paralelosDisponibles',
            'semestreSugerido',
            'paso',
            'totalCreditos',
            'costoTotal',
            'totalPagar',
            'montoMatricula',
            'montoArancel',
            'montoArancelBruto',
            'descuentoBeca',
            'montoReintegro',
            'tieneReintegro',
            'infoBeca',
            'infoConvenio',
            'descuentoConvenio',
            'porcentajeDescuentoTotal',
            'esGratuidad',
            'num_cuotas_arancel',
            'costoArrastres',
            'valorInscripcion',
        ]);
    }
    /* =========================
        RETIRO DE MATRÍCULA
    ========================== */

    public function abrirRetiro(int $matriculaId): void
    {
        if ($this->sinPermiso('cancelar_matriculas')) return;

        $matricula = Matricula::with('estudiante')->find($matriculaId);
        if (! $matricula || in_array($matricula->estado, ['Cancelada', 'Retirada'])) return;

        //dd($matriculaId);
        $this->retiroMatriculaId      = $matriculaId;
        $this->retiroEstudianteNombre = $matricula->estudiante->name;
        $this->retiroFecha            = now()->toDateString();
        $this->retiroMotivo           = '';
        $this->showRetiroModal        = true;
    }

    public function cerrarRetiro(): void
    {
        $this->showRetiroModal        = false;
        $this->retiroMatriculaId      = null;
        $this->retiroEstudianteNombre = '';
        $this->retiroFecha            = '';
        $this->retiroMotivo           = '';
        $this->retiroDocumento        = null;
        $this->resetValidation(['retiroFecha', 'retiroMotivo', 'retiroDocumento']);
    }

    public function confirmarRetiro(): void
    {
        if ($this->sinPermiso('cancelar_matriculas')) return;

        $this->validate([
            'retiroFecha'      => 'required|date',
            'retiroMotivo'     => 'nullable|string|max:1000',
            'retiroDocumento'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'retiroDocumento.required' => 'Debes adjuntar la solicitud o certificado de retiro.',
            'retiroDocumento.mimes'    => 'El documento debe ser PDF, JPG o PNG.',
            'retiroDocumento.max'      => 'El documento no puede superar los 5 MB.',
        ]);

        $matricula = Matricula::with('detalles', 'estudiante')->find($this->retiroMatriculaId);
        if (! $matricula) {
            $this->cerrarRetiro();
            return;
        }

        try {
            DB::transaction(function () use ($matricula) {
                $documentoPath = $this->retiroDocumento->store(
                    'retiros/' . $matricula->id,
                    'public'
                );

                Retiro::create([
                    'matricula_id'    => $matricula->id,
                    'user_id'         => $matricula->user_id,
                    'fecha_retiro'    => $this->retiroFecha,
                    'motivo'          => $this->retiroMotivo ?: null,
                    'documento_path'  => $documentoPath,
                    'recargo_cobrado' => false,
                    'registrado_por'  => auth()->id(),
                ]);

                $matricula->update(['estado' => 'Retirada']);

                $matricula->detalles()->update(['estado' => 'Retirado']);
            });

            $nombre = $matricula->estudiante->name;
            EnviarEmailRetiro::dispatch($matricula->id);
            unset($this->estudiantes);
            $this->cerrarRetiro();
            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => 'Retiro registrado',
                'text'  => "El retiro de {$nombre} fue registrado. Si se re-matricula, se aplicará un recargo del 10%.",
                'timer' => 4000,
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al registrar el retiro',
                'text'  => app()->isLocal() ? $e->getMessage() : 'Contacte al administrador del sistema.',
                'timer' => 6000,
            ]);
        }
    }

    /* =========================
        ANULACIÓN DE MATRÍCULA
    ========================== */

    public function abrirAnulacion(int $estudianteId): void
    {
        $this->dispatch('abrir-anulacion-matricula', estudianteId: $estudianteId);
    }

    #[On('matricula-anulada')]
    public function refrescarLista(): void
    {
        unset($this->estudiantes);
    }

    /* =========================
        RENDER
    ========================== */


    public function render()
    {
        return view('livewire.administration.matriculacion', [
            /* 'estudiantes' => User::role('Estudiante')->paginate(10), */
            'estudiantes' => $this->estudiantes,
            'periodos' => Periodo::all(),
            'carreras' => Carrera::all(),
            'semestres' => Semestre::all(),
        ]);
    }
}
