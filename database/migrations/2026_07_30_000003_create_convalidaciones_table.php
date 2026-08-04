<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convalidaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('carrera_id')
                  ->constrained('carreras')
                  ->onDelete('cascade');

            $table->foreignId('periodo_id')
                  ->constrained('periodos')
                  ->onDelete('cascade');

            // Admin que registra la convalidación
            $table->foreignId('registrado_por')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Matrícula generada al confirmar (null mientras esté en Borrador)
            $table->foreignId('matricula_id')
                  ->nullable()
                  ->constrained('matriculas')
                  ->onDelete('set null');

            $table->string('documento_path')->nullable();
            $table->text('observaciones')->nullable();

            $table->enum('estado', ['Borrador', 'Confirmada'])->default('Borrador');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convalidaciones');
    }
};
