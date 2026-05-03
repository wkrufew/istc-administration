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
use App\Traits\WithAuthorization;

class AsistenciaEstudiante extends Component
{
    use WithAuthorization;
    // =========================================================================
    // ESTADO PRINCIPAL
    // =========================================================================
    public ?int    $horario_id       = null;
    public string  $fecha            = '';
    public bool    $modoManual       = false;

    // Periodo/materia/paralelo — solo para modo manual
    public ?int    $periodo_id_manual  = null;
    public ?int    $materia_id_manual  = null;
    public ?int    $paralelo_id_manual = null;

    // =========================================================================
    // FORMULARIO
    // =========================================================================
    public array   $estudiantes      = [];
    public int     $total_clases     = 0;
    public ?object $modulo           = null;
    public array   $dias_horario     = [];

    // =========================================================================
    // CONTROL DE DOBLE GUARDADO
    // =========================================================================
    public bool    $yaRegistrada     = false;   // ya existe asistencia para este horario+fecha
    public bool    $modoEdicion      = false;   // docente desbloqueó edición
    public int     $totalMarcados    = 0;

    // =========================================================================
    // MENSAJES
    // =========================================================================
    public ?string $mensaje          = null;
    public string  $tipo_mensaje     = 'success';

    // =========================================================================
    // MOUNT
    // =========================================================================
    public function mount(): void
    {
        $this->fecha = Carbon::now()->toDateString();
        // Detectar automáticamente el horario activo ahora
        $this->detectarHorarioActivo();
    }

    // =========================================================================
    // DETECCIÓN AUTOMÁTICA
    // =========================================================================
    private function detectarHorarioActivo(): void
    {
        $ahora     = Carbon::now();
        $diaActual = $this->mapaDia($ahora->format('l'));
        $horaActual = $ahora->format('H:i:s');

        $horario = Horario::query()
            ->where('dia_semana', $diaActual)
            ->where('hora_inicio', '<=', $horaActual)
            ->where('hora_fin',    '>=', $horaActual)
            ->where('is_active', true)
            ->whereHas(
                'asignacionDocente',
                fn($q) =>
                $q->where('docente_id', Auth::id())
            )
            ->whereHas(
                'periodo',
                fn($q) =>
                $q->whereHas('carreras', fn($c) => $c->wherePivot('is_current', true))
            )
            ->first();

        if ($horario) {
            $this->horario_id = $horario->id;
            $this->cargarFormulario();
        }
    }

    // =========================================================================
    // HORARIOS DEL DÍA — para las cards de "próximas hoy"
    // =========================================================================
    public function getHorariosDiaProperty()
    {
        $diaActual = $this->mapaDia(Carbon::now()->format('l'));

        return Horario::query()
            ->with(['materia', 'paralelo', 'asignacionDocente'])
            ->where('dia_semana', $diaActual)
            ->where('is_active', true)
            ->whereHas(
                'asignacionDocente',
                fn($q) =>
                $q->where('docente_id', Auth::id())
            )
            ->whereHas(
                'periodo',
                fn($q) =>
                $q->whereHas('carreras', fn($c) => $c->wherePivot('is_current', true))
            )
            ->orderBy('hora_inicio')
            ->get();
    }

    // =========================================================================
    // SELECCIONAR HORARIO (desde cards)
    // =========================================================================
    public function seleccionarHorario(int $horarioId): void
    {
        $this->horario_id  = $horarioId;
        $this->modoManual  = false;
        $this->fecha       = Carbon::now()->toDateString();
        $this->resetMensajes();
        $this->cargarFormulario();
    }

    // =========================================================================
    // MODO MANUAL
    // =========================================================================
    public function activarModoManual(): void
    {
        $this->modoManual        = true;
        $this->horario_id        = null;
        $this->estudiantes       = [];
        $this->yaRegistrada      = false;
        $this->modoEdicion       = false;
        $this->total_clases      = 0;
        $this->resetMensajes();
    }

    public function updatedPeriodoIdManual(): void
    {
        $this->materia_id_manual  = null;
        $this->paralelo_id_manual = null;
        $this->horario_id         = null;
        $this->estudiantes        = [];
        $this->yaRegistrada       = false;
        $this->modoEdicion        = false;
        $this->resetMensajes();
    }

    public function updatedMateriaIdManual(): void
    {
        $this->paralelo_id_manual = null;
        $this->horario_id         = null;
        $this->estudiantes        = [];
        $this->yaRegistrada       = false;
        $this->modoEdicion        = false;
        $this->resetMensajes();
    }

    public function updatedParaleloIdManual(): void
    {
        $this->horario_id   = null;
        $this->estudiantes  = [];
        $this->yaRegistrada = false;
        $this->modoEdicion  = false;
        $this->resetMensajes();
    }

