<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function create()
    {
        $periodo = new Periodo();
        return view('administracion.periodos.create', compact('periodo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description'            => 'required|string|max:255',
            'code'                   => 'required|string|max:255|unique:periodos,code',
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_matricula' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_pago'      => 'required|date|after_or_equal:fecha_inicio',
        ]);

        Periodo::create(array_merge(
            $request->only([
                'description', 'code',
                'fecha_inicio', 'fecha_fin',
                'fecha_limite_matricula', 'fecha_limite_pago',
            ]),
            ['nuevo_calculo' => $request->boolean('nuevo_calculo', true)]
        ));

        return redirect()
            ->route('administracion.administrativa.periodos.index')
            ->with('success', 'Periodo creado. Ahora vincúlalo a las carreras correspondientes desde el módulo de Materia-Período-Paralelo.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Periodo $periodo)
    {
        return view('administracion.periodos.edit', compact('periodo'));
    }

    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'description'            => 'required|string|max:255',
            'code'                   => 'required|string|max:255|unique:periodos,code,' . $periodo->id,
            'fecha_inicio'           => 'required|date',
            'fecha_fin'              => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_matricula' => 'required|date|after_or_equal:fecha_inicio',
            'fecha_limite_pago'      => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $periodo->update(array_merge(
            $request->only([
                'description', 'code',
                'fecha_inicio', 'fecha_fin',
                'fecha_limite_matricula', 'fecha_limite_pago',
            ]),
            ['nuevo_calculo' => $request->boolean('nuevo_calculo', true)]
        ));

        return redirect()
            ->route('administracion.administrativa.periodos.index')
            ->with('success', 'Periodo actualizado correctamente.');
    }

    public function destroy(Periodo $periodo)
    {
        $periodo->delete();
        return redirect()
            ->route('administracion.administrativa.periodos.index')
            ->with('success', 'Periodo eliminado.');
    }
}
