<?php

namespace App\Livewire\Administration;

use App\Models\AuditoriaGeneral;
use App\Models\Carrera;
use App\Models\Comunitaria;
use App\Models\NotaTitulacion;
use App\Models\PracticaPreprofesional;
use App\Models\User;
use App\Services\BeneficiosService;
use App\Services\EstadoAcademicoService;
use App\Services\SaldoValidacionService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Traits\WithAuthorization;

class ProcesoTitulacion extends Component
{
    use WithPagination, WithAuthorization;

    // -------------------------------------------------------------------------
    // FILTROS DEL INDEX
    // -------------------------------------------------------------------------
    public $busqueda        = '';
    public $soloAptos       = false;
    public $showDropdown    = false;

    // -------------------------------------------------------------------------
    // MODAL + WIZARD
    // -------------------------------------------------------------------------
    public $showModal       = false;
    public $paso            = 1; // 1: Prácticas | 2: Titulación | 3: Resumen
    public $estudianteId    = null;
    public $estudianteNombre = '';
    public $carreraId       = null;

    // Datos calculados (solo lectura en modal)
    public $mallaCompleta       = false;
    public $promedioMalla       = null;
    public $semestresDetalle    = [];
    public int $semestresOk     = 0;
    public int $semestresTotal  = 0;
    public bool $obligacionesAlDia = true; // sin ningún saldo pendiente
    public float $saldoPendiente   = 0;
    public bool $exigeSinSaldo     = false; // Ajustes → Matrícula: el saldo bloquea la titulación
    public string $estadoAcademico = '';

    // Revertir egreso (marcado por error o antes de generar su saldo)
    public string $motivoReversionEgreso = '';
    public bool $reactivarBeneficios     = false;

    // -------------------------------------------------------------------------
    // PASO 1 — PRÁCTICAS PREPROFESIONALES
    // -------------------------------------------------------------------------
    public $practicaId              = null; // null = crear nueva
    public $practicaEmpresa         = '';
    public $practicaSector          = '';
    public $practicaDireccion       = '';
    public $practicaTutorEmpresa    = '';
    public $practicaCargoTutor      = '';
    public $practicaTelefono        = '';
    public $practicaEmail           = '';
    public $practicaCargoEstudiante = '';
    public $practicaActividades     = '';
    public $practicaFechaInicio     = '';
    public $practicaFechaFin        = '';
    public $practicaTotalHoras      = '';
    public $practicaNota            = '';
    public $practicaEstado          = 'En_Curso';
    public $practicaObservaciones   = '';

    // -------------------------------------------------------------------------
    // PASO 2 — TITULACIÓN
    // -------------------------------------------------------------------------
    public $titulacionId            = null;
    public $titulacionTipo          = 'Examen_Complexivo';
    public $titulacionNota          = '';
    public $titulacionPresidente    = '';
    public $titulacionMiembro1      = '';
    public $titulacionMiembro2      = '';
    public $titulacionFechaRegistro = '';
    public $titulacionFechaEval     = '';
    public $titulacionObservaciones = '';

    // =========================================================================
    // COMPUTED — TABLA INDEX
    // =========================================================================
    #[Computed]
    public function estudiantes()
    {
        return User::role('estudiante')
            ->when($this->soloAptos,
                // Solo malla completa = solo Egresados (estado confirma malla terminada)
                fn($q) => $q->whereIn('estado_academico', ['Egresado']),
                // Todos: Egresados siempre + Activos con matrícula Habilitada
                fn($q) => $q->where(function ($q2) {
                    $q2->whereIn('estado_academico', ['Egresado'])
                       ->orWhere(fn($q3) =>
                           $q3->where('estado_academico', 'Activo')
                              ->whereHas('matriculas', fn($mq) => $mq->where('estado', 'Habilitada'))
                       );
                })
            )
            ->when(strlen($this->busqueda) >= 3, function ($q) {
                $q->where(
                    fn($q2) =>
                    $q2->where('name',             'like', '%' . $this->busqueda . '%')
                        ->orWhere('cedula',          'like', '%' . $this->busqueda . '%')
                        ->orWhere('matricula_numero', 'like', '%' . $this->busqueda . '%')
                );
            })
            ->with([
                // Carga la última matrícula sin importar estado — Egresados no tienen Habilitada
                'matriculas' => fn($q) => $q->with('carrera')->latest(),
            ])
            ->orderBy('name')
            ->paginate(12);
    }

