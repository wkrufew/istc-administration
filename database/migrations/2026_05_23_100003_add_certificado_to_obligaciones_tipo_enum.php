<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE obligaciones_financieras MODIFY COLUMN tipo ENUM('MATRICULA','COLEGIATURA','ARRASTRE','MULTA','OTROS','INSCRIPCION','CERTIFICADO') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE obligaciones_financieras MODIFY COLUMN tipo ENUM('MATRICULA','COLEGIATURA','ARRASTRE','MULTA','OTROS','INSCRIPCION') NOT NULL");
        }
    }
};
