<?php

namespace App\Livewire\Administration;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UsersAdministration extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filtroRol   = '';
    public string $filtroEstado = '';

    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingFiltroRol(): void   { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }

    public function render()
    {
        $roles = Role::whereIn('name', ['Administrador', 'Secretaria', 'Docente', 'Estudiante', 'Admision'])
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
