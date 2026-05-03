<?php

namespace App\Policies;

use App\Models\Matricula;
use App\Models\User;

class MatriculaPolicy
{
    /**
     * Ver listado de matrículas (administración).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('gestionar_matriculas');
    }

    /**
     * Ver una matrícula específica.
     * Admin puede ver cualquiera; el estudiante solo la suya.
     */
    public function view(User $user, Matricula $matricula): bool
    {
        if ($user->can('gestionar_matriculas')) {
            return true;
        }

        // El estudiante puede ver su propia matrícula
        return $user->can('matricularse') && $matricula->user_id === $user->id;
    }

    /**
     * Crear una matrícula nueva.
     */
    public function create(User $user): bool
    {
        return $user->can('gestionar_matriculas');
    }

    /**
     * Editar/actualizar una matrícula.
     */
    public function update(User $user, Matricula $matricula): bool
    {
        return $user->can('gestionar_matriculas');
    }

    /**
     * Registrar el pago de una matrícula.
     */
    public function pay(User $user, Matricula $matricula): bool
    {
        return $user->can('gestionar_matriculas');
    }
}
