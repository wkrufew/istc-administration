<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE obligaciones_financieras MODIFY COLUMN tipo ENUM('MATRICULA','COLEGIATURA','ARRASTRE','MULTA','OTROS','INSCRIPCION','CERTIFICADO','REINTEGRO') NOT NULL");
        }

        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            // Retiro que originó la obligación REINTEGRO
            $table->foreignId('retiro_id')->nullable()->after('matricula_id')
                ->constrained('retiros')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('retiro_id');
        });

        if (DB::getDriverName() !== 'sqlite') {
            // Falla si ya existen obligaciones REINTEGRO: deben reclasificarse antes de revertir
            DB::statement("ALTER TABLE obligaciones_financieras MODIFY COLUMN tipo ENUM('MATRICULA','COLEGIATURA','ARRASTRE','MULTA','OTROS','INSCRIPCION','CERTIFICADO') NOT NULL");
        }
    }
};
