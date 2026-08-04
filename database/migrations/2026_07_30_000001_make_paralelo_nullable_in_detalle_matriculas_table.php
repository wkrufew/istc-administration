<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar FK existente, hacer el campo nullable, re-agregar FK con SET NULL
        DB::statement('ALTER TABLE detalle_matriculas DROP FOREIGN KEY detalle_matriculas_paralelo_id_foreign');
        DB::statement('ALTER TABLE detalle_matriculas MODIFY paralelo_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE detalle_matriculas ADD CONSTRAINT detalle_matriculas_paralelo_id_foreign FOREIGN KEY (paralelo_id) REFERENCES paralelos(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        // Registros sin paralelo no pueden revertirse; esto es solo para entorno de desarrollo
        DB::statement('ALTER TABLE detalle_matriculas DROP FOREIGN KEY detalle_matriculas_paralelo_id_foreign');
        DB::statement('UPDATE detalle_matriculas SET paralelo_id = 0 WHERE paralelo_id IS NULL');
        DB::statement('ALTER TABLE detalle_matriculas MODIFY paralelo_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE detalle_matriculas ADD CONSTRAINT detalle_matriculas_paralelo_id_foreign FOREIGN KEY (paralelo_id) REFERENCES paralelos(id) ON DELETE CASCADE');
    }
};
