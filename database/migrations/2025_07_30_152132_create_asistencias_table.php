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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();

            $table->date('fecha');
            $table->enum('estado', ['Presente', 'Ausente', 'Tardanza', 'Justificado']);
            $table->time('hora_entrada')->nullable();
            $table->text('observaciones')->nullable();

            // Relaciones foráneas
            $table->unsignedBigInteger('detalle_matricula_id');
            $table->unsignedBigInteger('horario_id');
            $table->unsignedBigInteger('docente_id');

            // Índices
            $table->index('fecha', 'idx_fecha_asistencia');
            $table->index('horario_id', 'idx_horario_asistencia');
            $table->index('docente_id', 'idx_docente_asistencia');

            // Clave única para evitar duplicados de asistencia en la misma fecha
            $table->unique(['detalle_matricula_id', 'horario_id', 'fecha'], 'unique_asistencia_fecha');

            // Claves foráneas con restricciones
            $table->foreign('detalle_matricula_id', 'fk_asistencia_detalle')
                ->references('id')
                ->on('detalle_matriculas')
                ->onDelete('cascade');

            $table->foreign('horario_id', 'fk_asistencia_horario')
                ->references('id')
                ->on('horarios')
                ->onDelete('cascade');

            $table->foreign('docente_id', 'fk_asistencia_docente')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
