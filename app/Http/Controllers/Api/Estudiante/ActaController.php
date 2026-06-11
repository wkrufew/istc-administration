<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\DetalleMatricula;
use App\Models\Matricula;
use App\Models\NotaTitulacion;
use Illuminate\Http\Request;

class ActaController extends Controller
{
    public function index(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;

        $matricula = Matricula::with('carrera.semestres.materias')
            ->where('user_id', $userId)
            ->where('estado', 'Habilitada')
            ->latest()
            ->first();

        if (! $matricula) {
            return response()->json([
                'success' => true,
                'data'    => null,
                'message' => 'No tienes una matrícula habilitada.',
            ]);
        }

        $carrera   = $matricula->carrera;
        $semestres = $carrera->semestres->sortBy('order');

        $semestresData      = [];
        $promediosSemestres = [];

        foreach ($semestres as $semestre) {
            $materiasData       = [];
            $notasSemestre      = [];
            $semestretieneDatos = false;

            foreach ($semestre->materias->sortBy('name') as $materia) {
                $detalle = DetalleMatricula::with([
                    'calificaciones' => fn($q) => $q->orderByDesc('numero_intento'),
                    'paralelo',
                ])
                    ->where('user_id', $userId)
                    ->where('materia_id', $materia->id)
                    ->latest()
                    ->first();

                $calificacion = $detalle?->calificaciones->first();

                if ($calificacion) {
                    $semestretieneDatos = true;
                    if ($calificacion->nota_final !== null) {
                        $notasSemestre[] = (float) $calificacion->nota_final;
                    }
                }

                $materiasData[] = [
                    'materia_id'         => $materia->id,
                    'materia_nombre'     => $materia->name,
                    'materia_code'       => $materia->code,
                    'creditos'           => $materia->credits ?? null,
                    'tipo'               => $detalle?->tipo ?? 'Normal',
                    'tiene_calificacion' => $calificacion !== null,
                    'insumo1'            => $calificacion?->insumo1,
                    'insumo2'            => $calificacion?->insumo2,
                    'insumo3'            => $calificacion?->insumo3,
                    'insumo4'            => $calificacion?->insumo4,
                    'insumo5'            => $calificacion?->insumo5,
                    'promedio_insumos'   => $calificacion?->promedio_insumos,
                    'examen_parcial'     => $calificacion?->examen_parcial,
                    'examen_final'       => $calificacion?->examen_final,
                    'nota_final'         => $calificacion?->nota_final,
                    'nota_suspenso'      => $calificacion?->nota_suspenso,
                    'estado_final'       => $calificacion?->estado_final,
                    'es_arrastre'        => (bool) ($calificacion?->es_arrastre ?? false),
                    'numero_intento'     => $calificacion?->numero_intento ?? 1,
                ];
            }

            $promedio = ! empty($notasSemestre)
                ? round(array_sum($notasSemestre) / count($notasSemestre), 2)
                : null;

            if ($promedio !== null) {
                $promediosSemestres[] = $promedio;
            }

            $semestresData[] = [
                'semestre_id'     => $semestre->id,
                'semestre_nombre' => $semestre->name,
                'semestre_order'  => $semestre->order,
                'tiene_datos'     => $semestretieneDatos,
                'materias'        => $materiasData,
                'promedio'        => $promedio,
                'total_materias'  => count($materiasData),
                'aprobadas'       => collect($materiasData)->where('estado_final', 'Aprobado')->count(),
            ];
        }

        $promedioMalla = ! empty($promediosSemestres)
            ? round(array_sum($promediosSemestres) / count($promediosSemestres), 2)
            : null;

        $titulacion = NotaTitulacion::where('user_id', $userId)
            ->where('carrera_id', $carrera->id)
            ->latest('numero_intento')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'estudiante' => [
                    'id'     => $user->id,
                    'nombre' => $user->name,
                    'cedula' => $user->cedula,
                ],
                'carrera' => [
                    'id'     => $carrera->id,
                    'nombre' => $carrera->name,
                    'codigo' => $carrera->code,
                ],
                'matricula' => [
                    'id'     => $matricula->id,
                    'code'   => $matricula->code,
                    'estado' => $matricula->estado,
                ],
                'semestres'      => $semestresData,
                'promedio_malla' => $promedioMalla,
                'titulacion'     => $titulacion ? [
                    'estado'     => $titulacion->estado,
                    'nota_final' => $titulacion->nota_final_egreso,
                    'tipo'       => $titulacion->tipo_titulacion,
                    'fecha'      => $titulacion->fecha_evaluacion?->format('d/m/Y'),
                ] : null,
                'malla_completa' => NotaTitulacion::mallaCurricular_Completada($userId, $carrera->id),
            ],
        ]);
    }
}
