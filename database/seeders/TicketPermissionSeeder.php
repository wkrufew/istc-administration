<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TicketPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $ticketPerms = [
            'ver_tickets',
            'ver_todos_tickets',
            'crear_tickets',
            'responder_tickets',
            'asignar_tickets',
            'cambiar_estado_tickets',
            'cerrar_tickets',
        ];

        foreach ($ticketPerms as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Administrador y Secretaria reciben todos los permisos de tickets
        foreach (['Administrador', 'Secretaria'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->givePermissionTo($ticketPerms);
        }
    }
}
