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
        $role = 'Administrador';

        // Crear el usuario
        $user = User::create([
            'name' => "SMITH VINICIO AVILES MATUTE",
            'email' => "smithva@hotmail.es",
            'password' => Hash::make('9invensible3'),
        ]);

        // Asignar rol
        $user->assignRole($role);
    }
}
