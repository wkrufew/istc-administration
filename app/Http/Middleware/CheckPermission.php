<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        //$user = Auth::user();
        $user = auth()->user();
        //dd($user);
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Si no se especifican permisos, permitir acceso
        if (empty($permissions)) {
            return $next($request);
        }

        // Verificar si el usuario tiene al menos uno de los permisos requeridos
        if (!$user->hasAnyPermission($permissions)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción.',
                ], 403);
            }

            // Redirigir con datos para SweetAlert
            $previous = url()->previous('');
            $current  = $request->fullUrl();
            $dest     = ($previous && $previous !== $current) ? $previous : route('administracion.administrativa.dashboard');

            return redirect($dest)->with('swal', [
                'icon'              => 'error',
                'title'             => 'Acceso denegado',
                'text'              => 'No tienes permisos para acceder a esta sección.',
                'confirmButtonText' => 'Entendido',
            ]);
        }

        return $next($request);
    }
}
