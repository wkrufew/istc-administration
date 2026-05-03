<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AsignacionDocente;

class DocenteController extends Controller
{
    public function index()
    {
        return view('administracion.docentes.index');
    }

    public function showAsignar(User $docente)
    {
        return view('administracion.docentes.asignacion', compact('docente'));
    }

    public function storeAsignacion(Request $request, User $docente)
    {
        $validated = $request->validate([
            'materia_id' => 'required|exists:materias,id',
            'paralelo_id' => 'required|exists:paralelos,id',
            'periodo_id' => 'required|exists:periodos,id',
        ]);

        // Verificar si ya existe la asignación
        $exists = AsignacionDocente::where([
            'docente_id' => $docente->id,
            'materia_id' => $validated['materia_id'],
            'paralelo_id' => $validated['paralelo_id'],
            'periodo_id' => $validated['periodo_id'],
        ])->exists();

        if ($exists) {
            return back()->withErrors(['Ya existe una asignación con esos datos.']);
        }

        //dd($validated, $docente);

        AsignacionDocente::create([
            'docente_id' => $docente->id,
            'materia_id' => $validated['materia_id'],
            'paralelo_id' => $validated['paralelo_id'],
            'periodo_id' => $validated['periodo_id'],
        ]);

        return back()->with('success', 'Asignación realizada con éxito.');
        //return redirect()->route('administracion.administrativa.docentes.index')->with('success', 'Asignación realizada con éxito.');
    }

    public function destroy($id)
    {
        $asignacion = AsignacionDocente::findOrFail($id);
        $asignacion->delete();

        return back()->with('success', 'Asignación eliminada correctamente.');
    }
}
