<?php

namespace App\Policies;

use App\Models\Asistencia;
use App\Models\User;

class AsistenciaPolicy
{
    /**
     * Ver y gestionar asistencias.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('gestionar_asistencias');
    }

    /**
     * Registrar o actualizar asistencias.
     * El docente solo puede registrar asistencias de horarios que le pertenecen.
     * La verificación de propiedad del horario la hace el componente con
     * horarioPerteneceAlDocente(); aquí garantizamos el permiso base.
     */
    public function create(User $user): bool
    {
        return $user->can('gestionar_asistencias');
    }

    /**
     * Editar/corregir una asistencia existente.
     * El docente solo puede editar asistencias que él mismo registró.
     */
    public function update(User $user, Asistencia $asistencia): bool
    {
        if (! $user->can('gestionar_asistencias')) {
            return false;
        }

        return $asistencia->docente_id === $user->id;
    }
}
