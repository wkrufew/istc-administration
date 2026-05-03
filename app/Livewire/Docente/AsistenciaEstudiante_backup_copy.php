<?php

namespace App\Livewire\Docente;

use App\Models\Asistencia;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\DetalleMatricula;
use App\Models\Paralelo;
use App\Models\Periodo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MateriaPeriodoParalelo;
use Carbon\CarbonPeriod;
use Livewire\Component;

class AsistenciaEstudiante extends Component
{
    public $periodo_id;
    public $materia_id;
    public $paralelo_id;
    public $horario_id;

    public $fecha;

    public $mensaje;
    public $tipo_mensaje = 'success';

    public $estudiantes = [];
    public $horarios = [];

    // PRO
    public $total_clases = 0;
    public $dias_horario = [];
    public $modulo = null; // materia_periodo_paralelo encontrado

    public function mount()
    {
        $this->fecha = Carbon::now()->toDateString();
    }

    // ===========================
    // RESETS
    // ===========================

    public function updatedPeriodoId()
    {
        $this->reset([
            'materia_id',
            'paralelo_id',
            'horario_id',
            'estudiantes',
            'horarios',
            'total_clases',
            'dias_horario',
            'modulo',
        ]);

        $this->resetMensajes();
    }

    public function updatedMateriaId()
    {
        $this->reset([
            'paralelo_id',
            'horario_id',
            'estudiantes',
            'horarios',
            'total_clases',
            'dias_horario',
            'modulo',
        ]);

        $this->resetMensajes();
    }

    public function updatedParaleloId()
    {
        $this->reset([
            'horario_id',
            'estudiantes',
            'total_clases',
            'dias_horario',
            'modulo',
        ]);

        $this->resetMensajes();

        $this->cargarHorarios();
    }

    public function updatedHorarioId()
    {
        $this->resetMensajes();
        $this->cargarEstudiantes();
    }

    public function updatedFecha()
    {
        $this->resetMensajes();
        $this->cargarEstudiantes();
    }

    private function resetMensajes()
    {
        $this->mensaje = null;
        $this->tipo_mensaje = 'success';
    }

    // ===========================
    // SEGURIDAD: HORARIO DEL DOCENTE
    // ===========================

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

    // ===========================
    // MODULO REAL (materia_periodo_paralelo)
    // ===========================

    private function cargarModulo()
    {
        $this->modulo = null;

        if (!$this->periodo_id || !$this->materia_id || !$this->paralelo_id) {
            return;
        }

        $this->modulo = MateriaPeriodoParalelo::query()
            ->where('periodo_id', $this->periodo_id)
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('is_active', 1)
            ->first();
    }

    // ===========================
    // VALIDACIONES DE FECHA
    // ===========================

    private function validarFechaDentroDelModulo(): bool
    {
        if (!$this->fecha) return false;

        $this->cargarModulo();

        // Si no existe el módulo en la tabla, NO podemos calcular
        if (!$this->modulo) return false;

        $fecha = Carbon::parse($this->fecha);
        $inicio = Carbon::parse($this->modulo->fecha_inicio);
        $fin = Carbon::parse($this->modulo->fecha_fin);

        return $fecha->betweenIncluded($inicio, $fin);
    }

    private function validarFechaCoincideConDiaHorario(): bool
    {
        if (!$this->fecha || empty($this->dias_horario)) return true;

        $fecha = Carbon::parse($this->fecha);

        $mapa = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        $dia = $mapa[$fecha->format('l')] ?? null;

        return $dia && in_array($dia, $this->dias_horario);
    }

    // ===========================
    // CARGAR HORARIOS
    // ===========================

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

    // ===========================
    // PRO: DIAS DE HORARIO
    // ===========================

