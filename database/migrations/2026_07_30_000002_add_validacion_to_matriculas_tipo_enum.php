<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE matriculas MODIFY tipo ENUM('Nueva','Renovacion','Arrastre','Validacion') NOT NULL DEFAULT 'Nueva'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("DELETE FROM matriculas WHERE tipo = 'Validacion'");
            DB::statement("ALTER TABLE matriculas MODIFY tipo ENUM('Nueva','Renovacion','Arrastre') NOT NULL DEFAULT 'Nueva'");
        }
    }
};
