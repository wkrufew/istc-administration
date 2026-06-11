<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'permisos' => \App\Http\Middleware\CheckPermission::class,
            'active'   => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // =====================================================================
        // CAPA DE FALLBACK — solo actúa cuando las capas anteriores no capturan
        //
        // Flujo de autorización (de más específico a más general):
        //   1. Middleware `permisos`        → portero de rutas (redirige con swal)
        //   2. WithAuthorization trait       → Livewire (dispatch swal + return)
        //   3. Policies vía Gate::authorize()→ controladores (AuthorizationException)
        //   4. Este bloque                  → fallback para cualquier 403 que escape
        // =====================================================================

        // ── Helper: datos del SweetAlert de acceso denegado ────────────────
        $swalDenied = fn(string $detalle = '') => [
            'icon'              => 'error',
            'title'             => 'Acceso denegado',
            'text'              => $detalle ?: 'No tienes permiso para acceder a esta sección.',
            'confirmButtonText' => 'Entendido',
        ];

        // ── Helper: URL de redirección segura (evita bucle) ───────────────
        $safeRedirect = function () {
            $previous = url()->previous('');
            $current  = request()->fullUrl();

            if ($previous && $previous !== $current) {
                return $previous;
            }

            // Redirigir según el portal del usuario autenticado
            $user = auth()->user();
            if (! $user) {
                return route('login');
            }
            if ($user->can('acceso_administrativo')) {
                return route('administracion.administrativa.dashboard');
            }
            if ($user->can('acceso_docencia')) {
                return route('administracion.docencia.dashboard');
            }
            if ($user->can('acceso_estudiantil')) {
                return route('administracion.estudiantil.dashboard');
            }
            return route('login');
        };

        // ── 1. Spatie: middleware `permission:` ────────────────────────────
        $exceptions->render(function (
            \Spatie\Permission\Exceptions\UnauthorizedException $e,
            \Illuminate\Http\Request $request
        ) use ($swalDenied, $safeRedirect) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para acceder a esta sección.'], 403);
            }
            return redirect($safeRedirect())->with('swal', $swalDenied());
        });

        // ── 2. abort_unless(…, 403) y abort(403) ─────────────────────────
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\HttpException $e,
            \Illuminate\Http\Request $request
        ) use ($swalDenied, $safeRedirect) {
            if ($e->getStatusCode() !== 403) {
                return null; // dejar que Laravel maneje otros códigos normalmente
            }
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage() ?: 'Sin permisos.'], 403);
            }
            return redirect($safeRedirect())->with('swal', $swalDenied($e->getMessage()));
        });

        // ── 3. $this->authorize() en controladores / Livewire ────────────
        $exceptions->render(function (
            \Illuminate\Auth\Access\AuthorizationException $e,
            \Illuminate\Http\Request $request
        ) use ($swalDenied, $safeRedirect) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage() ?: 'Sin permisos.'], 403);
            }
            return redirect($safeRedirect())->with('swal', $swalDenied($e->getMessage()));
        });

    })->create();
