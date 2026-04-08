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
        Schema::create('semestres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Ej: 2025-I, 2025-II
            $table->integer('order')->nullable()->comment('Orden del semestre: 1, 2, 3...');
            $table->decimal('creditos_minimos')->default(15);
            $table->decimal('creditos_maximos')->default(25);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('carrera_id');
            $table->foreign('carrera_id')->references('id')->on('carreras')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semestres');
    }
};