    private function calcularDiasHorario()
    {
        $this->dias_horario = [];

        if (!$this->materia_id || !$this->paralelo_id || !$this->periodo_id) {
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

    // ===========================
    // PRO: TOTAL CLASES (USANDO MODULO REAL)
    // ===========================

    private function calcularTotalClasesModulo()
    {
        $this->total_clases = 0;

        $this->cargarModulo();

        if (!$this->modulo) {
            return;
        }

        $this->calcularDiasHorario();

        if (empty($this->dias_horario)) {
            return;
        }

        $inicio = Carbon::parse($this->modulo->fecha_inicio);
        $fin = Carbon::parse($this->modulo->fecha_fin);

        $period = CarbonPeriod::create($inicio, $fin);

        $contador = 0;

        $mapa = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];

        foreach ($period as $date) {
            $dia = $mapa[$date->format('l')] ?? null;

            if ($dia && in_array($dia, $this->dias_horario)) {
                $contador++;
            }
        }

        $this->total_clases = $contador;
    }

    // ===========================
    // CARGAR ESTUDIANTES + CONTADORES
    // ===========================

    public function cargarEstudiantes()
    {
        if (!$this->periodo_id || !$this->materia_id || !$this->paralelo_id || !$this->horario_id || !$this->fecha) {
            $this->estudiantes = [];
            $this->total_clases = 0;
            return;
        }

        if (!$this->horarioPerteneceAlDocente()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No tienes permisos para usar este horario.';
            $this->estudiantes = [];
            return;
        }

        // Total clases reales del módulo
        $this->calcularTotalClasesModulo();

        if ($this->total_clases <= 0) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No se pudo calcular el total de clases. Verifica que exista el módulo (materia_periodo_paralelo) y horarios.';
            $this->estudiantes = [];
            return;
        }

        // Validar fecha dentro del módulo
        if (!$this->validarFechaDentroDelModulo()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'La fecha seleccionada está fuera del rango del módulo.';
            $this->estudiantes = [];
            return;
        }

        // Validar que fecha coincida con un día real de clase
        if (!$this->validarFechaCoincideConDiaHorario()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'La fecha seleccionada NO coincide con un día de clase según el horario.';
            $this->estudiantes = [];
            return;
        }

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

        // Asistencias SOLO de la fecha actual
        $asistencias_fecha = Asistencia::query()
            ->where('horario_id', $this->horario_id)
            ->where('fecha', $this->fecha)
            ->whereIn('detalle_matricula_id', $detalles->pluck('id'))
            ->get()
            ->keyBy('detalle_matricula_id');

        // Asistencias del módulo (para contar)
        $asistencias_modulo = Asistencia::query()
            ->whereHas('horario', function ($q) {
                $q->where('materia_id', $this->materia_id)
                    ->where('paralelo_id', $this->paralelo_id)
                    ->where('periodo_id', $this->periodo_id);
            })
            ->whereIn('detalle_matricula_id', $detalles->pluck('id'))
            ->get()
            ->groupBy('detalle_matricula_id');

        $horaActual = Carbon::now()->format('H:i');

        $this->estudiantes = $detalles->map(function ($detalle) use ($asistencias_fecha, $asistencias_modulo, $horaActual) {

            $asistencia_hoy = $asistencias_fecha->get($detalle->id);

            $lista = $asistencias_modulo->get($detalle->id, collect());

            // Contar asistencias válidas
            $asistidas = $lista->whereIn('estado', ['Presente', 'Justificado', 'Tardanza'])->count();

            // Nota sobre 10
            $nota = round(($asistidas / $this->total_clases) * 10, 2);

            return [
                'detalle_id' => $detalle->id,
                'codigo_matricula' => $detalle->code,
                'estudiante' => $detalle->estudiante,

                'estado' => $asistencia_hoy->estado ?? '',
                'hora_entrada' => $asistencia_hoy->hora_entrada ?? $horaActual,
                'observaciones' => $asistencia_hoy->observaciones ?? '',

                // PRO
                'asistidas' => $asistidas,
                'total_clases' => $this->total_clases,
                'nota_asistencia' => $nota,
            ];
        })->toArray();
    }

