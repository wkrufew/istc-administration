<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            $table->foreignId('solicitud_id')
                ->nullable()
                ->after('descripcion')
                ->constrained('solicitudes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            $table->dropForeign(['solicitud_id']);
            $table->dropColumn('solicitud_id');
        });
    }
};
