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
        Schema::create('detalle_matriculas', function (Blueprint $table) {
            $table->id();

            $table->date('asignacion')->nullable();
            $table->string('code', 45);
            $table->enum('tipo', ['Normal', 'Arrastre', 'Validacion'])->default('Normal');
            $table->enum('estado', ['Inscrito', 'Retirado', 'Finalizado'])->default('Inscrito');
            $table->decimal('costo_materia', 8, 2)->default(0);
            $table->boolean('es_repeticion')->default(false);

            $table->unsignedBigInteger('matricula_id');
            $table->unsignedBigInteger('materia_id');
            $table->unsignedBigInteger('paralelo_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('matricula_id')->references('id')->on('matriculas')->onDelete('cascade');
            $table->foreign('materia_id')->references('id')->on('materias')->onDelete('cascade');
            $table->foreign('paralelo_id')->references('id')->on('paralelos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'materia_id'], 'idx_detalle_user_materia');
            $table->index(['matricula_id', 'estado'], 'idx_detalle_matricula_estado');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_matriculas');
    }
};
