<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // El enum anterior solo tenía Masculino/Femenino/Otro — se amplía a varchar para
        // soportar Transgénero, Fluido, Cisgénero y "Prefiero no decirlo"
        DB::statement("ALTER TABLE users MODIFY COLUMN genero VARCHAR(50) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN genero ENUM('Masculino','Femenino','Otro') NULL");
    }
};
