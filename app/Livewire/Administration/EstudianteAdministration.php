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
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Estudiante');
        })
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->search . '%');
            })
            ->with('roles')
            ->paginate(10);

        return view('livewire.administration.estudiante-administration', compact('users'));
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
                'message' => $user->is_active ? 'Docente habilitado con éxito.' : 'Docente inhabilitado con éxito.',
                'type' => 'success',
                'title' => 'Actualización exitosa'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error al actualizar el estado del Docente' . $e->getMessage(),
                'type' => 'error',
                'title' => 'Error'
            ]);
        }
    }
}
