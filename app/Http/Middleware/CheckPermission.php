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
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'No tienes permisos para realizar esta acción.'
                ], 403);
            }

            // Redirigir con mensaje de error
            return redirect()->back()->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
