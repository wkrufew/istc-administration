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
use App\Models\MateriasArrastrada;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;

class Matriculacion extends Component
{
    use WithPagination;

    // Propiedades para la búsqueda y filtros
    public $search = '';
    public $selectedCarrera = '';
    public $selectedPeriodo = '';

    // Propiedades del modal de matrícula
    public $showModal = false;
    public $estudiante = null;
    public $matriculaId = null;

    // Datos de la matrícula
    #[Rule('required')]
    public $carrera_id = '';

    #[Rule('required')]
    public $periodo_id = '';

    public $tipo = 'Nueva';
    public $descuento = 0;
    public $observaciones = '';

    // Materias seleccionadas
    public $materiasSeleccionadas = [];
    public $materiasArrastradas = [];
    public $materiasDisponibles = [];
    public $paralelosSeleccionados = [];
    public $paralelosDisponibles = [];

    // Estados del proceso
    public $paso = 1;
    public $totalPasos = 4;

    // Cálculos
    public $totalCreditos = 0;
    public $costoTotal = 0;
    public $totalPagar = 0;

    protected $listeners = [
        'matricularEstudiante' => 'iniciarMatricula',
        'editarMatricula' => 'editarMatricula'
    ];

    public function mount()
    {
        $this->selectedPeriodo = Periodo::where('is_current', true)->first()?->id ?? '';
    }

