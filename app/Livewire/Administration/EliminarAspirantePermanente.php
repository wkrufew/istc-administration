<?php

namespace App\Livewire\Administration;

use App\Models\Aspirante;
use App\Traits\WithAuthorization;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class EliminarAspirantePermanente extends Component
{
    use WithAuthorization;

    public Aspirante $aspirante;
    public int    $codigoSeguridad;
    public string $codigoIngresado = '';
    public bool   $eliminado       = false;

    public function mount(int $id): void
    {
        $this->requierePermiso('gestionar_aspirantes');

        $this->aspirante       = Aspirante::onlyTrashed()->with(['user', 'carrera', 'cohorte'])->findOrFail($id);
        $this->codigoSeguridad = random_int(10, 99);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.administration.eliminar-aspirante-permanente');
    }

    public function confirmar(): void
    {
        if ($this->sinPermiso('gestionar_aspirantes')) return;

        $this->validate(
            ['codigoIngresado' => 'required'],
            ['codigoIngresado.required' => 'Ingresa el código de seguridad.']
        );

        if ((int) $this->codigoIngresado !== $this->codigoSeguridad) {
            $this->codigoIngresado = '';
            $this->codigoSeguridad = random_int(10, 99);
            $this->addError('codigoIngresado', 'Código incorrecto. Se generó un nuevo código.');
            return;
        }

        DB::transaction(function () {
            $userId = $this->aspirante->user_id;
            $this->aspirante->forceDelete();
            // Elimina el usuario solo si no tiene otros roles activos (no es estudiante/docente)
            $user = \App\Models\User::withTrashed()->find($userId);
            if ($user && $user->roles->count() <= 1 && ! $user->matriculas()->exists()) {
                $user->forceDelete();
            }
        });

        $this->eliminado = true;
    }
}
