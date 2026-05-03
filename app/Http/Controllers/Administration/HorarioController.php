<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Horario;

class HorarioController extends Controller
{
    public function create()
    {
        return view('administracion.horarios.create');
    }

    public function edit(Horario $horario)
    {
        return view('administracion.horarios.edit', compact('horario'));
    }

    public function destroy(Horario $horario)
    {
        $horario->delete();
        return redirect()->route('administracion.administrativa.horarios.index')
            ->with('success', 'Horario eliminado.');
    }
}
