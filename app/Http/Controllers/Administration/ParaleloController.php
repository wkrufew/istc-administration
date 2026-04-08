<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Paralelo;
use Illuminate\Http\Request;

class ParaleloController extends Controller
{
    public function index()
    {
        $paralelos  = Paralelo::latest()->get();
        return view('administracion.paralelos.index', compact('paralelos'));
    }

    public function create()
    {
        $paralelo = new Paralelo(['is_active' => true]); // Objeto vacío
        return view('administracion.paralelos.create', compact('paralelo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:45',
            'code' => 'required|string|max:45|unique:paralelos,code',
            'cupo_maximo' => 'required|integer|min:1',
            'cupo_actual' => 'nullable|integer|min:0|max:' . $request->input('cupo_maximo'),
            'is_active' => 'nullable|boolean',
        ]);

        Paralelo::create([
            'name' => $request->name,
            'code' => $request->code,
            'cupo_maximo' => $request->cupo_maximo,
            'cupo_actual' => $request->cupo_actual ?? 0,
            'is_active' => $request->is_active ?? false,
        ]);

        Paralelo::create($request->all());

        return redirect()->route('administracion.administrativa.paralelos.index')->with('success', 'Paralelo creado exitosamente.');
    }

    public function edit(Paralelo $paralelo)
    {
        return view('administracion.paralelos.edit', compact('paralelo'));
    }

    public function update(Request $request, Paralelo $paralelo)
    {
        $request->validate([
            'name' => 'required|string|max:45',
            'code' => 'required|string|max:45|unique:paralelos,code,' . $paralelo->id,
            'cupo_maximo' => 'required|integer|min:1',
            'cupo_actual' => 'nullable|integer|min:0|max:' . $request->input('cupo_maximo'),
            'is_active' => 'nullable|boolean',
        ]);

        $paralelo->update([
            'name' => $request->name,
            'code' => $request->code,
            'cupo_maximo' => $request->cupo_maximo,
            'cupo_actual' => $request->cupo_actual ?? 0,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()->route('administracion.administrativa.paralelos.index')->with('success', 'Paralelo actualizado exitosamente.');
    }

    public function destroy(Paralelo $paralelo)
    {
        $paralelo->delete();
        return redirect()->route('administracion.administrativa.paralelos.index')->with('success', 'Paralelo eliminado exitosamente.');
    }
}
