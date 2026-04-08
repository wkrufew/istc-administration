<?php

namespace App\Livewire\Docente;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use App\Models\Periodo;
use App\Models\Horario;
use App\Models\Asistencia;
use App\Models\DetalleMatricula;
use App\Models\MateriaPeriodoParalelo;

class AsistenciaCorreccionModulo extends Component
{
    public $periodo_id;
    public $materia_id;
    public $paralelo_id;
    public $horario_id;

    public $mensaje;
    public $tipo_mensaje = 'success';

    public $horarios = [];
    public $estudiantes = [];

    // PRO
    public $total_clases = 0;
    public $dias_horario = [];

    // Modal
    public $modalEditar = false;
    public $edit_detalle_id;

    public $edit_fecha; // fecha seleccionada del select
    public $edit_estado;
    public $edit_hora_entrada;
    public $edit_observaciones;

    // Fechas válidas del módulo (solo días reales de clase)
    public $fechas_validas = [];

    // ============================
    // RESETS
    // ============================

    public function updatedPeriodoId()
    {
        $this->reset([
            'materia_id',
            'paralelo_id',
            'horario_id',
            'horarios',
            'estudiantes',
            'total_clases',
            'dias_horario',
            'fechas_validas',
        ]);

        $this->resetMensajes();
    }

    public function updatedMateriaId()
    {
        $this->reset([
            'paralelo_id',
            'horario_id',
            'horarios',
            'estudiantes',
            'total_clases',
            'dias_horario',
            'fechas_validas',
        ]);

        $this->resetMensajes();
    }

    public function updatedParaleloId()
    {
        $this->reset([
            'horario_id',
            'horarios',
            'estudiantes',
            'total_clases',
            'dias_horario',
            'fechas_validas',
        ]);

        $this->resetMensajes();
        $this->cargarHorarios();
        $this->cargarFechasRegistradas();
    }

    public function updatedHorarioId()
    {
        $this->resetMensajes();
        $this->cargarEstudiantes();
        $this->cargarFechasRegistradas();
    }

    private function resetMensajes()
    {
        $this->mensaje = null;
        $this->tipo_mensaje = 'success';
    }

    // ============================
    // SEGURIDAD
    // ============================

    private function horarioPerteneceAlDocente(): bool
    {
        if (!$this->horario_id || !$this->periodo_id) return false;

        $user = Auth::user();

        return Horario::query()
            ->where('id', $this->horario_id)
            ->where('periodo_id', $this->periodo_id)
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('is_active', 1)
            ->whereHas('asignacionDocente', function ($q) use ($user) {
                $q->where('docente_id', $user->id);
            })
            ->exists();
    }

    // ============================
    // HORARIOS
    // ============================

