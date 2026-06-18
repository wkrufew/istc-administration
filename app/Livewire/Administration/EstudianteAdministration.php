<?php

namespace App\Livewire\Administration;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class EstudianteAdministration extends Component
{
    use WithPagination;

    public $search;

    public function render()
    {
        $users = User::permission('acceso_estudiantil')
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->search . '%');
            })
            ->with('roles')
            ->paginate(10);

        $totalActivos   = User::permission('acceso_estudiantil')->where('is_active', true)->count();
        $totalInactivos = User::permission('acceso_estudiantil')->where('is_active', false)->count();

        return view('livewire.administration.estudiante-administration', compact(
            'users', 'totalActivos', 'totalInactivos'
        ));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus(User $user)
    {
        try {
            $user->is_active = !$user->is_active;
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
