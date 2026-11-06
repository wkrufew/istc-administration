<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use App\Services\CredencialesService;

/**
 * Botón "Reenviar credenciales" de las pantallas de una persona (estudiante, docente, cuenta).
 * El componente debe tener la propiedad pública $user y usar WithAuthorization.
 */
trait ReenviaCredenciales
{
    public function reenviarCredenciales(): void
    {
        if ($this->sinPermiso('reenviar_credenciales')) return;

        $user = User::with('roles')->findOrFail($this->user->id);

        try {
            CredencialesService::reenviarAcceso($user);
        } catch (\DomainException $e) {
            $this->dispatch('swal', ['icon' => 'warning', 'title' => 'No se envió nada.', 'text' => $e->getMessage()]);
            return;
        } catch (\RuntimeException $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'No se pudo enviar el correo.', 'text' => $e->getMessage()]);
            return;
        }

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Credenciales reenviadas.',
            'text'  => 'Se envió el correo a ' . $user->email . '. Su contraseña ahora es su número de cédula.',
            'timer' => 3500,
        ]);
    }

    /** Si se muestra el botón: a quien no le aplica (sin rol, estudiante sin matrícula) no se le ofrece. */
    public function aplicaReenvioCredenciales(): bool
    {
        return CredencialesService::tipoAcceso($this->user) !== null;
    }
}
