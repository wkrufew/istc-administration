<?php

namespace App\Livewire\Administration;

use App\Models\AsignacionDocente;
use App\Models\Carrera;
use App\Models\Calificacion;
use App\Models\DetalleMatricula;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Paralelo;
use App\Models\Periodo;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DashboardPrincipal extends Component
{
    public ?int $periodoId = null;

    public function mount(): void
    {
        $actual = Periodo::where('is_current', true)->first();
        $this->periodoId = $actual?->id ?? Periodo::latest()->first()?->id;
    }

    // =========================================================================
    // COMPUTED — SELECTS
    // =========================================================================
    #[Computed]
    public function periodos()
    {
        return Periodo::orderByDesc('fecha_inicio')->get();
    }

    #[Computed]
    public function periodoActual()
    {
        return Periodo::find($this->periodoId);
    }

    // =========================================================================
    // FILA 1 — STATS GLOBALES (no dependen del periodo)
    // =========================================================================
    #[Computed]
    public function statsGlobales(): array
    {
        $totalEstudiantes = User::role('estudiante')->count();
        $totalDocentes    = User::role('docente')->count();
        $totalCarreras    = Carrera::where('is_active', true)->count();
        $totalMaterias    = Materia::where('is_active', true)->count();

        // Nuevos estudiantes este mes
        $nuevosEsteMes = User::role('estudiante')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at',  now()->year)
            ->count();

        return [
            'total_estudiantes' => $totalEstudiantes,
            'total_docentes'    => $totalDocentes,
            'total_carreras'    => $totalCarreras,
            'total_materias'    => $totalMaterias,
            'nuevos_este_mes'   => $nuevosEsteMes,
        ];
    }

    // =========================================================================
    // FILA 2 — STATS DEL PERIODO
    // =========================================================================
    #[Computed]
    public function statsPeriodo(): array
    {
        if (! $this->periodoId) return [];

        $pid = $this->periodoId;

        // Matrículas del periodo
        $matriculasTotal      = Matricula::where('periodo_id', $pid)->count();
        $matriculasHabilitadas = Matricula::where('periodo_id', $pid)->where('estado', 'Habilitada')->count();
        $matriculasPendientes  = Matricula::where('periodo_id', $pid)->where('estado', 'Pendiente_Pago')->count();
        $matriculasBorrador    = Matricula::where('periodo_id', $pid)->where('estado', 'Borrador')->count();

        // Docentes con asignación en el periodo
        $docentesAsignados = AsignacionDocente::where('periodo_id', $pid)
            ->distinct('docente_id')
            ->count('docente_id');

        // Docentes sin asignación
        $totalDocentes    = User::role('docente')->count();
        $docentesSinAsig  = $totalDocentes - $docentesAsignados;

        // Paralelos activos en el periodo (con al menos una asignación)
        $paralelosActivos = AsignacionDocente::where('periodo_id', $pid)
            ->distinct('paralelo_id')
            ->count('paralelo_id');

        // Materias sin horario en el periodo
        $materiasConHorario = Horario::where('periodo_id', $pid)
            ->where('is_active', true)
            ->distinct('materia_id')
            ->count('materia_id');

        $materiasAsignadas = AsignacionDocente::where('periodo_id', $pid)
            ->distinct('materia_id')
            ->count('materia_id');

        $materiasSinHorario = max(0, $materiasAsignadas - $materiasConHorario);

        // Tasa de aprobación del periodo
        $totalCalificaciones = Calificacion::whereHas(
            'detalleMatricula.matricula',
            fn($q) => $q->where('periodo_id', $pid)
        )->whereNotNull('estado_final')->count();

        $aprobados = Calificacion::whereHas(
            'detalleMatricula.matricula',
            fn($q) => $q->where('periodo_id', $pid)
        )->where('estado_final', 'Aprobado')->count();

        $tasaAprobacion = $totalCalificaciones > 0
            ? round(($aprobados / $totalCalificaciones) * 100, 1)
            : null;

        // Ocupación promedio de paralelos
        $paralelos = Paralelo::whereHas(
            'asignacionesDocentes',
            fn($q) => $q->where('periodo_id', $pid)
        )->get();

        $ocupacionPromedio = $paralelos->count() > 0
            ? round($paralelos->avg(
                fn($p) =>
                $p->cupo_maximo > 0 ? ($p->cupo_actual / $p->cupo_maximo) * 100 : 0
            ), 1)
            : null;

        return [
            'matriculas_total'       => $matriculasTotal,
            'matriculas_habilitadas' => $matriculasHabilitadas,
            'matriculas_pendientes'  => $matriculasPendientes,
            'matriculas_borrador'    => $matriculasBorrador,
            'docentes_asignados'     => $docentesAsignados,
            'docentes_sin_asig'      => $docentesSinAsig,
            'paralelos_activos'      => $paralelosActivos,
            'materias_sin_horario'   => $materiasSinHorario,
            'tasa_aprobacion'        => $tasaAprobacion,
            'ocupacion_promedio'     => $ocupacionPromedio,
            'total_calificaciones'   => $totalCalificaciones,
            'aprobados'              => $aprobados,
        ];
    }

    // =========================================================================
    // FILA 3 — FINANCIERO DEL PERIODO
    // =========================================================================
    #[Computed]
    public function statsFinancieros(): array
    {
        if (! $this->periodoId) return [];

        $pid = $this->periodoId;

        // Totales por estado de obligaciones
        $totalObligaciones = ObligacionesFinanciera::where('periodo_id', $pid)->sum('monto_final');
        $totalPagado       = ObligacionesFinanciera::where('periodo_id', $pid)->where('estado', 'Pagado')->sum('monto_final');
        $totalPendiente    = ObligacionesFinanciera::where('periodo_id', $pid)->whereIn('estado', ['Pendiente', 'Parcial'])->sum('monto_final');
        $totalVencido      = ObligacionesFinanciera::where('periodo_id', $pid)->where('estado', 'Vencido')->sum('monto_final');

        // Conteos
        $countPagado    = ObligacionesFinanciera::where('periodo_id', $pid)->where('estado', 'Pagado')->count();
        $countPendiente = ObligacionesFinanciera::where('periodo_id', $pid)->whereIn('estado', ['Pendiente', 'Parcial'])->count();
        $countVencido   = ObligacionesFinanciera::where('periodo_id', $pid)->where('estado', 'Vencido')->count();

        // Pagos aprobados del mes actual
        $pagosEsteMes = \App\Models\Pago::whereHas(
            'obligacion',
            fn($q) => $q->where('periodo_id', $pid)
        )
            ->where('estado', 'Aprobado')
            ->whereMonth('fecha_pago', now()->month)
            ->whereYear('fecha_pago',  now()->year)
            ->sum('monto');

        // Pagos pendientes de verificación (estado Pendiente en pagos)
        $pagosEnRevision = \App\Models\Pago::whereHas(
            'obligacion',
            fn($q) => $q->where('periodo_id', $pid)
        )->where('estado', 'Pendiente')->count();

        // Porcentaje recaudado
        $pctRecaudado = $totalObligaciones > 0
            ? round(($totalPagado / $totalObligaciones) * 100, 1)
            : 0;

        return [
            'total_obligaciones' => $totalObligaciones,
            'total_pagado'       => $totalPagado,
            'total_pendiente'    => $totalPendiente,
            'total_vencido'      => $totalVencido,
            'count_pagado'       => $countPagado,
            'count_pendiente'    => $countPendiente,
            'count_vencido'      => $countVencido,
            'pagos_este_mes'     => $pagosEsteMes,
            'pagos_en_revision'  => $pagosEnRevision,
            'pct_recaudado'      => $pctRecaudado,
        ];
    }

    // =========================================================================
    // FILA 4 — ALERTAS DINÁMICAS
    // =========================================================================
    #[Computed]
    public function alertas(): array
    {
        if (! $this->periodoId) return [];

        $pid    = $this->periodoId;
        $alerts = [];

        // Pagos vencidos
        $vencidos = ObligacionesFinanciera::where('periodo_id', $pid)
            ->where('estado', 'Vencido')->count();
        if ($vencidos > 0) {
            $alerts[] = [
                'tipo'    => 'error',
                'mensaje' => "{$vencidos} obligaciones vencidas requieren atención",
                'detalle' => 'Revisar módulo de obligaciones financieras',
                'icono'   => 'exclamation',
            ];
        }

        // Matrículas pendientes de pago
        $matPendientes = Matricula::where('periodo_id', $pid)
            ->where('estado', 'Pendiente_Pago')->count();
        if ($matPendientes > 0) {
            $alerts[] = [
                'tipo'    => 'warning',
                'mensaje' => "{$matPendientes} matrículas pendientes de pago",
                'detalle' => 'Ver listado de matrículas',
                'icono'   => 'clock',
            ];
        }

        // Pagos en revisión
        $enRevision = \App\Models\Pago::whereHas(
            'obligacion',
            fn($q) => $q->where('periodo_id', $pid)
        )->where('estado', 'Pendiente')->count();
        if ($enRevision > 0) {
            $alerts[] = [
                'tipo'    => 'info',
                'mensaje' => "{$enRevision} comprobantes de pago en revisión",
                'detalle' => 'Aprobar o rechazar pagos',
                'icono'   => 'eye',
            ];
        }

        // Docentes sin asignación
        $totalDocentes    = User::role('docente')->count();
        $docentesAsignados = AsignacionDocente::where('periodo_id', $pid)
            ->distinct('docente_id')->count('docente_id');
        $sinAsig = $totalDocentes - $docentesAsignados;
        if ($sinAsig > 0) {
            $alerts[] = [
                'tipo'    => 'warning',
                'mensaje' => "{$sinAsig} docentes sin materias asignadas",
                'detalle' => 'Asignar carga académica',
                'icono'   => 'user',
            ];
        }

        // Materias sin horario
        $materiasConHorario = Horario::where('periodo_id', $pid)
            ->where('is_active', true)->distinct('materia_id')->count('materia_id');
        $materiasAsignadas  = AsignacionDocente::where('periodo_id', $pid)
            ->distinct('materia_id')->count('materia_id');
        $sinHorario = max(0, $materiasAsignadas - $materiasConHorario);
        if ($sinHorario > 0) {
            $alerts[] = [
                'tipo'    => 'warning',
                'mensaje' => "{$sinHorario} materias sin horario registrado",
                'detalle' => 'Completar horarios académicos',
                'icono'   => 'calendar',
            ];
        }

        // Sin alertas
        if (empty($alerts)) {
            $alerts[] = [
                'tipo'    => 'success',
                'mensaje' => 'Todo en orden en este periodo',
                'detalle' => 'No hay alertas pendientes',
                'icono'   => 'check',
            ];
        }

        return $alerts;
    }

    // =========================================================================
    // ÚLTIMAS MATRÍCULAS
    // =========================================================================
    #[Computed]
    public function ultimasMatriculas()
    {
        if (! $this->periodoId) return collect();

        return Matricula::with(['estudiante', 'carrera'])
            ->where('periodo_id', $this->periodoId)
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    // =========================================================================
    // ESTUDIANTES POR CARRERA (sidebar)
    // =========================================================================
    #[Computed]
    public function estudiantesPorCarrera(): array
    {
        if (! $this->periodoId) return [];

        $data = Matricula::where('periodo_id', $this->periodoId)
            ->where('estado', 'Habilitada')
            ->selectRaw('carrera_id, COUNT(*) as total')
            ->groupBy('carrera_id')
            ->with('carrera')
            ->get();

        $max = $data->max('total') ?: 1;

        return $data->sortByDesc('total')->map(fn($m) => [
            'carrera' => $m->carrera?->code ?? '—',
            'nombre'  => $m->carrera?->name ?? '—',
            'total'   => $m->total,
            'pct'     => round(($m->total / $max) * 100),
        ])->values()->toArray();
    }

    // =========================================================================
    // BARRAS DE ESTADO ACADÉMICO
    // =========================================================================
    #[Computed]
    public function estadoAcademico(): array
    {
        if (! $this->periodoId) return [];

        $pid = $this->periodoId;

        // % matrículas habilitadas
        $totalMat      = Matricula::where('periodo_id', $pid)->count();
        $habilitadas   = Matricula::where('periodo_id', $pid)->where('estado', 'Habilitada')->count();
        $pctMat        = $totalMat > 0 ? round(($habilitadas / $totalMat) * 100) : 0;

        // % pagos al día (Pagado / total obligaciones)
        $totalOblig    = ObligacionesFinanciera::where('periodo_id', $pid)->count();
        $pagadas       = ObligacionesFinanciera::where('periodo_id', $pid)->where('estado', 'Pagado')->count();
        $pctPagos      = $totalOblig > 0 ? round(($pagadas / $totalOblig) * 100) : 0;

        // % aprobación general
        $totalCal      = Calificacion::whereHas(
            'detalleMatricula.matricula',
            fn($q) => $q->where('periodo_id', $pid)
        )->whereNotNull('estado_final')->count();
        $aprobados     = Calificacion::whereHas(
            'detalleMatricula.matricula',
            fn($q) => $q->where('periodo_id', $pid)
        )->where('estado_final', 'Aprobado')->count();
        $pctAprobacion = $totalCal > 0 ? round(($aprobados / $totalCal) * 100) : 0;

        // % ocupación paralelos
        $paralelos = Paralelo::whereHas(
            'asignacionesDocentes',
            fn($q) => $q->where('periodo_id', $pid)
        )->get();
        $pctOcupacion = $paralelos->count() > 0
            ? round($paralelos->avg(
                fn($p) =>
                $p->cupo_maximo > 0 ? ($p->cupo_actual / $p->cupo_maximo) * 100 : 0
            ))
            : 0;

        return [
            ['label' => 'Matrículas habilitadas', 'pct' => $pctMat,        'color' => 'bg-emerald-500', 'valor' => "{$habilitadas}/{$totalMat}"],
            ['label' => 'Obligaciones pagadas',   'pct' => $pctPagos,      'color' => 'bg-blue-500',    'valor' => "{$pagadas}/{$totalOblig}"],
            ['label' => 'Aprobación general',      'pct' => $pctAprobacion, 'color' => 'bg-violet-500',  'valor' => "{$aprobados}/{$totalCal}"],
            ['label' => 'Ocupación paralelos',     'pct' => $pctOcupacion,  'color' => 'bg-amber-500',   'valor' => "{$pctOcupacion}%"],
        ];
    }

    public function render()
    {
        return view('livewire.administration.dashboard-principal');
    }
}
