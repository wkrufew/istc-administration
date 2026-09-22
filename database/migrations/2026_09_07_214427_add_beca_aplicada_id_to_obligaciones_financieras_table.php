<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            $table->foreignId('beca_aplicada_id')
                ->nullable()
                ->after('solicitud_id')
                ->constrained('becas_aplicadas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            $table->dropForeign(['beca_aplicada_id']);
            $table->dropColumn('beca_aplicada_id');
        });
    }
};
