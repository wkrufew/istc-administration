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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();

            //$table->string('dia', 45);
            $table->enum('dia_semana', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);
            $table->string('aula', 45)->nullable()->comment('Aula o salón donde se imparte la clase');
            //modalidad_clase
            $table->enum('modalidad_clase', ['Presencial', 'Virtual', 'Híbrida', 'Semipresencial'])->default('Presencial');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('materia_id');
            $table->unsignedBigInteger('paralelo_id');
            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('asignacion_docente_id');
            $table->foreign('materia_id')->references('id')->on('materias')->onDelete('cascade');
            $table->foreign('paralelo_id')->references('id')->on('paralelos')->onDelete('cascade');
            $table->foreign('periodo_id')->references('id')->on('periodos')->onDelete('cascade');
            $table->foreign('asignacion_docente_id')->references('id')->on('asignacion_docentes')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
