<?php

namespace App\Livewire\Docente;

use App\Models\AsignacionDocente;
use App\Models\Calificacion;
use App\Models\DetalleMatricula;
use App\Models\Materia;
use App\Models\MateriasArrastrada;
use App\Models\Paralelo;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Asistencia;
use App\Models\Horario;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use App\Services\SettingService;
use App\Traits\WithAuthorization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CalificacionEstudiante extends Component
{
    use WithAuthorization;
    const MAX_INTENTOS_PERMITIDOS = 3;

    // Filtros
    public $periodo_id  = '';
    public $materia_id  = '';
    public $paralelo_id = '';

    // Datos cargados
    public $periodos           = [];
    public $materias_asignadas = [];
    public $paralelos          = [];
    public $estudiantes        = [];

    // Estado del modal
    public $mostrar_formulario    = false;
    public $estudiante_seleccionado = null;
    public $calificacion_actual   = null;
    public $es_borrador           = false;
    public $suspenso              = false;

    // Fórmula del periodo seleccionado
    public bool  $nuevo_calculo          = true;
    public float $nota_minima_aprobacion = 7.00;

    // Campos de calificación
    public $insumo1 = '';
    public $insumo2 = '';
    public $insumo3 = '';
    public $insumo4 = '';
    public $insumo5 = '';

    public $es_arrastre    = false;
    public $numero_intento = 1; // auto-calculado desde historial

    // Calculados
    public $promedio_insumos = 0;
    public $nota_base        = 0;
    public $nota_final       = 0;
    public $examen_parcial   = '';
    public $examen_final     = '';
    public $nota_suspenso    = '';
    public $estado_final     = '';

    // Mensajes
    public $mensaje      = '';
    public $tipo_mensaje = 'success';

    // Asistencias
    public $asistencias_asistidas = 0;
    public $asistencias_totales   = 0;
    public $nota_asistencia       = 0.00;
    public $insumo1_manual        = false;

    protected $rules = [
        'insumo1'        => 'nullable|numeric|min:0|max:10',
        'insumo2'        => 'nullable|numeric|min:0|max:10',
        'insumo3'        => 'nullable|numeric|min:0|max:10',
        'insumo4'        => 'nullable|numeric|min:0|max:10',
        'insumo5'        => 'nullable|numeric|min:0|max:10',
        'examen_parcial' => 'nullable|numeric|min:0|max:10',
        'examen_final'   => 'nullable|numeric|min:0|max:10',
        'nota_suspenso'  => 'nullable|numeric|min:0|max:10',
    ];

    protected $messages = [
        'insumo1.numeric'        => 'La asistencia debe ser un número válido',
        'insumo1.min'            => 'La asistencia no puede ser menor a 0',
        'insumo1.max'            => 'La asistencia no puede ser mayor a 10',
        'insumo2.numeric'        => 'Las Actividades Autónomas debe ser un número válido',
        'insumo2.min'            => 'Las Actividades Autónomas no puede ser menor a 0',
        'insumo2.max'            => 'Las Actividades Autónomas no puede ser mayor a 10',
        'insumo3.numeric'        => 'Las Actividades Prácticas debe ser un número válido',
        'insumo3.min'            => 'Las Actividades Prácticas no puede ser menor a 0',
        'insumo3.max'            => 'Las Actividades Prácticas no puede ser mayor a 10',
        'insumo4.numeric'        => 'Las Actividades con Docente debe ser un número válido',
        'insumo4.min'            => 'Las Actividades con Docente no puede ser menor a 0',
        'insumo4.max'            => 'Las Actividades con Docente no puede ser mayor a 10',
        'insumo5.numeric'        => 'El valor de ética debe ser un número válido',
        'insumo5.min'            => 'El valor de ética no puede ser menor a 0',
        'insumo5.max'            => 'El valor de ética no puede ser mayor a 10',
        'examen_parcial.numeric' => 'El examen parcial debe ser un número válido',
        'examen_parcial.min'     => 'El examen parcial no puede ser menor a 0',
        'examen_parcial.max'     => 'El examen parcial no puede ser mayor a 10',
        'examen_final.numeric'   => 'El examen final debe ser un número válido',
        'examen_final.min'       => 'El examen final no puede ser menor a 0',
        'examen_final.max'       => 'El examen final no puede ser mayor a 10',
        'nota_suspenso.numeric'  => 'La nota de suspenso debe ser un número válido',
        'nota_suspenso.min'      => 'La nota de suspenso no puede ser menor a 0',
        'nota_suspenso.max'      => 'La nota de suspenso no puede ser mayor a 10',
    ];

    public function mount()
    {
        $this->cargarPeriodos();
        $this->cargarMateriasAsignadas();
    }

    public function cargarAsistenciaDelEstudiante($detalle_matricula_id)
    {
        if (!$this->periodo_id || !$this->materia_id || !$this->paralelo_id) {
            $this->asistencias_totales   = 0;
            $this->asistencias_asistidas = 0;
            $this->nota_asistencia       = 0.00;
            if (! $this->insumo1_manual) $this->insumo1 = number_format(0, 2, '.', '');
            return;
        }

        $modulo = DB::table('materia_periodo_paralelo')
            ->where('materia_id', $this->materia_id)
            ->where('periodo_id', $this->periodo_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->first();

        if (!$modulo) {
            $this->asistencias_totales   = 0;
            $this->asistencias_asistidas = 0;
            $this->nota_asistencia       = 0.00;
            if (! $this->insumo1_manual) $this->insumo1 = number_format(0, 2, '.', '');
            return;
        }

        $inicio = Carbon::parse($modulo->fecha_inicio)->startOfDay();
        $fin    = Carbon::parse($modulo->fecha_fin)->startOfDay();

        $diasHorario = Horario::where('periodo_id', $this->periodo_id)
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->pluck('dia_semana')
            ->unique()
            ->values()
            ->toArray();

        if (empty($diasHorario)) {
            $this->asistencias_totales   = 0;
            $this->asistencias_asistidas = 0;
            $this->nota_asistencia       = 0.00;
            if (! $this->insumo1_manual) $this->insumo1 = number_format(0, 2, '.', '');
            return;
        }

        $mapDias = [
            'Lunes'     => 1, 'Martes'    => 2, 'Miércoles' => 3,
            'Jueves'    => 4, 'Viernes'   => 5, 'Sábado'    => 6,
        ];

        $diasIso = collect($diasHorario)
            ->map(fn($d) => $mapDias[$d] ?? null)
            ->filter()
            ->values()
            ->toArray();

        $period             = CarbonPeriod::create($inicio, $fin);
        $total_clases_modulo = 0;
        foreach ($period as $date) {
            if (in_array($date->dayOfWeekIso, $diasIso)) {
                $total_clases_modulo++;
            }
        }

        $horarios_ids = Horario::where('periodo_id', $this->periodo_id)
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->pluck('id');

        $asistidas_estudiante = Asistencia::where('detalle_matricula_id', $detalle_matricula_id)
            ->whereIn('horario_id', $horarios_ids)
            ->whereIn('estado', ['Presente', 'Justificado', 'Tardanza'])
            ->count();

        $this->asistencias_totales  = $total_clases_modulo;
        $this->asistencias_asistidas = $asistidas_estudiante;

        $this->nota_asistencia = $total_clases_modulo > 0
            ? round(($asistidas_estudiante / $total_clases_modulo) * 10, 2)
            : 0.00;

        if (! $this->insumo1_manual) {
            $this->insumo1 = number_format($this->nota_asistencia, 2, '.', '');
        }
    }

    public function cargarPeriodos()
    {
        $this->periodos      = Periodo::orderBy('fecha_inicio', 'desc')->get();
        $periodoActivo       = Periodo::periodoActivoGlobal();
        $this->periodo_id    = $periodoActivo?->id ?? '';
        $this->nuevo_calculo = $periodoActivo?->nuevo_calculo ?? true;
    }

    public function cargarMateriasAsignadas()
    {
        if (!$this->periodo_id) return;

        $docente_id = Auth::id();

        $this->materias_asignadas = AsignacionDocente::with(['materia', 'paralelo'])
            ->where('docente_id', $docente_id)
            ->where('periodo_id', $this->periodo_id)
            ->get()
            ->groupBy('materia.name')
            ->map(function ($asignaciones) {
                return [
                    'materia'   => $asignaciones->first()->materia,
                    'paralelos' => $asignaciones->pluck('paralelo'),
                ];
            });
    }

    public function updatedPeriodoId()
    {
        $this->materia_id  = '';
        $this->paralelo_id = '';
        $this->estudiantes = [];
        $this->mostrar_formulario = false;
        $this->cargarMateriasAsignadas();

        if ($this->periodo_id) {
            $periodo             = Periodo::find($this->periodo_id);
            $this->nuevo_calculo = $periodo?->nuevo_calculo ?? true;
        }
    }

    public function updatedMateriaId()
    {
        $this->paralelo_id = '';
        $this->estudiantes = [];
        $this->mostrar_formulario = false;

        if ($this->materia_id) {
            $materia = Materia::find($this->materia_id);
            $this->nota_minima_aprobacion = floatval($materia?->nota_minima_aprobacion ?? 7.00);
        } else {
            $this->nota_minima_aprobacion = 7.00;
        }

        $this->cargarParalelos();
    }

    public function updatedParaleloId()
    {
        $this->estudiantes = [];
        $this->mostrar_formulario = false;
        $this->cargarEstudiantes();
    }

    public function cargarParalelos()
    {
        if (!$this->materia_id) return;

        $this->paralelos = AsignacionDocente::with('paralelo')
            ->where('docente_id', Auth::id())
            ->where('periodo_id', $this->periodo_id)
            ->where('materia_id', $this->materia_id)
            ->get()
            ->pluck('paralelo');
    }

    public function cargarEstudiantes()
    {
        if (!$this->materia_id || !$this->paralelo_id) return;

        $this->estudiantes = DetalleMatricula::with([
            'estudiante',
            'matricula',
            'materia',
            'paralelo',
            'calificaciones' => function ($query) {
                $query->where('docente_id', Auth::id());
            },
        ])
            ->whereHas('matricula', fn($q) => $q->where('periodo_id', $this->periodo_id))
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('estado', 'Inscrito')
            ->get()
            ->map(function ($detalle) {
                $calificacion = $detalle->calificaciones->first();

                return [
                    'detalle_matricula_id' => $detalle->id,
                    'estudiante'           => $detalle->estudiante,
                    'codigo_matricula'     => $detalle->matricula->code ?? '---',
                    'tipo'                 => $detalle->tipo,
                    'es_repeticion'        => $detalle->es_repeticion,
                    'calificacion'         => $calificacion,
                    'tiene_calificacion'   => (bool) $calificacion,
                    'nota_final'           => $calificacion?->nota_final,
                    'estado_final'         => $calificacion?->estado_final ?? 'Pendiente',
                    'es_borrador'          => $calificacion?->es_borrador ?? false,
                ];
            });
    }

    // ── Auto-calculate attempt number from history ────────────────────────────
    protected function calcularNumeroIntento(int $estudiante_id, int $materia_id): int
    {
        $intentos_previos = MateriasArrastrada::where('user_id', $estudiante_id)
            ->where('materia_id', $materia_id)
            ->where('periodo_reprobado_id', '!=', $this->periodo_id)
            ->count();

        return min($intentos_previos + 1, self::MAX_INTENTOS_PERMITIDOS);
    }

    public function abrirFormularioCalificacion($detalle_matricula_id)
    {
        $this->resetearFormulario();

        $estudiante_data = $this->estudiantes->firstWhere('detalle_matricula_id', $detalle_matricula_id);

        if (!$estudiante_data) {
            $this->mensaje      = 'Estudiante no encontrado';
            $this->tipo_mensaje = 'error';
            return;
        }

        $this->estudiante_seleccionado = $estudiante_data;
        $this->mostrar_formulario      = true;

        $this->cargarAsistenciaDelEstudiante($detalle_matricula_id);

        if ($estudiante_data['calificacion']) {
            $this->cargarCalificacionExistente($estudiante_data['calificacion']);
        } else {
            $this->es_arrastre = $estudiante_data['tipo'] === 'Arrastre';

            $detalle = DetalleMatricula::with('estudiante')->find($detalle_matricula_id);
            if ($detalle && $detalle->estudiante) {
                $this->numero_intento = $this->calcularNumeroIntento(
                    $detalle->estudiante->id,
                    (int) $this->materia_id
                );
            }
        }
    }

    public function cargarCalificacionExistente($calificacion)
    {
        $this->calificacion_actual = $calificacion;

        $savedInsumo1 = $calificacion->insumo1 !== null
            ? number_format((float) $calificacion->insumo1, 2, '.', '')
            : null;
        $autoInsumo1  = number_format($this->nota_asistencia, 2, '.', '');

        if ($savedInsumo1 !== null && $savedInsumo1 !== $autoInsumo1) {
            $this->insumo1_manual = true;
            $this->insumo1        = $savedInsumo1;
        } else {
            $this->insumo1_manual = false;
            $this->insumo1        = $autoInsumo1;
        }

        $this->insumo2             = $calificacion->insumo2;
        $this->insumo3             = $calificacion->insumo3;
        $this->insumo4             = $calificacion->insumo4;
        $this->insumo5             = $calificacion->insumo5;
        $this->examen_parcial      = $calificacion->examen_parcial;
        $this->examen_final        = $calificacion->examen_final;
        $this->nota_suspenso       = $calificacion->nota_suspenso;
        $this->es_arrastre         = $calificacion->es_arrastre;
        $this->numero_intento      = $calificacion->numero_intento;
        $this->es_borrador         = $calificacion->es_borrador ?? false;

        $this->calcularPromedios();
    }

    public function resetearFormulario()
    {
        $this->estudiante_seleccionado = null;
        $this->calificacion_actual     = null;
        $this->insumo1                 = '';
        $this->insumo2                 = '';
        $this->insumo3                 = '';
        $this->insumo4                 = '';
        $this->insumo5                 = '';
        $this->examen_parcial          = '';
        $this->examen_final            = '';
        $this->nota_suspenso           = '';
        $this->es_arrastre             = false;
        $this->numero_intento          = 1;
        $this->promedio_insumos        = 0;
        $this->nota_base               = 0;
        $this->nota_final              = 0;
        $this->estado_final            = '';
        $this->es_borrador             = false;
        $this->suspenso                = false;
        $this->mostrar_formulario      = false;
        $this->insumo1_manual          = false;
        $this->mensaje                 = '';
    }

    public function calcularPromedios()
    {
        $insumos = collect([$this->insumo1, $this->insumo2, $this->insumo3, $this->insumo4, $this->insumo5])
            ->filter(fn($v) => $v !== '' && $v !== null);

        $this->promedio_insumos = $insumos->count() > 0 ? round($insumos->avg(), 2) : 0;

        $this->calcularNotaFinal();
    }

    public function calcularNotaFinal()
    {
        $promedio = $this->promedio_insumos;
        $parcial  = ($this->examen_parcial !== '' && $this->examen_parcial !== null) ? floatval($this->examen_parcial) : 0;
        $final    = ($this->examen_final   !== '' && $this->examen_final   !== null) ? floatval($this->examen_final)   : 0;

        if ($this->nuevo_calculo) {
            $this->nota_base = ($promedio > 0 || $parcial > 0 || $final > 0)
                ? round(($promedio * 0.6) + ($parcial * 0.2) + ($final * 0.2), 2)
                : 0;
        } else {
            $this->nota_base = ($promedio > 0 || $parcial > 0 || $final > 0)
                ? round(($promedio * 0.3) + ($parcial * 0.3) + ($final * 0.4), 2)
                : 0;
        }

        $nota_minima_arrastre = $this->nota_minima_aprobacion - 3;

        $this->nota_final = $this->nota_base;

        if ($this->nota_base >= $nota_minima_arrastre && $this->nota_base < $this->nota_minima_aprobacion) {
            $this->suspenso = true;

            if ($this->nota_suspenso !== '' && $this->nota_suspenso !== null) {
                $brecha           = $this->nota_minima_aprobacion - $nota_minima_arrastre;
                $incremento       = (floatval($this->nota_suspenso) / 10) * $brecha;
                $this->nota_final = round($this->nota_base + $incremento, 2);
            }
        } else {
            $this->suspenso      = false;
            $this->nota_suspenso = '';
        }

        $this->determinarEstadoFinal();
    }

    public function determinarEstadoFinal()
    {
        $nota_minima_arrastre = $this->nota_minima_aprobacion - 3;

        if ($this->nota_final >= $this->nota_minima_aprobacion) {
            $this->estado_final = 'Aprobado';
        } elseif ($this->nota_base >= $nota_minima_arrastre && $this->nota_base < $this->nota_minima_aprobacion) {
            if ($this->nota_suspenso !== null && $this->nota_suspenso !== '') {
                $this->estado_final = $this->nota_final >= $this->nota_minima_aprobacion ? 'Aprobado' : 'Reprobado';
            } else {
                $this->estado_final = 'Incompleto';
            }
        } elseif ($this->nota_base < $nota_minima_arrastre) {
            $this->estado_final = 'Reprobado';
        } else {
            $this->estado_final = 'Incompleto';
        }
    }

    public function updatedInsumo1Manual()
    {
        if (! $this->insumo1_manual) {
            $this->insumo1 = number_format($this->nota_asistencia, 2, '.', '');
            $this->calcularPromedios();
        }
    }

    public function updatedInsumo1()  { $this->calcularPromedios(); }
    public function updatedInsumo2()  { $this->calcularPromedios(); }
    public function updatedInsumo3()  { $this->calcularPromedios(); }
    public function updatedInsumo4()  { $this->calcularPromedios(); }
    public function updatedInsumo5()  { $this->calcularPromedios(); }
    public function updatedExamenParcial() { $this->calcularNotaFinal(); }
    public function updatedExamenFinal()   { $this->calcularNotaFinal(); }
    public function updatedNotaSuspenso()  { $this->calcularNotaFinal(); }

    protected function obtenerCamposIncompletos(): array
    {
        $faltantes = [];

        if ($this->insumo2 === '' || $this->insumo2 === null) $faltantes[] = 'Actividades Autónomas';
        if ($this->insumo3 === '' || $this->insumo3 === null) $faltantes[] = 'Actividades Prácticas';
        if ($this->insumo4 === '' || $this->insumo4 === null) $faltantes[] = 'Actividades con Docente';
        if ($this->insumo5 === '' || $this->insumo5 === null) $faltantes[] = 'Ética';
        if ($this->examen_parcial === '' || $this->examen_parcial === null)
            $faltantes[] = $this->nuevo_calculo ? 'Examen Parcial (20%)' : 'Examen Parcial (30%)';
        if ($this->examen_final === '' || $this->examen_final === null)
            $faltantes[] = $this->nuevo_calculo ? 'Examen Final (20%)' : 'Examen Final (40%)';
        if ($this->suspenso && ($this->nota_suspenso === '' || $this->nota_suspenso === null)) {
            $faltantes[] = 'Nota de Suspenso';
        }

        return $faltantes;
    }

    protected function calificacionCompleta(): bool
    {
        $insumos_completos = ($this->insumo1 !== '' && $this->insumo1 !== null)
            && ($this->insumo2 !== '' && $this->insumo2 !== null)
            && ($this->insumo3 !== '' && $this->insumo3 !== null)
            && ($this->insumo4 !== '' && $this->insumo4 !== null)
            && ($this->insumo5 !== '' && $this->insumo5 !== null);

        $tiene_parcial = $this->examen_parcial !== '' && $this->examen_parcial !== null;
        $tiene_final   = $this->examen_final   !== '' && $this->examen_final   !== null;

        $nota_minima_arrastre = $this->nota_minima_aprobacion - 3;

        if ($this->nota_base >= $nota_minima_arrastre && $this->nota_base < $this->nota_minima_aprobacion) {
            $tiene_suspenso = $this->nota_suspenso !== '' && $this->nota_suspenso !== null;
            return $insumos_completos && $tiene_parcial && $tiene_final && $tiene_suspenso;
        }

        return $insumos_completos && $tiene_parcial && $tiene_final;
    }

    // ── Guardar borrador (guardado parcial sin requerir datos completos) ────────
    public function guardarBorrador()
    {
        if ($this->sinPermiso('ingresar_notas_estudiantes')) return;

        try {
            $this->cargarAsistenciaDelEstudiante($this->estudiante_seleccionado['detalle_matricula_id']);

            $this->validate();

            DB::beginTransaction();

            $datos = [
                'insumo1'              => $this->insumo1 !== '' ? $this->insumo1 : null,
                'insumo2'              => $this->insumo2 !== '' ? $this->insumo2 : null,
                'insumo3'              => $this->insumo3 !== '' ? $this->insumo3 : null,
                'insumo4'              => $this->insumo4 !== '' ? $this->insumo4 : null,
                'insumo5'              => $this->insumo5 !== '' ? $this->insumo5 : null,
                'promedio_insumos'     => $this->promedio_insumos,
                'examen_parcial'       => $this->examen_parcial !== '' ? $this->examen_parcial : null,
                'examen_final'         => $this->examen_final   !== '' ? $this->examen_final   : null,
                'nota_final'           => $this->nota_final,
                'nota_suspenso'        => $this->nota_suspenso  !== '' ? $this->nota_suspenso  : null,
                'estado_final'         => $this->estado_final ?: 'Incompleto',
                'es_arrastre'          => $this->es_arrastre,
                'numero_intento'       => $this->numero_intento,
                'es_borrador'          => true,
                'detalle_matricula_id' => $this->estudiante_seleccionado['detalle_matricula_id'],
                'docente_id'           => Auth::id(),
            ];

            if ($this->calificacion_actual) {
                $this->calificacion_actual->update($datos);
                $this->calificacion_actual = $this->calificacion_actual->fresh();
            } else {
                $this->calificacion_actual = Calificacion::create($datos);
            }

            $this->es_borrador = true;

            DB::commit();

            $this->cargarEstudiantes();

            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => '¡Borrador guardado!',
                'text'  => 'Puedes continuar ingresando las notas restantes.',
                'timer' => 2500,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Livewire maneja los errores inline
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al guardar',
                'text'  => 'Ocurrió un error: ' . $e->getMessage(),
            ]);
        }
    }

    // ── Publicar calificación (requiere datos completos) ──────────────────────
    public function guardarCalificacion()
    {
        if ($this->sinPermiso('ingresar_notas_estudiantes')) return;

        try {
            $this->cargarAsistenciaDelEstudiante($this->estudiante_seleccionado['detalle_matricula_id']);
        } catch (\Exception $e) {
            // asistencia no crítica, continuar con insumo1 ya seteado
        }

        $this->validate();

        if (!$this->calificacionCompleta()) {
            $faltantes = $this->obtenerCamposIncompletos();
            $items = implode('', array_map(
                fn($f) => "<li style='padding:3px 0;display:flex;align-items:center;gap:6px'>"
                        . "<span style='color:#f59e0b;font-size:1rem'>⚠</span> {$f}</li>",
                $faltantes
            ));
            $this->dispatch('swal', [
                'icon'  => 'warning',
                'title' => 'Campos incompletos',
                'html'  => '<p style="font-size:0.9rem;color:#64748b;margin-bottom:10px;text-align:center">'
                         . 'Para publicar las notas debes completar:</p>'
                         . '<ul style="text-align:left;font-size:0.875rem;color:#334155;list-style:none;padding:0;margin:0">'
                         . $items . '</ul>',
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $datos_calificacion = [
                'insumo1'              => $this->insumo1 ?: null,
                'insumo2'              => $this->insumo2 ?: null,
                'insumo3'              => $this->insumo3 ?: null,
                'insumo4'              => $this->insumo4 ?: null,
                'insumo5'              => $this->insumo5 ?: null,
                'promedio_insumos'     => $this->promedio_insumos,
                'examen_parcial'       => $this->examen_parcial ?: null,
                'examen_final'         => $this->examen_final ?: null,
                'nota_final'           => $this->nota_final,
                'nota_suspenso'        => $this->nota_suspenso ?: null,
                'estado_final'         => $this->estado_final,
                'es_arrastre'          => $this->es_arrastre,
                'numero_intento'       => $this->numero_intento,
                'es_borrador'          => false,
                'detalle_matricula_id' => $this->estudiante_seleccionado['detalle_matricula_id'],
                'docente_id'           => Auth::id(),
            ];

            if ($this->calificacion_actual) {
                $calificacion_anterior = $this->calificacion_actual->toArray();
                $this->calificacion_actual->update($datos_calificacion);
                $calificacion_guardada = $this->calificacion_actual->fresh();
                $this->registrarAuditoria($calificacion_anterior, $datos_calificacion);
            } else {
                $calificacion_guardada = Calificacion::create($datos_calificacion);
            }

            $this->gestionarMateriaArrastrada($calificacion_guardada);

            DB::commit();

            $nombre = $this->estudiante_seleccionado['estudiante']->name ?? '';

            $this->cargarEstudiantes();
            $this->resetearFormulario();

            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => '¡Notas publicadas!',
                'text'  => $nombre ? "Calificación de {$nombre} registrada correctamente." : 'Calificación registrada correctamente.',
                'timer' => 3000,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al publicar',
                'text'  => 'Ocurrió un error: ' . $e->getMessage(),
            ]);
        }
    }

    protected function calcularCostoAdicional($materia_id, int $numero_intento = 1)
    {
        try {
            $materia = Materia::find($materia_id);
            if (!$materia || !$materia->semestre_id) return 0;

            $semestre = \App\Models\Semestre::find($materia->semestre_id);
            if (!$semestre || !$semestre->carrera_id) return 0;

            $carrera = \App\Models\Carrera::find($semestre->carrera_id);
            if (!$carrera) return 0;

            $porcentaje    = floatval(SettingService::get('matricula.porcentaje_arrastre', 30));
            // Intento 2 = falla por 2da vez → siguiente inscripción (intento 3) cuesta el doble
            if ($numero_intento >= 2) {
                $porcentaje *= 2;
            }

            $creditosVivos   = ($materia->horas_teoricas + $materia->horas_practicas) / 48;
            $costo_normal    = $creditosVivos * $carrera->costo_credito;
            $costo_adicional = $costo_normal * ($porcentaje / 100);

            return round($costo_adicional, 2);
        } catch (\Exception $e) {
            Log::error('Error al calcular costo adicional: ' . $e->getMessage());
            return 0;
        }
    }

    protected function gestionarMateriaArrastrada($calificacion_guardada)
    {
        $detalle_matricula = DetalleMatricula::with(['matricula', 'estudiante'])
            ->find($this->estudiante_seleccionado['detalle_matricula_id']);

        if (!$detalle_matricula) return;

        $estudiante_id = $detalle_matricula->estudiante->id;
        $materia_id    = $this->materia_id;
        $periodo_id    = $this->periodo_id;

        $arrastre_existente = MateriasArrastrada::where('user_id', $estudiante_id)
            ->where('materia_id', $materia_id)
            ->where('periodo_reprobado_id', $periodo_id)
            ->first();

        $nota_minima_arrastre = $this->nota_minima_aprobacion - 3;

        if ($this->calificacionCompleta()) {
            if ($this->nota_final >= $this->nota_minima_aprobacion) {
                $arrastre_existente?->delete();
            } elseif ($this->nota_final < $nota_minima_arrastre) {
                $numero_intento  = $arrastre_existente?->numero_intento ?? $this->numero_intento;
                $porcentaje_base = floatval(SettingService::get('matricula.porcentaje_arrastre', 30));
                $porcentaje_real = $numero_intento >= 2 ? $porcentaje_base * 2 : $porcentaje_base;
                $costo_adicional = $this->calcularCostoAdicional($materia_id, $numero_intento);

                $datos_arrastre = [
                    'user_id'               => $estudiante_id,
                    'materia_id'            => $materia_id,
                    'periodo_reprobado_id'  => $periodo_id,
                    'nota_obtenida'         => $this->nota_final,
                    'nota_minima_requerida' => $this->nota_minima_aprobacion,
                    'porcentaje_penalizacion' => $porcentaje_real,
                    'numero_intento'        => $numero_intento,
                    'estado'                => 'Perdida_Definitiva',
                    'costo_adicional'       => $costo_adicional,
                ];

                $arrastre_existente
                    ? $arrastre_existente->update($datos_arrastre)
                    : MateriasArrastrada::create($datos_arrastre);
            } elseif ($this->nota_final >= $nota_minima_arrastre && $this->nota_final < $this->nota_minima_aprobacion) {
                $tiene_suspenso = $this->nota_suspenso !== '' && $this->nota_suspenso !== null;

                if ($tiene_suspenso) {
                    $numero_intento  = $arrastre_existente?->numero_intento ?? $this->numero_intento;
                    $porcentaje_base = floatval(SettingService::get('matricula.porcentaje_arrastre', 30));
                    $porcentaje_real = $numero_intento >= 2 ? $porcentaje_base * 2 : $porcentaje_base;
                    $costo_adicional = $this->calcularCostoAdicional($materia_id, $numero_intento);

                    $datos_arrastre = [
                        'user_id'               => $estudiante_id,
                        'materia_id'            => $materia_id,
                        'periodo_reprobado_id'  => $periodo_id,
                        'nota_obtenida'         => $this->nota_final,
                        'nota_minima_requerida' => $this->nota_minima_aprobacion,
                        'porcentaje_penalizacion' => $porcentaje_real,
                        'numero_intento'        => $numero_intento,
                        'estado'                => 'Arrastrada',
                        'costo_adicional'       => $costo_adicional,
                    ];

                    $arrastre_existente
                        ? $arrastre_existente->update($datos_arrastre)
                        : MateriasArrastrada::create($datos_arrastre);
                }
            }
        }
    }

    protected function registrarAuditoria($datos_anteriores, $datos_nuevos)
    {
        $campos_auditables = [
            'insumo1', 'insumo2', 'insumo3', 'insumo4', 'insumo5',
            'promedio_insumos', 'examen_parcial', 'examen_final',
            'nota_final', 'nota_suspenso', 'estado_final', 'es_arrastre', 'numero_intento',
        ];

        foreach ($campos_auditables as $campo) {
            if (isset($datos_anteriores[$campo], $datos_nuevos[$campo])
                && $datos_anteriores[$campo] != $datos_nuevos[$campo]) {

                \App\Models\AuditoriaCalificacion::create([
                    'campo_modificado'    => $campo,
                    'valor_anterior'      => $datos_anteriores[$campo],
                    'valor_nuevo'         => $datos_nuevos[$campo],
                    'motivo_modificacion' => 'Modificación por docente',
                    'calificacion_id'     => $this->calificacion_actual->id,
                    'docente_id'          => Auth::id(),
                    'fecha_modificacion'  => now(),
                ]);
            }
        }
    }

    public function cerrarFormulario()
    {
        $this->resetearFormulario();
    }

    // ── Exportar Acta de Calificaciones ──────────────────────────────────────
    public function exportarActaPDF()
    {
        if (!$this->periodo_id || !$this->materia_id || !$this->paralelo_id) return;

        $materia  = Materia::with('semestre.carrera')->find($this->materia_id);
        $paralelo = Paralelo::find($this->paralelo_id);
        $periodo  = Periodo::find($this->periodo_id);
        $docente  = Auth::user();

        if (!$materia || !$paralelo || !$periodo) return;

        // Cargar estudiantes con calificaciones frescas desde BD
        $detalles = DetalleMatricula::with([
            'estudiante',
            'matricula',
            'calificaciones' => fn($q) => $q->where('docente_id', $docente->id)->latest(),
        ])
            ->whereHas('matricula', fn($q) => $q->where('periodo_id', $this->periodo_id))
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('estado', 'Inscrito')
            ->get()
            ->sortBy('estudiante.name')
            ->values();

        // Pesos según fórmula del período
        $pct_pi = $this->nuevo_calculo ? 0.60 : 0.30;
        $pct_ep = $this->nuevo_calculo ? 0.20 : 0.30;
        $pct_ef = $this->nuevo_calculo ? 0.20 : 0.40;

        $nota_minima = $materia->nota_minima_aprobacion ?? 7.00;

        $filas = $detalles->map(function ($det, $idx) use ($pct_pi, $pct_ep, $pct_ef, $nota_minima) {
            $cal = $det->calificaciones->first();

            $i1  = $cal ? floatval($cal->insumo1 ?? 0) : null;
            $i2  = $cal ? floatval($cal->insumo2 ?? 0) : null;
            $i3  = $cal ? floatval($cal->insumo3 ?? 0) : null;
            $i4  = $cal ? floatval($cal->insumo4 ?? 0) : null;
            $i5  = $cal ? floatval($cal->insumo5 ?? 0) : null;

            $prom_ins  = $cal ? floatval($cal->promedio_insumos ?? 0) : null;
            $ep        = $cal ? floatval($cal->examen_parcial ?? 0) : null;
            $ef        = $cal ? floatval($cal->examen_final ?? 0) : null;
            $nota_susp = ($cal && $cal->nota_suspenso !== null) ? floatval($cal->nota_suspenso) : null;
            $nota_fin  = $cal ? floatval($cal->nota_final ?? 0) : null;

            // Columnas calculadas
            $col_pi  = $prom_ins !== null ? round($prom_ins * $pct_pi, 2) : null;
            $col_ep  = $ep       !== null ? round($ep * $pct_ep, 2)       : null;
            $col_ef  = $ef       !== null ? round($ef * $pct_ef, 2)       : null;

            // Nota base (sin suspenso)
            $nota_base = ($col_pi !== null && $col_ep !== null && $col_ef !== null)
                ? round($col_pi + $col_ep + $col_ef, 2)
                : null;

            // Porcentaje del suspenso (incremento que otorgó)
            $nota_minima_arr = $nota_minima - 3;
            $pct_susp = null;
            if ($nota_susp !== null && $nota_base !== null
                && $nota_base >= $nota_minima_arr && $nota_base < $nota_minima) {
                $brecha   = $nota_minima - $nota_minima_arr;
                $pct_susp = round(($nota_susp / 10) * $brecha, 2);
            }

            return [
                'num'         => $idx + 1,
                'nombre'      => $det->estudiante?->name ?? '—',
                'cedula'      => $det->estudiante?->cedula ?? '—',
                'matricula'   => $det->matricula?->code ?? '—',
                'tipo'        => $det->tipo,
                'i1'          => $i1,
                'i2'          => $i2,
                'i3'          => $i3,
                'i4'          => $i4,
                'i5'          => $i5,
                'prom_ins'    => $prom_ins,
                'col_pi'      => $col_pi,
                'ep'          => $ep,
                'col_ep'      => $col_ep,
                'ef'          => $ef,
                'col_ef'      => $col_ef,
                'nota_base'   => $nota_base,
                'nota_susp'   => $nota_susp,
                'pct_susp'    => $pct_susp,
                'nota_fin'    => $nota_fin,
                'estado'      => $cal?->estado_final ?? 'Pendiente',
                'es_borrador' => $cal?->es_borrador ?? false,
                'sin_notas'   => $cal === null,
            ];
        });

        // Logo
        $logoPath = SettingService::get('instituto.logo_path');
        $logoFile = $logoPath
            ? storage_path('app/public/' . $logoPath)
            : public_path('imagenes/icono.webp');
        $ext    = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
        $mime   = match($ext) { 'png' => 'png', 'gif' => 'gif', 'webp' => 'webp', default => 'jpeg' };
        $logo64 = file_exists($logoFile)
            ? "data:image/{$mime};base64," . base64_encode(file_get_contents($logoFile))
            : null;

        $data = [
            'filas'         => $filas,
            'materia'       => $materia,
            'paralelo'      => $paralelo,
            'periodo'       => $periodo,
            'docente'       => $docente,
            'nuevo_calculo' => $this->nuevo_calculo,
            'pct_pi'        => (int) ($pct_pi * 100),
            'pct_ep'        => (int) ($pct_ep * 100),
            'pct_ef'        => (int) ($pct_ef * 100),
            'nota_minima'   => $nota_minima,
            'logo64'        => $logo64,
            'instituto' => [
                'nombre_largo'  => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
                'nombre_corto'  => SettingService::get('instituto.nombre_corto', 'ISTC'),
                'ruc'           => SettingService::get('instituto.ruc', ''),
                'senescyt'      => SettingService::get('instituto.senescyt', ''),
                'direccion'     => SettingService::get('instituto.direccion', ''),
                'telefono'      => SettingService::get('instituto.telefono', ''),
                'ciudad'        => SettingService::get('documentos.ciudad', 'Ecuador'),
                'rector'        => SettingService::get('documentos.rector', ''),
                'secretario'    => SettingService::get('documentos.secretario', ''),
                'pie_pagina'    => SettingService::get('documentos.pie_pagina',
                    'Documento generado por el Sistema Académico. Válido solo con firma y sello institucional.'),
            ],
        ];

        $pdf = Pdf::loadView('pdf.acta-calificaciones', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('dpi', 96);

        $pdf->render();
        $canvas = $pdf->getDomPDF()->getCanvas();
        $this->agregarFooterActa($canvas, $data['instituto']['pie_pagina']);

        $slug   = Str::slug($materia->name ?? 'materia');
        $nombre = "acta_{$slug}_{$paralelo->code}_{$periodo->code}.pdf";

        return response()->streamDownload(fn() => print($pdf->output()), $nombre);
    }

    private function agregarFooterActa(\Dompdf\Canvas $canvas, string $piePagina): void
    {
        $font  = $canvas->get_dompdf()->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $w     = $canvas->get_width();
        $h     = $canvas->get_height();
        $yLine = $h - 26;
        $yTxt  = $h - 18;

        $canvas->page_line(10, $yLine, $w - 10, $yLine, [0.08, 0.27, 0.10], 0.5);
        $canvas->page_text(10, $yTxt, $piePagina, $font, 5.5, [0.58, 0.64, 0.71]);
        $canvas->page_text($w - 68, $yTxt, 'Pág. {PAGE_NUM} / {PAGE_COUNT}', $font, 7, [0.08, 0.27, 0.10]);
    }

    public function render()
    {
        return view('livewire.docente.calificacion-estudiante');
    }
}
