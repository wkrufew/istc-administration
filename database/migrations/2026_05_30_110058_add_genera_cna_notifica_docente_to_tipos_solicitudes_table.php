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
        Schema::table('tipos_solicitudes', function (Blueprint $table) {
            $table->boolean('genera_cna')->default(false)->after('requiere_documento');
            $table->boolean('notifica_docente')->default(false)->after('genera_cna');
        });
    }

    public function down(): void
    {
        Schema::table('tipos_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['genera_cna', 'notifica_docente']);
        });
    }
};
