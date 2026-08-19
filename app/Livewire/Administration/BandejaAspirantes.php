<?php

namespace App\Livewire\Administration;

use App\Models\Aspirante;
use App\Models\User;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class BandejaAspirantes extends Component
{
    use WithAuthorization, WithPagination;

    public string $buscar = '';

    public function mount(): void
    {
        $this->requierePermiso('gestionar_aspirantes');
    }

    public function updatedBuscar(): void { $this->resetPage(); }

    #[Computed]
    public function eliminados()
    {
        return Aspirante::onlyTrashed()
            ->with(['user', 'cohorte.carrera', 'registradoPor'])
            ->when($this->buscar, function ($q) {
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$this->buscar}%")
                      ->orWhere('cedula', 'like', "%{$this->buscar}%")
                      ->orWhere('email', 'like', "%{$this->buscar}%")
                );
            })
            ->orderByDesc('deleted_at')
            ->paginate(20);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.bandeja-aspirantes', [
            'eliminados' => $this->eliminados,
        ]);
    }

    public function restaurar(int $id): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        Aspirante::onlyTrashed()->findOrFail($id)->restore();
        unset($this->eliminados);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Aspirante restaurado',
            'text'  => 'El aspirante vuelve a la lista activa.',
            'timer' => 3000,
        ]);
    }
}