    public function updatedHorarioId(): void
    {
        $this->estudiantes  = [];
        $this->yaRegistrada = false;
        $this->modoEdicion  = false;
        $this->resetMensajes();
        // Solo calcula info, NO abre formulario todavía
        $this->calcularTotalClasesModulo();
    }

    public function updatedFecha(): void
    {
        $this->resetMensajes();
        $this->modoEdicion  = false;
        $this->yaRegistrada = false;
        $this->cargarFormulario();
    }

    // =========================================================================
    // DESBLOQUEAR EDICIÓN
    // =========================================================================
    public function desbloquearEdicion(): void
    {
        $this->modoEdicion = true;
        $this->mensaje     = 'Modo edición activo. Puedes modificar la asistencia registrada.';
        $this->tipo_mensaje = 'warning';
    }

    // =========================================================================
    // CARGAR FORMULARIO COMPLETO
    // =========================================================================
    private function cargarFormulario(): void
    {
        $this->estudiantes  = [];
        $this->yaRegistrada = false;
        $this->totalMarcados = 0;

        if (! $this->horario_id || ! $this->fecha) return;

        $horario = Horario::with(['materia', 'paralelo', 'asignacionDocente'])
            ->find($this->horario_id);

        if (! $horario) return;

        // Seguridad: horario pertenece al docente
        if (! $this->horarioPerteneceAlDocente($horario)) {
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'No tienes permisos para usar este horario.';
            return;
        }

        // Calcular totales
        $this->calcularTotalClasesModulo();

        if ($this->total_clases <= 0) {
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'No se pudo calcular el total de clases. Verifica el módulo y horarios.';
            return;
        }

        // Validar fecha dentro del módulo
        if (! $this->validarFechaDentroDelModulo()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'La fecha está fuera del rango del módulo ('
                . Carbon::parse($this->modulo->fecha_inicio)->format('d/m/Y')
                . ' – '
                . Carbon::parse($this->modulo->fecha_fin)->format('d/m/Y') . ').';
            return;
        }

        // Validar que la fecha coincida con el día del horario
        if (! $this->validarFechaCoincideConDiaHorario()) {
            $diaHorario = $horario->dia_semana;
            $this->tipo_mensaje = 'error';
            $this->mensaje      = "La fecha seleccionada no es {$diaHorario}. Elige una fecha que corresponda al día de clase.";
            return;
        }

        // =====================================================================
        // VERIFICAR SI YA EXISTE ASISTENCIA PARA ESTE HORARIO + FECHA
        // =====================================================================
        $detalles = DetalleMatricula::query()
            ->with('estudiante')
            ->whereHas(
                'matricula',
                fn($q) =>
                $q->where('periodo_id', $horario->periodo_id)
            )
            ->where('materia_id',  $horario->materia_id)
            ->where('paralelo_id', $horario->paralelo_id)
            ->where('estado', 'Inscrito')
            ->orderBy('id')
            ->get();

        $asistenciasExistentes = Asistencia::query()
            ->where('horario_id', $this->horario_id)
            ->where('fecha', $this->fecha)
            ->whereIn('detalle_matricula_id', $detalles->pluck('id'))
            ->get();

        // Si ya hay asistencias registradas para esta fecha → bloquear
        $this->yaRegistrada = $asistenciasExistentes->count() > 0;

        $asistenciasFecha = $asistenciasExistentes->keyBy('detalle_matricula_id');

        // Asistencias acumuladas del módulo para las barras
        $asistenciasModulo = Asistencia::query()
            ->whereHas(
                'horario',
                fn($q) =>
                $q->where('materia_id',  $horario->materia_id)
                    ->where('paralelo_id', $horario->paralelo_id)
                    ->where('periodo_id',  $horario->periodo_id)
            )
            ->whereIn('detalle_matricula_id', $detalles->pluck('id'))
            ->get()
            ->groupBy('detalle_matricula_id');

        $horaActual = Carbon::now()->format('H:i');

        $this->estudiantes = $detalles->map(function ($detalle) use (
            $asistenciasFecha,
            $asistenciasModulo,
            $horaActual
        ) {
            $asistenciaHoy = $asistenciasFecha->get($detalle->id);
            $lista         = $asistenciasModulo->get($detalle->id, collect());
            $asistidas     = $lista->whereIn('estado', ['Presente', 'Justificado', 'Tardanza'])->count();
            $pct           = $this->total_clases > 0
                ? round(($asistidas / $this->total_clases) * 100)
                : 0;

            return [
                'detalle_id'       => $detalle->id,
                'nombre'           => $detalle->estudiante?->nombre_completo
                    ?? $detalle->estudiante?->name
                    ?? '—',
                'matricula_numero' => $detalle->estudiante?->matricula_numero ?? '—',
                'estado'           => $asistenciaHoy?->estado ?? '',
                'hora_entrada'     => $asistenciaHoy?->hora_entrada ?? $horaActual,
                'observaciones'    => $asistenciaHoy?->observaciones ?? '',
                'asistidas'        => $asistidas,
                'total_clases'     => $this->total_clases,
                'pct_asistencia'   => $pct,
                'nota_asistencia'  => round(($asistidas / max($this->total_clases, 1)) * 10, 2),
                'en_riesgo'        => $pct < 80 && $pct >= 60,
                'critico'          => $pct < 60,
            ];
        })->toArray();

        $this->totalMarcados = collect($this->estudiantes)
            ->filter(fn($e) => ! empty($e['estado']))
            ->count();
    }

