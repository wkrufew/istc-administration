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
        Schema::create('notas_titulacion', function (Blueprint $table) {
            $table->id();
            // Estudiante y carrera
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('carrera_id')
                ->constrained('carreras')
                ->cascadeOnDelete();

            // Referencia a prácticas preprofesionales
            $table->foreignId('practica_id')
                ->nullable()
                ->constrained('practicas_preprofesionales')
                ->nullOnDelete()
                ->comment('Prácticas preprofesionales aprobadas del estudiante');

            // Referencia a prácticas comunitarias
            $table->foreignId('comunitaria_id')
                ->nullable()
                ->constrained('comunitarias')
                ->nullOnDelete()
                ->comment('Prácticas comunitarias aprobadas del estudiante');

            // Promedio de malla (calculado automáticamente)
            $table->decimal('promedio_malla', 4, 2)->nullable()
                ->comment('Promedio de promedios de cada semestre. Se calcula automáticamente al registrar.');

            // Titulación: uno u otro
            $table->enum('tipo_titulacion', [
                'Examen_Complexivo',
                'Proyecto_Investigacion',
            ])->comment('Modalidad de titulación escogida por el estudiante');

            $table->decimal('nota_titulacion', 4, 2)->nullable()
                ->comment('Nota del examen complexivo o proyecto de investigación sobre 10');

            // Prácticas (siempre obligatorio — se toma de practica_id pero se guarda aquí para el cálculo)
            $table->decimal('nota_practicas', 4, 2)->nullable()
                ->comment('Nota de prácticas preprofesionales sobre 10. Copiada de practicas_preprofesionales.');

            // Nota final de egreso
            $table->decimal('nota_final_egreso', 4, 2)->nullable()
                ->comment('(promedio_malla + nota_titulacion + nota_practicas) / 3');

            // Estado y control de intentos
            $table->enum('estado', [
                'Pendiente',   // en proceso, aún no tiene todas las notas
                'Aprobado',    // nota_final_egreso >= 7
                'Reprobado',   // nota_final_egreso < 7
            ])->default('Pendiente');

            $table->unsignedInteger('numero_intento')->default(1)
                ->comment('Número de intento. Cada vez que repite se crea un nuevo registro.');

            // Tribunal / evaluadores (opcional pero útil para el acta)
            $table->string('presidente_tribunal')->nullable();
            $table->string('miembro_tribunal_1')->nullable();
            $table->string('miembro_tribunal_2')->nullable();

            // Fechas clave
            $table->date('fecha_registro')->nullable()
                ->comment('Fecha en que se registró el proceso de titulación');
            $table->date('fecha_evaluacion')->nullable()
                ->comment('Fecha del examen complexivo o sustentación del proyecto');

            // Documentos
            $table->string('documento_titulacion_path', 2048)->nullable()
                ->comment('Proyecto de investigación o acta del examen complexivo');

            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Un estudiante puede tener múltiples intentos en la misma carrera
            // pero el número de intento debe ser único por estudiante + carrera
            $table->unique(['user_id', 'carrera_id', 'numero_intento'], 'unique_intento_titulacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_titulacion');
    }
};
