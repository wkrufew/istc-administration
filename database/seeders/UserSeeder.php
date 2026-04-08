<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'name' => "Usuario $i",
                'email' => "usuario$i@example.com",
                'password' => Hash::make('password'), // Usa bcrypt
            ]);

            // Asignar roles cada 10 usuarios
            if ($i <= 10) {
                $user->assignRole('Administrador');
            } elseif ($i <= 20) {
                $user->assignRole('Docente');
            } else {
                $user->assignRole('Estudiante');
            }
        } */
        for ($i = 1; $i <= 5; $i++) {
            // Determinar el rol y prefijo de correo
            if ($i <= 1) {
                $role = 'Administrador';
                $prefix = 'administrador';
            } elseif ($i <= 4) {
                $role = 'Docente';
                $prefix = 'docente';
            } /* else {
                $role = 'Estudiante';
                $prefix = 'estudiante';
            } */

            // Crear el usuario
            $user = User::create([
                'name' => ucfirst($prefix) . " $i",
                'email' => "$prefix$i@example.com",
                'password' => Hash::make('password'),
            ]);

            // Asignar rol
            $user->assignRole($role);
        }

        /* // Usuarios sin rol para pruebas
        for ($j = 1; $j <= 2; $j++) {
            User::create([
                'name' => "Usuario $j",
                'email' => "usuario$j@example.com",
                'password' => Hash::make('password'),
            ]);
        } */
    }
}
