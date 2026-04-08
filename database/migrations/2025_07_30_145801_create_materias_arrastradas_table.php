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
        Schema::create('materias_arrastradas', function (Blueprint $table) {
            $table->id();
            $table->decimal('nota_obtenida', 3, 2);
            $table->decimal('nota_minima_requerida', 3, 2);
            $table->decimal('porcentaje_penalizacion', 5, 2);
            $table->integer('numero_intento')->default(1);
            $table->enum('estado', ['Arrastrada', 'Inscrita', 'Aprobada', 'Perdida_Definitiva'])->default('Arrastrada');
            $table->decimal('costo_adicional', 8, 2)->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained()->onDelete('cascade');
            $table->foreignId('periodo_reprobado_id')->constrained('periodos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias_arrastradas');
    }
};
