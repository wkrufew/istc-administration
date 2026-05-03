<?php

namespace App\Policies;

use App\Models\Calificacion;
use App\Models\User;

class CalificacionPolicy
{
    /**
     * Ver calificaciones.
     * Docente puede ver las de sus materias; admin/secretaria pueden ver todas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_notas_estudiantes')
            || $user->can('ingresar_notas_estudiantes');
    }

    /**
     * Crear o actualizar una calificación.
     * Solo el docente asignado a esa materia+paralelo puede ingresarla.
     */
    public function create(User $user): bool
    {
        return $user->can('ingresar_notas_estudiantes');
    }

    /**
     * Actualizar una calificación existente.
     * El docente solo puede editar calificaciones que él mismo registró.
     */
    public function update(User $user, Calificacion $calificacion): bool
    {
        if (! $user->can('ingresar_notas_estudiantes')) {
            return false;
        }

        return $calificacion->docente_id === $user->id;
    }
}
