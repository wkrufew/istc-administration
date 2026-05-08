<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login as EventsLogin;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Requests\LoginRequest;
use App\Models\User;
use App\Observers\AuditObserver;
use App\Observers\PagoObserver;
use Illuminate\Support\Facades\Hash;
use App\Models\Asistencia;
use App\Models\Aviso;
use App\Models\Calificacion;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Matricula;
use App\Models\ObligacionesFinanciera;
use App\Models\Pago;
use App\Models\Semestre;
use App\Models\Ticket;
use App\Policies\AsistenciaPolicy;
use App\Policies\AvisoPolicy;
use App\Policies\CalificacionPolicy;
use App\Policies\MatriculaPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Registro explícito de Policies ────────────────────────────────────
        Gate::policy(Ticket::class,      TicketPolicy::class);
        Gate::policy(Aviso::class,       AvisoPolicy::class);
        Gate::policy(Calificacion::class, CalificacionPolicy::class);
        Gate::policy(Asistencia::class,  AsistenciaPolicy::class);
        Gate::policy(Matricula::class,   MatriculaPolicy::class);

        Pago::observe(PagoObserver::class);

        // Auditoría general — registra cambios en modelos críticos
        $auditables = [Matricula::class, Carrera::class, Semestre::class, Materia::class, ObligacionesFinanciera::class, Pago::class];
        foreach ($auditables as $model) {
            $model::observe(AuditObserver::class);
        }

        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Validación personalizada al autenticar
        Fortify::authenticateUsing(function ($request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Verificar si el usuario está activo
                if (!$user->is_active) {
                    session()->flash('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
                    return null;
                }

                // Verificar permisos básicos aquí también (opcional)
                if (!$user->hasAnyPermission([
                    'acceso_administrativo',
                    'acceso_docencia',
                    'acceso_estudiantil'
                ])) {
                    session()->flash('error', 'Acceso denegado. No tienes permisos para acceder al sistema.');
                    return null;
                }

                return $user;
            }
            session()->flash('error', 'Estas credenciales no coinciden con nuestros registros.');
            return null;
        });
    }
}
