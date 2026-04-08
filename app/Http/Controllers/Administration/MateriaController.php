<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\Semestre;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materias = Materia::all();
        return view('administracion.materias.index', compact('materias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $materia = new Materia(['is_active' => true]); // Objeto vacío
        $semestres = Semestre::pluck('name', 'id')->toArray();

        //dd($semestres);
        return view('administracion.materias.create', compact('materia', 'semestres'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:materias,code',
            'description' => 'nullable|string|max:1000',
            'credits' => 'required|numeric|min:0',
            'horas_teoricas' => 'required|integer|min:0',
            'horas_practicas' => 'required|integer|min:0',
            'nota_minima_aprobacion' => 'required|numeric|min:0|max:10',
            'tipo' => 'required|in:Obligatoria,Electiva,Nivelacion',
            'semestre_id' => 'required|exists:semestres,id',
            'is_active' => 'nullable|boolean',
        ]);

        Materia::create($request->all());
        return redirect()->route('administracion.administrativa.materias.index')->with('success', 'Materia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materia $materia)
    {
        $semestres = Semestre::pluck('name', 'id')->toArray();
        return view('administracion.materias.edit', compact('materia', 'semestres'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Materia $materia)
    {
        //dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:materias,code,' . $materia->id,
            'description' => 'nullable|string|max:1000',
            'credits' => 'required|integer|min:0',
            'horas_teoricas' => 'required|integer|min:0',
            'horas_practicas' => 'required|integer|min:0',
            'nota_minima_aprobacion' => 'required|numeric|min:0|max:10',
            'tipo' => 'required|in:Obligatoria,Electiva,Nivelacion',
            'semestre_id' => 'required|exists:semestres,id',
            'is_active' => 'nullable|boolean',
        ]);

        $materia->update($request->all());
        return redirect()->route('administracion.administrativa.materias.index')->with('success', 'Materia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
        $materia->delete();
        return redirect()->route('administracion.administrativa.materias.index')->with('success', 'Materia eliminada.');
    }
}
