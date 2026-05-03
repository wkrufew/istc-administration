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
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ej: 2025-I, 2025-II
            $table->string('description')->nullable(); // Ej: 2025-I, 2025-II
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_limite_matricula');
            $table->date('fecha_limite_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodos');
    }
};
