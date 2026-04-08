<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\Paralelo;
use App\Models\Periodo;
use Illuminate\Http\Request;
use App\Models\MateriaPeriodoParalelo;

class MateriaPeriodoParaleloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administracion.modulos-periodos.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $periodos = Periodo::orderBy('fecha_inicio', 'desc')->get();
        $paralelos = Paralelo::orderBy('name')->get();
        $materias = Materia::orderBy('name')->get();

        return view('administracion.modulos-periodos.create', compact(
            'periodos',
            'paralelos',
            'materias'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'materia_id' => ['required', 'exists:materias,id'],
            'periodo_id' => ['required', 'exists:periodos,id'],
            'paralelo_id' => ['required', 'exists:paralelos,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Validación extra: que el módulo esté dentro del periodo
        $periodo = Periodo::findOrFail($request->periodo_id);

        $fechaInicio = $request->date('fecha_inicio');
        $fechaFin = $request->date('fecha_fin');

        if ($fechaInicio->lt($periodo->fecha_inicio) || $fechaFin->gt($periodo->fecha_fin)) {
            return back()
                ->withInput()
                ->withErrors([
                    'fecha_inicio' => 'Las fechas del módulo deben estar dentro del rango del período lectivo.',
                ]);
        }

        // Evitar duplicado por materia+periodo+paralelo (por si acaso)
        $existe = MateriaPeriodoParalelo::where('materia_id', $request->materia_id)
            ->where('periodo_id', $request->periodo_id)
            ->where('paralelo_id', $request->paralelo_id)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'materia_id' => 'Ya existe un módulo registrado para esa Materia en ese Período y Paralelo.',
                ]);
        }

        MateriaPeriodoParalelo::create([
            'materia_id' => $request->materia_id,
            'periodo_id' => $request->periodo_id,
            'paralelo_id' => $request->paralelo_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('administracion.administrativa.materia_periodo_paralelo.index')
            ->with('success', 'Módulo creado correctamente.');
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
    public function edit(MateriaPeriodoParalelo $materia_periodo_paralelo)
    {
        $periodos = Periodo::orderBy('fecha_inicio', 'desc')->get();
        $paralelos = Paralelo::orderBy('name')->get();
        $materias = Materia::orderBy('name')->get();

        return view('administracion.modulos-periodos.edit', compact(
            'materia_periodo_paralelo',
            'periodos',
            'paralelos',
            'materias'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MateriaPeriodoParalelo $materia_periodo_paralelo)
    {
        $request->validate([
            'materia_id' => ['required', 'exists:materias,id'],
            'periodo_id' => ['required', 'exists:periodos,id'],
            'paralelo_id' => ['required', 'exists:paralelos,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'is_active' => ['nullable', 'boolean'],
        ]);


        $periodo = Periodo::findOrFail($request->periodo_id);

        $fechaInicio = $request->date('fecha_inicio');
        $fechaFin = $request->date('fecha_fin');

        if ($fechaInicio->lt($periodo->fecha_inicio) || $fechaFin->gt($periodo->fecha_fin)) {
            return back()
                ->withInput()
                ->withErrors([
                    'fecha_inicio' => 'Las fechas del módulo deben estar dentro del rango del período lectivo.',
                ]);
        }

        // Validar duplicado (excepto el actual)
        $existe = MateriaPeriodoParalelo::where('materia_id', $request->materia_id)
            ->where('periodo_id', $request->periodo_id)
            ->where('paralelo_id', $request->paralelo_id)
            ->where('id', '!=', $materia_periodo_paralelo->id)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'materia_id' => 'Ya existe un módulo registrado para esa Materia en ese Período y Paralelo.',
                ]);
        }

        $materia_periodo_paralelo->update([
            'materia_id' => $request->materia_id,
            'periodo_id' => $request->periodo_id,
            'paralelo_id' => $request->paralelo_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('administracion.administrativa.materia_periodo_paralelo.index')
            ->with('success', 'Módulo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MateriaPeriodoParalelo $materia_periodo_paralelo)
    {
        $materia_periodo_paralelo->delete();

        return redirect()
            ->route('administracion.administrativa.materia_periodo_paralelo.index')
            ->with('success', 'Módulo eliminado correctamente.');
    }
}
