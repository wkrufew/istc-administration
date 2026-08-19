<?php

namespace App\Livewire\Administration;

use App\Models\User;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class UsersEliminados extends Component
{
    use WithPagination, WithAuthorization;

    public string $search = '';

    public function mount(): void
    {
        $this->requierePermiso('eliminar_usuarios');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function forceEliminar(int $userId): void
    {
        if ($this->sinPermiso('eliminar_usuarios')) return;

        try {
            $user = User::onlyTrashed()->findOrFail($userId);

            $tieneRegistros =
                $user->matriculas()->exists() ||
                $user->detalleMatriculas()->exists() ||
                $user->materiasArrastradas()->exists() ||
                $user->obligacionesFinancieras()->exists() ||
                $user->asignacionesDocente()->exists() ||
                $user->practicasPreprofesionales()->exists() ||
                $user->notasTitulacion()->exists() ||
                $user->document()->exists();

            if ($tieneRegistros) {
                $this->dispatch('swal', [
                    'icon'              => 'error',
                    'title'             => 'No se puede eliminar',
                    'html'              => 'El usuario <strong>' . e($user->name) . '</strong> tiene registros vinculados en el sistema y no puede ser eliminado de forma permanente.',
                    'confirmButtonText' => 'Entendido',
                ]);
                return;
            }

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            if ($user->certificado_discapacidad_path) {
                Storage::disk('public')->delete($user->certificado_discapacidad_path);
            }

            $aspirante = $user->aspirante()->withTrashed()->first();
            if ($aspirante) {
                Storage::disk('public')->deleteDirectory("aspirantes/{$aspirante->id}");
            }

            $nombre = $user->name;
            $user->forceDelete();

            $this->dispatch('swal', [
                'icon'              => 'success',
                'title'             => 'Usuario eliminado definitivamente.',
                'text'              => $nombre . ' ha sido eliminado de forma permanente del sistema.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon'  => 'error',
                'title' => 'Error al eliminar.',
                'text'  => $e->getMessage(),
            ]);
        }
    }

    public function restaurar(int $userId): void
    {
        if ($this->sinPermiso('eliminar_usuarios')) return;

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
