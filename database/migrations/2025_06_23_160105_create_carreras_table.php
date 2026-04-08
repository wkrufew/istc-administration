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
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('costo_credito', 8, 2)->default(0); // Costo por crédito
            $table->decimal('costo_carrera', 8, 2)->default(0); // Costo por carrera
            $table->integer('duracion_semestres')->default(6);
            $table->enum('modalidad', ['Presencial', 'Virtual', 'Híbrida', 'Semipresencial'])->default('Presencial');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carreras');
    }
};
