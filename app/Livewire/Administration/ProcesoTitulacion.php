<?php

namespace App\Livewire\Administration;

use App\Models\Carrera;
use App\Models\NotaTitulacion;
use App\Models\PracticaPreprofesional;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ProcesoTitulacion extends Component
{
    use WithPagination;

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
            ->whereHas(
                'matriculas',
                fn($q) =>
                $q->where('estado', 'Habilitada')
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
                'matriculas' => fn($q) => $q->where('estado', 'Habilitada')
                    ->with('carrera')
                    ->latest(),
            ])
            ->orderBy('name')
            ->paginate(12);
    }

    // =========================================================================
    // HELPERS DE TABLA — calculados por fila sin N+1
    // =========================================================================
    public function estadoMallaEstudiante(int $userId, int $carreraId): array
    {
        $carrera = Carrera::with('semestres.materias')->find($carreraId);
        if (! $carrera) return ['completa' => false, 'semestres_ok' => 0, 'semestres_total' => 0];

        $total = $carrera->semestres->count();
        $ok    = 0;

        foreach ($carrera->semestres as $semestre) {
            $completo = true;
            foreach ($semestre->materias as $materia) {
                $aprobada = \App\Models\Calificacion::whereHas(
                    'detalleMatricula',
                    fn($q) =>
                    $q->where('user_id', $userId)->where('materia_id', $materia->id)
                )->where('estado_final', 'Aprobado')->exists();

                if (! $aprobada) {
                    $completo = false;
                    break;
                }
            }
            if ($completo) $ok++;
        }

        return ['completa' => $ok === $total, 'semestres_ok' => $ok, 'semestres_total' => $total];
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
            'matriculas' => fn($q) => $q->where('estado', 'Habilitada')->with('carrera')->latest(),
        ])->find($estudianteId);

        if (! $estudiante) return;

        $matricula = $estudiante->matriculas->first();
        if (! $matricula) return;

        $this->estudianteId     = $estudianteId;
        $this->estudianteNombre = $estudiante->name;
        $this->carreraId        = $matricula->carrera_id;

        // Calcular malla
        $estadoMalla = $this->estadoMallaEstudiante($estudianteId, $this->carreraId);
        $this->mallaCompleta  = $estadoMalla['completa'];
        $this->promedioMalla  = NotaTitulacion::calcularPromedioMalla($estudianteId, $this->carreraId);
        $this->semestresDetalle = $this->calcularDetalleSemestres($estudianteId, $this->carreraId);

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
                'total_horas'         => $this->practicaTotalHoras ?: 0,
                'nota'                => $this->practicaNota !== '' ? $this->practicaNota : null,
                'estado'              => $this->practicaEstado,
                'observaciones'       => $this->practicaObservaciones ?: null,
            ];

            if ($this->practicaId) {
                PracticaPreprofesional::find($this->practicaId)?->update($datos);
            } else {
                $practica = PracticaPreprofesional::create($datos);
                $this->practicaId = $practica->id;
            }

            // Si la práctica está completada, sincronizar nota en titulación si existe
            if ($this->titulacionId && $this->practicaEstado === 'Completada' && $this->practicaNota !== '') {
                NotaTitulacion::find($this->titulacionId)?->update([
                    'practica_id'    => $this->practicaId,
                    'nota_practicas' => $this->practicaNota,
                ]);
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

            $datos = [
                'user_id'               => $this->estudianteId,
                'carrera_id'            => $this->carreraId,
                'practica_id'           => $this->practicaId,
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

    private function resetModal(): void
    {
        $this->reset([
            'showModal',
            'paso',
            'estudianteId',
            'estudianteNombre',
            'carreraId',
            'mallaCompleta',
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
