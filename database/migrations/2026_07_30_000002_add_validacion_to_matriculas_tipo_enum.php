<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE matriculas MODIFY tipo ENUM('Nueva','Renovacion','Arrastre','Validacion') NOT NULL DEFAULT 'Nueva'");
    }

    public function down(): void
    {
        // Eliminar registros tipo Validacion antes de revertir el enum
        DB::statement("DELETE FROM matriculas WHERE tipo = 'Validacion'");
        DB::statement("ALTER TABLE matriculas MODIFY tipo ENUM('Nueva','Renovacion','Arrastre') NOT NULL DEFAULT 'Nueva'");
    }
};