    public function cargarHorarios()
    {
        if (!$this->materia_id || !$this->paralelo_id || !$this->periodo_id) {
            $this->horarios = [];
            return;
        }

        $user = Auth::user();

        $this->horarios = Horario::query()
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('periodo_id', $this->periodo_id)
            ->where('is_active', 1)
            ->whereHas('asignacionDocente', function ($q) use ($user) {
                $q->where('docente_id', $user->id);
            })
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    // ============================
    // PRO: DIAS DEL HORARIO
    // ============================

    private function calcularDiasHorario()
    {
        $this->dias_horario = [];

        if (!$this->materia_id || !$this->paralelo_id || !$this->periodo_id || !$this->horario_id) {
            return;
        }

        $user = Auth::user();

        $dias = Horario::query()
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('periodo_id', $this->periodo_id)
            ->where('is_active', 1)
            ->whereHas('asignacionDocente', function ($q) use ($user) {
                $q->where('docente_id', $user->id);
            })
            ->pluck('dia_semana')
            ->unique()
            ->values()
            ->toArray();

        $this->dias_horario = $dias;
    }

    // ============================
    // PRO: FECHAS DEL MODULO
    // ============================

    private function getFechasModulo()
    {
        if (!$this->materia_id || !$this->periodo_id || !$this->paralelo_id) return null;

        return MateriaPeriodoParalelo::query()
            ->where('materia_id', $this->materia_id)
            ->where('periodo_id', $this->periodo_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('is_active', 1)
            ->first();
    }

    // ============================
    // PRO: CALCULAR FECHAS VALIDAS
    // ============================

    private function calcularFechasValidas()
    {
        $this->fechas_validas = [];

        $mpp = $this->getFechasModulo();
        if (!$mpp) return;

        if (!$mpp->fecha_inicio || !$mpp->fecha_fin) return;

        $this->calcularDiasHorario();
        if (empty($this->dias_horario)) return;

        $inicio = Carbon::parse($mpp->fecha_inicio);
        $fin = Carbon::parse($mpp->fecha_fin);

        $period = CarbonPeriod::create($inicio, $fin);

        $mapa = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        $fechas = [];

        foreach ($period as $date) {
            $dia = $mapa[$date->format('l')] ?? null;

            if ($dia && in_array($dia, $this->dias_horario)) {
                $fechas[] = $date->toDateString();
            }
        }

        $this->fechas_validas = $fechas;
    }

    // ============================
    // PRO: TOTAL CLASES (CORRECTO)
    // ============================

    private function calcularTotalClasesModulo()
    {
        $this->total_clases = 0;

        $this->calcularFechasValidas();

        $this->total_clases = count($this->fechas_validas);
    }

    // ============================
    // CARGAR ESTUDIANTES + NOTA
    // ============================

    public function cargarEstudiantes()
    {
        $this->estudiantes = [];
        $this->total_clases = 0;

        if (!$this->periodo_id || !$this->materia_id || !$this->paralelo_id || !$this->horario_id) {
            return;
        }

        if (!$this->horarioPerteneceAlDocente()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No tienes permisos para usar este horario.';
            return;
        }

        // Total correcto
        $this->calcularTotalClasesModulo();
        $this->cargarFechasRegistradas();

        $detalles = DetalleMatricula::query()
            ->with('estudiante')
            ->whereHas('matricula', function ($q) {
                $q->where('periodo_id', $this->periodo_id);
            })
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('estado', 'Inscrito')
            ->orderBy('id')
            ->get();

        $asistencias_modulo = Asistencia::query()
            ->whereHas('horario', function ($q) {
                $q->where('materia_id', $this->materia_id)
                    ->where('paralelo_id', $this->paralelo_id)
                    ->where('periodo_id', $this->periodo_id);
            })
            ->whereIn('detalle_matricula_id', $detalles->pluck('id'))
            ->get()
            ->groupBy('detalle_matricula_id');

        $this->estudiantes = $detalles->map(function ($detalle) use ($asistencias_modulo) {

            $lista = $asistencias_modulo->get($detalle->id, collect());

            $asistidas = $lista->whereIn('estado', ['Presente', 'Justificado', 'Tardanza'])->count();

            $nota = 0;
            if ($this->total_clases > 0) {
                $nota = round(($asistidas / $this->total_clases) * 10, 2);
            }

            return [
                'detalle_id' => $detalle->id,
                'codigo_matricula' => $detalle->code,
                'estudiante' => $detalle->estudiante,

                'asistidas' => $asistidas,
                'total_clases' => $this->total_clases,
                'nota_asistencia' => $nota,
            ];
        })->toArray();
    }

    //CARGAR FECHAS REGISTRADAS

    public function cargarFechasRegistradas()
    {
        /* $this->fechas_validas = [];

        if (!$this->periodo_id || !$this->materia_id || !$this->paralelo_id || !$this->horario_id) {
            return;
        }

        $fechas = Asistencia::query()
            ->where('horario_id', $this->horario_id)
            ->select('fecha')
            ->distinct()
            ->orderBy('fecha')
            ->pluck('fecha')
            ->toArray();

        $this->fechas_validas = $fechas; */
        $this->fechas_validas = [];

        if (!$this->horario_id) {
            return;
        }

        $this->fechas_validas = Asistencia::query()
            ->where('horario_id', $this->horario_id)
            ->select('fecha')
            ->distinct()
            ->orderBy('fecha')
            ->pluck('fecha')
            ->toArray();
    }

    // ============================
    // MODAL: ABRIR
    // ============================

    public function abrirModalEditar($detalle_id)
    {
        if (!$this->horarioPerteneceAlDocente()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No tienes permisos.';
            return;
        }

        //$this->calcularFechasValidas();
        $this->cargarFechasRegistradas();

        if (empty($this->fechas_validas)) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No hay fechas válidas para este módulo.';
            return;
        }

        $this->edit_detalle_id = $detalle_id;

        // Seleccionamos la primera fecha válida por defecto
        //$this->edit_fecha = $this->fechas_validas[0];
        $this->edit_fecha = $this->fechas_validas[0] ?? Carbon::now()->toDateString();

        // Cargar automáticamente la asistencia de esa fecha
        $this->cargarAsistenciaEdit();
        //NUEVO
        $this->updatedEditFecha();

        $this->modalEditar = true;
    }

    public function updatedEditFecha()
    {
        $this->cargarAsistenciaEdit();
    }

    private function cargarAsistenciaEdit()
    {
        if (!$this->edit_detalle_id || !$this->edit_fecha) return;

        // Validación: solo fechas válidas
        if (!in_array($this->edit_fecha, $this->fechas_validas)) {
            $this->edit_estado = '';
            $this->edit_hora_entrada = '';
            $this->edit_observaciones = '';
            return;
        }

        $asistencia = Asistencia::query()
            ->where('detalle_matricula_id', $this->edit_detalle_id)
            ->where('horario_id', $this->horario_id)
            ->where('fecha', $this->edit_fecha)
            ->first();

        $this->edit_estado = $asistencia->estado ?? '';
        $this->edit_hora_entrada = $asistencia->hora_entrada ?? '';
        $this->edit_observaciones = $asistencia->observaciones ?? '';
    }

    // ============================
    // MODAL: GUARDAR
    // ============================

    public function guardarCorreccion()
    {
        if (!$this->horarioPerteneceAlDocente()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No tienes permisos.';
            return;
        }

        if (!$this->edit_detalle_id || !$this->edit_fecha) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'Seleccione una fecha.';
            return;
        }

        // PRO: evitar fechas inventadas
        if (!in_array($this->edit_fecha, $this->fechas_validas)) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'La fecha seleccionada no corresponde a un día válido de clase.';
            return;
        }

