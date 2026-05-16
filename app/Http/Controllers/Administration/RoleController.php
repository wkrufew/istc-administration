<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /* public function __construct()
    {
        $this->middleware('can:Generar Roles y Permisos')->only('index', 'create', 'store', 'edit', 'update', 'destroy');
        $this->middleware('can:Generar Roles y Permisos')->except('index');
    } */

    public function index()
    {

        $roles = Role::whereNotIn('name', ['Super Admin'])->paginate(10);
        return view('administracion.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        return view('administracion.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles',
            'permissions' => 'required'
        ]);


        $role = Role::create([
            'name'       => $request->name,
            'guard_name' => 'web',
        ]);

        $role->permissions()->attach($request->permissions);

        $menssage = "El rol $request->name se ha añadido correctamente";

        return redirect()->route('administracion.administrativa.roles.index')->with(compact('menssage'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('administracion.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        $role->permissions()->sync($request->permissions);

        return redirect()->route('administracion.administrativa.roles.index')->with('menssage', 'El rol se ha actualizado con correctamente');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('administracion.administrativa.roles.index')->with('menssage', 'El rol se ha eliminado correctamente');
    }
}
