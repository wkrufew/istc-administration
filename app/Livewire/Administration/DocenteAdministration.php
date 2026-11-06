<?php

namespace App\Livewire\Administration;

use App\Support\PerfilUsuario;
use App\Traits\WithAuthorization;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Sección Docentes: quienes dictan clases, incluidas las autoridades que también son docentes
 * (sin Administrador ni Super Admin).
 */
class DocenteAdministration extends Component
{
    use WithPagination, WithAuthorization;

    public $search;

    public function mount(): void
    {
        $this->requierePermiso('gestionar_docentes');
    }

    public function render()
    {
        $users = PerfilUsuario::docentes()
            ->when($this->search, fn($q) => $q->where(fn($sub) =>
                $sub->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('cedula', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $this->search . '%')
            ))
            ->with('roles')
            ->paginate(10);

        $totalDocentes  = PerfilUsuario::docentes()->count();
        $totalActivos   = PerfilUsuario::docentes()->where('is_active', true)->count();
        $totalInactivos = PerfilUsuario::docentes()->where('is_active', false)->count();

        return view('livewire.administration.docente-administration', compact(
            'users', 'totalDocentes', 'totalActivos', 'totalInactivos'
        ));
    }

    public function toggleStatus(int $userId)
    {
        if ($this->sinPermiso('gestionar_docentes')) return;

        $user = PerfilUsuario::docentes()->findOrFail($userId);
        if ($user->is_active && ($motivo = PerfilUsuario::motivoNoPuedeModificar($user, 'desactivar'))) {
            $this->dispatch('alert', ['message' => $motivo, 'type' => 'error', 'title' => 'No permitido']);
            return;
        }

        try {
            $user->is_active = ! $user->is_active;
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

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
