<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('certificado_codigo')->nullable()->after('notas_admin');
            $table->foreignId('docente_notificado_id')->nullable()->after('certificado_codigo')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['docente_notificado_id']);
            $table->dropColumn(['certificado_codigo', 'docente_notificado_id']);
        });
    }
};
