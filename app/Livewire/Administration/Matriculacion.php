<?php

namespace App\Livewire\Administration;

use App\Models\User;
use App\Models\Carrera;
use App\Models\Periodo;
use App\Models\Semestre;
use App\Models\Materia;
use App\Models\Paralelo;
use App\Models\Matricula;
use App\Models\DetalleMatricula;
use App\Models\AsignacionDocente;
use App\Models\MateriasArrastrada;
use App\Models\Pago;
use App\Models\ObligacionesFinanciera;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use App\Jobs\EnviarEmailMatricula;
use App\Jobs\EnviarWhatsappMatricula;
use App\Services\SettingService;
use App\Traits\WithAuthorization;

class Matriculacion extends Component
{
    use WithPagination, WithAuthorization;

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

    // -------------------------------------------------------------------------
    // CÁLCULOS
    // -------------------------------------------------------------------------
    public $totalCreditos = 0;
    public $costoTotal    = 0;
    public $totalPagar    = 0;

    // Costos calculados automáticamente según carrera
    public $montoMatricula  = 0; // (costo_carrera * 10%) / duracion_semestres
    public $montoArancel    = 0; // costo_carrera / duracion_semestres
    public $montoCostoTotal = 0; // montoMatricula + costoArrastres

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
        return User::role('estudiante')
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
            ->when(
                $this->selectedCarrera || $this->selectedPeriodo,
                fn($q) => $q->whereHas('matriculas', fn($q2) => $q2
                    ->when($this->selectedCarrera, fn($q3) => $q3->where('carrera_id', $this->selectedCarrera))
                    ->when($this->selectedPeriodo, fn($q3) => $q3->where('periodo_id', $this->selectedPeriodo))
                )
            )
            ->with([
                'matriculas' => fn($q) => $q
                    ->when($this->selectedCarrera, fn($q2) => $q2->where('carrera_id', $this->selectedCarrera))
                    ->when($this->selectedPeriodo, fn($q2) => $q2->where('periodo_id', $this->selectedPeriodo))
                    ->latest(),
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
        if ($this->sinPermiso('gestionar_matriculas')) return;

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
        if ($this->sinPermiso('gestionar_matriculas')) return;

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

        $carrera = Carrera::with('semestres.materias')->find($this->carrera_id);

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

                $materiasSemestre[] = [
                    'id'                     => $materia->id,
                    'name'                   => $materia->name,
                    'code'                   => $materia->code,
                    'credits'                => ($materia->horas_teoricas + $materia->horas_practicas) / 48,
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

        foreach ($todasLasMaterias as $materiaId) {
            $paralelos = Paralelo::whereHas('asignacionesDocentes', function ($query) use ($materiaId) {
                $query->where('materia_id', $materiaId)
                    ->where('periodo_id', $this->periodo_id);
            })
                ->where('is_active', true)
                ->get()
                ->map(fn($paralelo) => [
                    'id'              => $paralelo->id,
                    'name'            => $paralelo->name,
                    'cupo_disponible' => $paralelo->cupo_maximo - $paralelo->cupo_actual,
                    'tiene_cupo'      => $paralelo->tieneCupoDisponible(),
                ]);

            $this->paralelosDisponibles[$materiaId] = $paralelos;
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
     * Matrícula  = (costo_carrera × 10%) / duracion_semestres
     * Arancel    = costo_carrera / duracion_semestres
     */
    public function calcularMontosPorCarrera()
    {
        $carrera = Carrera::find($this->carrera_id);
        if (! $carrera) return;

        $semestres = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;

        $this->montoMatricula = round(($carrera->costo_carrera * 0.10) / $semestres, 2);
        $this->montoArancel   = round($carrera->costo_carrera / $semestres, 2);
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
        $this->montoArancel   = round($carrera->costo_carrera / $semestres, 2);

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
                $porcentaje     = floatval($materiaArrastrada['porcentaje_penalizacion'] ?? 5) / 100;
                $costoAdicional = round($materia->credits * $carrera->costo_credito * $porcentaje, 2);
                $materiaArrastrada['costo_adicional'] = $costoAdicional;
            }

            $costoArrastres += $costoAdicional;
        }
        unset($materiaArrastrada);

        // División única al final: suma de horas brutas / 48
        $this->totalCreditos = $totalHoras / 48;

        // El total que aparece en el resumen = matrícula + arrastres
        $this->costoTotal  = $this->montoMatricula + $costoArrastres;
        $this->totalPagar  = max(0, $this->costoTotal - $this->descuento);
    }

    // =========================================================================
    // GUARDAR MATRÍCULA
    // =========================================================================
    public function guardarMatricula()
    {
        if ($this->sinPermiso('gestionar_matriculas')) return;

        $this->validate([
            'carrera_id'  => 'required|exists:carreras,id',
            'periodo_id'  => 'required|exists:periodos,id',
            'descuento'   => 'numeric|min:0|max:' . $this->costoTotal,
        ]);

        try {
            DB::beginTransaction();

            $esEdicion = (bool) $this->matriculaId;

            $carrera   = Carrera::find($this->carrera_id);
            $semestres = $carrera->duracion_semestres > 0 ? $carrera->duracion_semestres : 1;

            // ------------------------------------------------------------------
            // MONTOS
            // ------------------------------------------------------------------
            $montoMatricula = round(($carrera->costo_carrera * 0.10) / $semestres, 2);
            $montoArancel   = round($carrera->costo_carrera / $semestres, 2);

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
                    'fecha_matricula' => now(),
                    'code'            => $this->generarCodigoMatricula(),
                    'tipo'            => $this->tipo,
                    'estado'          => 'Pendiente_Pago',
                    'observaciones'   => $this->observaciones,
                    'periodo_id'      => $this->periodo_id,
                    'carrera_id'      => $this->carrera_id,
                    'user_id'         => $this->estudiante->id,
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
                // No es inmediata; se gestiona desde el componente de pagos
                // --------------------------------------------------------------
                ObligacionesFinanciera::create([
                    'user_id'          => $this->estudiante->id,
                    'periodo_id'       => $this->periodo_id,
                    'matricula_id'     => $matricula->id,
                    'tipo'             => 'COLEGIATURA',
                    'monto_original'   => $montoArancel,
                    'descuento'        => 0,
                    'monto_final'      => $montoArancel,
                    'estado'           => 'Pendiente',
                    'fecha_vencimiento' => now()->addDays(30),
                    'descripcion'      => 'Arancel semestral - Período ' . $matricula->periodo_id,
                ]);
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
                    'costo_materia' => $materia->credits * $carrera->costo_credito,
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

            // Incrementar cupo una sola vez por paralelo único (no una vez por materia)
            $paralelosUsados->unique()->each(
                fn($paraleloId) => Paralelo::find($paraleloId)?->increment('cupo_actual')
            );

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
            'paso',
            'totalCreditos',
            'costoTotal',
            'totalPagar',
            'montoMatricula',
            'montoArancel',
        ]);
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
