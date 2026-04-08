<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Ocsa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ActasOCSController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administracion.actas-colegiado.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administracion.actas-colegiado.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->all();
        $validatedData['is_active'] = $request->has('is_active');

        $validated = validator($validatedData, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|mimes:pdf|max:10240',
            'is_active' => 'boolean',
        ])->validate();

        //dd($validated);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $slug = Str::slug($request->title); // convierte título a slug
            $filename = $slug . '-' . time() . '.' . $file->getClientOriginalExtension();

            $validated['file'] = $file->storeAs('actas-organo-colegiado-superior', $filename, 'public');
        }

        Ocsa::create($validated);

        $notificacion = "El documento {$request->title} se ha creado correctamente";
        return redirect()->route('administracion.administrativa.actas-colegiado.index')->with('notificacion', $notificacion);
    }

    public function edit(Ocsa $document)
    {
        return view('administracion.actas-colegiado.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ocsa $document)
    {
        $validatedData = $request->all();
        $validatedData['is_active'] = $request->has('is_active');

        $validated = validator($validatedData, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|mimes:pdf|max:10240',
            'is_active' => 'boolean',
        ])->validate();

        if ($request->hasFile('file')) {
            // Eliminar archivo anterior si existe
            if ($document->file && Storage::disk('public')->exists($document->file)) {
                Storage::disk('public')->delete($document->file);
            }

            $file = $request->file('file');
            $slug = Str::slug($request->title);
            $filename = $slug . '-' . time() . '.' . $file->getClientOriginalExtension();

            $validated['file'] = $file->storeAs('actas-organo-colegiado-superior', $filename, 'public');
        }

        $document->update($validated);

        $notificacion = "El documento {$request->title} se ha actualizado correctamente";
        return redirect()->route('administracion.administrativa.actas-colegiado.index')->with('notificacion', $notificacion);
    }
}
