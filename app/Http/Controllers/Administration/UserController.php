<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /* public function __construct()
    {
        $this->middleware('can:Asignacion Roles')->only('index', 'edit', 'update');        
    } */

    public function index()
    {
        return view('administracion.users.index');
    }

    /* public function create()
    {
        $roles = Role::all();
        return view('administracion.users.create', compact('roles'));
    } */

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('administracion.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->roles);

        // Obtener los nombres de los roles asignados
        $roles = $user->getRoleNames()->toArray();

        // Convertir el array de roles a una cadena
        $rolesString = implode(', ', $roles);
        $menssage = "Al usuario $user->name se le asigno el rol $rolesString  correctamente";

        return redirect()->route('administracion.administrativa.users.index')->with('menssage', $menssage);
    }

    public function profile()
    {
        return view('administracion.users.profile');
    }
}
