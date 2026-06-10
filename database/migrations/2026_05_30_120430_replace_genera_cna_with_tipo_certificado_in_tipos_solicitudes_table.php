<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipos_solicitudes', function (Blueprint $table) {
            $table->string('tipo_certificado')->nullable()->after('notifica_docente');
        });

        // Migrar datos existentes
        DB::table('tipos_solicitudes')
            ->where('genera_cna', true)
            ->update(['tipo_certificado' => 'cna']);

        Schema::table('tipos_solicitudes', function (Blueprint $table) {
            $table->dropColumn('genera_cna');
        });
    }

    public function down(): void
    {
        Schema::table('tipos_solicitudes', function (Blueprint $table) {
            $table->boolean('genera_cna')->default(false)->after('notifica_docente');
        });

        DB::table('tipos_solicitudes')
            ->where('tipo_certificado', 'cna')
            ->update(['genera_cna' => true]);

        Schema::table('tipos_solicitudes', function (Blueprint $table) {
            $table->dropColumn('tipo_certificado');
        });
    }
};