    // ===========================
    // GUARDAR
    // ===========================

    public function guardarAsistencias()
    {
        if (!$this->periodo_id || !$this->materia_id || !$this->paralelo_id || !$this->horario_id || !$this->fecha) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'Seleccione Período, Materia, Paralelo, Horario y Fecha.';
            return;
        }

        if (!$this->horarioPerteneceAlDocente()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No tienes permisos para guardar asistencias con este horario.';
            return;
        }

        if (!$this->validarFechaDentroDelModulo()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No puedes registrar asistencia fuera del rango del módulo.';
            return;
        }

        if (!$this->validarFechaCoincideConDiaHorario()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje = 'No puedes registrar asistencia en un día que no corresponde al horario.';
            return;
        }

        try {

            foreach ($this->estudiantes as $est) {

                if (empty($est['estado'])) {
                    continue;
                }

                Asistencia::updateOrCreate(
                    [
                        'detalle_matricula_id' => $est['detalle_id'],
                        'horario_id' => $this->horario_id,
                        'fecha' => $this->fecha,
                    ],
                    [
                        'estado' => $est['estado'],
                        'hora_entrada' => !empty($est['hora_entrada']) ? $est['hora_entrada'] : null,
                        'observaciones' => !empty($est['observaciones']) ? $est['observaciones'] : null,
                        'docente_id' => Auth::id(),
                    ]
                );
            }

            $this->tipo_mensaje = 'success';
            $this->mensaje = 'Asistencias registradas correctamente.';

            $this->cargarEstudiantes();
        } catch (\Throwable $e) {

            $this->tipo_mensaje = 'error';
            $this->mensaje = 'Error al guardar asistencias: ' . $e->getMessage();
        }
    }

    // ===========================
    // BOTONES
    // ===========================

    public function marcarTodosPresentes()
    {
        $horaActual = Carbon::now()->format('H:i');

        foreach ($this->estudiantes as $index => $est) {
            $this->estudiantes[$index]['estado'] = 'Presente';
            $this->estudiantes[$index]['hora_entrada'] = $horaActual;
        }

        $this->mensaje = 'Todos los estudiantes marcados como Presente.';
        $this->tipo_mensaje = 'success';
    }

    public function marcarTodosAusentes()
    {
        foreach ($this->estudiantes as $index => $est) {
            $this->estudiantes[$index]['estado'] = 'Ausente';
            $this->estudiantes[$index]['hora_entrada'] = null;
        }

        $this->mensaje = 'Todos los estudiantes marcados como Ausente.';
        $this->tipo_mensaje = 'success';
    }

    public function copiarHoraActualATodos()
    {
        $horaActual = Carbon::now()->format('H:i');

        foreach ($this->estudiantes as $index => $est) {
            if (!empty($this->estudiantes[$index]['estado'])) {
                $this->estudiantes[$index]['hora_entrada'] = $horaActual;
            }
        }

        $this->mensaje = 'Hora actual copiada a todos los estudiantes con estado.';
        $this->tipo_mensaje = 'success';
    }

    public function limpiarTodo()
    {
        foreach ($this->estudiantes as $index => $est) {
            $this->estudiantes[$index]['estado'] = '';
            $this->estudiantes[$index]['hora_entrada'] = '';
            $this->estudiantes[$index]['observaciones'] = '';
        }

        $this->mensaje = 'Campos limpiados.';
        $this->tipo_mensaje = 'success';
    }

    // ===========================
    // RENDER
    // ===========================

    public function render()
    {
        $user = Auth::user();

        $periodos = Periodo::orderBy('id', 'desc')->get();

        // SOLO materias asignadas al docente
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

        return view('livewire.docente.asistencia-estudiante', [
            'periodos' => $periodos,
            'materias_asignadas' => $materias_asignadas,
            'paralelos' => $paralelos,
            'horarios' => $this->horarios,
        ]);
    }
}
