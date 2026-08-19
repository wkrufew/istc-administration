<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohortes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('carrera_id')->constrained('carreras')->cascadeOnDelete();
            $table->date('fecha_inicio_matriculacion')->nullable();
            $table->date('fecha_inicio_clases')->nullable();
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');
            $table->text('descripcion')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohortes');
    }
};
