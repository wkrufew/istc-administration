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
use App\Observers\PagoObserver;
use Illuminate\Support\Facades\Hash;
use App\Models\Pago;

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
        Pago::observe(PagoObserver::class);

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
