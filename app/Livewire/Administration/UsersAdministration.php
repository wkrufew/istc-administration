<?php

namespace App\Livewire\Administration;

use App\Models\User;
use App\Traits\WithAuthorization;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UsersAdministration extends Component
{
    use WithPagination, WithAuthorization;

    public string $search      = '';
    public string $filtroRol   = '';
    public string $filtroEstado = '';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_usuarios');
    }

    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingFiltroRol(): void   { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }

    public function eliminar(User $user): void
    {
        if ($this->sinPermiso('eliminar_usuarios')) return;

        try {
            $user->is_active = false;
            $user->save();
            $user->delete();

            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => 'Usuario eliminado.',
                'text'  => $user->name . ' ha sido desactivado y movido a la papelera.',
                'timer' => 2500,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al eliminar.',
                'text'  => $e->getMessage(),
                'toast' => true,
            ]);
        }
    }

    public function render()
    {
        $roles = Role::where('name', '!=', 'Super Admin')
            ->orderBy('name')
            ->get();

        $users = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Super Admin'))
            ->when($this->search, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('name',    'LIKE', '%'.$this->search.'%')
                        ->orWhere('email',  'LIKE', '%'.$this->search.'%')
                        ->orWhere('cedula', 'LIKE', '%'.$this->search.'%')
                )
            )
            ->when($this->filtroRol, fn($q) =>
                $q->whereHas('roles', fn($r) => $r->where('name', $this->filtroRol))
            )
            ->when($this->filtroEstado !== '', fn($q) =>
                $q->where('is_active', $this->filtroEstado === '1')
            )
            ->with('roles')
            ->paginate(10);

        return view('livewire.administration.users-administration', compact('users', 'roles'));
    }

    public function toggleStatus(User $user): void
    {
        if ($this->sinPermiso('editar_usuarios')) return;

        try {
            $user->is_active = ! $user->is_active;
            $user->save();

            $this->dispatch('alert', [
                'message' => $user->is_active ? 'Usuario habilitado con éxito.' : 'Usuario inhabilitado con éxito.',
                'type'    => 'success',
                'title'   => 'Actualización exitosa',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'message' => 'Error al actualizar el estado del usuario: ' . $e->getMessage(),
                'type'    => 'error',
                'title'   => 'Error',
            ]);
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['filtroRol', 'filtroEstado']);
        $this->resetPage();
    }
}
