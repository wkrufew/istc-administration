<?php

namespace App\Livewire\Administration;

use App\Models\User;
use App\Models\Aspirante;
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
use App\Models\Convalidacion;
use App\Models\Retiro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
use App\Services\BeneficiosService;
use App\Services\CalculoMatriculaService;
use App\Services\SaldoValidacionService;
use App\Services\SecuenciaService;
use App\Services\HabilitacionMatriculaService;
use App\Services\SettingService;
use App\Services\SemestreActualService;
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

    // -------------------------------------------------------------------------
    // DATOS DE LA MATRÍCULA
    // -------------------------------------------------------------------------
    public $carrera_id    = '';
    public $periodo_id    = '';
    public $tipo          = 'Nueva';
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
    public $montoCostoTotal  = 0; // montoMatricula + costoArrastres
    public $costoArrastres   = 0; // suma de costo_adicional de arrastres incluidos
    public $valorInscripcion = 0; // solo primera matrícula — leído de settings

    // Convalidación de conocimientos — detectado automáticamente al calcular costos
    public bool $esConvalidacion          = false;
    public int  $semestresExonerados      = 0;
    public int  $semestresYaCursados      = 0;
    public bool $pendienteConvalidacion   = false;

    // Saldo de validación: ya no le quedan materias, pero sí la parte de la carrera exonerada
    public ?array $saldoValidacion = null;
    public string $motivoReversionSaldo = '';

    // -------------------------------------------------------------------------
    // PROPIEDADES DEL MODAL RETIRO
    // -------------------------------------------------------------------------
    public bool   $showRetiroModal        = false;
    public ?int   $retiroMatriculaId      = null;
    public string $retiroEstudianteNombre = '';
    public string $retiroFecha            = '';
    public string $retiroTipo             = '';
    public string $retiroMotivo           = '';
    public        $retiroDocumento        = null;

    protected $listeners = [
        'matricularEstudiante' => 'iniciarMatricula',
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
        return User::where(fn($q) => $q
                ->permission('acceso_estudiantil')
                // Aspirantes aprobados aún con rol Admisión: el rol cambia al guardar su primera matrícula.
                // Los de validación solo aparecen cuando ya tienen su validación registrada.
                ->orWhere(fn($asp) => $asp
                    ->whereHas('aspirante', fn($a) => $a->where('estado', 'aprobado')->where('tipo_proceso', 'regular'))
                )
                ->orWhere(fn($asp) => $asp
                    ->whereHas('aspirante', fn($a) => $a->where('estado', 'aprobado')->where('tipo_proceso', 'validacion_conocimientos'))
                    ->whereHas('convalidaciones', fn($c) => $c->where('estado', 'Confirmada'))
                )
            )
            ->where('estado_academico', 'Activo')
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
                'aspirante:id,user_id,estado,tipo_proceso',
            ])
            ->orderBy('name')
            ->paginate(10);
    }

    public function updatedSelectedCarrera(): void { $this->resetPage(); }
    public function updatedSelectedPeriodo(): void  { $this->resetPage(); }
    public function updatedSearch(): void           { $this->resetPage(); }

    public function updatedMateriasSeleccionadas(): void
    {
        $arrastresIncluidos = count(array_filter($this->materiasArrastradas, fn($m) => $m['incluir'] ?? false));
        $maxNormales = max(0, 8 - $arrastresIncluidos);
        if (count($this->materiasSeleccionadas) > $maxNormales) {
            $this->materiasSeleccionadas = array_slice($this->materiasSeleccionadas, 0, $maxNormales);
        }
    }

    public function updatedMateriasArrastradas($value, $key): void
    {
        if (! str_ends_with((string) $key, '.incluir') || ! $value) return;

        $total = count($this->materiasSeleccionadas)
            + count(array_filter($this->materiasArrastradas, fn($m) => $m['incluir'] ?? false));

        if ($total > 8) {
            $index = (int) explode('.', $key)[0];
            $this->materiasArrastradas[$index]['incluir'] = false;
        }
    }

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

        // Pre-seleccionar carrera más reciente
        $ultimaMatricula = $this->estudiante->matriculas()->where('tipo', '!=', Matricula::TIPO_VALIDACION)->latest()->first();
        if ($carreraAdmision = $this->carreraDeAdmision()) {
            // Primera matrícula de un aspirante: la carrera es la elegida en admisión
            $this->carrera_id = $carreraAdmision;
        } elseif ($ultimaMatricula) {
            $this->carrera_id = $ultimaMatricula->carrera_id;
        } else {
            $this->carrera_id = $this->estudiante->matriculas()->latest()->value('carrera_id') ?? '';
        }

        // Período vigente de SU carrera (las carreras pueden ir en períodos distintos)
        $this->periodo_id = ($this->carrera_id ? Carrera::find($this->carrera_id)?->periodoActual()?->id : null)
            ?? Periodo::periodoActivoGlobal()?->id
            ?? '';

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
        } elseif ($this->estudiante->matriculas()->where('tipo', '!=', Matricula::TIPO_VALIDACION)->count() > 0) {
            $this->tipo = 'Renovacion';
        } else {
            $this->tipo = 'Nueva';
        }

        $this->paso      = 1;
        $this->showModal = true;

        $this->pendienteConvalidacion = $this->convalidacionPendiente();

        $this->dispatch('modal-opened');
    }

    /**
     * Aspirante de validación de conocimientos que aún no tiene su convalidación confirmada:
     * no puede matricularse porque sus valores dependen de los semestres exonerados.
     * Se consulta la BD en cada llamada para no depender de la propiedad pública del componente.
     */
    /**
     * Aspirante de admisión en su primera matrícula: pasa a rol Estudiante, pierde el acceso
     * al portal de admisión y su contraseña pasa a ser la cédula, que es la que anuncia el
     * correo de primera matrícula. Se ejecuta dentro de la transacción de guardarMatricula.
     */
    private function convertirAspiranteEnEstudiante(): void
    {
        $aspirante = $this->estudiante->aspirante;
        if (! $aspirante) return;

        // Solo la primera vez: si ya tiene acceso estudiantil no se toca su rol ni su contraseña
        if (! $this->estudiante->hasPermissionTo('acceso_estudiantil')) {
            $this->estudiante->syncRoles(['Estudiante']);

            if ($this->estudiante->hasDirectPermission('acceso_admision')) {
                $this->estudiante->revokePermissionTo('acceso_admision');
            }

            if ($this->estudiante->cedula) {
                $this->estudiante->update(['password' => Hash::make($this->estudiante->cedula)]);
            }
        }

        // Sin correo de admisión: ya recibe el de matrícula
        if ($aspirante->estado !== 'matriculado') {
            $aspirante->update(['estado' => 'matriculado']);
        }

        // Sus documentos de admisión pasan a su expediente de estudiante (sin duplicar si se repite)
        \App\Services\ExpedienteService::traspasarDesdeAdmision($aspirante);
    }

    /**
     * Carrera fijada por admisión: en la primera matrícula de un aspirante (aún no matriculado y
     * sin matrículas regulares) la carrera es la de su postulación y no se cambia aquí, porque el
     * valor de matrícula, el pago de admisión y el convenio se calcularon con ella. Si hay que
     * cambiarla, se hace antes en Aspirantes → Editar. null = la carrera se elige libremente.
     */
    public function carreraDeAdmision(): ?int
    {
        if (! $this->estudiante) return null;

        $aspirante = $this->estudiante->aspirante()->first();
        if (! $aspirante || $aspirante->estado === 'matriculado' || ! $aspirante->carrera_id) return null;

        $yaTieneMatricula = $this->estudiante->matriculas()->where('tipo', '!=', Matricula::TIPO_VALIDACION)->exists();
        return $yaTieneMatricula ? null : (int) $aspirante->carrera_id;
    }

    /** Verifica en el servidor que no se haya cambiado la carrera fijada por admisión. */
    private function carreraRespetaAdmision(): bool
    {
        $fijada = $this->carreraDeAdmision();
        if ($fijada && (int) $this->carrera_id !== $fijada) {
            $this->carrera_id = $fijada;
            $this->addError('carrera_id', 'La carrera es la elegida en admisión. Para cambiarla, edítala primero en Aspirantes.');
            return false;
        }
        return true;
    }

    /**
     * Deudas del estudiante (cualquier período) al iniciar una matrícula nueva.
     * Por ahora solo se informa; no bloquea la matrícula.
     */
    #[Computed]
    public function deudaPendiente(): ?array
    {
        if (! $this->estudiante) return null;

        $pendientes = ObligacionesFinanciera::where('user_id', $this->estudiante->id)
            ->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
            ->withSum(['pagos as pagado' => fn($q) => $q->where('estado', Pago::ESTADO_APROBADO)], 'monto')
            ->with('periodo:id,code')
            ->get()
            ->map(fn($ob) => [
                'saldo'   => max(0, (float) $ob->monto_final - (float) ($ob->pagado ?? 0)),
                'periodo' => $ob->periodo?->code,
                'vencida' => $ob->estado === 'Vencido',
            ])
            ->filter(fn($fila) => $fila['saldo'] > 0);

        if ($pendientes->isEmpty()) return null;

        return [
            'total'    => round($pendientes->sum('saldo'), 2),
            'cantidad' => $pendientes->count(),
            'vencidas' => $pendientes->where('vencida', true)->count(),
            'periodos' => $pendientes->pluck('periodo')->filter()->unique()->values()->all(),
        ];
    }

    /**
     * Resumen para el último paso del wizard cuando el estudiante viene de admisión:
     * cómo se aplicará su pago verificado al guardar (mismos criterios que aplicarPagoAdmision).
     */
    #[Computed]
    public function infoAdmision(): ?array
    {
        if (! $this->estudiante) return null;

        $aspirante = $this->estudiante->aspirante()->first();
        if (! $aspirante || $aspirante->estado === 'matriculado') return null;

        // El pago de admisión cubre solo la obligación MATRICULA (la matrícula se cobra siempre completa)
        $totalMatricula = round(max(0, (float) $this->montoMatricula), 2);
        $aplicable      = $aspirante->tienePagoAdmisionPorAplicar() && $totalMatricula > 0;
        $monto          = (float) $aspirante->pago_monto;
        $aplicado       = $aplicable ? min($monto, $totalMatricula) : 0;

        return [
            'es_validacion' => $aspirante->esValidacionConocimientos(),
            'verificado'    => $aspirante->pago_estado === 'verificado',
            'aplicable'     => $aplicable,
            // Verificado antes de registrar monto y método: se completa en Aspirantes
            'sin_monto'     => $aspirante->pagoVerificadoSinMonto(),
            'cedula'        => $this->estudiante->cedula,
            'monto'         => $monto,
            'metodo'        => $aspirante->pago_metodo,
            'fecha'         => ($aspirante->pago_fecha ?? $aspirante->pago_verificado_at)?->format('d/m/Y'),
            'aplicado'      => $aplicado,
            'faltante'      => $aplicable ? max(0, round($totalMatricula - $aplicado, 2)) : $totalMatricula,
            'excedente'     => $aplicable ? max(0, round($monto - $totalMatricula, 2)) : 0,
        ];
    }

    /**
     * Aplica a la obligación MATRICULA el pago verificado en admisión. Si lo cubre, la
     * matrícula queda habilitada; si no (validación con semestres exonerados), queda Parcial
     * con el faltante por cobrar. Se ejecuta dentro de la transacción de guardarMatricula.
     *
     * @return array{estado:string, aplicado:float, faltante:float, obligacion_id:int}|null
     */
    private function aplicarPagoAdmision(Matricula $matricula, ObligacionesFinanciera $obligacion): ?array
    {
        $aspirante = Aspirante::where('user_id', $this->estudiante->id)->lockForUpdate()->first();
        if (! $aspirante || ! $aspirante->tienePagoAdmisionPorAplicar()) return null;
        if ((float) $obligacion->monto_final <= 0) return null;

        $monto = min((float) $aspirante->pago_monto, (float) $obligacion->monto_final);

        $pago = Pago::create([
            'numero_comprobante' => 'TEMP',
            'obligacion_id'      => $obligacion->id,
            'monto'              => $monto,
            'metodo_pago'        => $aspirante->pago_metodo ?: Pago::METODO_TRANSFERENCIA,
            'estado'             => Pago::ESTADO_APROBADO,
            'numero_cuota'       => 1,
            'fecha_pago'         => $aspirante->pago_fecha ?? $aspirante->pago_verificado_at,
            'descripcion'        => 'Pago de matrícula realizado en admisión (verificado por secretaría)',
            'comprobante_path'   => $aspirante->pago_comprobante_path,
        ]);
        // PagoObserver::created() ya recalculó el estado de la obligación
        $pago->update([
            'numero_comprobante' => 'ISTC-CP-MATRICULA-' . now()->year . '-' . str_pad($pago->id, 5, '0', STR_PAD_LEFT),
        ]);

        $aspirante->update(['pago_aplicado_id' => $pago->id]);

        // Lo que sobre después de la matrícula se abona a la colegiatura, cuota por cuota
        $excedente = round((float) $aspirante->pago_monto - $monto, 2);
        $abonado   = 0;
        if ($excedente > 0) {
            $cuotas = ObligacionesFinanciera::where('matricula_id', $matricula->id)
                ->where('tipo', 'COLEGIATURA')
                ->whereIn('estado', ['Pendiente', 'Parcial'])
                ->orderBy('fecha_vencimiento')
                ->orderBy('id')
                ->get();

            foreach ($cuotas as $cuota) {
                $abono = min(round($excedente - $abonado, 2), round((float) $cuota->saldo, 2));
                if ($abono <= 0) break;

                $pagoCuota = Pago::create([
                    'numero_comprobante' => 'TEMP',
                    'obligacion_id'      => $cuota->id,
                    'monto'              => $abono,
                    'metodo_pago'        => $aspirante->pago_metodo ?: Pago::METODO_TRANSFERENCIA,
                    'estado'             => Pago::ESTADO_APROBADO,
                    'numero_cuota'       => 1,
                    'fecha_pago'         => $aspirante->pago_fecha ?? $aspirante->pago_verificado_at,
                    'descripcion'        => 'Excedente del pago de admisión abonado a la colegiatura',
                    'comprobante_path'   => $aspirante->pago_comprobante_path,
                ]);
                $pagoCuota->update([
                    'numero_comprobante' => 'ISTC-CP-COLEGIATURA-' . now()->year . '-' . str_pad($pagoCuota->id, 5, '0', STR_PAD_LEFT),
                ]);
                $abonado = round($abonado + $abono, 2);
            }
        }

        $habilitada = HabilitacionMatriculaService::verificar($matricula);

        return [
            'estado'        => $habilitada ? 'Pagado' : 'Parcial',
            'aplicado'      => $monto,
            'abonado'       => $abonado,
            'faltante'      => HabilitacionMatriculaService::saldoPendiente($matricula->id),
            'obligacion_id' => $obligacion->id,
        ];
    }

    private function convalidacionPendiente(): bool
    {
        if (! $this->estudiante) return false;

        return $this->estudiante->aspirante()->where('tipo_proceso', 'validacion_conocimientos')->exists()
            && ! Convalidacion::where('user_id', $this->estudiante->id)->where('estado', 'Confirmada')->exists();
    }

    // =========================================================================
    // NAVEGACIÓN DE PASOS
    // =========================================================================
    public function siguientePaso()
    {
        if ($this->paso == 1) {
            if ($this->convalidacionPendiente()) {
                $this->pendienteConvalidacion = true;
                $this->addError('convalidacion', 'No se puede matricular: primero registra su validación de conocimientos.');
                return;
            }

            $this->validate([
                'carrera_id' => 'required|exists:carreras,id',
                'periodo_id' => 'required|exists:periodos,id',
            ]);
            if (! $this->carreraRespetaAdmision()) return;

            // Validar que no exista ya una matrícula en el mismo periodo
            $existe = Matricula::where('user_id', $this->estudiante->id)
                ->where('periodo_id', $this->periodo_id)
                ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
                ->exists();
            if ($existe) {
                // El saldo de validación no es una matrícula nueva: puede generarse en el mismo período
                $this->cargarMateriasDisponibles();
                $this->calcularMontosPorCarrera();
                $this->detectarSaldoValidacion();
                if (! $this->saldoValidacion) {
                    $this->addError('periodo_id', 'Este estudiante ya tiene una matrícula registrada en el período seleccionado.');
                    return;
                }
            }

            $this->cargarMateriasDisponibles();
            $this->calcularMontosPorCarrera();
            $this->detectarSaldoValidacion();
        }

        if ($this->paso == 2) {
            // Saldo de validación: no hay materias que inscribir, se genera desde la tarjeta
            if ($this->saldoValidacion) return;

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

        $materiasEnArrastre = array_column($this->materiasArrastradas, 'materia_id');

        // Materias con al menos un paralelo activo en el período actual (MPP gap check).
        // Si no hay periodo_id todavía, se trata todo como ofertado para no bloquear la UI.
        $ofertadasIds = [];
        if ($this->periodo_id) {
            $ofertadasIds = MateriaPeriodoParalelo::where('periodo_id', $this->periodo_id)
                ->where('is_active', true)
                ->pluck('materia_id')
                ->unique()
                ->all();
        }

        // Restrict to the student's current semester.
        // null = no restriction (new student, válvula de escape, or all complete).
        $semestreActualId = (new SemestreActualService())
            ->obtenerSemestreActual($this->estudiante->id, (int) $this->carrera_id)
            ?->id;

        $this->materiasDisponibles = [];

        foreach ($carrera->semestres as $semestre) {
            if ($semestreActualId !== null && $semestre->id !== $semestreActualId) continue;

            $materiasSemestre = [];

            foreach ($semestre->materias as $materia) {
                if (in_array($materia->id, $materiasAprobadas)) continue;
                if (in_array($materia->id, $materiasEnArrastre)) continue;

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
                    'ofertada'               => ! $this->periodo_id || in_array($materia->id, $ofertadasIds),
                ];
            }

            if (! empty($materiasSemestre)) {
                $this->materiasDisponibles[$semestre->name] = $materiasSemestre;
            }
        }

        // Auto-sugerir el siguiente semestre: primer semestre con materias inscribibles.
        // No sobreescribe una selección previa.
        $this->semestreSugerido = null;
        if (empty($this->materiasSeleccionadas)) {
            foreach ($carrera->semestres->sortBy('order') as $semestre) {
                if (! isset($this->materiasDisponibles[$semestre->name])) continue;

                $inscribibles = collect($this->materiasDisponibles[$semestre->name])
                    ->filter(fn($m) => $m['puede_inscribir'] && $m['ofertada']);

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

        $this->detectarConvalidacion();

        $semestresTotal   = CalculoMatriculaService::semestresCarrera($carrera);
        $fraccionRestante = ($semestresTotal - $this->semestresYaCursados) / $semestresTotal;
        $semestres        = max(1, $semestresTotal - $this->semestresExonerados - $this->semestresYaCursados);

        $this->montoMatricula = CalculoMatriculaService::matriculaPorSemestre($carrera, $semestres, $fraccionRestante);

        $this->aplicarCalculoArancel($carrera, $semestres, $fraccionRestante);
    }

    /**
     * Helper compartido: calcula base arancel, reintegro y beca,
     * y actualiza las propiedades correspondientes.
     */
    private function aplicarCalculoArancel(Carrera $carrera, int $semestres, float $fraccionRestante = 1.0): void
    {
        // Base arancel: convalidación directa usa costo_convalidacion; track normal usa fracción restante
        if ($this->esConvalidacion
            && $carrera->costo_convalidacion !== null
            && (float) $carrera->costo_convalidacion > 0
        ) {
            $base = round((float) $carrera->costo_convalidacion * $fraccionRestante / $semestres, 2);
        } else {
            $base = round($carrera->costo_carrera * $fraccionRestante / $semestres, 2);
        }

        // Reintegro: +10% costo_carrera si tiene retiro sin cobrar. Se cobra como obligación
        // REINTEGRO propia (pago inmediato), no dentro del arancel, y sin descuentos.
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

        // Convenio aplicable en el período de esta matrícula
        $convenio = $this->estudiante
            ? ConvenioAplicado::aplicablesEnPeriodo($this->estudiante->id, $this->periodo_id ? (int) $this->periodo_id : null)
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
        // Beca y convenio descuentan solo el arancel
        $descuentoTotal                  = round($base * ($pctTotal / 100), 2);

        $this->descuentoBeca     = $beca    ? round($base * ($pctBeca / 100), 2)    : 0;
        $this->descuentoConvenio = $convenio ? round($base * ($pctConvenio / 100), 2) : 0;

        $this->montoArancelBruto = $base;
        $this->montoArancel      = max(0, $base - $descuentoTotal);
    }

    /**
     * Calcula costos completos: matrícula + arrastres seleccionados.
     * costo_arrastre = (costo_credito × creditos_materia) × 1.10
     */
    public function calcularCostos()
    {
        $carrera = Carrera::find($this->carrera_id);
        if (! $carrera) return;

        $this->detectarConvalidacion();

        $semestresTotal   = CalculoMatriculaService::semestresCarrera($carrera);
        $fraccionRestante = ($semestresTotal - $this->semestresYaCursados) / $semestresTotal;
        $semestres        = max(1, $semestresTotal - $this->semestresExonerados - $this->semestresYaCursados);

        $this->montoMatricula = CalculoMatriculaService::matriculaPorSemestre($carrera, $semestres, $fraccionRestante);
        $this->aplicarCalculoArancel($carrera, $semestres, $fraccionRestante);

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
                $porcentaje     = floatval($materiaArrastrada['porcentaje_penalizacion'] ?? SettingService::get('matricula.porcentaje_arrastre', 10)) / 100;
                $creditosVivos  = ($materia->horas_teoricas + $materia->horas_practicas) / 48;
                $costoAdicional = round($creditosVivos * $carrera->costo_credito * $porcentaje, 2);
                $materiaArrastrada['costo_adicional'] = $costoAdicional;
            }

            $costoArrastres += $costoAdicional;
        }
        unset($materiaArrastrada);

        // División única al final: suma de horas brutas / 48
        $this->totalCreditos = $totalHoras / 48;

        // Pago inmediato = matrícula + arrastres + reintegro (sin descuentos: becas y convenios solo van al arancel)
        $this->costoArrastres = $costoArrastres;
        $this->costoTotal     = $this->montoMatricula + $costoArrastres;
        $this->totalPagar     = $this->costoTotal + $this->montoReintegro;

        // Inscripción solo primera matrícula
        $this->valorInscripcion = 0;
        if ($this->tipo === 'Nueva') {
            $this->valorInscripcion = (float) SettingService::get('matricula.valor_inscripcion', '10.00');
        }
    }

    // =========================================================================
    // GUARDAR MATRÍCULA
    // =========================================================================
    public function guardarMatricula()
    {
        if ($this->sinPermiso('crear_matriculas')) return;

        $this->validate([
            'carrera_id'  => 'required|exists:carreras,id',
            'periodo_id'  => 'required|exists:periodos,id',
        ]);
        if (! $this->carreraRespetaAdmision()) return;

        // Red de seguridad: el bloqueo del paso 1 también se verifica al guardar
        if ($this->convalidacionPendiente()) {
            $this->paso = 1;
            $this->pendienteConvalidacion = true;
            $this->addError('convalidacion', 'No se puede matricular: primero registra su validación de conocimientos.');
            return;
        }

        // Cap de 8 materias: red de seguridad por si el guard del frontend fue eludido
        if ($this->totalMateriasActual() > 8) {
            $this->addError('materiasSeleccionadas', 'No se pueden matricular más de 8 materias en total (regulares + arrastres).');
            return;
        }

        // Red de seguridad: evitar duplicado si el wizard se envía sin pasar por el paso 1
        $existe = Matricula::where('user_id', $this->estudiante->id)
            ->where('periodo_id', $this->periodo_id)
            ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
            ->exists();
        if ($existe) {
            $this->addError('periodo_id', 'Este estudiante ya tiene una matrícula en el período seleccionado.');
            return;
        }

        // Verificar cupo disponible al momento de guardar.
        // Esto protege contra el caso en que el cupo se llenó mientras el admin
        // avanzaba los pasos del wizard.
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

        try {
            DB::beginTransaction();

            $resultadoAdmision = null;

            // La matrícula interna de Validación no cuenta: el estudiante sigue siendo nuevo
            // (credenciales, Moodle e inscripción dependen de este valor).
            $esPrimeraMatricula = Matricula::where('user_id', $this->estudiante->id)
                ->where('tipo', '!=', Matricula::TIPO_VALIDACION)
                ->count() === 0;

            $carrera        = Carrera::find($this->carrera_id);
            $periodoActual  = Periodo::find($this->periodo_id);
            $carreraPeriodo = DB::table('carrera_periodo')
                ->where('carrera_id', $this->carrera_id)
                ->where('periodo_id', $this->periodo_id)
                ->first();
            $fechaFinPeriodo = Carbon::parse(
                $carreraPeriodo?->fecha_fin ?? $periodoActual?->fecha_fin ?? now()->addMonths(5)->toDateString()
            );
            $semestresTotal   = CalculoMatriculaService::semestresCarrera($carrera);
            $fraccionRestante = ($semestresTotal - $this->semestresYaCursados) / $semestresTotal;
            $semestres        = max(1, $semestresTotal - $this->semestresExonerados - $this->semestresYaCursados);

            // ------------------------------------------------------------------
            // MONTOS
            // ------------------------------------------------------------------
            $montoMatricula = CalculoMatriculaService::matriculaPorSemestre($carrera, $semestres, $fraccionRestante);

            // Base arancel: convalidación directa usa costo_convalidacion; track normal usa fracción restante
            if ($this->esConvalidacion
                && $carrera->costo_convalidacion !== null
                && (float) $carrera->costo_convalidacion > 0
            ) {
                $montoArancelBase = round((float) $carrera->costo_convalidacion * $fraccionRestante / $semestres, 2);
            } else {
                $montoArancelBase = round($carrera->costo_carrera * $fraccionRestante / $semestres, 2);
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

            // Convenio aplicable en el período de esta matrícula
            $convenioActivo = ConvenioAplicado::aplicablesEnPeriodo($this->estudiante->id, (int) $this->periodo_id)
                ->with('tipoConvenio')
                ->first();
            $pctConvenioGuardar = $convenioActivo ? (float) $convenioActivo->porcentaje_aplicado : 0;

            // Beca y convenio descuentan solo el arancel (el reintegro va aparte, sin descuento)
            $pctTotalGuardar      = min(100, $pctBecaGuardar + $pctConvenioGuardar);
            $descuentoBecaGuardar = round($montoArancelBase * ($pctTotalGuardar / 100), 2);

            $montoArancel = max(0, $montoArancelBase - $descuentoBecaGuardar);

            $costoArrastres   = 0;
            $detalleArrastres = [];
            foreach ($this->materiasArrastradas as $ma) {
                if ($ma['incluir'] ?? false) {
                    $costoArrastres    += $ma['costo_adicional'] ?? 0;
                    $detalleArrastres[] = ($ma['materia']['name'] ?? 'Materia') . ' ($' . number_format((float) ($ma['costo_adicional'] ?? 0), 2) . ')';
                }
            }

            // La matrícula y los arrastres se cobran completos (no hay descuento manual en el asistente)
            $montoFinalMatricula = $montoMatricula;
            $montoFinalArrastre  = $costoArrastres;

            // ------------------------------------------------------------------
            // CREAR MATRÍCULA
            // Una matrícula ya creada no se rehace: se corrige desde "Ver matrícula" (VerMatricula)
            // ------------------------------------------------------------------
            $matricula = Matricula::create([
                'fecha_matricula'   => now(),
                'code'              => $this->generarCodigoMatricula(),
                'tipo'              => $this->tipo,
                'estado'            => 'Pendiente_Pago',
                'observaciones'     => $this->observaciones,
                'periodo_id'        => $this->periodo_id,
                'carrera_id'        => $this->carrera_id,
                'user_id'           => $this->estudiante->id,
                'num_cuotas_arancel' => 5,
            ]);

            // ← AQUÍ: asignar matricula_numero si el estudiante no tiene uno aún
            if (! $this->estudiante->matricula_numero) {
                $ultimo = User::whereNotNull('matricula_numero')->max('matricula_numero');
                $siguiente = str_pad(($ultimo ? (int) substr($ultimo, 5) : 0) + 1, 5, '0', STR_PAD_LEFT);
                $this->estudiante->update(['matricula_numero' => 'ISTC-' . $siguiente]);
            }

            $this->convertirAspiranteEnEstudiante();

            // --------------------------------------------------------------
            // OBLIGACIÓN FINANCIERA: MATRÍCULA (pago inmediato)
            // Se genera aquí; el pago se registra desde la vista de obligaciones
            // --------------------------------------------------------------
            $obligMatricula = ObligacionesFinanciera::create([
                'user_id'          => $this->estudiante->id,
                'periodo_id'       => $this->periodo_id,
                'matricula_id'     => $matricula->id,
                'tipo'             => 'MATRICULA',
                'monto_original'   => $montoMatricula,
                'descuento'        => 0,
                'monto_final'      => $montoFinalMatricula,
                'estado'           => 'Pendiente',
                'fecha_vencimiento' => now()->addDays(5),
                'descripcion'      => 'Pago de matrícula - Período ' . $matricula->periodo_id,
            ]);

            // --------------------------------------------------------------
            // OBLIGACIÓN FINANCIERA: ARRASTRES (pago inmediato, requerido para habilitar)
            // Una sola obligación con el detalle de materias en la descripción
            // --------------------------------------------------------------
            if ($costoArrastres > 0) {
                ObligacionesFinanciera::create([
                    'user_id'           => $this->estudiante->id,
                    'periodo_id'        => $this->periodo_id,
                    'matricula_id'      => $matricula->id,
                    'tipo'              => 'ARRASTRE',
                    'monto_original'    => $costoArrastres,
                    'descuento'         => 0,
                    'monto_final'       => $montoFinalArrastre,
                    'estado'            => $montoFinalArrastre <= 0 ? 'Pagado' : 'Pendiente',
                    'fecha_vencimiento' => now()->addDays(5),
                    'descripcion'       => 'Arrastres - Período ' . $matricula->periodo_id . ': ' . implode(', ', $detalleArrastres),
                ]);
            }

            // --------------------------------------------------------------
            // OBLIGACIÓN FINANCIERA: REINTEGRO por retiro anterior
            // (pago inmediato, sin descuentos, requerido para habilitar)
            // --------------------------------------------------------------
            if ($retiroPendiente && $montoRecargo > 0) {
                ObligacionesFinanciera::create([
                    'user_id'           => $this->estudiante->id,
                    'periodo_id'        => $this->periodo_id,
                    'matricula_id'      => $matricula->id,
                    'retiro_id'         => $retiroPendiente->id,
                    'tipo'              => 'REINTEGRO',
                    'monto_original'    => $montoRecargo,
                    'descuento'         => 0,
                    'monto_final'       => $montoRecargo,
                    'estado'            => 'Pendiente',
                    'fecha_vencimiento' => now()->addDays(5),
                    'descripcion'       => 'Reintegro por retiro (10% del costo de la carrera) - Período ' . $matricula->periodo_id,
                ]);
            }
            $retiroPendiente?->update(['recargo_cobrado' => true]);

            // --------------------------------------------------------------
            // OBLIGACIÓN FINANCIERA: ARANCEL/COLEGIATURA del semestre
            // 5 cuotas con el descuento de beca/convenio.
            // --------------------------------------------------------------
            $this->crearCuotasColegiatura(
                $matricula,
                (int) $this->periodo_id,
                $montoArancelBase,
                $descuentoBecaGuardar,
                $montoArancel,
                $becaActiva,
                $pctConvenioGuardar > 0 ? $convenioActivo : null,
                $fechaFinPeriodo
            );

            // Un convenio semestral queda marcado con el período en que se usó:
            // vence al cerrar ese período y no vuelve a aplicarse en otro.
            if ($convenioActivo && $pctConvenioGuardar > 0 && $convenioActivo->esSemestral() && ! $convenioActivo->periodo_id) {
                $convenioActivo->update(['periodo_id' => $this->periodo_id]);
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

            $resultadoAdmision = $this->aplicarPagoAdmision($matricula, $obligMatricula);

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
            // WHATSAPP
            // El try independiente evita que un fallo del job (sync) rompa la UI
            // ------------------------------------------------------------------
            try {
                $whatsappActivo = SettingService::get('whatsapp.activo', '0') === '1';
                $notifWA        = SettingService::get('notificaciones.matricula_whatsapp', '0') === '1';

                if ($whatsappActivo && $notifWA) {
                    EnviarWhatsappMatricula::dispatch(
                        $matricula->id,
                        $esPrimeraMatricula
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('WhatsApp dispatch falló (no crítico)', [
                    'matricula_id' => $matricula->id,
                    'error'        => $e->getMessage(),
                ]);
            }

            // ------------------------------------------------------------------
            // EMAIL (el Job verifica internamente los settings)
            // ------------------------------------------------------------------
            try {
                EnviarEmailMatricula::dispatch($matricula->id, $esPrimeraMatricula);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Email dispatch falló (no crítico)', [
                    'matricula_id' => $matricula->id,
                    'error'        => $e->getMessage(),
                ]);
            }

            $this->cerrarModal();

            // Redirigir a la vista de obligaciones filtrando por la matrícula recién creada
            $estudianteId = $matricula->user_id;
            $caso         = null;
            $mensaje      = 'Matrícula creada. Proceda a registrar el pago.';

            if ($resultadoAdmision && $resultadoAdmision['estado'] === 'Pagado') {
                $caso    = 'admision_pagada';
                $mensaje = 'Se aplicó el pago de admisión ($' . number_format($resultadoAdmision['aplicado'], 2) . ') y la matrícula quedó habilitada.'
                    . ($resultadoAdmision['abonado'] > 0
                        ? ' El excedente ($' . number_format($resultadoAdmision['abonado'], 2) . ') se abonó a la colegiatura.'
                        : '');
            } elseif ($resultadoAdmision) {
                $caso    = 'admision_parcial';
                $mensaje = 'Se aplicó el pago de admisión ($' . number_format($resultadoAdmision['aplicado'], 2) . '). '
                    . 'Falta cobrar $' . number_format($resultadoAdmision['faltante'], 2) . ' de matrícula. '
                    . 'El estudiante puede subir su comprobante desde su portal; la matrícula se habilitará al completar el pago.';
            }

            $this->dispatch('matricula-guardada', [
                'mensaje'          => $mensaje,
                'matricula_id'     => $matricula->id,
                'redirigir'        => true,
                'caso'             => $caso,
                'url_pago'         => route('administracion.administrativa.matriculas.pago', $matricula->id),
                'url_obligaciones' => route('administracion.administrativa.obligaciones.index', ['estudiante' => $estudianteId]),
                'url_cobrar'       => route('administracion.administrativa.obligaciones.index', [
                    'estudiante' => $estudianteId,
                    'cobrar'     => $resultadoAdmision['obligacion_id'] ?? null,
                ]),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('error', ['mensaje' => 'Error al guardar la matrícula: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    /**
     * Código correlativo desde el contador 'matricula' (ver SecuenciaService). Se llama dentro
     * de la transacción de guardarMatricula, así que el número queda reservado hasta el commit.
     */
    private function generarCodigoMatricula(): string
    {
        $ultimoUsado = fn() => Matricula::withTrashed()->where('code', 'like', 'ISTC-MATRICULA-%')->pluck('code')
            ->map(fn($code) => preg_match('/^ISTC-MATRICULA-(\d+)$/', $code, $m) ? (int) $m[1] : 0)
            ->max() ?? 0;

        // Red de seguridad: si un código ya existe (cargado a mano), se salta al siguiente
        do {
            $code = sprintf('ISTC-MATRICULA-%04d', SecuenciaService::siguiente('matricula', $ultimoUsado));
        } while (Matricula::withTrashed()->where('code', $code)->exists());

        return $code;
    }

    private function generarCodigoDetalle(string $codigoMatricula, string $codigoMateria): string
    {
        return $codigoMatricula . '-' . $codigoMateria;
    }

    /**
     * Colegiatura en 5 cuotas: la primera vence en 5 días, la última al fin del período y las
     * intermedias se reparten entre ambas. La última absorbe el redondeo. Cada cuota guarda la
     * beca/convenio que la descontó (solo se pasa el convenio si realmente aplicó %).
     */
    private function crearCuotasColegiatura(
        Matricula $matricula,
        int $periodoId,
        float $montoBase,
        float $descuento,
        float $montoNeto,
        ?BecaAplicada $beca,
        ?ConvenioAplicado $convenio,
        Carbon $fechaFinPeriodo,
        string $concepto = 'Arancel semestral',
        ?string $periodoEtiqueta = null
    ): void {
        $periodoEtiqueta ??= (string) $periodoId;
        $numCuotas       = 5;
        $arancelRestante = $montoNeto;
        $baseRestante    = $montoBase;
        $descRestante    = $descuento;

        $fechaCuota1 = now()->addDays(5);
        if ($fechaFinPeriodo->lte($fechaCuota1)) {
            $fechaFinPeriodo = $fechaCuota1->copy()->addMonths(4);
        }
        $totalDias = (int) $fechaCuota1->diffInDays($fechaFinPeriodo);

        $descPartes = array_filter([
            $beca && (float) $beca->porcentaje_aplicado > 0 ? 'beca' : null,
            $convenio ? 'convenio' : null,
        ]);
        $descSufijo = count($descPartes) ? ' con ' . implode(' y ', $descPartes) : '';

        for ($cuota = 1; $cuota <= $numCuotas; $cuota++) {
            $esUltima = ($cuota === $numCuotas);

            // La última cuota absorbe el redondeo en los tres valores: original − descuento = final
            $montoEsta = $esUltima ? round($arancelRestante, 2) : round($montoNeto / $numCuotas, 2);
            $baseEsta  = $esUltima ? round($baseRestante, 2)    : round($montoBase / $numCuotas, 2);
            $descEsta  = $esUltima ? round($descRestante, 2)    : round($descuento / $numCuotas, 2);
            $arancelRestante -= $montoEsta;
            $baseRestante    -= $baseEsta;
            $descRestante    -= $descEsta;

            if ($cuota === 1) {
                $fechaVenc = $fechaCuota1->copy();
            } elseif ($esUltima) {
                $fechaVenc = $fechaFinPeriodo->copy();
            } else {
                $diasAdicionales = (int) round($totalDias * ($cuota - 1) / 4);
                $fechaVenc = $fechaCuota1->copy()->addDays($diasAdicionales);
            }

            ObligacionesFinanciera::create([
                'user_id'              => $matricula->user_id,
                'periodo_id'           => $periodoId,
                'matricula_id'         => $matricula->id,
                'tipo'                 => 'COLEGIATURA',
                'monto_original'       => $baseEsta,
                'descuento'            => $descEsta,
                'monto_final'          => $montoEsta,
                'estado'               => $montoEsta <= 0 ? 'Pagado' : 'Pendiente',
                'fecha_vencimiento'    => $fechaVenc->toDateString(),
                'descripcion'          => "{$concepto}{$descSufijo} - Período {$periodoEtiqueta} (Cuota {$cuota}/5)",
                'beca_aplicada_id'     => $beca?->id,
                'convenio_aplicado_id' => $convenio?->id,
            ]);
        }
    }

    // =========================================================================
    // SALDO DE VALIDACIÓN DE CONOCIMIENTOS
    // =========================================================================

    /**
     * Caso especial del saldo de validación (regla en SaldoValidacionService::detectar). El saldo
     * se asienta sobre la matrícula interna de validación: no se crea una matrícula vacía. Si ya
     * se generó, la tarjeta se sigue mostrando —con lo realmente generado— aunque la detección
     * haya dejado de aplicar, para poder revertirlo. Nunca reemplaza materias por inscribir.
     */
    private function detectarSaldoValidacion(): void
    {
        $this->saldoValidacion = null;
        if (! $this->estudiante || ! $this->carrera_id) return;
        if (! empty($this->materiasDisponibles)) return;

        $saldo = SaldoValidacionService::detectar($this->estudiante->id, (int) $this->carrera_id);

        if (! $saldo) {
            $matriculaValidacion = SaldoValidacionService::matriculaValidacion($this->estudiante->id, (int) $this->carrera_id);
            if (! $matriculaValidacion || ! SaldoValidacionService::yaGenerado($matriculaValidacion->id)) return;

            $saldo = [
                'matricula_id' => $matriculaValidacion->id,
                'codigo'       => $matriculaValidacion->code,
                'total'        => CalculoMatriculaService::semestresCarrera(Carrera::find($this->carrera_id)),
                'cursados'     => $this->semestresYaCursados,
                'exonerados'   => $this->semestresExonerados,
                'generado'     => true,
            ];
        }

        // Ya generado: mostrar lo que realmente se registró, no un recálculo
        if ($saldo['generado']) {
            $vigentes = SaldoValidacionService::obligacionesVigentes($saldo['matricula_id']);
            $saldo['generados'] = [
                'matricula'   => (float) $vigentes->where('tipo', 'MATRICULA')->sum('monto_final'),
                'colegiatura' => (float) $vigentes->where('tipo', 'COLEGIATURA')->sum('monto_final'),
                'descuento'   => (float) $vigentes->where('tipo', 'COLEGIATURA')->sum('descuento'),
                'reintegro'   => (float) $vigentes->where('tipo', 'REINTEGRO')->sum('monto_final'),
                'periodo'     => Periodo::find($vigentes->first()?->periodo_id)?->code,
            ];
        }

        $this->saldoValidacion = $saldo;
    }

    /**
     * Genera la matrícula (pago inmediato), el reintegro pendiente si lo hay y la colegiatura en
     * 5 cuotas del saldo, en el período seleccionado, con beca/convenio como cualquier
     * colegiatura. Todo se recalcula aquí: no se confía en lo mostrado en pantalla.
     */
    public function generarSaldoValidacion(): void
    {
        if ($this->sinPermiso('crear_matriculas')) return;
        if (! $this->estudiante || ! $this->carrera_id || ! $this->periodo_id) return;

        $this->detectarConvalidacion();
        $this->cargarMateriasDisponibles();
        $this->detectarSaldoValidacion();

        if (! $this->saldoValidacion) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'No aplica', 'text' => 'Este estudiante no tiene un saldo de validación por generar.']);
            return;
        }
        if ($this->saldoValidacion['generado']) {
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'Saldo ya generado', 'text' => 'Las obligaciones de este saldo ya existen.']);
            return;
        }

        $carrera        = Carrera::find($this->carrera_id);
        $periodoActual  = Periodo::find($this->periodo_id);
        $carreraPeriodo = DB::table('carrera_periodo')
            ->where('carrera_id', $this->carrera_id)
            ->where('periodo_id', $this->periodo_id)
            ->first();

        // El saldo debe quedar en un período de la carrera: si no, su cierre nunca lo procesaría
        if (! $carreraPeriodo) {
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Período no válido para la carrera',
                'text'  => 'El período seleccionado no pertenece a ' . $carrera->name . '. Vuelve al paso 1 y elige el período vigente de la carrera.',
            ]);
            return;
        }

        $fechaFinPeriodo = Carbon::parse(
            $carreraPeriodo->fecha_fin ?? $periodoActual?->fecha_fin ?? now()->addMonths(5)->toDateString()
        );

        $semestresTotal   = CalculoMatriculaService::semestresCarrera($carrera);
        $fraccionRestante = ($semestresTotal - $this->semestresYaCursados) / $semestresTotal;
        $semestres        = max(1, $semestresTotal - $this->semestresExonerados - $this->semestresYaCursados);

        $montoMatricula = CalculoMatriculaService::matriculaPorSemestre($carrera, $semestres, $fraccionRestante);
        $montoBase      = round($carrera->costo_carrera * $fraccionRestante / $semestres, 2);

        // Reintegro por un retiro aún sin cobrar: igual que en la matrícula normal (sin descuentos)
        $retiroPendiente = Retiro::where('user_id', $this->estudiante->id)->where('recargo_cobrado', false)->first();
        $montoRecargo    = $retiroPendiente ? round($carrera->costo_carrera * 0.10, 2) : 0;

        $beca     = BecaAplicada::where('user_id', $this->estudiante->id)->where('is_active', true)->first();
        $convenio = ConvenioAplicado::aplicablesEnPeriodo($this->estudiante->id, (int) $this->periodo_id)
            ->with('tipoConvenio')
            ->first();
        $pctBeca     = $beca ? (float) $beca->porcentaje_aplicado : 0;
        $pctConvenio = $convenio ? (float) $convenio->porcentaje_aplicado : 0;
        $descuento   = round($montoBase * (min(100, $pctBeca + $pctConvenio) / 100), 2);
        $montoNeto   = max(0, $montoBase - $descuento);

        $periodoCode = $periodoActual?->code ?? $this->periodo_id;

        try {
            // Todo o nada: si algo falla, no queda ninguna obligación a medias
            $resultado = DB::transaction(function () use ($montoMatricula, $montoBase, $descuento, $montoNeto, $beca, $convenio, $pctConvenio, $fechaFinPeriodo, $periodoCode, $retiroPendiente, $montoRecargo) {
                // Bloqueo de la fila: dos clics seguidos no deben generar el saldo dos veces
                $matriculaValidacion = Matricula::whereKey($this->saldoValidacion['matricula_id'])->lockForUpdate()->first();
                if (! $matriculaValidacion || SaldoValidacionService::yaGenerado($matriculaValidacion->id)) {
                    return 'ya_generado';
                }

                if ($montoMatricula > 0) {
                    ObligacionesFinanciera::create([
                        'user_id'           => $this->estudiante->id,
                        'periodo_id'        => $this->periodo_id,
                        'matricula_id'      => $matriculaValidacion->id,
                        'tipo'              => 'MATRICULA',
                        'monto_original'    => $montoMatricula,
                        'descuento'         => 0,
                        'monto_final'       => $montoMatricula,
                        'estado'            => 'Pendiente',
                        'fecha_vencimiento' => now()->addDays(5),
                        'descripcion'       => "Saldo de matrícula por validación de conocimientos - Período {$periodoCode}",
                    ]);
                }

                if ($retiroPendiente && $montoRecargo > 0) {
                    ObligacionesFinanciera::create([
                        'user_id'           => $this->estudiante->id,
                        'periodo_id'        => $this->periodo_id,
                        'matricula_id'      => $matriculaValidacion->id,
                        'retiro_id'         => $retiroPendiente->id,
                        'tipo'              => 'REINTEGRO',
                        'monto_original'    => $montoRecargo,
                        'descuento'         => 0,
                        'monto_final'       => $montoRecargo,
                        'estado'            => 'Pendiente',
                        'fecha_vencimiento' => now()->addDays(5),
                        'descripcion'       => "Reintegro por retiro (10% del costo de la carrera) - Período {$periodoCode}",
                    ]);
                    $retiroPendiente->update(['recargo_cobrado' => true]);
                }

                $this->crearCuotasColegiatura(
                    $matriculaValidacion,
                    (int) $this->periodo_id,
                    $montoBase,
                    $descuento,
                    $montoNeto,
                    $beca,
                    $pctConvenio > 0 ? $convenio : null,
                    $fechaFinPeriodo,
                    'Saldo por validación de conocimientos',
                    (string) $periodoCode
                );

                // Igual que en una matrícula: el convenio semestral queda usado en este período
                if ($convenio && $pctConvenio > 0 && $convenio->esSemestral() && ! $convenio->periodo_id) {
                    $convenio->update(['periodo_id' => $this->periodo_id]);
                }

                return 'ok';
            });
        } catch (\Throwable $e) {
            $this->reportarErrorSaldo('generar', $e);
            return;
        }

        $this->detectarSaldoValidacion();

        if ($resultado === 'ya_generado') {
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'Saldo ya generado', 'text' => 'Las obligaciones de este saldo ya existen.']);
            return;
        }

        $textoReintegro = $montoRecargo > 0 ? ', el reintegro ($' . number_format($montoRecargo, 2) . ')' : '';
        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Saldo generado',
            'text'  => 'Se generaron la matrícula ($' . number_format($montoMatricula, 2) . ')' . $textoReintegro . ' y la colegiatura ($'
                . number_format($montoNeto, 2) . ' en 5 cuotas). El estudiante ya puede verlas y pagarlas desde su portal.',
        ]);
    }

    /**
     * Deshace un saldo generado por error (período equivocado, beca que faltaba asignar…).
     * Se basa solo en que existan obligaciones del saldo, no en volver a detectar el caso.
     * Solo si no tiene pagos aprobados ni en revisión. Las obligaciones no se borran: quedan
     * Invalidadas con el motivo (y en la auditoría), y el saldo puede volver a generarse.
     */
    public function revertirSaldoValidacion(): void
    {
        if ($this->sinPermiso('cancelar_matriculas')) return;
        if (! $this->estudiante || ! $this->carrera_id) return;

        $motivo = trim($this->motivoReversionSaldo);
        if (mb_strlen($motivo) < 5) {
            $this->addError('motivoReversionSaldo', 'Indica el motivo de la reversión (mínimo 5 caracteres).');
            return;
        }

        $matriculaValidacion = SaldoValidacionService::matriculaValidacion($this->estudiante->id, (int) $this->carrera_id);
        if (! $matriculaValidacion || ! SaldoValidacionService::yaGenerado($matriculaValidacion->id)) {
            $this->detectarSaldoValidacion();
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'Nada que revertir', 'text' => 'Este estudiante no tiene un saldo de validación generado.']);
            return;
        }

        try {
            // Todo o nada: o se invalidan todas las obligaciones del saldo o ninguna
            $resultado = DB::transaction(function () use ($motivo, $matriculaValidacion) {
                Matricula::whereKey($matriculaValidacion->id)->lockForUpdate()->first();

                $obligaciones = SaldoValidacionService::obligacionesVigentes($matriculaValidacion->id);
                if ($obligaciones->isEmpty()) return 'sin_saldo';

                $conPagos = Pago::whereIn('obligacion_id', $obligaciones->pluck('id'))
                    ->whereIn('estado', [Pago::ESTADO_APROBADO, Pago::ESTADO_PENDIENTE, Pago::ESTADO_PROCESANDO])
                    ->exists();
                if ($conPagos) return 'con_pagos';

                // Uno por uno para que cada cambio quede en la auditoría
                foreach ($obligaciones as $obligacion) {
                    $obligacion->update([
                        'estado'      => 'Invalidado',
                        'descripcion' => $obligacion->descripcion . ' · Revertido: ' . $motivo,
                    ]);
                }

                // El reintegro vuelve a quedar pendiente de cobro
                foreach ($obligaciones->where('tipo', 'REINTEGRO')->pluck('retiro_id')->filter()->unique() as $retiroId) {
                    Retiro::find($retiroId)?->update(['recargo_cobrado' => false]);
                }

                // El convenio semestral que este saldo marcó como usado vuelve a quedar disponible,
                // salvo que otra obligación vigente de ese período también lo use
                foreach ($obligaciones->pluck('convenio_aplicado_id')->filter()->unique() as $convenioId) {
                    $convenio = ConvenioAplicado::with('tipoConvenio')->find($convenioId);
                    if (! $convenio || ! $convenio->esSemestral()) continue;

                    $periodosSaldo = $obligaciones->where('convenio_aplicado_id', $convenioId)->pluck('periodo_id')->unique();
                    if (! $periodosSaldo->contains($convenio->periodo_id)) continue;

                    $sigueUsado = ObligacionesFinanciera::where('convenio_aplicado_id', $convenioId)
                        ->where('periodo_id', $convenio->periodo_id)
                        ->where('estado', '!=', 'Invalidado')
                        ->exists();
                    if (! $sigueUsado) {
                        $convenio->update(['periodo_id' => null]);
                    }
                }

                return 'ok';
            });
        } catch (\Throwable $e) {
            $this->reportarErrorSaldo('revertir', $e);
            return;
        }

        if ($resultado === 'con_pagos') {
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'No se puede revertir',
                'text'  => 'El saldo ya tiene pagos registrados (aprobados o en revisión). Resuelve esos pagos antes de revertirlo.',
            ]);
            return;
        }

        $this->detectarSaldoValidacion();

        if ($resultado === 'sin_saldo') {
            $this->dispatch('swal', ['icon' => 'info', 'title' => 'Nada que revertir', 'text' => 'Las obligaciones de este saldo ya no están vigentes.']);
            return;
        }

        \Illuminate\Support\Facades\Log::info('Saldo de validación revertido', [
            'estudiante_id' => $this->estudiante->id,
            'matricula_id'  => $matriculaValidacion->id,
            'motivo'        => $motivo,
            'usuario_id'    => auth()->id(),
        ]);

        $this->motivoReversionSaldo = '';
        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Saldo revertido',
            'text'  => 'Las obligaciones quedaron invalidadas. Puedes volver a generar el saldo cuando lo necesites.',
        ]);
    }

    /**
     * Error inesperado al generar o revertir el saldo: la transacción ya deshizo todo. Se
     * registra el detalle técnico en el log y al usuario se le muestra un mensaje claro.
     */
    private function reportarErrorSaldo(string $accion, \Throwable $e): void
    {
        \Illuminate\Support\Facades\Log::error("Saldo de validación: error al {$accion}", [
            'estudiante_id' => $this->estudiante?->id,
            'carrera_id'    => $this->carrera_id,
            'periodo_id'    => $this->periodo_id,
            'error'         => $e->getMessage(),
            'archivo'       => $e->getFile() . ':' . $e->getLine(),
        ]);

        $this->dispatch('swal', [
            'icon'  => 'error',
            'title' => $accion === 'generar' ? 'No se pudo generar el saldo' : 'No se pudo revertir el saldo',
            'text'  => app()->isLocal()
                ? $e->getMessage()
                : 'No se realizó ningún cambio. Inténtalo nuevamente y, si el problema continúa, contacta al administrador del sistema.',
            'timer' => 6000,
        ]);
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
            'carrera_id',
            'periodo_id',
            'tipo',
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
            'costoArrastres',
            'valorInscripcion',
            'esConvalidacion',
            'semestresExonerados',
            'semestresYaCursados',
            'pendienteConvalidacion',
            'saldoValidacion',
            'motivoReversionSaldo',
        ]);
    }

    private function totalMateriasActual(): int
    {
        return count($this->materiasSeleccionadas)
            + count(array_filter($this->materiasArrastradas, fn($m) => $m['incluir'] ?? false));
    }

    private function detectarConvalidacion(): void
    {
        $estado = ($this->estudiante && $this->carrera_id)
            ? SaldoValidacionService::estadoConvalidacion($this->estudiante->id, (int) $this->carrera_id)
            : ['es_convalidacion' => false, 'exonerados' => 0, 'cursados' => 0];

        // Casos 1/2/3 de validación de conocimientos: ver SaldoValidacionService::estadoConvalidacion
        $this->esConvalidacion     = $estado['es_convalidacion'];
        $this->semestresExonerados = $estado['exonerados'];
        $this->semestresYaCursados = $estado['cursados'];
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
        $this->retiroTipo             = '';
        $this->retiroMotivo           = '';
        $this->showRetiroModal        = true;
    }

    public function cerrarRetiro(): void
    {
        $this->showRetiroModal        = false;
        $this->retiroMatriculaId      = null;
        $this->retiroEstudianteNombre = '';
        $this->retiroFecha            = '';
        $this->retiroTipo             = '';
        $this->retiroMotivo           = '';
        $this->retiroDocumento        = null;
        $this->resetValidation(['retiroFecha', 'retiroTipo', 'retiroMotivo', 'retiroDocumento']);
    }

    public function confirmarRetiro(): void
    {
        if ($this->sinPermiso('cancelar_matriculas')) return;

        $this->validate([
            'retiroFecha'      => 'required|date',
            'retiroTipo'       => 'required|in:Voluntario,Administrativo',
            'retiroMotivo'     => 'nullable|string|max:1000',
            'retiroDocumento'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'retiroTipo.required'      => 'Debes seleccionar el tipo de retiro.',
            'retiroTipo.in'            => 'El tipo de retiro no es válido.',
            'retiroDocumento.required' => 'Debes adjuntar la solicitud o certificado de retiro.',
            'retiroDocumento.mimes'    => 'El documento debe ser PDF, JPG o PNG.',
            'retiroDocumento.max'      => 'El documento no puede superar los 5 MB.',
        ]);

        $matricula = Matricula::with('detalles', 'estudiante')->find($this->retiroMatriculaId);
        if (! $matricula) {
            $this->cerrarRetiro();
            return;
        }

        // Un pago en revisión sobre una obligación invalidada quedaría sin a dónde aplicarse
        $pagosEnRevision = Pago::whereHas('obligacion', fn($q) => $q->where('matricula_id', $matricula->id))
            ->whereIn('estado', [Pago::ESTADO_PENDIENTE, Pago::ESTADO_PROCESANDO])
            ->exists();
        if ($pagosEnRevision) {
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Hay pagos en revisión',
                'text'  => 'Esta matrícula tiene pagos subidos por el estudiante que aún no se revisan. Apruébalos o recházalos antes de registrar el retiro.',
            ]);
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
                    'tipo'            => $this->retiroTipo,
                    'motivo'          => $this->retiroMotivo ?: null,
                    'documento_path'  => $documentoPath,
                    'recargo_cobrado' => false,
                    'registrado_por'  => auth()->id(),
                ]);

                $matricula->update(['estado' => 'Retirada']);

                $matricula->detalles()->update(['estado' => 'Retirado']);

                // Los arrastres que cursaba en esta matrícula vuelven a quedar pendientes:
                // sin nota nunca saldrían de 'Inscrita' y no se le volverían a ofrecer
                $arrastres = $matricula->detalles->where('tipo', 'Arrastre')->pluck('materia_id');
                if ($arrastres->isNotEmpty()) {
                    MateriasArrastrada::where('user_id', $matricula->user_id)
                        ->whereIn('materia_id', $arrastres)
                        ->where('estado', 'Inscrita')
                        ->update(['estado' => 'Arrastrada']);
                }

                // Uno por uno para que cada cambio quede en la auditoría
                ObligacionesFinanciera::where('matricula_id', $matricula->id)
                    ->whereIn('estado', ['Pendiente', 'Parcial', 'Vencido'])
                    ->get()
                    ->each(fn($obligacion) => $obligacion->update(['estado' => 'Invalidado']));

                // Al retirarse pierde becas y convenios (de cualquier tipo); si se reintegra,
                // se le vuelven a asignar manualmente. Quedan en su historial.
                BeneficiosService::desactivarTodos($matricula->user_id, 'retiro', "Matrícula {$matricula->code}");
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
    #[On('matricula-modificada')]
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
            'estudiantes'               => $this->estudiantes,
            'periodos'                  => Periodo::all(),
            'carreras'                  => Carrera::all(),
            'semestres'                 => Semestre::all(),
            'totalMateriasSeleccionadas' => $this->totalMateriasActual(),
        ]);
    }
}
