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
        Schema::create('asignacion_docentes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('docente_id');
            $table->unsignedBigInteger('materia_id');
            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('paralelo_id');
            $table->foreign('docente_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('materia_id')->references('id')->on('materias')->onDelete('cascade');
            $table->foreign('periodo_id')->references('id')->on('periodos')->onDelete('cascade');
            $table->foreign('paralelo_id')->references('id')->on('paralelos')->onDelete('cascade');

            $table->timestamps();

            // Índice único con nombre personalizado
            $table->unique(
                ['docente_id', 'materia_id', 'periodo_id', 'paralelo_id'],
                'unique_asignacion_docente'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_docentes');
    }
};
