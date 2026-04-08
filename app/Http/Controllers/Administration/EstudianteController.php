<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class EstudianteController extends Controller
{
    public function index()
    {
        return view('administracion.estudiantes.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('administracion.estudiantes.create', compact('roles'));
    }

    public function edit(User $estudiante)
    {
        /* dd($estudiante); */
        $roles = Role::all();
        return view('administracion.estudiantes.edit', compact('estudiante', 'roles'));
    }

    //funcion para la vista de importar usuarios masivamente

    public function import()
    {
        return view('administracion.estudiantes.importation-users');
    }
}
