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
        Schema::table('calificacions', function (Blueprint $table) {
            $table->boolean('es_borrador')->default(false)->after('numero_intento');
        });
    }

    public function down(): void
    {
        Schema::table('calificacions', function (Blueprint $table) {
            $table->dropColumn('es_borrador');
        });
    }
};
