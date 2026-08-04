<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Convertir datos existentes antes de cambiar el ENUM
        DB::statement("UPDATE tickets SET estado = 'pendiente' WHERE estado IN ('abierto', 'esperando')");
        DB::statement("UPDATE tickets SET estado = 'cerrado'   WHERE estado = 'resuelto'");
        DB::statement("UPDATE tickets SET closed_at = resolved_at WHERE estado = 'cerrado' AND closed_at IS NULL AND resolved_at IS NOT NULL");

        // 2. Cambiar el ENUM al nuevo conjunto
        DB::statement("ALTER TABLE tickets MODIFY COLUMN estado ENUM('pendiente','en_proceso','cerrado') NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN estado ENUM('abierto','en_proceso','esperando','resuelto','cerrado') NOT NULL DEFAULT 'abierto'");
        DB::statement("UPDATE tickets SET estado = 'abierto' WHERE estado = 'pendiente'");
    }
};