    #[Computed]
    public function estudiantes()
    {
        return User::role('estudiante')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('cedula', 'like', '%' . $this->search . '%')
                        ->orWhere('matricula_numero', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['matriculas' => function ($query) {
                $query->when($this->selectedPeriodo, function ($q) {
                    $q->where('periodo_id', $this->selectedPeriodo);
                });
            }])
            ->orderBy('name')
            ->paginate(10);
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

    public function iniciarMatricula($estudianteId)
    {
        //dd($estudianteId);
        $this->reset(['matriculaId', 'carrera_id', 'periodo_id', 'tipo', 'descuento', 'observaciones', 'materiasSeleccionadas', 'materiasArrastradas', 'paralelosSeleccionados']);

        $this->estudiante = User::with(['materiasArrastradas.materia', 'matriculas.carrera'])->find($estudianteId);
        $this->periodo_id = Periodo::where('is_current', true)->first()?->id ?? '';

        // Detectar carrera más reciente del estudiante
        $ultimaMatricula = $this->estudiante->matriculas()->latest()->first();
        if ($ultimaMatricula) {
            $this->carrera_id = $ultimaMatricula->carrera_id;
        }

        $this->materiasArrastradas = $this->estudiante->materiasArrastradas()
            ->where('estado', 'Arrastrada')
            ->with('materia')
            ->get()
            ->toArray();

        $this->paso = 1;
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function editarMatricula($matriculaId)
    {
        $this->reset(['estudiante', 'carrera_id', 'periodo_id', 'tipo', 'descuento', 'observaciones', 'materiasSeleccionadas', 'materiasArrastradas', 'paralelosSeleccionados']);
        $matricula = Matricula::with(['estudiante', 'detalles.materia', 'detalles.paralelo'])->find($matriculaId);
        //dd($matricula);

        if (!$matricula) return;

        $this->matriculaId = $matriculaId;
        $this->estudiante = $matricula->estudiante;
        //dd($this->estudiante);
        $this->carrera_id = $matricula->carrera_id;
        $this->periodo_id = $matricula->periodo_id;
        $this->tipo = $matricula->tipo;
        $this->descuento = $matricula->descuento;
        $this->observaciones = $matricula->observaciones;

        // Cargar materias ya matriculadas
        $this->materiasSeleccionadas = $matricula->detalles->pluck('materia_id')->toArray();
        $this->paralelosSeleccionados = $matricula->detalles->pluck('paralelo_id', 'materia_id')->toArray();

        $this->cargarMateriasDisponibles();
        $this->calcularCostos();
        //dd($this->estudiante);
        $this->paso = 2;
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function siguientePaso()
    {
        if ($this->paso == 1) {
            $this->validate([
                'carrera_id' => 'required|exists:carreras,id',
                'periodo_id' => 'required|exists:periodos,id'
            ]);
            $this->cargarMateriasDisponibles();
        }

        if ($this->paso == 2) {
            if (empty($this->materiasSeleccionadas) && empty($this->materiasArrastradas)) {
                $this->addError('materias', 'Debe seleccionar al menos una materia.');
                return;
            }
            $this->cargarParalelos();
        }

        if ($this->paso == 3) {
            $this->validarParalelos();
            $this->calcularCostos();
        }

        $this->paso++;
    }

    public function pasoAnterior()
    {
        $this->paso--;
    }

    public function cargarMateriasDisponibles()
    {
        if (!$this->carrera_id) return;
        //dd($this->carrera_id, $this->estudiante);
        $carrera = Carrera::with('semestres.materias')->find($this->carrera_id);

        // Obtener materias ya aprobadas por el estudiante
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
                // No incluir materias ya aprobadas
                if (in_array($materia->id, $materiasAprobadas)) continue;

                // Verificar prerequisitos
                $prerequisitosCumplidos = $this->verificarPrerequisitos($materia, $materiasAprobadas);

                $materiasSemestre[] = [
                    'id' => $materia->id,
                    'name' => $materia->name,
                    'code' => $materia->code,
                    'credits' => $materia->credits,
                    'tipo' => $materia->tipo,
                    'puede_inscribir' => $prerequisitosCumplidos,
                    'prerequisitos_faltantes' => $prerequisitosCumplidos ? [] : $this->getPrerrequisitosFaltantes($materia, $materiasAprobadas)
                ];
            }

            if (!empty($materiasSemestre)) {
                $this->materiasDisponibles[$semestre->name] = $materiasSemestre;
            }
        }
    }

    public function verificarPrerequisitos($materia, $materiasAprobadas)
    {
        $prerequisitos = DB::table('prerequisitos')
            ->where('materia_id', $materia->id)
            ->where('es_obligatorio', true)
            ->pluck('prerequisito_id')
            ->toArray();

        return empty(array_diff($prerequisitos, $materiasAprobadas));
    }

    public function getPrerrequisitosFaltantes($materia, $materiasAprobadas)
    {
        $prerequisitos = DB::table('prerequisitos')
            ->join('materias', 'prerequisitos.prerequisito_id', '=', 'materias.id')
            ->where('prerequisitos.materia_id', $materia->id)
            ->where('prerequisitos.es_obligatorio', true)
            ->whereNotIn('prerequisitos.prerequisito_id', $materiasAprobadas)
            ->pluck('materias.name')
            ->toArray();

        return $prerequisitos;
    }

    public function cargarParalelos()
    {
        $todasLasMaterias = array_merge($this->materiasSeleccionadas, array_column($this->materiasArrastradas, 'materia_id'));
        $this->paralelosDisponibles = [];

        foreach ($todasLasMaterias as $materiaId) {
            $paralelos = Paralelo::whereHas('asignacionesDocentes', function ($query) use ($materiaId) {
                $query->where('materia_id', $materiaId)
                    ->where('periodo_id', $this->periodo_id);
            })
                ->where('is_active', true)
                ->get()
                ->map(function ($paralelo) {
                    return [
                        'id' => $paralelo->id,
                        'name' => $paralelo->name,
                        'cupo_disponible' => $paralelo->cupo_maximo - $paralelo->cupo_actual,
                        'tiene_cupo' => $paralelo->tieneCupoDisponible()
                    ];
                });

            $this->paralelosDisponibles[$materiaId] = $paralelos;
        }
    }

    public function validarParalelos()
    {
        $todasLasMaterias = array_merge($this->materiasSeleccionadas, array_column($this->materiasArrastradas, 'materia_id'));

        foreach ($todasLasMaterias as $materiaId) {
            if (empty($this->paralelosSeleccionados[$materiaId])) {
                $this->addError('paralelos', 'Debe seleccionar un paralelo para todas las materias.');
                return false;
            }
        }
        return true;
    }

    public function calcularCostos()
    {
        $carrera = Carrera::find($this->carrera_id);
        $this->totalCreditos = 0;
        $this->costoTotal = 0;

        // Calcular créditos y costos de materias seleccionadas
        if (!empty($this->materiasSeleccionadas)) {
            $materias = Materia::whereIn('id', $this->materiasSeleccionadas)->get();
            foreach ($materias as $materia) {
                $this->totalCreditos += $materia->credits;
                $this->costoTotal += $materia->credits * $carrera->costo_credito;
            }
        }

        // Agregar costos de materias arrastradas
        foreach ($this->materiasArrastradas as $materiaArrastrada) {
            if (isset($materiaArrastrada['incluir']) && $materiaArrastrada['incluir']) {
                $materia = Materia::find($materiaArrastrada['materia_id']);
                $this->totalCreditos += $materia->credits;
                $this->costoTotal += $materiaArrastrada['costo_adicional'] ?? ($materia->credits * $carrera->costo_credito);
            }
        }

        $this->totalPagar = $this->costoTotal - $this->descuento;
    }

    public function guardarMatricula()
    {
        $this->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'periodo_id' => 'required|exists:periodos,id',
            'descuento' => 'numeric|min:0|max:' . $this->costoTotal
        ]);