    // =========================================================================
    // GUARDAR
    // =========================================================================
    public function guardarAsistencias(): void
    {
        if ($this->sinPermiso('gestionar_asistencias')) return;
        if (empty($this->estudiantes)) return;

        if ($this->yaRegistrada && ! $this->modoEdicion) {
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'La asistencia ya fue registrada. Usa el botón de desbloquear para editar.';
            return;
        }

        if (! $this->horarioPerteneceAlDocente()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'No tienes permisos para guardar asistencias con este horario.';
            return;
        }

        if (! $this->validarFechaDentroDelModulo()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'No puedes registrar asistencia fuera del rango del módulo.';
            return;
        }

        if (! $this->validarFechaCoincideConDiaHorario()) {
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'No puedes registrar asistencia en un día que no corresponde al horario.';
            return;
        }

        try {
            DB::beginTransaction();

            foreach ($this->estudiantes as $est) {
                if (empty($est['estado'])) continue;

                Asistencia::updateOrCreate(
                    [
                        'detalle_matricula_id' => $est['detalle_id'],
                        'horario_id'           => $this->horario_id,
                        'fecha'                => $this->fecha,
                    ],
                    [
                        'estado'       => $est['estado'],
                        'hora_entrada' => ! empty($est['hora_entrada']) ? $est['hora_entrada'] : null,
                        'observaciones' => ! empty($est['observaciones']) ? $est['observaciones'] : null,
                        'docente_id'   => Auth::id(),
                    ]
                );
            }

            DB::commit();

            // Bloquear de nuevo tras guardar
            $this->yaRegistrada = true;
            $this->modoEdicion  = false;
            $this->tipo_mensaje = 'success';
            $this->mensaje      = '✓ Asistencias guardadas correctamente.';

            // Recargar para reflejar estado actualizado
            $this->cargarFormulario();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->tipo_mensaje = 'error';
            $this->mensaje      = 'Error al guardar: ' . $e->getMessage();
        }
    }

    // =========================================================================
    // ACCIONES MASIVAS
    // =========================================================================
    public function marcarTodosPresentes(): void
    {
        $hora = Carbon::now()->format('H:i');
        foreach ($this->estudiantes as $i => $_) {
            $this->estudiantes[$i]['estado']      = 'Presente';
            $this->estudiantes[$i]['hora_entrada'] = $hora;
        }
        $this->actualizarContador();
    }

    public function marcarTodosAusentes(): void
    {
        foreach ($this->estudiantes as $i => $_) {
            $this->estudiantes[$i]['estado']      = 'Ausente';
            $this->estudiantes[$i]['hora_entrada'] = '';
        }
        $this->actualizarContador();
    }

    public function actualizarContador(): void
    {
        $this->totalMarcados = collect($this->estudiantes)
            ->filter(fn($e) => ! empty($e['estado']))
            ->count();
    }

    // =========================================================================
    // MÓDULO Y VALIDACIONES
    // =========================================================================
    private function cargarModulo(): void
    {
        $this->modulo = null;
        if (! $this->horario_id) return;

        $horario = Horario::find($this->horario_id);
        if (! $horario) return;

        $this->modulo = MateriaPeriodoParalelo::query()
            ->where('periodo_id', $horario->periodo_id)
            ->where('materia_id', $horario->materia_id)
            ->where('paralelo_id', $horario->paralelo_id)
            ->where('is_active', true)
            ->first();
    }