        if (empty($this->edit_estado)) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'Seleccione un estado.';
            return;
        }

        Asistencia::updateOrCreate(
            [
                'detalle_matricula_id' => $this->edit_detalle_id,
                'horario_id' => $this->horario_id,
                'fecha' => $this->edit_fecha,
            ],
            [
                'estado' => $this->edit_estado,
                'hora_entrada' => !empty($this->edit_hora_entrada) ? $this->edit_hora_entrada : null,
                'observaciones' => !empty($this->edit_observaciones) ? $this->edit_observaciones : null,
                'docente_id' => Auth::id(),
            ]
        );

        $this->tipo_mensaje = 'success';
        $this->mensaje = 'Corrección guardada correctamente.';

        $this->modalEditar = false;

        $this->cargarEstudiantes();
    }

    public function cerrarModal()
    {
        $this->modalEditar = false;
    }

    // ============================
    // RENDER
    // ============================

    public function render()
    {
        $user = Auth::user();

        $periodos = Periodo::orderBy('id', 'desc')->get();

        $materias_asignadas = [];

        if ($this->periodo_id) {

            $asignaciones = $user->asignacionesDocente()
                ->with(['materia', 'paralelo'])
                ->where('periodo_id', $this->periodo_id)
                ->get();

            foreach ($asignaciones as $asig) {

                if (!$asig->materia) continue;
                if (!$asig->paralelo) continue;

                $materias_asignadas[$asig->materia->id]['materia'] = $asig->materia;
                $materias_asignadas[$asig->materia->id]['paralelos'][] = $asig->paralelo;
            }
        }

        $paralelos = collect();

        if ($this->materia_id && isset($materias_asignadas[$this->materia_id])) {
            $paralelos = collect($materias_asignadas[$this->materia_id]['paralelos'] ?? [])
                ->unique('id')
                ->values();
        }

        return view('livewire.docente.asistencia-correccion-modulo', [
            'periodos' => $periodos,
            'materias_asignadas' => $materias_asignadas,
            'paralelos' => $paralelos,
            'horarios' => $this->horarios,
            'total_clases' => $this->total_clases,
        ]);
    }
}
