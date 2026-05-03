<?php

namespace App\Livewire\Docente;

use App\Models\Asistencia;
use App\Models\Horario;
use App\Models\DetalleMatricula;
use App\Models\MateriaPeriodoParalelo;
use App\Models\Periodo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReporteAsistencias extends Component
{
    // =========================================================================
    // FILTROS
    // =========================================================================
    public ?int   $periodoId   = null;
    public ?int   $materiaId   = null;
    public ?int   $paraleloId  = null;
    public ?int   $horarioId   = null;   // null = materia completa

    public function mount(): void
    {
        $this->periodoId = Periodo::periodoActivoGlobal()?->id ?? Periodo::latest()->first()?->id;
    }

    public function updatedPeriodoId(): void
    {
        $this->materiaId  = null;
        $this->paraleloId = null;
        $this->horarioId  = null;
    }

    public function updatedMateriaId(): void
    {
        $this->paraleloId = null;
        $this->horarioId  = null;
    }

    public function updatedParaleloId(): void
    {
        $this->horarioId = null;
    }

    // =========================================================================
    // COMPUTED — SELECTS DEL DOCENTE
    // =========================================================================
    #[Computed]
    public function periodos()
    {
        return Periodo::orderByDesc('id')->get();
    }

    #[Computed]
    public function materiasAsignadas()
    {
        if (! $this->periodoId) return collect();

        return Auth::user()
            ->asignacionesDocente()
            ->with('materia')
            ->where('periodo_id', $this->periodoId)
            ->get()
            ->pluck('materia')
            ->filter()
            ->unique('id')
            ->values();
    }

    #[Computed]
    public function paralelos()
    {
        if (! $this->periodoId || ! $this->materiaId) return collect();

        return Auth::user()
            ->asignacionesDocente()
            ->with('paralelo')
            ->where('periodo_id', $this->periodoId)
            ->where('materia_id', $this->materiaId)
            ->get()
            ->pluck('paralelo')
            ->filter()
            ->unique('id')
            ->values();
    }

    #[Computed]
    public function horarios()
    {
        if (! $this->periodoId || ! $this->materiaId || ! $this->paraleloId) return collect();

        return Horario::query()
            ->where('periodo_id',  $this->periodoId)
            ->where('materia_id',  $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
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

    // =========================================================================
    // COMPUTED — MÓDULO
    // =========================================================================
    #[Computed]
    public function modulo()
    {
        if (! $this->periodoId || ! $this->materiaId || ! $this->paraleloId) return null;

        return MateriaPeriodoParalelo::query()
            ->where('periodo_id',  $this->periodoId)
            ->where('materia_id',  $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
            ->where('is_active', true)
            ->first();
    }

    // =========================================================================
    // COMPUTED — DATOS DEL REPORTE
    // =========================================================================
    #[Computed]
    public function reporte(): array
    {
        if (! $this->periodoId || ! $this->materiaId || ! $this->paraleloId) return [];

        $modulo = $this->modulo;
        if (! $modulo) return [];

        // ── Estudiantes inscritos ─────────────────────────────────────────────
        $detalles = DetalleMatricula::query()
            ->with('estudiante')
            ->whereHas(
                'matricula',
                fn($q) =>
                $q->where('periodo_id', $this->periodoId)
            )
            ->where('materia_id',  $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
            ->where('estado', 'Inscrito')
            ->orderBy('id')
            ->get();

        if ($detalles->isEmpty()) return [];

        // ── Total clases del módulo ───────────────────────────────────────────
        $diasHorario = $this->calcularDiasHorario();
        $totalClases = $this->calcularTotalClases($modulo, $diasHorario);

        // ── Query base de asistencias ─────────────────────────────────────────
        $asistenciasQuery = Asistencia::query()
            ->whereHas(
                'horario',
                fn($q) =>
                $q->where('materia_id',  $this->materiaId)
                    ->where('paralelo_id', $this->paraleloId)
                    ->where('periodo_id',  $this->periodoId)
                    ->when(
                        $this->horarioId,
                        fn($q) =>
                        $q->where('id', $this->horarioId)
                    )
            )
            ->whereIn('detalle_matricula_id', $detalles->pluck('id'));

        $todasAsistencias = $asistenciasQuery->get();

        // ── POR ESTUDIANTE ────────────────────────────────────────────────────
        $porEstudiante = $detalles->map(function ($det) use ($todasAsistencias, $totalClases) {
            $lista      = $todasAsistencias->where('detalle_matricula_id', $det->id);
            $presentes  = $lista->whereIn('estado', ['Presente', 'Justificado', 'Tardanza'])->count();
            $ausentes   = $lista->where('estado', 'Ausente')->count();
            $tardanzas  = $lista->where('estado', 'Tardanza')->count();
            $justificados = $lista->where('estado', 'Justificado')->count();
            $pct        = $totalClases > 0 ? round(($presentes / $totalClases) * 100, 1) : 0;

            return [
                'nombre'      => $det->estudiante?->nombre_completo
                    ?? $det->estudiante?->name
                    ?? '—',
                'matricula'   => $det->estudiante?->matricula_numero ?? '—',
                'presentes'   => $presentes,
                'ausentes'    => $ausentes,
                'tardanzas'   => $tardanzas,
                'justificados' => $justificados,
                'total_clases' => $totalClases,
                'pct'         => $pct,
                'nota'        => round(($presentes / max($totalClases, 1)) * 10, 2),
                'estado'      => $pct >= 80 ? 'ok' : ($pct >= 60 ? 'riesgo' : 'critico'),
            ];
        })->sortByDesc('pct')->values()->toArray();

        // ── TENDENCIA POR FECHA ───────────────────────────────────────────────
        $fechasRegistradas = $todasAsistencias
            ->groupBy('fecha')
            ->sortKeys();

        $tendencia = $fechasRegistradas->map(function ($registros, $fecha) use ($detalles) {
            $total     = $detalles->count();
            $presentes = $registros->whereIn('estado', ['Presente', 'Justificado', 'Tardanza'])->count();
            $ausentes  = $registros->where('estado', 'Ausente')->count();
            $pct       = $total > 0 ? round(($presentes / $total) * 100, 1) : 0;

            return [
                'fecha'     => Carbon::parse($fecha)->format('d/m'),
                'fecha_raw' => $fecha,
                'presentes' => $presentes,
                'ausentes'  => $ausentes,
                'total'     => $total,
                'pct'       => $pct,
            ];
        })->values()->toArray();

        // ── TOTALES GLOBALES ──────────────────────────────────────────────────
        $totalPresentes  = $todasAsistencias->whereIn('estado', ['Presente', 'Justificado', 'Tardanza'])->count();
        $totalAusentes   = $todasAsistencias->where('estado', 'Ausente')->count();
        $totalTardanzas  = $todasAsistencias->where('estado', 'Tardanza')->count();
        $totalJustif     = $todasAsistencias->where('estado', 'Justificado')->count();
        $totalRegistros  = $todasAsistencias->count();

        // Estudiantes en riesgo / críticos
        $enRiesgo  = collect($porEstudiante)->where('estado', 'riesgo')->count();
        $criticos  = collect($porEstudiante)->where('estado', 'critico')->count();

        return [
            'por_estudiante'  => $porEstudiante,
            'tendencia'       => $tendencia,
            'total_clases'    => $totalClases,
            'total_estudiantes' => $detalles->count(),
            'clases_tomadas'  => count($fechasRegistradas),
            'total_presentes' => $totalPresentes,
            'total_ausentes'  => $totalAusentes,
            'total_tardanzas' => $totalTardanzas,
            'total_justif'    => $totalJustif,
            'total_registros' => $totalRegistros,
            'en_riesgo'       => $enRiesgo,
            'criticos'        => $criticos,
            'pct_asistencia_general' => $totalRegistros > 0
                ? round(($totalPresentes / $totalRegistros) * 100, 1)
                : 0,
        ];
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function calcularDiasHorario(): array
    {
        $query = Horario::query()
            ->where('materia_id',  $this->materiaId)
            ->where('paralelo_id', $this->paraleloId)
            ->where('periodo_id',  $this->periodoId)
            ->where('is_active', true)
            ->whereHas(
                'asignacionDocente',
                fn($q) =>
                $q->where('docente_id', Auth::id())
            );

        if ($this->horarioId) {
            $query->where('id', $this->horarioId);
        }

        return $query->pluck('dia_semana')->unique()->values()->toArray();
    }

    private function calcularTotalClases(object $modulo, array $dias): int
    {
        if (empty($dias)) return 0;

        $mapa = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
        ];

        $count = 0;
        foreach (CarbonPeriod::create($modulo->fecha_inicio, $modulo->fecha_fin) as $dia) {
            $nombre = $mapa[$dia->format('l')] ?? null;
            if ($nombre && in_array($nombre, $dias)) $count++;
        }

        return $count;
    }

    public function render()
    {
        return view('livewire.docente.reporte-asistencias');
    }
}
