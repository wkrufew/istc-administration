<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE matriculas MODIFY estado ENUM('Borrador','Pendiente_Pago','Habilitada','Cancelada','Retirada') DEFAULT 'Borrador'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE matriculas MODIFY estado ENUM('Borrador','Pendiente_Pago','Habilitada','Cancelada') DEFAULT 'Borrador'");
    }
};
