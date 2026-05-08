<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE obligaciones_financieras MODIFY COLUMN tipo ENUM('MATRICULA','COLEGIATURA','ARRASTRE','MULTA','OTROS','INSCRIPCION') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE obligaciones_financieras MODIFY COLUMN tipo ENUM('MATRICULA','COLEGIATURA','ARRASTRE','MULTA','OTROS') NOT NULL");
    }
};
