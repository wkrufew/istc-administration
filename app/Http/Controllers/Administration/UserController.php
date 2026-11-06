<?php

namespace App\Http\Controllers\Administration;

use App\Exports\EstudiantesReporteExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PerfilUsuario;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Sección Usuarios: las cuentas de acceso. El permiso 'gestionar_usuarios' lo exige la ruta.
 * El rol se cambia en la pantalla de la cuenta (Usuarios\EditarCuenta), que exige 'asignar_roles'.
 */
class UserController extends Controller
{
    public function index()
    {
        return view('administracion.users.index');
    }

    /** Nueva cuenta administrativa (estudiantes y docentes se crean desde su sección). */
    public function create()
    {
        return view('administracion.personas.crear', ['perfil' => PerfilUsuario::CUENTA]);
    }

    public function edit(User $user)
    {
        return view('administracion.users.edit', compact('user'));
    }

    public function profile()
    {
        return view('administracion.users.profile');
    }

    public function reporteEstudiantes()
    {
        abort_unless(auth()->user()->can('gestionar_estudiantes'), 403);

        $filename = 'reporte-estudiantes-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new EstudiantesReporteExport(), $filename);
    }
}
