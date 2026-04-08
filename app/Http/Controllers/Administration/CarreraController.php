<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Semestre;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carreras = Carrera::all();
        return view('administracion.carreras.index', compact('carreras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $semestre = new Semestre(['is_active' => true]);
        $carrera = new Carrera(['is_active' => true]); // Objeto vacío
        return view('administracion.carreras.create', compact('carrera', 'semestre'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:carreras,code',
            'description' => 'nullable|string|max:1000',
            'costo_credito' => 'required|numeric|min:0',
            'costo_carrera' => 'required|numeric|min:0',
            'duracion_semestres' => 'required|integer|min:1',
            'modalidad' => 'required|in:Presencial,Virtual,Híbrida,Semipresencial',
            'is_active' => 'nullable|boolean',
        ]);

        Carrera::create($request->all());
        return redirect()->route('administracion.administrativa.carreras.index')->with('success', 'Carrera creada exitosamente.');
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
    public function edit(Carrera $carrera)
    {
        return view('administracion.carreras.edit', compact('carrera'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:carreras,code,' . $carrera->id,
            'description' => 'nullable|string|max:1000',
            'costo_credito' => 'required|numeric|min:0',
            'costo_carrera' => 'required|numeric|min:0',
            'duracion_semestres' => 'required|integer|min:1',
            'modalidad' => 'required|in:Presencial,Virtual,Híbrida,Semipresencial',
            'is_active' => 'nullable|boolean',
        ]);

        $carrera->update($request->all());
        return redirect()->route('administracion.administrativa.carreras.index')->with('success', 'Carrera actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrera $carrera)
    {
        $carrera->delete();
        return redirect()->route('administracion.administrativa.carreras.index')->with('success', 'Carrera eliminada.');
    }
}
