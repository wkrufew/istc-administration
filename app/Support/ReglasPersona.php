<?php

namespace App\Support;

/**
 * Reglas de validación de los datos que comparten todas las personas (cuenta, docente y
 * estudiante), para que un cambio en una regla no haya que repetirlo en cada formulario.
 */
class ReglasPersona
{
    /** Identidad y acceso: nombres, cédula y correo (únicos en el sistema). */
    public static function identidad(?int $ignorarId = null): array
    {
        $ignorar = $ignorarId ? ',' . $ignorarId : '';

        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'cedula'     => 'required|string|max:20|unique:users,cedula' . $ignorar,
            'email'      => 'required|email|max:255|unique:users,email' . $ignorar,
        ];
    }

    /** Contacto y datos personales básicos. */
    public static function contacto(): array
    {
        return [
            'phone'               => 'nullable|string|max:20',
            'address'             => 'nullable|string|max:500',
            'fecha_nacimiento'    => 'nullable|date',
            'nacionalidad'        => 'nullable|string|max:100',
            'genero'              => 'nullable|in:Masculino,Femenino,Otro',
            'estado_civil'        => 'nullable|string|max:50',
            'contacto_emergencia' => 'nullable|string|max:255',
            'telefono_emergencia' => 'nullable|string|max:20',
        ];
    }

    /** Contraseña escrita a mano (opcional al editar). */
    public static function contrasenaOpcional(): array
    {
        return ['password' => 'nullable|min:8|confirmed'];
    }

    public static function mensajes(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio',
            'last_name.required'  => 'El apellido es obligatorio',
            'email.required'      => 'El correo electrónico es obligatorio',
            'email.email'         => 'Ingrese un correo válido',
            'email.unique'        => 'Este correo ya está registrado',
            'cedula.required'     => 'La cédula es obligatoria',
            'cedula.unique'       => 'Esta cédula ya está registrada',
            'password.required'   => 'La contraseña es obligatoria',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed'  => 'Las contraseñas no coinciden',
            'role_id.required'    => 'Debe seleccionar un rol',
            'role_id.in'          => 'Ese rol no se puede asignar desde aquí.',
        ];
    }
}
