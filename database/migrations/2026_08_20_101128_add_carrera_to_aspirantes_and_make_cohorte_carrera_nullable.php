<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La carrera del aspirante ahora vive directamente en aspirantes
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->foreignId('carrera_id')
                  ->nullable()
                  ->after('cohorte_id')
                  ->constrained('carreras')
                  ->nullOnDelete();
        });

        // La cohorte deja de tener carrera obligatoria
        Schema::table('cohortes', function (Blueprint $table) {
            $table->foreignId('carrera_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropForeign(['carrera_id']);
            $table->dropColumn('carrera_id');
        });

        Schema::table('cohortes', function (Blueprint $table) {
            $table->foreignId('carrera_id')->nullable(false)->change();
        });
    }
};
