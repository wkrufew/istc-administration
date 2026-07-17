<?php

namespace App\Livewire\Estudiante;

use App\Models\AsignacionDocente;
use App\Models\DiaNoLectivo;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\DetalleMatricula;
use App\Models\Horario;
use App\Models\Periodo;
use Illuminate\Support\Facades\Auth;

class HorarioEstudiante extends Component
{
    public $periodo_id = '';
    public $periodos = [];

    public $horariosPorDia = [
        'Lunes' => [],
        'Martes' => [],
        'Miércoles' => [],
        'Jueves' => [],
        'Viernes' => [],
    ];

    public function mount()
    {
        $this->cargarPeriodos();
        $this->cargarHorario();
    }

    public function cargarPeriodos()
    {
        $this->periodos = Periodo::orderBy('fecha_inicio', 'desc')->get();

        // Período activo para la carrera del estudiante, con fallback al primero disponible
        $ultimaMatricula  = Auth::user()->matriculas()->with('carrera')->latest()->first();
        $periodoDeCarrera = $ultimaMatricula?->carrera?->periodoActual();
        $this->periodo_id = $periodoDeCarrera?->id ?? $this->periodos->first()?->id;
    }

    public function updatedPeriodoId()
    {
        $this->cargarHorario();
    }

    public function cargarHorario()
    {
        $this->resetHorario();

        if (!$this->periodo_id) return;

        $estudiante_id = Auth::id();

        /**
         * 1) Traer detalles de matrícula del estudiante en este período
         * (Aquí ya sabemos materias y paralelos reales donde está inscrito)
         */
        $detalles = DetalleMatricula::query()
            ->whereHas('matricula', function ($q) use ($estudiante_id) {
                $q->where('user_id', $estudiante_id)
                    ->where('periodo_id', $this->periodo_id);
            })
            ->where('estado', 'Inscrito')
            ->get(['id', 'materia_id', 'paralelo_id']);

        if ($detalles->isEmpty()) return;

        /**
         * 2) Buscar horarios SOLO de esas materias/paralelos en este período
         */
        $horarios = Horario::query()
            ->with([
                'materia:id,name,code',
                'paralelo:id,name,code',
            ])
            ->where('periodo_id', $this->periodo_id)
            ->where(function ($q) use ($detalles) {
                foreach ($detalles as $d) {
                    $q->orWhere(function ($sub) use ($d) {
                        $sub->where('materia_id', $d->materia_id)
                            ->where('paralelo_id', $d->paralelo_id);
                    });
                }
            })
            ->whereIn('dia_semana', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'])
            ->orderBy('hora_inicio')
            ->get();

        /**
         * 3) Agrupar por día — docente consultado directamente igual que en el dashboard
         */
        foreach ($horarios as $h) {

            $dia = $h->dia_semana;

            if (!isset($this->horariosPorDia[$dia])) continue;

            // Consulta directa por materia+paralelo+periodo, mismo patrón que materiasConDocente()
            $asignacion = AsignacionDocente::with('docente')
                ->where('materia_id',  $h->materia_id)
                ->where('paralelo_id', $h->paralelo_id)
                ->where('periodo_id',  $this->periodo_id)
                ->first();

            $this->horariosPorDia[$dia][] = [
                'id'          => $h->id,
                'materia'     => $h->materia?->name ?? 'Sin materia',
                'materia_code' => $h->materia?->code ?? '',
                'docente'     => $asignacion?->docente?->name ?? 'Sin docente asignado',
                'aula'        => $h->aula ?? null,
                'hora_inicio' => $h->hora_inicio,
                'hora_fin'    => $h->hora_fin,
                'paralelo'    => $h->paralelo?->code ?? null,
                'modalidad'   => $h->modalidad_clase ?? null,
                'color'       => $this->colorMateria($h->materia_id),
            ];
        }

        /**
         * 4) Ordenar cada día por hora
         */
        foreach ($this->horariosPorDia as $dia => $lista) {
            usort($lista, function ($a, $b) {
                return strcmp($a['hora_inicio'], $b['hora_inicio']);
            });
            $this->horariosPorDia[$dia] = $lista;
        }
    }

    private function resetHorario()
    {
        $this->horariosPorDia = [
            'Lunes' => [],
            'Martes' => [],
            'Miércoles' => [],
            'Jueves' => [],
            'Viernes' => [],
        ];
    }

    /**
     * Color automático estable por materia
     * (sin tabla extra, sin guardar nada)
     */
    private function colorMateria($materia_id)
    {
        $paleta = [
            '#2563eb', // azul
            '#16a34a', // verde
            '#dc2626', // rojo
            '#7c3aed', // morado
            '#ea580c', // naranja
            '#0891b2', // cyan
            '#0f766e', // teal
            '#ca8a04', // amarillo oscuro
        ];

        return $paleta[$materia_id % count($paleta)];
    }

    public function render()
    {
        $hoy = Carbon::today()->toDateString();

        $mapaIngles = [
            'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves', 'Friday' => 'Viernes',
        ];
        $diaHoy = $mapaIngles[Carbon::now()->format('l')] ?? '';

        // IDs de horarios del estudiante que caen hoy
        $idsHoy = collect($this->horariosPorDia[$diaHoy] ?? [])->pluck('id');

        // Días no lectivos de hoy: globales del período activo O específicos de sus horarios
        $suspensionesHoy = DiaNoLectivo::where('fecha', $hoy)
            ->where(function ($q) use ($idsHoy) {
                $q->where(fn($g) => $g->where('alcance', 'global')->where('periodo_id', $this->periodo_id))
                  ->orWhere(fn($s) => $s->where('alcance', 'horario')->whereIn('horario_id', $idsHoy));
            })
            ->get(['id', 'nombre', 'tipo', 'alcance', 'horario_id']);

        return view('livewire.estudiante.horario-estudiante', [
            'suspensionesHoy' => $suspensionesHoy,
            'diaHoy'          => $diaHoy,
            'hoy'             => $hoy,
        ]);
    }
}
