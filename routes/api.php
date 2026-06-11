<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Estudiante\ActaController;
use App\Http\Controllers\Api\Estudiante\AvisosController;
use App\Http\Controllers\Api\Estudiante\CalificacionesController;
use App\Http\Controllers\Api\Estudiante\DashboardController;
use App\Http\Controllers\Api\Estudiante\HorariosController;
use App\Http\Controllers\Api\Estudiante\ObligacionesController;
use App\Http\Controllers\Api\Estudiante\PerfilController;
use App\Http\Controllers\Api\Estudiante\SolicitudesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — ISTC Administración
|--------------------------------------------------------------------------
|
| Seguridad en capas:
|   1. throttle:5,1     → máximo 5 intentos de login por minuto por IP
|   2. auth:sanctum     → token Sanctum válido obligatorio
|   3. active           → usuario con is_active = true
|   4. permission:...   → permiso Spatie válido
|
*/

// ── Autenticación ─────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',     [AuthController::class, 'me']);
    });
});

// ── Portal estudiantil ────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'active', 'permission:acceso_estudiantil'])
    ->prefix('estudiante')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Perfil
        Route::get('perfil', [PerfilController::class, 'index']);

        // Calificaciones
        Route::get('calificaciones',             [CalificacionesController::class, 'periodos']);
        Route::get('calificaciones/{periodo_id}', [CalificacionesController::class, 'notas']);

        // Acta oficial de calificaciones (malla curricular completa)
        Route::get('acta', [ActaController::class, 'index']);

        // Horarios
        Route::get('horarios',             [HorariosController::class, 'periodos']);
        Route::get('horarios/{periodo_id}', [HorariosController::class, 'horario']);

        // Obligaciones financieras — resumen debe ir antes de {id}
        Route::get('obligaciones/resumen',         [ObligacionesController::class, 'resumen']);
        Route::get('obligaciones',                  [ObligacionesController::class, 'index']);
        Route::post('obligaciones/{id}/pagar',      [ObligacionesController::class, 'pagar']);
        Route::get('obligaciones/{id}/historial',   [ObligacionesController::class, 'historial']);

        // Avisos — leer-todos debe ir antes de {id}
        Route::get('avisos',                   [AvisosController::class, 'index']);
        Route::post('avisos/leer-todos',        [AvisosController::class, 'marcarTodosLeidos']);
        Route::post('avisos/{id}/leer',         [AvisosController::class, 'marcarLeido']);

        // Solicitudes — tipos debe ir antes de {id}
        Route::get('solicitudes/tipos',  [SolicitudesController::class, 'tipos']);
        Route::get('solicitudes',        [SolicitudesController::class, 'index']);
        Route::post('solicitudes',       [SolicitudesController::class, 'store']);
    });
