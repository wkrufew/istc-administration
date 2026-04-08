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
        Schema::create('materia_periodo_paralelo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->constrained()->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('paralelo_id')->constrained()->cascadeOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['materia_id', 'periodo_id', 'paralelo_id'], 'mpp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materia_periodo_paralelo');
    }
};
