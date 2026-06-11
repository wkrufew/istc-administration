<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\Periodo;
use Illuminate\Http\Request;

class CalificacionesController extends Controller
{
    public function periodos(Request $request)
    {
        $user = $request->user();

        $ultimaMatricula  = $user->matriculas()->with('carrera')->latest()->first();
        $periodoDeCarrera = $ultimaMatricula?->carrera?->periodoActual();

        $periodos = Periodo::orderBy('id', 'desc')->get()->map(fn($p) => [
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

    public function notas(Request $request, int $periodoId)
    {
        $userId = $request->user()->id;

        $matricula = Matricula::where('user_id', $userId)
            ->where('periodo_id', $periodoId)
            ->latest('id')
            ->first();

        if (! $matricula) {
            return response()->json([
                'success' => true,
                'data'    => ['matricula' => null, 'filas' => [], 'resumen' => null],
            ]);
        }

        $detalles = DetalleMatricula::with([
            'materia:id,name,code,nota_minima_aprobacion',
            'paralelo:id,code',
            'calificacion.docente:id,name',
        ])
            ->where('matricula_id', $matricula->id)
            ->where('estado', 'Inscrito')
            ->orderBy('materia_id')
            ->get();

        $filas = $detalles->map(function ($d) {
            $cal = $d->calificacion;
            return [
                'detalle_id'       => $d->id,
                'materia'          => $d->materia?->name ?? '---',
                'materia_code'     => $d->materia?->code ?? '',
                'nota_minima'      => $d->materia?->nota_minima_aprobacion,
                'paralelo'         => $d->paralelo?->code ?? '---',
                'tipo'             => $d->tipo,
                'es_repeticion'    => (bool) $d->es_repeticion,
                'insumo1'          => $cal?->insumo1,
                'insumo2'          => $cal?->insumo2,
                'insumo3'          => $cal?->insumo3,
                'insumo4'          => $cal?->insumo4,
                'insumo5'          => $cal?->insumo5,
                'promedio_insumos' => $cal?->promedio_insumos,
                'examen_parcial'   => $cal?->examen_parcial,
                'examen_final'     => $cal?->examen_final,
                'nota_final'       => $cal?->nota_final,
                'nota_suspenso'    => $cal?->nota_suspenso,
                'estado_final'     => $cal?->estado_final ?? 'Pendiente',
                'es_borrador'      => (bool) ($cal?->es_borrador ?? false),
                'docente'          => $cal?->docente?->name ?? '---',
            ];
        })->values();

        $notasValidas = $filas->pluck('nota_final')->filter(fn($n) => $n !== null);

        return response()->json([
            'success' => true,
            'data' => [
                'periodo_id' => $periodoId,
                'matricula'  => ['id' => $matricula->id, 'code' => $matricula->code],
                'filas'      => $filas,
                'resumen'    => [
                    'total_materias'   => $filas->count(),
                    'aprobadas'        => $filas->where('estado_final', 'Aprobado')->count(),
                    'reprobadas'       => $filas->where('estado_final', 'Reprobado')->count(),
                    'promedio_general' => $notasValidas->count() > 0 ? round($notasValidas->avg(), 2) : 0,
                ],
            ],
        ]);
    }
}
