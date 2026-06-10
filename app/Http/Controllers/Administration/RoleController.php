<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $pivotTable = config('permission.table_names.model_has_roles', 'model_has_roles');

        $roles = Role::select('roles.*')
            ->selectSub(
                DB::table($pivotTable)
                    ->selectRaw('count(*)')
                    ->whereColumn('role_id', 'roles.id')
                    ->where('model_type', User::class),
                'users_count'
            )
            ->whereNotIn('name', ['Super Admin'])
            ->paginate(10);

        return view('administracion.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

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
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

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
        $pivotTable = config('permission.table_names.model_has_roles', 'model_has_roles');
        $usersCount = DB::table($pivotTable)
            ->where('role_id', $role->id)
            ->where('model_type', User::class)
            ->count();

        if ($usersCount > 0) {
            return redirect()->route('administracion.administrativa.roles.index')
                ->with('error_rol', 'El rol "' . $role->name . '" está asignado a ' . $usersCount . ' usuario(s) y no puede eliminarse.');
        }

        $role->delete();

        return redirect()->route('administracion.administrativa.roles.index')->with('menssage', 'El rol se ha eliminado correctamente.');
    }
}
