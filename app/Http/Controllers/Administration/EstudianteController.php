<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PerfilUsuario;

/** Sección Estudiantes. El permiso 'gestionar_estudiantes' lo exige la ruta. */
class EstudianteController extends Controller
{
    public function index()
    {
        return view('administracion.estudiantes.index');
    }

    public function create()
    {
        return view('administracion.personas.crear', ['perfil' => PerfilUsuario::ESTUDIANTE]);
    }

    /** Si no es estudiante, lo lleva a donde sí se editan sus datos (docente o cuenta). */
    public function edit(User $estudiante)
    {
        if (! PerfilUsuario::esEstudiante($estudiante)) {
            return redirect()->to(PerfilUsuario::rutaFicha($estudiante)
                ?? route('administracion.administrativa.users.edit', $estudiante));
        }

        return view('administracion.estudiantes.edit', compact('estudiante'));
    }

    // Vista de importar estudiantes masivamente
    public function import()
    {
        return view('administracion.estudiantes.importation-users');
    }
}
