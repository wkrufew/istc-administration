<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\MateriaPeriodoParalelo;

class MateriaPeriodoParaleloController extends Controller
{
    public function index()
    {
        return view('administracion.modulos-periodos.index');
    }

    public function create()
    {
        return view('administracion.modulos-periodos.create');
    }

    public function edit(MateriaPeriodoParalelo $materia_periodo_paralelo)
    {
        return view('administracion.modulos-periodos.edit', [
            'mpp' => $materia_periodo_paralelo,
        ]);
    }

    public function destroy(MateriaPeriodoParalelo $materia_periodo_paralelo)
    {
        $materia_periodo_paralelo->delete();
        return redirect()
            ->route('administracion.administrativa.materia_periodo_paralelo.index')
            ->with('success', 'Módulo eliminado correctamente.');
    }
}
