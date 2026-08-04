<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convalidacion_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('convalidacion_id')
                  ->constrained('convalidaciones')
                  ->onDelete('cascade');

            $table->foreignId('materia_id')
                  ->constrained('materias')
                  ->onDelete('cascade');

            $table->float('nota')->nullable();

            $table->enum('estado', ['Aprobado', 'Reprobado'])->default('Aprobado');

            // FK al DetalleMatricula generado al confirmar (null en Borrador)
            $table->foreignId('detalle_matricula_id')
                  ->nullable()
                  ->constrained('detalle_matriculas')
                  ->onDelete('set null');

            $table->unique(['convalidacion_id', 'materia_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convalidacion_detalles');
    }
};
