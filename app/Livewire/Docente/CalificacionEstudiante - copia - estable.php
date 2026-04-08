<?php

namespace App\Livewire\Docente;

use App\Models\AsignacionDocente;
use App\Models\Calificacion;
use App\Models\DetalleMatricula;
use App\Models\Materia;
//use App\Models\MateriaArrastrada;
use App\Models\MateriasArrastrada;
use App\Models\Paralelo;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CalificacionEstudiante extends Component
{
    // Constante de penalización (puedes cambiarla cuando quieras)
    const PORCENTAJE_PENALIZACION = 5; // 5%
    const NOTA_MINIMA_APROBACION = 7;
    const NOTA_MINIMA_ARRASTRE = 4;
    const MAX_INTENTOS_PERMITIDOS = 3;

    // Propiedades para filtros
    public $periodo_id = '';
    public $materia_id = '';
    public $paralelo_id = '';

    // Datos cargados
    public $periodos = [];
    public $materias_asignadas = [];
    public $paralelos = [];
    public $estudiantes = [];

    // Estado del componente
    public $mostrar_formulario = false;
    public $estudiante_seleccionado = null;
    public $calificacion_actual = null;

    // Campos de calificación
    public $insumo1 = '';
    public $insumo2 = '';
    public $insumo3 = '';
    public $insumo4 = '';
    public $insumo5 = '';

    public $es_arrastre = false;
    public $numero_intento = 1;

    // Propiedades calculadas
    public $promedio_insumos = 0;
    public $nota_final = 0;
    public $examen_parcial = 0;
    public $examen_final = '';
    public $nota_suspenso = '';
    public $estado_final = '';

    // Mensajes
    public $mensaje = '';
    public $tipo_mensaje = 'success';

    protected $rules = [
        'insumo1' => 'nullable|numeric|min:0|max:10',
        'insumo2' => 'nullable|numeric|min:0|max:10',
        'insumo3' => 'nullable|numeric|min:0|max:10',
        'insumo4' => 'nullable|numeric|min:0|max:10',
        'insumo5' => 'nullable|numeric|min:0|max:10',
        'examen_parcial' => 'nullable|numeric|min:0|max:10',
        'examen_final' => 'nullable|numeric|min:0|max:10',
        'nota_suspenso' => 'nullable|numeric|min:0|max:10',
        'numero_intento' => 'required|integer|min:1|max:3',
    ];

    protected $messages = [
        'insumo1.numeric' => 'El insumo 1 debe ser un número válido',
        'insumo1.min' => 'El insumo 1 no puede ser menor a 0',
        'insumo1.max' => 'El insumo 1 no puede ser mayor a 10',
        'insumo2.numeric' => 'El insumo 2 debe ser un número válido',
        'insumo2.min' => 'El insumo 2 no puede ser menor a 0',
        'insumo2.max' => 'El insumo 2 no puede ser mayor a 10',
        'insumo3.numeric' => 'El insumo 3 debe ser un número válido',
        'insumo3.min' => 'El insumo 3 no puede ser menor a 0',
        'insumo3.max' => 'El insumo 3 no puede ser mayor a 10',
        'insumo4.numeric' => 'El insumo 4 debe ser un número válido',
        'insumo4.min' => 'El insumo 4 no puede ser menor a 0',
        'insumo4.max' => 'El insumo 4 no puede ser mayor a 10',
        'insumo5.numeric' => 'El insumo 5 debe ser un número válido',
        'insumo5.min' => 'El insumo 5 no puede ser menor a 0',
        'insumo5.max' => 'El insumo 5 no puede ser mayor a 10',
        'examen_parcial.numeric' => 'El examen parcial debe ser un número válido',
        'examen_parcial.min' => 'El examen parcial no puede ser menor a 0',
        'examen_parcial.max' => 'El examen parcial no puede ser mayor a 10',
        'examen_final.numeric' => 'El examen final debe ser un número válido',
        'examen_final.min' => 'El examen final no puede ser menor a 0',
        'examen_final.max' => 'El examen final no puede ser mayor a 10',
        'nota_suspenso.numeric' => 'La nota de suspenso debe ser un número válido',
        'nota_suspenso.min' => 'La nota de suspenso no puede ser menor a 0',
        'nota_suspenso.max' => 'La nota de suspenso no puede ser mayor a 10',
        'numero_intento.required' => 'El número de intento es obligatorio',
        'numero_intento.integer' => 'El número de intento debe ser un número entero',
        'numero_intento.min' => 'El número de intento no puede ser menor a 1',
        'numero_intento.max' => 'El número de intento no puede ser mayor a 3',
    ];

    public function mount()
    {
        $this->cargarPeriodos();
        $this->cargarMateriasAsignadas();
    }

    public function cargarPeriodos()
    {
        $this->periodos = Periodo::orderBy('fecha_inicio', 'desc')->get();

        // Seleccionar período actual por defecto
        $periodo_actual = Periodo::where('is_current', true)->first();
        if ($periodo_actual) {
            $this->periodo_id = $periodo_actual->id;
        }
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
                    'materia' => $asignaciones->first()->materia,
                    'paralelos' => $asignaciones->pluck('paralelo')
                ];
            });
    }

    public function updatedPeriodoId()
    {
        $this->materia_id = '';
        $this->paralelo_id = '';
        $this->estudiantes = [];
        $this->mostrar_formulario = false;
        $this->cargarMateriasAsignadas();
    }

    public function updatedMateriaId()
    {
        $this->paralelo_id = '';
        $this->estudiantes = [];
        $this->mostrar_formulario = false;
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

        $docente_id = Auth::id();

        $this->paralelos = AsignacionDocente::with('paralelo')
            ->where('docente_id', $docente_id)
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
            'materia',
            'paralelo',
            'calificaciones' => function ($query) {
                $query->where('docente_id', Auth::id());
            }
        ])
            ->whereHas('matricula', function ($query) {
                $query->where('periodo_id', $this->periodo_id);
            })
            ->where('materia_id', $this->materia_id)
            ->where('paralelo_id', $this->paralelo_id)
            ->where('estado', 'Inscrito')
            ->get()
            ->map(function ($detalle) {
                $calificacion = $detalle->calificaciones->first();

                return [
                    'detalle_matricula_id' => $detalle->id,
                    'estudiante' => $detalle->estudiante,
                    'codigo_matricula' => $detalle->code,
                    'tipo' => $detalle->tipo,
                    'es_repeticion' => $detalle->es_repeticion,
                    'calificacion' => $calificacion,
                    'tiene_calificacion' => $calificacion ? true : false,
                    'nota_final' => $calificacion ? $calificacion->nota_final : null,
                    'estado_final' => $calificacion ? $calificacion->estado_final : 'Pendiente'
                ];
            });
    }

    public function abrirFormularioCalificacion($detalle_matricula_id)
    {
        $this->resetearFormulario();

        $estudiante_data = $this->estudiantes->firstWhere('detalle_matricula_id', $detalle_matricula_id);

        if (!$estudiante_data) {
            $this->mensaje = 'Estudiante no encontrado';
            $this->tipo_mensaje = 'error';
            return;
        }

        $this->estudiante_seleccionado = $estudiante_data;
        $this->mostrar_formulario = true;

        // Si ya tiene calificación, cargar los datos
        if ($estudiante_data['calificacion']) {
            $this->cargarCalificacionExistente($estudiante_data['calificacion']);
        } else {
            // Valores por defecto para nueva calificación
            $this->es_arrastre = $estudiante_data['tipo'] === 'Arrastre';
            $this->numero_intento = $estudiante_data['es_repeticion'] ? 2 : 1;
        }
    }

    public function cargarCalificacionExistente($calificacion)
    {
        $this->calificacion_actual = $calificacion;
        $this->insumo1 = $calificacion->insumo1;
        $this->insumo2 = $calificacion->insumo2;
        $this->insumo3 = $calificacion->insumo3;
        $this->insumo4 = $calificacion->insumo4;
        $this->insumo5 = $calificacion->insumo5;
        $this->examen_parcial = $calificacion->examen_parcial;
        $this->examen_final = $calificacion->examen_final;
        $this->nota_suspenso = $calificacion->nota_suspenso;
        $this->es_arrastre = $calificacion->es_arrastre;
        $this->numero_intento = $calificacion->numero_intento;

        $this->calcularPromedios();
    }

    public function resetearFormulario()
    {
        $this->estudiante_seleccionado = null;
        $this->calificacion_actual = null;
        $this->insumo1 = '';
        $this->insumo2 = '';
        $this->insumo3 = '';
        $this->insumo4 = '';
        $this->insumo5 = '';
        $this->examen_parcial = '';
        $this->examen_final = '';
        $this->nota_suspenso = '';
        $this->es_arrastre = false;
        $this->numero_intento = 1;
        $this->promedio_insumos = 0;
        $this->nota_final = 0;
        $this->estado_final = '';
        $this->mostrar_formulario = false;
        $this->mensaje = '';
    }

    public function calcularPromedios()
    {
        // Calcular promedio de insumos
        $insumos = collect([
            $this->insumo1,
            $this->insumo2,
            $this->insumo3,
            $this->insumo4,
            $this->insumo5
        ])->filter(function ($value) {
            return $value !== '' && $value !== null;
        });

        if ($insumos->count() > 0) {
            $this->promedio_insumos = round($insumos->avg(), 2);
        } else {
            $this->promedio_insumos = 0;
        }

        // Calcular nota final
        $this->calcularNotaFinal();
    }

    public function calcularNotaFinal()
    {
        $promedio = $this->promedio_insumos;
        $parcial = $this->examen_parcial !== '' && $this->examen_parcial !== null ? floatval($this->examen_parcial) : 0;
        $final = $this->examen_final !== '' && $this->examen_final !== null ? floatval($this->examen_final) : 0;

        // Fórmula: 30% Insumos + 30% Parcial + 40% Final
        if ($promedio > 0 || $parcial > 0 || $final > 0) {
            $this->nota_final = round(
                ($promedio * 0.3) + ($parcial * 0.3) + ($final * 0.4),
                2
            );
        } else {
            $this->nota_final = 0;
        }

        // Si la nota está entre 4 y 7, puede dar suspenso
        if ($this->nota_final >= 4 && $this->nota_final < 7) {
            $this->suspenso = true;

            // Si ya ingresó la nota de suspenso, calcular
            if ($this->nota_suspenso !== '' && $this->nota_suspenso !== null) {
                $nota_suspenso_valor = floatval($this->nota_suspenso);
                $incremento = ($nota_suspenso_valor / 10) * 2.99;
                $this->nota_final = round($this->nota_final + $incremento, 2);
            }
        } else {
            $this->suspenso = false;
            $this->nota_suspenso = '';
        }

        // Determinar estado final
        $this->determinarEstadoFinal();
    }

    public function determinarEstadoFinal()
    {
        // Si la nota final es mayor o igual a 7, está aprobado
        if ($this->nota_final >= self::NOTA_MINIMA_APROBACION) {
            $this->estado_final = 'Aprobado';
        }
        // Si la nota está entre 4 y 7 (rango de suspenso)
        elseif ($this->nota_final >= self::NOTA_MINIMA_ARRASTRE && $this->nota_final < self::NOTA_MINIMA_APROBACION) {
            // Si ya tiene nota de suspenso
            if ($this->nota_suspenso !== null && $this->nota_suspenso !== '') {
                // Después de aplicar el suspenso, verificar si alcanzó el 7
                if ($this->nota_final >= self::NOTA_MINIMA_APROBACION) {
                    $this->estado_final = 'Aprobado';
                } else {
                    // No alcanzó el 7 ni con el suspenso
                    $this->estado_final = 'Reprobado';
                }
            } else {
                // Aún no tiene nota de suspenso
                $this->estado_final = 'Incompleto'; // cuando formatee la  base poner este valor Suspenso_Pendiente
            }
        }
        // Si la nota es menor a 4, está reprobado directamente
        elseif ($this->nota_final < self::NOTA_MINIMA_ARRASTRE) {
            $this->estado_final = 'Reprobado';
        }
        // En cualquier otro caso
        else {
            $this->estado_final = 'Incompleto';
        }
    }

    public function updatedInsumo1()
    {
        $this->calcularPromedios();
    }
    public function updatedInsumo2()
    {
        $this->calcularPromedios();
    }
    public function updatedInsumo3()
    {
        $this->calcularPromedios();
    }
    public function updatedInsumo4()
    {
        $this->calcularPromedios();
    }
    public function updatedInsumo5()
    {
        $this->calcularPromedios();
    }
    public function updatedExamenParcial()
    {
        $this->calcularNotaFinal();
    }
    public function updatedExamenFinal()
    {
        $this->calcularNotaFinal();
    }
    public function updatedNotaSuspenso()
    {
        $this->calcularNotaFinal();
        $this->determinarEstadoFinal();
    }

    /**
     * Verificar si la calificación está completa (tiene todas las notas necesarias)
     */
    protected function calificacionCompleta()
    {
        // Verificar que tenga al menos un insumo, el parcial y el final
        $tiene_insumo = !empty($this->insumo1) || !empty($this->insumo2) ||
            !empty($this->insumo3) || !empty($this->insumo4) ||
            !empty($this->insumo5);

        $tiene_parcial = !empty($this->examen_parcial) && $this->examen_parcial !== '';
        $tiene_final = !empty($this->examen_final) && $this->examen_final !== '';

        // Si la nota está entre 4 y 7, debe tener nota de suspenso
        if (
            $this->nota_final >= self::NOTA_MINIMA_ARRASTRE &&
            $this->nota_final < self::NOTA_MINIMA_APROBACION
        ) {
            $tiene_suspenso = !empty($this->nota_suspenso) && $this->nota_suspenso !== '';
            return $tiene_insumo && $tiene_parcial && $tiene_final && $tiene_suspenso;
        }

        return $tiene_insumo && $tiene_parcial && $tiene_final;
    }

    /**
     * Calcular el costo adicional por penalización
     */
    protected function calcularCostoAdicional($materia_id)
    {
        try {
            $materia = Materia::find($materia_id);

            if (!$materia || !$materia->semestre_id) {
                return 0;
            }

            // Obtener el semestre
            $semestre = \App\Models\Semestre::find($materia->semestre_id);

            if (!$semestre || !$semestre->carrera_id) {
                return 0;
            }

            // Obtener la carrera
            $carrera = \App\Models\Carrera::find($semestre->carrera_id);

            if (!$carrera) {
                return 0;
            }

            // Costo normal = créditos * costo_credito
            $costo_normal = $materia->credits * $carrera->costo_credito;

            // Aplicar penalización
            $porcentaje = self::PORCENTAJE_PENALIZACION / 100;
            $costo_adicional = $costo_normal * $porcentaje;

            return round($costo_adicional, 2);
        } catch (\Exception $e) {
            // Si hay algún error, retornar 0
            \Log::error('Error al calcular costo adicional: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gestionar el registro de materia arrastrada
     */
    protected function gestionarMateriaArrastrada($calificacion_guardada)
    {
        // Obtener el detalle de matrícula
        $detalle_matricula = DetalleMatricula::with(['matricula', 'estudiante'])
            ->find($this->estudiante_seleccionado['detalle_matricula_id']);

        if (!$detalle_matricula) {
            return;
        }

        $estudiante_id = $detalle_matricula->estudiante->id;
        $materia_id = $this->materia_id;
        $periodo_id = $this->periodo_id;

        // Verificar si ya existe un registro de arrastre para esta materia
        $arrastre_existente = MateriasArrastrada::where('user_id', $estudiante_id)
            ->where('materia_id', $materia_id)
            ->where('periodo_reprobado_id', $periodo_id)
            ->first();

        // Si la calificación está completa
        if ($this->calificacionCompleta()) {

            // Caso 1: Aprobó (nota >= 7) - Eliminar arrastre si existe
            if ($this->nota_final >= self::NOTA_MINIMA_APROBACION) {
                if ($arrastre_existente) {
                    $arrastre_existente->delete();
                }
            }
            // Caso 2: Nota < 4 - Reprobado directo, crear arrastre sin esperar suspenso
            elseif ($this->nota_final < self::NOTA_MINIMA_ARRASTRE) {

                $numero_intento = $this->numero_intento;
                if ($arrastre_existente) {
                    $numero_intento = $arrastre_existente->numero_intento;
                }

                $costo_adicional = $this->calcularCostoAdicional($materia_id);

                $datos_arrastre = [
                    'user_id' => $estudiante_id,
                    'materia_id' => $materia_id,
                    'periodo_reprobado_id' => $periodo_id,
                    'nota_obtenida' => $this->nota_final,
                    'nota_minima_requerida' => self::NOTA_MINIMA_APROBACION,
                    'porcentaje_penalizacion' => self::PORCENTAJE_PENALIZACION,
                    'numero_intento' => $numero_intento,
                    'estado' => 'Perdida_Definitiva',
                    'costo_adicional' => $costo_adicional,
                ];

                if ($arrastre_existente) {
                    $arrastre_existente->update($datos_arrastre);
                } else {
                    MateriasArrastrada::create($datos_arrastre);
                }
            }
            // Caso 3: Nota entre 4 y 6.99
            elseif (
                $this->nota_final >= self::NOTA_MINIMA_ARRASTRE &&
                $this->nota_final < self::NOTA_MINIMA_APROBACION
            ) {

                // Solo crear arrastre si YA TIENE nota de suspenso y no alcanzó el 7
                $tiene_suspenso = !empty($this->nota_suspenso) && $this->nota_suspenso !== '';

                if ($tiene_suspenso) {
                    // Ya rindió suspenso y no alcanzó el 7, crear arrastre

                    $numero_intento = $this->numero_intento;
                    if ($arrastre_existente) {
                        $numero_intento = $arrastre_existente->numero_intento;
                    }

                    $costo_adicional = $this->calcularCostoAdicional($materia_id);

                    $datos_arrastre = [
                        'user_id' => $estudiante_id,
                        'materia_id' => $materia_id,
                        'periodo_reprobado_id' => $periodo_id,
                        'nota_obtenida' => $this->nota_final,
                        'nota_minima_requerida' => self::NOTA_MINIMA_APROBACION,
                        'porcentaje_penalizacion' => self::PORCENTAJE_PENALIZACION,
                        'numero_intento' => $numero_intento,
                        'estado' => 'Arrastrada',
                        'costo_adicional' => $costo_adicional,
                    ];

                    if ($arrastre_existente) {
                        $arrastre_existente->update($datos_arrastre);
                    } else {
                        MateriasArrastrada::create($datos_arrastre);
                    }
                } else {
                    // NO tiene suspenso todavía, NO crear arrastre (esperar)
                    // Si existía un arrastre, lo dejamos (no eliminamos)
                }
            }
        }
    }

    public function guardarCalificacion()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // Datos para guardar
            $datos_calificacion = [
                'insumo1' => $this->insumo1 ?: null,
                'insumo2' => $this->insumo2 ?: null,
                'insumo3' => $this->insumo3 ?: null,
                'insumo4' => $this->insumo4 ?: null,
                'insumo5' => $this->insumo5 ?: null,
                'promedio_insumos' => $this->promedio_insumos,
                'examen_parcial' => $this->examen_parcial ?: null,
                'examen_final' => $this->examen_final ?: null,
                'nota_final' => $this->nota_final,
                'nota_suspenso' => $this->nota_suspenso ?: null,
                'estado_final' => $this->estado_final,
                'es_arrastre' => $this->es_arrastre,
                'numero_intento' => $this->numero_intento,
                'detalle_matricula_id' => $this->estudiante_seleccionado['detalle_matricula_id'],
                'docente_id' => Auth::id(),
            ];

            $calificacion_guardada = null;

            if ($this->calificacion_actual) {
                // Actualizar calificación existente
                $calificacion_anterior = $this->calificacion_actual->toArray();
                $this->calificacion_actual->update($datos_calificacion);
                $calificacion_guardada = $this->calificacion_actual->fresh();

                // Registrar auditoría
                $this->registrarAuditoria($calificacion_anterior, $datos_calificacion);

                $this->mensaje = 'Calificación actualizada correctamente';
            } else {
                // Crear nueva calificación
                $calificacion_guardada = Calificacion::create($datos_calificacion);
                $this->mensaje = 'Calificación guardada correctamente';
            }

            // Gestionar materia arrastrada
            $this->gestionarMateriaArrastrada($calificacion_guardada);

            $this->tipo_mensaje = 'success';

            DB::commit();

            // Recargar estudiantes para actualizar la vista
            $this->cargarEstudiantes();

            // Cerrar formulario
            $this->resetearFormulario();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->mensaje = 'Error al guardar la calificación: ' . $e->getMessage();
            $this->tipo_mensaje = 'error';
        }
    }

    protected function registrarAuditoria($datos_anteriores, $datos_nuevos)
    {
        $campos_auditables = [
            'insumo1',
            'insumo2',
            'insumo3',
            'insumo4',
            'insumo5',
            'promedio_insumos',
            'examen_parcial',
            'examen_final',
            'nota_final',
            'nota_suspenso',
            'estado_final',
            'es_arrastre',
            'numero_intento'
        ];

        foreach ($campos_auditables as $campo) {
            if (
                isset($datos_anteriores[$campo]) && isset($datos_nuevos[$campo])
                && $datos_anteriores[$campo] != $datos_nuevos[$campo]
            ) {

                \App\Models\AuditoriaCalificacion::create([
                    'campo_modificado' => $campo,
                    'valor_anterior' => $datos_anteriores[$campo],
                    'valor_nuevo' => $datos_nuevos[$campo],
                    'motivo_modificacion' => 'Modificación por docente',
                    'calificacion_id' => $this->calificacion_actual->id,
                    'docente_id' => Auth::id(),
                    'fecha_modificacion' => now(),
                ]);
            }
        }
    }

    public function cerrarFormulario()
    {
        $this->resetearFormulario();
    }

    public function render()
    {
        return view('livewire.docente.calificacion-estudiante');
    }
}
