<?php

namespace App\Policies;

use App\Models\Aviso;
use App\Models\User;

class AvisoPolicy
{
    /**
     * Ver avisos (estudiante ve los de sus materias; docente ve los suyos).
     * La query ya filtra por pertenencia, aquí solo verificamos el permiso base.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('acceso_docencia') || $user->can('acceso_estudiantil');
    }

    /**
     * Crear un aviso. Solo docentes con acceso al portal de docencia.
     */
    public function create(User $user): bool
    {
        return $user->can('acceso_docencia');
    }

    /**
     * Eliminar un aviso. El docente solo puede eliminar sus propios avisos.
     * La verificación de propiedad la hace el whereHas en el componente,
     * aquí garantizamos que tiene el rol correcto.
     */
    public function delete(User $user, Aviso $aviso): bool
    {
        if (! $user->can('acceso_docencia')) {
            return false;
        }

        // Verificar que el aviso pertenece a una asignación del docente
        return $aviso->asignacionDocente?->docente_id === $user->id;
    }
}
