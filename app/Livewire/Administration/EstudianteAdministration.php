<?php

namespace App\Livewire\Administration;

use App\Models\User;
use App\Support\PerfilUsuario;
use App\Traits\WithAuthorization;
use Livewire\Component;
use Livewire\WithPagination;

/** Sección Estudiantes: listado de estudiantes (sin Administrador ni Super Admin). */
class EstudianteAdministration extends Component
{
    use WithPagination, WithAuthorization;

    public $search;

    public function mount(): void
    {
        $this->requierePermiso('gestionar_estudiantes');
    }

    public function render()
    {
        $users = PerfilUsuario::estudiantes()
            ->when($this->search, fn($q) => $q->where(fn($sub) =>
                $sub->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('cedula', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->search . '%')
            ))
            ->with('roles')
            ->paginate(10);

        $totalActivos   = PerfilUsuario::estudiantes()->where('is_active', true)->count();
        $totalInactivos = PerfilUsuario::estudiantes()->where('is_active', false)->count();

        return view('livewire.administration.estudiante-administration', compact(
            'users', 'totalActivos', 'totalInactivos'
        ));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus(int $userId)
    {
        if ($this->sinPermiso('gestionar_estudiantes')) return;

        $user = PerfilUsuario::estudiantes()->findOrFail($userId);

        try {
            $user->is_active = ! $user->is_active;
            $user->save();

            $this->dispatch('alert', [
                'message' => $user->is_active ? 'Estudiante habilitado con éxito.' : 'Estudiante inhabilitado con éxito.',
                'type' => 'success',
                'title' => 'Actualización exitosa'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error al actualizar el estado del Estudiante' . $e->getMessage(),
                'type' => 'error',
                'title' => 'Error'
            ]);
        }
    }
}
