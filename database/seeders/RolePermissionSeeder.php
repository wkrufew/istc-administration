<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos
        $permissions = [
            'acceso_administrativo',
            'asignar_roles',
            'crear_roles',
            'acceso_docencia',
            'ver_notas_estudiantes',
            'ingresar_notas_estudiantes',
            'acceso_estudiantil',
            'ver_calificaciones',
            'matricularse',
        ];

        // Crear permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $secretaria = Role::firstOrCreate(['name' => 'Secretaria']);
        $docente = Role::firstOrCreate(['name' => 'Docente']);
        $estudiante = Role::firstOrCreate(['name' => 'Estudiante']);
        $estudiante = Role::firstOrCreate(['name' => 'Admision']);

        // Asignar permisos por rol
        $admin->syncPermissions([
            'acceso_administrativo',
            'asignar_roles',
            'crear_roles',
            'acceso_docencia',
            'ver_notas_estudiantes',
            'ingresar_notas_estudiantes',
        ]);

        $secretaria->syncPermissions([
            'acceso_administrativo',
            'asignar_roles',
            'crear_roles',
            'acceso_docencia',
            'ver_notas_estudiantes',
            'ingresar_notas_estudiantes',
        ]);

        $docente->syncPermissions([
            'acceso_docencia',
            'ver_notas_estudiantes',
            'ingresar_notas_estudiantes',
        ]);

        $estudiante->syncPermissions([
            'acceso_estudiantil',
            'ver_calificaciones',
            'matricularse',
        ]);
    }
}
