<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDocente;
use App\Models\DetalleMatricula;
use App\Models\Horario;
use App\Models\Periodo;
use Illuminate\Http\Request;

class HorariosController extends Controller
{
    public function periodos(Request $request)
    {
        $user = $request->user();

        $ultimaMatricula  = $user->matriculas()->with('carrera')->latest()->first();
        $periodoDeCarrera = $ultimaMatricula?->carrera?->periodoActual();

        $periodos = Periodo::orderBy('fecha_inicio', 'desc')->get()->map(fn($p) => [
            'id'          => $p->id,
            'descripcion' => $p->description,
            'es_actual'   => $p->id === $periodoDeCarrera?->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'periodo_actual_id' => $periodoDeCarrera?->id,
                'periodos'          => $periodos,
            ],
        ]);
    }

    public function horario(Request $request, int $periodoId)
    {
        $userId = $request->user()->id;

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $horariosPorDia = array_fill_keys($dias, []);

        $detalles = DetalleMatricula::query()
            ->whereHas('matricula', fn($q) =>
                $q->where('user_id', $userId)->where('periodo_id', $periodoId)
            )
            ->where('estado', 'Inscrito')
            ->get(['id', 'materia_id', 'paralelo_id']);

        if ($detalles->isEmpty()) {
            return response()->json([
                'success' => true,
                'data'    => ['periodo_id' => $periodoId, 'horarios_por_dia' => $horariosPorDia],
            ]);
        }

        $horarios = Horario::with(['materia:id,name,code', 'paralelo:id,name'])
            ->where('periodo_id', $periodoId)
            ->where(function ($q) use ($detalles) {
                foreach ($detalles as $d) {
                    $q->orWhere(fn($sub) =>
                        $sub->where('materia_id', $d->materia_id)->where('paralelo_id', $d->paralelo_id)
                    );
                }
            })
            ->whereIn('dia_semana', $dias)
            ->orderBy('hora_inicio')
            ->get();

        foreach ($horarios as $h) {
            if (! array_key_exists($h->dia_semana, $horariosPorDia)) continue;

            $asignacion = AsignacionDocente::with('docente')
                ->where('materia_id', $h->materia_id)
                ->where('paralelo_id', $h->paralelo_id)
                ->where('periodo_id', $periodoId)
                ->first();

            $horariosPorDia[$h->dia_semana][] = [
                'id'           => $h->id,
                'materia'      => $h->materia?->name ?? 'Sin materia',
                'materia_code' => $h->materia?->code ?? '',
                'docente'      => $asignacion?->docente?->name ?? 'Sin docente asignado',
                'aula'         => $h->aula,
                'hora_inicio'  => $h->hora_inicio,
                'hora_fin'     => $h->hora_fin,
                'paralelo'     => $h->paralelo?->name,
                'modalidad'    => $h->modalidad_clase ?? null,
                'color'        => $this->colorMateria($h->materia_id),
            ];
        }

        foreach ($horariosPorDia as $dia => $lista) {
            usort($lista, fn($a, $b) => strcmp($a['hora_inicio'], $b['hora_inicio']));
            $horariosPorDia[$dia] = array_values($lista);
        }

        return response()->json([
            'success' => true,
            'data'    => ['periodo_id' => $periodoId, 'horarios_por_dia' => $horariosPorDia],
        ]);
    }

    private function colorMateria(int $materiaId): string
    {
        $paleta = ['#2563eb', '#16a34a', '#dc2626', '#7c3aed', '#ea580c', '#0891b2', '#0f766e', '#ca8a04'];
        return $paleta[$materiaId % count($paleta)];
    }
}
