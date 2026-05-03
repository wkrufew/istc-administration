<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_docente_id')->constrained('asignacion_docentes')->cascadeOnDelete();
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->enum('tipo', ['examen', 'tarea', 'evaluacion', 'general'])->default('general');
            $table->date('fecha_aviso');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
