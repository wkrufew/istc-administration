<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function index(Request $request)
    {
        $user            = $request->user()->load(['matriculas.carrera', 'matriculas.periodo']);
        $ultimaMatricula = $user->matriculas->sortByDesc('id')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id'               => $user->id,
                'nombre'           => $user->name,
                'first_name'       => $user->first_name,
                'last_name'        => $user->last_name,
                'email'            => $user->email,
                'cedula'           => $user->cedula,
                'phone'            => $user->phone,
                'address'          => $user->address,
                'fecha_nacimiento' => $user->fecha_nacimiento?->format('d/m/Y'),
                'genero'           => $user->genero,
                'estado_civil'     => $user->estado_civil,
                'nacionalidad'     => $user->nacionalidad,
                'matricula_numero' => $user->matricula_numero,
                'carrera'          => $ultimaMatricula?->carrera ? [
                    'id'     => $ultimaMatricula->carrera->id,
                    'nombre' => $ultimaMatricula->carrera->name,
                    'codigo' => $ultimaMatricula->carrera->code,
                ] : null,
                'periodo_actual' => $ultimaMatricula ? [
                    'id'          => $ultimaMatricula->periodo_id,
                    'descripcion' => $ultimaMatricula->periodo?->description,
                ] : null,
            ],
        ]);
    }
}
