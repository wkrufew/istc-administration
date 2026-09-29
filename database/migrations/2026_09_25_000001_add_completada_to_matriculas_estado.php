<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE matriculas MODIFY estado ENUM('Borrador','Pendiente_Pago','Habilitada','Cancelada','Retirada','Completada') DEFAULT 'Borrador'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE matriculas MODIFY estado ENUM('Borrador','Pendiente_Pago','Habilitada','Cancelada','Retirada') DEFAULT 'Borrador'");
        }
    }
};
