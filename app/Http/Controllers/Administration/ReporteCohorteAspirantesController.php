<?php

namespace App\Http\Controllers\Administration;

use App\Models\Aspirante;
use App\Models\Cohorte;
use App\Services\SettingService;
use Illuminate\Http\Response;

class ReporteCohorteAspirantesController
{
    public function __invoke(Cohorte $cohorte): Response
    {
        abort_unless(auth()->user()?->can('gestionar_aspirantes'), 403);

        $aspirantes = Aspirante::with(['user', 'carrera'])
            ->join('users', 'users.id', '=', 'aspirantes.user_id')
            ->where('aspirantes.cohorte_id', $cohorte->id)
            ->orderBy('users.name')
            ->select('aspirantes.*')
            ->get();

        $instituto = [
            'nombre_largo' => SettingService::get('instituto.nombre_largo', 'Instituto Superior Tecnológico'),
            'nombre_corto' => SettingService::get('instituto.nombre_corto', 'ISTC'),
        ];

        return response()->view('reports.aspirantes-cohorte', compact('cohorte', 'aspirantes', 'instituto'));
    }
}
