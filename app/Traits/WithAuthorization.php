<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait WithAuthorization
 *
 * Centraliza los checks de autorización en componentes Livewire.
 * Todas las respuestas de denegación pasan por SweetAlert2 y siguen
 * el mismo patrón visual sin depender del handler global de excepciones.
 *
 * USO:
 *
 * En mount() para bloquear todo el componente:
 *   $this->requierePermiso('gestionar_usuarios');
 *
 * En métodos de acción para salida temprana:
 *   if ($this->sinPermiso('gestionar_matriculas')) return;
 *
 * Para verificar propiedad de un registro:
 *   if ($this->noEsPropietario($ticket, 'created_by')) return;
 */
trait WithAuthorization
{
    /**
     * Verifica que el usuario autenticado tenga el permiso indicado.
     * Si no lo tiene, muestra un SweetAlert modal y redirige al dashboard.
     * Usar en mount() para proteger el componente completo.
     */
    protected function requierePermiso(string $permiso, ?string $mensaje = null): void
    {
        if (! auth()->check() || ! auth()->user()->can($permiso)) {
            // session()->flash persiste hasta el siguiente request (la página destino).
            // dispatch() se pierde con el redirect, por eso usamos la sesión.
            session()->flash('swal', [
                'icon'              => 'error',
                'title'             => 'Acceso denegado',
                'text'              => $mensaje ?? 'No tienes permiso para acceder a esta sección.',
                'confirmButtonText' => 'Entendido',
            ]);
            $this->redirectRoute($this->rutaFallback(), navigate: true);
        }
    }

    /**
     * Verifica si el usuario NO tiene el permiso indicado.
     * Si está denegado muestra un toast de advertencia y retorna true.
     * Usar en métodos de acción: if ($this->sinPermiso('crear_tickets')) return;
     */
    protected function sinPermiso(string $permiso, ?string $mensaje = null): bool
    {
        if (! auth()->check() || ! auth()->user()->can($permiso)) {
            $this->dispatch('swal', [
                'icon'     => 'warning',
                'title'    => 'Sin permiso',
                'text'     => $mensaje ?? 'No tienes permiso para realizar esta acción.',
                'toast'    => true,
                'position' => 'top-end',
                'timer'    => 4000,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Verifica si el usuario autenticado NO es propietario del modelo.
     * Si no lo es, muestra un toast de error y retorna true.
     * Usar en métodos de acción: if ($this->noEsPropietario($aviso, 'created_by')) return;
     */
    protected function noEsPropietario(Model $modelo, string $campo = 'user_id', ?string $mensaje = null): bool
    {
        if ((int) auth()->id() !== (int) $modelo->{$campo}) {
            $this->dispatch('swal', [
                'icon'     => 'error',
                'title'    => 'Acceso denegado',
                'text'     => $mensaje ?? 'No puedes modificar registros que no te pertenecen.',
                'toast'    => true,
                'position' => 'top-end',
                'timer'    => 4000,
            ]);
            return true;
        }
        return false;
    }

    /**
     * Determina la ruta de fallback según el rol del usuario autenticado.
     */
    private function rutaFallback(): string
    {
        $user = auth()->user();
        if (! $user) {
            return 'login';
        }
        if ($user->can('acceso_administrativo')) {
            return 'administracion.administrativa.dashboard';
        }
        if ($user->can('acceso_docencia')) {
            return 'administracion.docencia.dashboard';
        }
        if ($user->can('acceso_estudiantil')) {
            return 'administracion.estudiantil.dashboard';
        }
        if ($user->can('acceso_admision')) {
            return 'administracion.admision.dashboard';
        }
        return 'login';
    }
}
