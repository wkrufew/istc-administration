<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Foto de perfil en las fichas de una persona: subir una nueva (reemplaza la anterior) o quitarla.
 * El componente debe tener la propiedad pública $user, usar WithFileUploads y WithAuthorization,
 * y definir permisoEdicion() con el permiso que exige modificar la ficha.
 * La vista se incluye con @include('livewire.administration.partials.foto-perfil').
 */
trait GestionaFotoPerfil
{
    public $profile_photo;
    public ?string $profile_photo_actual = null;

    protected function reglasFoto(): array
    {
        return ['profile_photo' => 'nullable|image|max:2048'];
    }

    /** Agrega la foto nueva a los datos a guardar y borra la anterior. */
    protected function aplicarFotoNueva(array &$datos): void
    {
        if (! $this->profile_photo) return;

        if ($this->profile_photo_actual) {
            Storage::disk('public')->delete($this->profile_photo_actual);
        }
        $datos['profile_photo_path']  = $this->profile_photo->store('users-data/profile-photos', 'public');
        $this->profile_photo_actual = $datos['profile_photo_path'];
        $this->profile_photo        = null;
    }

    public function deletePhoto(): void
    {
        if ($this->sinPermiso($this->permisoEdicion())) return;
        if (! $this->profile_photo_actual) return;

        Storage::disk('public')->delete($this->profile_photo_actual);
        $this->user->update(['profile_photo_path' => null]);
        $this->profile_photo_actual = null;
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Foto eliminada.', 'timer' => 2000]);
    }
}
