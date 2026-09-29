<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE obligaciones_financieras MODIFY estado ENUM('Pendiente','Parcial','Pagado','Vencido','Invalidado') DEFAULT 'Pendiente'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE obligaciones_financieras MODIFY estado ENUM('Pendiente','Parcial','Pagado','Vencido') DEFAULT 'Pendiente'");
        }
    }
};