    // =========================================================================
    // HELPERS DE TABLA — calculados por fila sin N+1
    // =========================================================================
    public function estadoMallaEstudiante(int $userId, int $carreraId): array
    {
        return EstadoAcademicoService::estadoMalla($userId, $carreraId);
    }

    public function estadoPracticaEstudiante(int $userId, int $carreraId): ?PracticaPreprofesional
    {
        return PracticaPreprofesional::where('user_id', $userId)
            ->where('carrera_id', $carreraId)
            ->latest()
            ->first();
    }

    public function estadoTitulacionEstudiante(int $userId, int $carreraId): ?NotaTitulacion
    {
        return NotaTitulacion::where('user_id', $userId)
            ->where('carrera_id', $carreraId)
            ->latest('numero_intento')
            ->first();
    }

    // =========================================================================
    // ABRIR MODAL
    // =========================================================================
    public function abrirModal(int $estudianteId)
    {
        $this->resetModal();

        $estudiante = User::with([
            // Sin filtro de estado — Egresados tienen su última matrícula en estado anterior
            'matriculas' => fn($q) => $q->with('carrera')->latest(),
        ])->find($estudianteId);

        if (! $estudiante) return;

        // Preferimos la matrícula Habilitada; si no existe (Egresado), tomamos la última
        $matricula = $estudiante->matriculas->firstWhere('estado', 'Habilitada')
            ?? $estudiante->matriculas->first();
        if (! $matricula) return;

        $this->estudianteId     = $estudianteId;
        $this->estudianteNombre = $estudiante->name;
        $this->carreraId        = $matricula->carrera_id;
        $this->estadoAcademico  = (string) $estudiante->estado_academico;

        // Calcular malla
        $estadoMalla = $this->estadoMallaEstudiante($estudianteId, $this->carreraId);
        $this->mallaCompleta    = $estadoMalla['completa'];
        $this->semestresOk      = $estadoMalla['semestres_ok'];
        $this->semestresTotal   = $estadoMalla['semestres_total'];
        $this->promedioMalla    = NotaTitulacion::calcularPromedioMalla($estudianteId, $this->carreraId);
        $this->semestresDetalle = $this->calcularDetalleSemestres($estudianteId, $this->carreraId);

        // Saldo pendiente (vencido o no): bloquea la titulación solo si está activo en Ajustes
        $this->saldoPendiente    = EstadoAcademicoService::saldoPendiente($estudianteId);
        $this->obligacionesAlDia = $this->saldoPendiente <= 0;
        $this->exigeSinSaldo     = EstadoAcademicoService::exigeSinSaldoParaTitulacion();

        // Cargar práctica si existe
        $practica = $this->estadoPracticaEstudiante($estudianteId, $this->carreraId);
        if ($practica) $this->cargarPractica($practica);

        // Cargar titulación si existe
        $titulacion = $this->estadoTitulacionEstudiante($estudianteId, $this->carreraId);
        if ($titulacion) $this->cargarTitulacion($titulacion);

        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    // =========================================================================
    // PASO 1 — GUARDAR PRÁCTICA
    // =========================================================================
    public function guardarPractica()
    {
        if ($this->sinPermiso('gestionar_titulacion')) return;

        $this->validate([
            'practicaEmpresa'         => 'required|string|max:255',
            'practicaTutorEmpresa'    => 'required|string|max:255',
            'practicaCargoEstudiante' => 'required|string|max:255',
            'practicaFechaInicio'     => 'required|date',
            'practicaFechaFin'        => 'nullable|date|after_or_equal:practicaFechaInicio',
            'practicaTotalHoras'      => 'nullable|integer|min:1',
            'practicaNota'            => 'nullable|numeric|min:0|max:10',
            'practicaEstado'          => 'required|in:En_Curso,Completada,Reprobada',
        ], [
            'practicaEmpresa.required'         => 'El nombre de la empresa es requerido.',
            'practicaTutorEmpresa.required'    => 'El tutor de empresa es requerido.',
            'practicaCargoEstudiante.required' => 'El cargo del estudiante es requerido.',
            'practicaFechaInicio.required'     => 'La fecha de inicio es requerida.',
        ]);

        // Validación de horas mínimas y determinación de estado según nota
        $carrera  = Carrera::find($this->carreraId);
        $horasMin = $carrera ? $carrera->horasMinPreprofesional() : 240;
        $nota     = $this->practicaNota !== '' ? (float) $this->practicaNota : null;
        $horas    = (int) ($this->practicaTotalHoras ?: 0);

        if ($nota !== null && $nota >= 7 && $horas < $horasMin) {
            $this->addError('practicaTotalHoras', "Se requieren mínimo {$horasMin} horas para {$carrera->tipo_label} ({$horas} registradas).");
            return;
        }

        $estadoCalculado = $this->practicaEstado;
        if ($nota !== null) {
            if ($nota >= 7 && $horas >= $horasMin) {
                $estadoCalculado = 'Completada';
            } elseif ($nota < 7) {
                $estadoCalculado = 'Reprobada';
            }
        }
        $this->practicaEstado = $estadoCalculado;

        try {
            DB::beginTransaction();

            $datos = [
                'user_id'             => $this->estudianteId,
                'carrera_id'          => $this->carreraId,
                'empresa'             => $this->practicaEmpresa,
                'sector'              => $this->practicaSector ?: null,
                'direccion_empresa'   => $this->practicaDireccion ?: null,
                'tutor_empresa'       => $this->practicaTutorEmpresa,
                'cargo_tutor_empresa' => $this->practicaCargoTutor ?: null,
                'telefono_empresa'    => $this->practicaTelefono ?: null,
                'email_empresa'       => $this->practicaEmail ?: null,
                'cargo_estudiante'    => $this->practicaCargoEstudiante,
                'actividades_realizadas' => $this->practicaActividades ?: null,
                'fecha_inicio'        => $this->practicaFechaInicio,
                'fecha_fin'           => $this->practicaFechaFin ?: null,
                'total_horas'         => $horas,
                'nota'                => $nota,
                'estado'              => $estadoCalculado,
                'observaciones'       => $this->practicaObservaciones ?: null,
            ];

            if ($this->practicaId) {
                PracticaPreprofesional::find($this->practicaId)?->update($datos);
            } else {
                $practica = PracticaPreprofesional::create($datos);
                $this->practicaId = $practica->id;
            }

            // Si la práctica quedó completada, sincronizar nota en titulación si existe
            if ($this->titulacionId && $estadoCalculado === 'Completada' && $nota !== null) {
                $comunitaria = Comunitaria::where('user_id', $this->estudianteId)
                    ->where('carrera_id', $this->carreraId)
                    ->where('estado', 'Completada')
                    ->latest()
                    ->first();

                $titulacion = NotaTitulacion::find($this->titulacionId);
                if ($titulacion) {
                    $titulacion->practica_id    = $this->practicaId;
                    $titulacion->nota_practicas  = $nota;
                    $titulacion->comunitaria_id  = $comunitaria?->id;
                    $titulacion->save();
                    $titulacion->recalcularNotaFinal();
                }
            }

            DB::commit();

            $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Prácticas guardadas correctamente.']);
            $this->paso = 2;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('practica_general', 'Error: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PASO 2 — GUARDAR TITULACIÓN
    // =========================================================================
    public function guardarTitulacion()
    {
        if (! $this->mallaCompleta) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'El estudiante no ha completado la malla curricular.']);
            return;
        }

        // Se recalcula aquí: el estudiante pudo pagar (o generarse un cobro) con el modal abierto
        $this->saldoPendiente    = EstadoAcademicoService::saldoPendiente((int) $this->estudianteId);
        $this->obligacionesAlDia = $this->saldoPendiente <= 0;
        $this->exigeSinSaldo     = EstadoAcademicoService::exigeSinSaldoParaTitulacion();
        if ($this->exigeSinSaldo && ! $this->obligacionesAlDia) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'El estudiante tiene $' . number_format($this->saldoPendiente, 2) . ' pendientes. Debe estar sin saldo para registrar la titulación.']);
            return;
        }

        $this->validate([
            'titulacionTipo'          => 'required|in:Examen_Complexivo,Proyecto_Investigacion',
            'titulacionNota'          => 'nullable|numeric|min:0|max:10',
            'titulacionFechaRegistro' => 'nullable|date',
            'titulacionFechaEval'     => 'nullable|date',
            'titulacionPresidente'    => 'nullable|string|max:255',
            'titulacionMiembro1'      => 'nullable|string|max:255',
            'titulacionMiembro2'      => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $notaPracticas = $this->practicaNota !== '' && $this->practicaEstado === 'Completada'
                ? $this->practicaNota
                : null;

            $comunitaria = Comunitaria::where('user_id', $this->estudianteId)
                ->where('carrera_id', $this->carreraId)
                ->latest()
                ->first();

            $datos = [
                'user_id'               => $this->estudianteId,
                'carrera_id'            => $this->carreraId,
                'practica_id'           => $this->practicaId,
                'comunitaria_id'        => $comunitaria?->id,
                'promedio_malla'        => $this->promedioMalla,
                'tipo_titulacion'       => $this->titulacionTipo,
                'nota_titulacion'       => $this->titulacionNota !== '' ? $this->titulacionNota : null,
                'nota_practicas'        => $notaPracticas,
                'presidente_tribunal'   => $this->titulacionPresidente ?: null,
                'miembro_tribunal_1'    => $this->titulacionMiembro1 ?: null,
                'miembro_tribunal_2'    => $this->titulacionMiembro2 ?: null,
                'fecha_registro'        => $this->titulacionFechaRegistro ?: now()->toDateString(),
                'fecha_evaluacion'      => $this->titulacionFechaEval ?: null,
                'observaciones'         => $this->titulacionObservaciones ?: null,
            ];

            if ($this->titulacionId) {
                $titulacion = NotaTitulacion::find($this->titulacionId);
                $titulacion->update($datos);
            } else {
                $datos['numero_intento'] = NotaTitulacion::proximoIntento($this->estudianteId, $this->carreraId);
                $titulacion = NotaTitulacion::create($datos);
                $this->titulacionId = $titulacion->id;
            }

            // Recalcular nota final si todas las notas están disponibles
            $titulacion->recalcularNotaFinal();

            DB::commit();

            $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => 'Proceso de titulación guardado.']);
            $this->paso = 3;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('titulacion_general', 'Error: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================
    private function calcularDetalleSemestres(int $userId, int $carreraId): array
    {
        $carrera = Carrera::with('semestres.materias')->find($carreraId);
        if (! $carrera) return [];

        $detalle = [];

        foreach ($carrera->semestres->sortBy('order') as $semestre) {
            $notas = [];
            foreach ($semestre->materias as $materia) {
                $cal = \App\Models\Calificacion::whereHas(
                    'detalleMatricula',
                    fn($q) =>
                    $q->where('user_id', $userId)->where('materia_id', $materia->id)
                )->whereNotNull('nota_final')
                    ->orderByDesc('numero_intento')
                    ->first();

                if ($cal) $notas[] = (float) $cal->nota_final;
            }

            $promedio = ! empty($notas)
                ? round(array_sum($notas) / count($notas), 2)
                : null;

            $detalle[] = [
                'semestre'  => $semestre->name,
                'materias'  => count($semestre->materias),
                'con_notas' => count($notas),
                'promedio'  => $promedio,
            ];
        }

        return $detalle;
    }

    private function cargarPractica(PracticaPreprofesional $p): void
    {
        $this->practicaId              = $p->id;
        $this->practicaEmpresa         = $p->empresa;
        $this->practicaSector          = $p->sector ?? '';
        $this->practicaDireccion       = $p->direccion_empresa ?? '';
        $this->practicaTutorEmpresa    = $p->tutor_empresa;
        $this->practicaCargoTutor      = $p->cargo_tutor_empresa ?? '';
        $this->practicaTelefono        = $p->telefono_empresa ?? '';
        $this->practicaEmail           = $p->email_empresa ?? '';
        $this->practicaCargoEstudiante = $p->cargo_estudiante;
        $this->practicaActividades     = $p->actividades_realizadas ?? '';
        $this->practicaFechaInicio     = $p->fecha_inicio?->format('Y-m-d') ?? '';
        $this->practicaFechaFin        = $p->fecha_fin?->format('Y-m-d') ?? '';
        $this->practicaTotalHoras      = $p->total_horas ?? '';
        $this->practicaNota            = $p->nota ?? '';
        $this->practicaEstado          = $p->estado;
        $this->practicaObservaciones   = $p->observaciones ?? '';
    }

    private function cargarTitulacion(NotaTitulacion $t): void
    {
        $this->titulacionId            = $t->id;
        $this->titulacionTipo          = $t->tipo_titulacion;
        $this->titulacionNota          = $t->nota_titulacion ?? '';
        $this->titulacionPresidente    = $t->presidente_tribunal ?? '';
        $this->titulacionMiembro1      = $t->miembro_tribunal_1 ?? '';
        $this->titulacionMiembro2      = $t->miembro_tribunal_2 ?? '';
        $this->titulacionFechaRegistro = $t->fecha_registro?->format('Y-m-d') ?? '';
        $this->titulacionFechaEval     = $t->fecha_evaluacion?->format('Y-m-d') ?? '';
        $this->titulacionObservaciones = $t->observaciones ?? '';
    }

    // =========================================================================
    // MARCAR EGRESADO
    // =========================================================================
    public function marcarEgresado(): void
    {
        if ($this->sinPermiso('gestionar_titulacion')) return;

        if (! $this->mallaCompleta) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'El estudiante debe tener la malla completa para ser marcado como Egresado.']);
            return;
        }

        $estudiante = User::find($this->estudianteId);
        if (! $estudiante) return;

        if ($estudiante->estado_academico !== 'Activo') {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => "El estudiante ya está como {$estudiante->estado_academico}."]);
            $this->estadoAcademico = (string) $estudiante->estado_academico;
            return;
        }

        // Un Egresado ya no aparece para matricular y pierde becas/convenios: su saldo de
        // validación de conocimientos tiene que generarse antes
        $saldoValidacion = SaldoValidacionService::detectar($estudiante->id, (int) $this->carreraId);
        if ($saldoValidacion && ! $saldoValidacion['generado']) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'Primero genera su saldo de validación de conocimientos en Matriculación. Después podrás marcarlo como Egresado.']);
            return;
        }

        DB::transaction(function () use ($estudiante) {
            $estudiante->update(['estado_academico' => 'Egresado']);
            // Ya no tendrá más matrículas: sus becas y convenios terminan (quedan en el historial)
            BeneficiosService::desactivarTodos($estudiante->id, 'egreso');
        });

        $this->estadoAcademico = 'Egresado';
        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => "{$estudiante->name} ha sido marcado como Egresado correctamente."]);
        unset($this->estudiantes);
    }

    // =========================================================================
    // REVERTIR EGRESO (marcado por error o antes de generar su saldo)
    // =========================================================================
    public function revertirEgreso(): void
    {
        if ($this->sinPermiso('gestionar_titulacion')) return;

        $this->resetErrorBag('motivoReversionEgreso');
        $motivo = trim($this->motivoReversionEgreso);
        if (mb_strlen($motivo) < 5) {
            $this->addError('motivoReversionEgreso', 'Indica el motivo (mínimo 5 caracteres).');
            return;
        }

        // Con titulación registrada el proceso ya avanzó: no se revierte desde aquí
        if (NotaTitulacion::where('user_id', $this->estudianteId)->where('carrera_id', $this->carreraId)->exists()) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'El estudiante ya tiene una titulación registrada. No se puede revertir su egreso.']);
            return;
        }

        $resultado = DB::transaction(function () use ($motivo) {
            $estudiante = User::lockForUpdate()->find($this->estudianteId);
            if (! $estudiante || $estudiante->estado_academico !== 'Egresado') return null;

            $estudiante->update(['estado_academico' => 'Activo']);
            $reactivados = $this->reactivarBeneficios
                ? BeneficiosService::reactivarDesactivadosPor($estudiante->id, 'egreso')
                : 0;

            // User no tiene observer de auditoría: se registra a mano con el motivo
            AuditoriaGeneral::create([
                'auditable_type'  => User::class,
                'auditable_id'    => $estudiante->id,
                'evento'          => 'updated',
                'valores_antes'   => ['estado_academico' => 'Egresado'],
                'valores_despues' => [
                    'estado_academico'       => 'Activo',
                    'motivo'                 => 'Egreso revertido: ' . $motivo,
                    'beneficios_reactivados' => $reactivados,
                ],
                'user_id'         => auth()->id(),
                'ip_address'      => request()?->ip(),
            ]);

            return ['nombre' => $estudiante->name, 'reactivados' => $reactivados];
        });

        if (! $resultado) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'El estudiante ya no está como Egresado.']);
            $this->cerrarModal();
            unset($this->estudiantes);
            return;
        }

        $detalle = $this->reactivarBeneficios
            ? ($resultado['reactivados'] > 0
                ? " Se reactivaron {$resultado['reactivados']} beneficio(s)."
                : ' No había becas ni convenios del egreso para reactivar.')
            : '';

        $this->estadoAcademico = 'Activo';
        $this->reset(['motivoReversionEgreso', 'reactivarBeneficios']);
        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => "{$resultado['nombre']} vuelve a estar Activo.{$detalle}"]);
        unset($this->estudiantes);
    }

    // =========================================================================
    // MARCAR TITULADO (cuando el acta es generada)
    // =========================================================================
    public function marcarTitulado(): void
    {
        if ($this->sinPermiso('gestionar_titulacion')) return;

        $titulacion = NotaTitulacion::find($this->titulacionId);
        if (! $titulacion || ! $titulacion->nota_final) {
            $this->dispatch('toast', ['tipo' => 'error', 'mensaje' => 'El proceso de titulación debe estar completo con nota final para marcar como Titulado.']);
            return;
        }

        $estudiante = User::find($this->estudianteId);
        if (! $estudiante) return;

        $estudiante->update(['estado_academico' => 'Titulado']);

        $this->dispatch('toast', ['tipo' => 'success', 'mensaje' => "{$estudiante->name} ha sido marcado como Titulado."]);
        $this->cerrarModal();
        unset($this->estudiantes);
    }

    private function resetModal(): void
    {
        $this->reset([
            'showModal',
            'paso',
            'estudianteId',
            'estudianteNombre',
            'carreraId',
            'mallaCompleta',
            'semestresOk',
            'semestresTotal',
            'obligacionesAlDia',
            'saldoPendiente',
            'exigeSinSaldo',
            'estadoAcademico',
            'motivoReversionEgreso',
            'reactivarBeneficios',
            'promedioMalla',
            'semestresDetalle',
            'practicaId',
            'practicaEmpresa',
            'practicaSector',
            'practicaDireccion',
            'practicaTutorEmpresa',
            'practicaCargoTutor',
            'practicaTelefono',
            'practicaEmail',
            'practicaCargoEstudiante',
            'practicaActividades',
            'practicaFechaInicio',
            'practicaFechaFin',
            'practicaTotalHoras',
            'practicaNota',
            'practicaEstado',
            'practicaObservaciones',
            'titulacionId',
            'titulacionTipo',
            'titulacionNota',
            'titulacionPresidente',
            'titulacionMiembro1',
            'titulacionMiembro2',
            'titulacionFechaRegistro',
            'titulacionFechaEval',
            'titulacionObservaciones',
        ]);
    }

    // =========================================================================
    // RENDER
    // =========================================================================
    public function render()
    {
        return view('livewire.administration.proceso-titulacion', [
            'estudiantes' => $this->estudiantes,
        ]);
    }
}