    private function calcularTotalClasesModulo(): void
    {
        $this->total_clases = 0;
        $this->cargarModulo();

        if (! $this->modulo) return;

        $this->calcularDiasHorario();
        if (empty($this->dias_horario)) return;

        $inicio = Carbon::parse($this->modulo->fecha_inicio);
        $fin    = Carbon::parse($this->modulo->fecha_fin);

        $mapaInverso = [
            'Lunes'     => 'Monday',
            'Martes'    => 'Tuesday',
            'Miércoles' => 'Wednesday',
            'Jueves'    => 'Thursday',
            'Viernes'   => 'Friday',
            'Sábado'    => 'Saturday',
            'Domingo'   => 'Sunday',
        ];

        $count = 0;
        foreach (CarbonPeriod::create($inicio, $fin) as $dia) {
            $nombreDia = $this->mapaDia($dia->format('l'));
            if (in_array($nombreDia, $this->dias_horario)) $count++;
        }

        $this->total_clases = $count;
    }

    private function calcularDiasHorario(): void
    {
        $this->dias_horario = [];
        if (! $this->horario_id) return;

        $horario = Horario::find($this->horario_id);
        if (! $horario) return;

        $this->dias_horario = Horario::query()
            ->where('materia_id',  $horario->materia_id)
            ->where('paralelo_id', $horario->paralelo_id)
            ->where('periodo_id',  $horario->periodo_id)
            ->where('is_active', true)
            ->whereHas(
                'asignacionDocente',
                fn($q) =>
                $q->where('docente_id', Auth::id())
            )
            ->pluck('dia_semana')
            ->unique()
            ->values()
            ->toArray();
    }

    private function validarFechaDentroDelModulo(): bool
    {
        if (! $this->fecha) return false;
        $this->cargarModulo();
        if (! $this->modulo) return false;

        return Carbon::parse($this->fecha)->betweenIncluded(
            Carbon::parse($this->modulo->fecha_inicio),
            Carbon::parse($this->modulo->fecha_fin)
        );
    }

    private function validarFechaCoincideConDiaHorario(): bool
    {
        if (! $this->fecha || empty($this->dias_horario)) return true;
        $dia = $this->mapaDia(Carbon::parse($this->fecha)->format('l'));
        return $dia && in_array($dia, $this->dias_horario);
    }

    private function horarioPerteneceAlDocente(?object $horario = null): bool
    {
        $id = $this->horario_id;
        if (! $id) return false;

        return Horario::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->whereHas(
                'asignacionDocente',
                fn($q) =>
                $q->where('docente_id', Auth::id())
            )
            ->exists();
    }

    private function mapaDia(string $enIngles): string
    {
        return [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ][$enIngles] ?? $enIngles;
    }

    private function resetMensajes(): void
    {
        $this->mensaje      = null;
        $this->tipo_mensaje = 'success';
    }

    // =========================================================================
    // RENDER
    // =========================================================================
    public function render()
    {
        $user    = Auth::user();
        $periodos = Periodo::orderByDesc('id')->get();

        // Para modo manual — materias del docente
        $materiasAsignadas = [];
        $paralelos         = collect();
        $horariosManual    = collect();

        if ($this->modoManual && $this->periodo_id_manual) {
            $asignaciones = $user->asignacionesDocente()
                ->with(['materia', 'paralelo'])
                ->where('periodo_id', $this->periodo_id_manual)
                ->get();

            foreach ($asignaciones as $asig) {
                if (! $asig->materia || ! $asig->paralelo) continue;
                $materiasAsignadas[$asig->materia->id]['materia']    = $asig->materia;
                $materiasAsignadas[$asig->materia->id]['paralelos'][] = $asig->paralelo;
            }

            if ($this->materia_id_manual && isset($materiasAsignadas[$this->materia_id_manual])) {
                $paralelos = collect($materiasAsignadas[$this->materia_id_manual]['paralelos'] ?? [])
                    ->unique('id')->values();
            }

            if ($this->materia_id_manual && $this->paralelo_id_manual) {
                $horariosManual = Horario::query()
                    ->where('materia_id',  $this->materia_id_manual)
                    ->where('paralelo_id', $this->paralelo_id_manual)
                    ->where('periodo_id',  $this->periodo_id_manual)
                    ->where('is_active', true)
                    ->whereHas(
                        'asignacionDocente',
                        fn($q) =>
                        $q->where('docente_id', Auth::id())
                    )
                    ->orderBy('dia_semana')
                    ->orderBy('hora_inicio')
                    ->get();
            }
        }

        // Horario seleccionado actualmente
        $horarioActual = $this->horario_id
            ? Horario::with(['materia', 'paralelo'])->find($this->horario_id)
            : null;

        return view('livewire.docente.asistencia-estudiante', [
            'periodos'         => $periodos,
            'materiasAsignadas' => $materiasAsignadas,
            'paralelos'        => $paralelos,
            'horariosManual'   => $horariosManual,
            'horarioActual'    => $horarioActual,
            'horariosDia'      => $this->horariosDia,
        ]);
    }
}
