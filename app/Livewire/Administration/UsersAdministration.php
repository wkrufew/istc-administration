<?php

namespace App\Livewire\Administration;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersAdministration extends Component
{
    use WithPagination;

    public $search;

    public function render()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Super Admin');
        })
            ->where(function ($query) {
                $query->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->search . '%');
            })
            ->with('roles')
            ->paginate(10);

        return view('livewire.administration.users-administration', compact('users'));
    }

    public function toggleStatus(User $user)
    {
        try {
            $user->is_active = !$user->is_active;
            $user->save();

            $this->dispatch('alert', [
                'message' => $user->is_active ? 'Usuario habilitado con éxito.' : 'Usuario inhabilitado con éxito.',
                'type' => 'success',
                'title' => 'Actualización exitosa'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error al actualizar el estado del usuario' . $e->getMessage(),
                'type' => 'error',
                'title' => 'Error'
            ]);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
