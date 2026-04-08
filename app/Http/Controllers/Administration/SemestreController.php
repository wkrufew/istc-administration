<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Semestre;
use Illuminate\Http\Request;

class SemestreController extends Controller
{
    public function index()
    {
        //ordenar por medio del campo order
        $semestres = Semestre::orderBy('order')->get();

        //$semestres = Semestre::all();
        return view('administracion.semestres.index', compact('semestres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $carreras = Carrera::pluck('name', 'id')->toArray();
        //$semestre = Semestre::pluck('name', 'id')(['is_active' => true]); // Objeto vacío
        return view('administracion.semestres.create', compact(/* 'semestre', */'carreras'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:semestres,code',
            'order' => 'required|integer|min:1',
            'creditos_minimos' => 'required|integer|min:0',
            'creditos_maximos' => 'required|integer|min:0|gte:creditos_minimos',
            'carrera_id' => 'required|exists:carreras,id',
            'is_active' => 'nullable|boolean',
        ]);

        Semestre::create($request->all());
        return redirect()->route('administracion.administrativa.semestres.index')->with('success', 'Semestre creado exitosamente.');
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
    public function edit(Semestre $semestre)
    {
        $carreras = Carrera::pluck('name', 'id')->toArray();
        return view('administracion.semestres.edit', compact('semestre', 'carreras'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semestre $semestre)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:semestres,code,' . $semestre->id,
            'order' => 'required|integer|min:1',
            'creditos_minimos' => 'required|integer|min:0',
            'creditos_maximos' => 'required|integer|min:0|gte:creditos_minimos',
            'carrera_id' => 'required|exists:carreras,id',
            'is_active' => 'nullable|boolean',
        ]);

        $semestre->update($request->all());
        return redirect()->route('administracion.administrativa.semestres.index')->with('success', 'Semestre actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semestre $semestre)
    {
        $semestre->delete();
        return redirect()->route('administracion.administrativa.semestres.index')->with('success', 'Semestre eliminado.');
    }
}