        try {
            DB::beginTransaction();

            if ($this->matriculaId) {
                // Actualizar matrícula existente
                $matricula = Matricula::find($this->matriculaId);
                $matricula->update([
                    'tipo' => $this->tipo,
                    'total_creditos' => $this->totalCreditos,
                    'costo_total' => $this->costoTotal,
                    'descuento' => $this->descuento,
                    'total_pagar' => $this->totalPagar,
                    'observaciones' => $this->observaciones,
                ]);

                // Eliminar detalles existentes para recrearlos
                $matricula->detalles()->delete();
            } else {
                // Crear nueva matrícula
                $matricula = Matricula::create([
                    'fecha_matricula' => now(),
                    'code' => $this->generarCodigoMatricula(),
                    'tipo' => $this->tipo,
                    'status' => 'Pendiente_Pago',
                    'total_creditos' => $this->totalCreditos,
                    'costo_total' => $this->costoTotal,
                    'descuento' => $this->descuento,
                    'total_pagar' => $this->totalPagar,
                    'observaciones' => $this->observaciones,
                    'periodo_id' => $this->periodo_id,
                    'carrera_id' => $this->carrera_id,
                    'user_id' => $this->estudiante->id,
                ]);

                //Almacena el el pago en la matricula
                Pago::create([
                    'matricula_id' => $matricula->id,
                    'user_id' => $matricula->user_id,
                    'monto' => $this->totalPagar,
                    'concepto' => 'MATRICULA',
                    'estado' => 'Pendiente',
                    'metodo_pago' => 'Transferencia',
                    'fecha_vencimiento' => now()->addDays(10),
                ]);
            }

            // Crear detalles de matrícula para materias normales
            foreach ($this->materiasSeleccionadas as $materiaId) {
                $materia = Materia::find($materiaId);
                $carrera = Carrera::find($this->carrera_id);

                DetalleMatricula::create([
                    'asignacion' => now(),
                    'code' => $this->generarCodigoDetalle($matricula->code, $materia->code),
                    'tipo' => 'Normal',
                    'estado' => 'Inscrito',
                    'costo_materia' => $materia->credits * $carrera->costo_credito,
                    'es_repeticion' => false,
                    'matricula_id' => $matricula->id,
                    'materia_id' => $materiaId,
                    'paralelo_id' => $this->paralelosSeleccionados[$materiaId],
                    'user_id' => $this->estudiante->id,
                ]);

                // Actualizar cupo del paralelo
                $paralelo = Paralelo::find($this->paralelosSeleccionados[$materiaId]);
                $paralelo->increment('cupo_actual');
            }

            // Crear detalles para materias arrastradas
            foreach ($this->materiasArrastradas as $materiaArrastrada) {
                if (isset($materiaArrastrada['incluir']) && $materiaArrastrada['incluir']) {
                    $materia = Materia::find($materiaArrastrada['materia_id']);

                    DetalleMatricula::create([
                        'asignacion' => now(),
                        'code' => $this->generarCodigoDetalle($matricula->code, $materia->code),
                        'tipo' => 'Arrastre',
                        'estado' => 'Inscrito',
                        'costo_materia' => $materiaArrastrada['costo_adicional'] ?? 0,
                        'es_repeticion' => true,
                        'matricula_id' => $matricula->id,
                        'materia_id' => $materiaArrastrada['materia_id'],
                        'paralelo_id' => $this->paralelosSeleccionados[$materiaArrastrada['materia_id']],
                        'user_id' => $this->estudiante->id,
                    ]);

                    // Actualizar estado de materia arrastrada
                    MateriasArrastrada::find($materiaArrastrada['id'])->update(['estado' => 'Inscrita']);

                    // Actualizar cupo del paralelo
                    $paralelo = Paralelo::find($this->paralelosSeleccionados[$materiaArrastrada['materia_id']]);
                    $paralelo->increment('cupo_actual');
                }
            }

            DB::commit();

            $this->dispatch('matricula-guardada', [
                'mensaje' => $this->matriculaId ? 'Matrícula actualizada exitosamente' : 'Matrícula creada exitosamente',
                'matricula_id' => $matricula->id
            ]);

            $this->cerrarModal();
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('error', ['mensaje' => 'Error al guardar la matrícula: ' . $e->getMessage()]);
        }
    }

    private function generarCodigoMatricula()
    {
        /* $periodo = Periodo::find($this->periodo_id);
        $anio = date('Y');
        $mes = date('m');

        $ultimo = Matricula::where('periodo_id', $this->periodo_id)
            ->whereYear('created_at', $anio)
            ->count();

        return sprintf('%s-%04d-%04d', $periodo->code, $anio, $ultimo + 1); */
        $ultimo = Matricula::count();

        return sprintf('ISTC-%04d', $ultimo + 1);
    }

    private function generarCodigoDetalle($codigoMatricula, $codigoMateria)
    {
        return $codigoMatricula . '-' . $codigoMateria;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->reset(['estudiante', 'matriculaId', 'carrera_id', 'periodo_id', 'tipo', 'descuento', 'observaciones', 'materiasSeleccionadas', 'materiasArrastradas', 'paralelosSeleccionados', 'paso']);
        $this->dispatch('modal-closed');
    }

    public function render()
    {
        //dd($this->estudiantes, $this->carreras, $this->periodos);
        return view('livewire.administration.matriculacion', [
            'estudiantes' => $this->estudiantes,
            'carreras' => $this->carreras,
            'periodos' => $this->periodos
        ]);
    }
}
