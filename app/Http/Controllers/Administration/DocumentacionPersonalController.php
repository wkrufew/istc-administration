<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentacionPersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administracion.documentacion-personal.index'/* , compact('user') */);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(User $user = null)
    {
        /* $users = User::orderBy('name')->get(); */
        /*  $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['estudiante', 'admision']);
        })
            ->orderBy('name')
            ->get(); */
        $users = null;

        if (!$user) {
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['estudiante', 'admision']);
            })->orderBy('name')->get();
        }

        return view('administracion.documentacion-personal.create', compact('user', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'file_curriculum' => 'nullable|mimes:pdf|max:10240',
            'file_senescyt' => 'nullable|mimes:pdf|max:10240',
            'file_contrato' => 'nullable|mimes:pdf|max:10240',
            'file_otro' => 'nullable|mimes:pdf|max:10240',
        ]);

        // Verificar que al menos un archivo sea cargado
        if (
            !$request->hasFile('file_curriculum') &&
            !$request->hasFile('file_senescyt') &&
            !$request->hasFile('file_contrato') &&
            !$request->hasFile('file_otro')
        ) {
            return back()->withErrors(['files' => 'Debe cargar al menos un archivo.'])->withInput();
        }

        $user = User::find($request->user_id);
        $userSlug = Str::slug($user->name);

        // Procesar cada archivo
        if ($request->hasFile('file_curriculum')) {
            $file = $request->file('file_curriculum');
            $filename = $userSlug . '-curriculum-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_curriculum'] = $file->storeAs('documentos-personal/curriculum', $filename, 'public');
        }

        if ($request->hasFile('file_senescyt')) {
            $file = $request->file('file_senescyt');
            $filename = $userSlug . '-senescyt-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_senescyt'] = $file->storeAs('documentos-personal/senescyt', $filename, 'public');
        }

        if ($request->hasFile('file_contrato')) {
            $file = $request->file('file_contrato');
            $filename = $userSlug . '-contrato-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_contrato'] = $file->storeAs('documentos-personal/contrato', $filename, 'public');
        }

        if ($request->hasFile('file_otro')) {
            $file = $request->file('file_otro');
            $filename = $userSlug . '-otro-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_otro'] = $file->storeAs('documentos-personal/otro', $filename, 'public');
        }

        Document::create($validated);

        $notificacion = "Los documentos de {$user->name} se han cargado correctamente";
        return redirect()->route('administracion.administrativa.documentacion-personal.index')->with('notificacion', $notificacion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $documentacion_personal)
    {
        /* $users = User::orderBy('name')->get(); */
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Estudiante', 'Admision']);
        })
            ->orderBy('name')
            ->get();

        /* $docente = User::select('name', 'cedula')
            ->where('id', $documentacion_personal->user_id)
            ->first(); */

        /* $docente = $documentacion_personal
            ->user()
            ->select('name', 'cedula')
            ->first(); */
        //dd($docente);

        return view('administracion.documentacion-personal.edit', compact('documentacion_personal', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $documentacion_personal)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'file_curriculum' => 'nullable|mimes:pdf|max:10240',
            'file_senescyt' => 'nullable|mimes:pdf|max:10240',
            'file_contrato' => 'nullable|mimes:pdf|max:10240',
            'file_otro' => 'nullable|mimes:pdf|max:10240',
        ]);

        $user = User::find($request->user_id);
        $userSlug = Str::slug($user->name);

        // Procesar archivo curriculum
        if ($request->hasFile('file_curriculum')) {
            if ($documentacion_personal->file_curriculum && Storage::disk('public')->exists($documentacion_personal->file_curriculum)) {
                Storage::disk('public')->delete($documentacion_personal->file_curriculum);
            }
            $file = $request->file('file_curriculum');
            $filename = $userSlug . '-curriculum-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_curriculum'] = $file->storeAs('documentos-personal/curriculum', $filename, 'public');
        }

        // Procesar archivo senescyt
        if ($request->hasFile('file_senescyt')) {
            if ($documentacion_personal->file_senescyt && Storage::disk('public')->exists($documentacion_personal->file_senescyt)) {
                Storage::disk('public')->delete($documentacion_personal->file_senescyt);
            }
            $file = $request->file('file_senescyt');
            $filename = $userSlug . '-senescyt-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_senescyt'] = $file->storeAs('documentos-personal/senescyt', $filename, 'public');
        }

        // Procesar archivo contrato
        if ($request->hasFile('file_contrato')) {
            if ($documentacion_personal->file_contrato && Storage::disk('public')->exists($documentacion_personal->file_contrato)) {
                Storage::disk('public')->delete($documentacion_personal->file_contrato);
            }
            $file = $request->file('file_contrato');
            $filename = $userSlug . '-contrato-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_contrato'] = $file->storeAs('documentos-personal/contrato', $filename, 'public');
        }

        // Procesar archivo otro
        if ($request->hasFile('file_otro')) {
            if ($documentacion_personal->file_otro && Storage::disk('public')->exists($documentacion_personal->file_otro)) {
                Storage::disk('public')->delete($documentacion_personal->file_otro);
            }
            $file = $request->file('file_otro');
            $filename = $userSlug . '-otro-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_otro'] = $file->storeAs('documentos-personal/otro', $filename, 'public');
        }

        $documentacion_personal->update($validated);

        $notificacion = "Los documentos de {$user->name} se han actualizado correctamente";
        return redirect()->route('administracion.administrativa.documentacion-personal.index')->with('notificacion', $notificacion);
    }
}
