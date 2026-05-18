<?php

namespace App\Livewire\Administration;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class UsersEliminados extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function restaurar(int $userId): void
    {
        try {
            $user = User::onlyTrashed()->findOrFail($userId);
            $user->restore();
            $user->update(['is_active' => true]);

            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => 'Usuario restaurado.',
                'text'  => $user->name . ' ha sido habilitado correctamente.',
                'timer' => 2500,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al restaurar.',
                'text'  => $e->getMessage(),
                'toast' => true,
            ]);
        }
    }

    public function render()
    {
        $users = User::onlyTrashed()
            ->when($this->search, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('name',    'LIKE', '%'.$this->search.'%')
                        ->orWhere('email',  'LIKE', '%'.$this->search.'%')
                        ->orWhere('cedula', 'LIKE', '%'.$this->search.'%')
                )
            )
            ->with('roles')
            ->latest('deleted_at')
            ->paginate(10);

        return view('livewire.administration.users-eliminados', compact('users'));
    }
}
