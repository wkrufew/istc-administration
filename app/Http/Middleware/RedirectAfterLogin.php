<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAfterLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);

        /*  $response = $next($request); */

        // Solo actuar si el usuario acaba de hacer login y está autenticado
        /* if (Auth::check() && $request->route()->getName() === 'login' && $request->isMethod('POST')) {
            $user = Auth::user();

            // Verificar si el usuario tiene al menos un rol permitido
            if (!$user->hasAnyRole(['admin', 'docente', 'estudiante'])) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Acceso denegado. No tienes permisos para acceder al sistema.');
            }

            // Obtener el primer rol del usuario
            $role = $user->getRoleNames()->first();

            // Mensaje de bienvenida personalizado
            $welcomeMessage = "¡Bienvenido, " . $user->name . "!";

            return match ($role) {
                'admin' => redirect()->route('administrativa.administrativa')->with('success', $welcomeMessage),
                'docente' => redirect()->route('administrativa.docencia')->with('success', $welcomeMessage),
                'estudiante' => redirect()->route('administrativa.estudiantil')->with('success', $welcomeMessage),
                default => redirect()->route('login')->with('error', 'Rol no reconocido. Contacta al administrador.'),
            };
        } */

        return $response;
    }
}
