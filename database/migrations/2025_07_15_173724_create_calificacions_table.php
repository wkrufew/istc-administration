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
        Schema::create('calificacions', function (Blueprint $table) {
            $table->id();

            $table->float('insumo1')->nullable();
            $table->float('insumo2')->nullable();
            $table->float('insumo3')->nullable();
            $table->float('insumo4')->nullable();
            $table->float('insumo5')->nullable();
            $table->float('promedio_insumos')->nullable();
            $table->float('examen_parcial')->nullable();
            $table->float('examen_final')->nullable();
            $table->float('nota_final')->nullable();
            $table->float('nota_suspenso')->nullable();
            $table->enum('estado_final', ['Aprobado', 'Reprobado', 'Retirado', 'Incompleto', 'Suspenso_Pendiente'])->nullable();
            $table->boolean('es_arrastre')->default(false);
            $table->integer('numero_intento')->default(1);

            $table->unsignedBigInteger('detalle_matricula_id');
            $table->unsignedBigInteger('docente_id');
            $table->foreign('detalle_matricula_id')->references('id')->on('detalle_matriculas')->onDelete('cascade');
            $table->foreign('docente_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificacions');
    }
};
