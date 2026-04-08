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
        Schema::create('comunitarias', function (Blueprint $table) {
            $table->id();
            // Estudiante
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('carrera_id')
                ->constrained('carreras')
                ->cascadeOnDelete();

            // Programa de vinculacion
            $table->string('programa_vinculacion');
            // Datos de la empresa / institución
            $table->string('empresa');
            $table->string('sector')->nullable()
                ->comment('Sector: Público, Privado, ONG, etc.');
            $table->string('direccion_empresa')->nullable();
            $table->string('tutor_empresa')
                ->comment('Nombre del tutor asignado en la empresa');
            $table->string('cargo_tutor_empresa')->nullable()
                ->comment('Cargo del tutor dentro de la empresa');
            $table->string('telefono_empresa')->nullable();
            $table->string('email_empresa')->nullable();

            // Datos de la práctica
            $table->string('cargo_estudiante')
                ->comment('Cargo o rol que desempeñó el estudiante');
            $table->text('actividades_realizadas')->nullable()
                ->comment('Descripción de actividades realizadas');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->unsignedInteger('total_horas')->default(0)
                ->comment('Total de horas de práctica completadas');

            // Nota y estado
            $table->decimal('nota', 4, 2)->nullable()
                ->comment('Nota final de prácticas sobre 10');
            $table->enum('estado', [
                'En_Curso',
                'Completada',
                'Reprobada',
            ])->default('En_Curso');

            // Documentos
            $table->string('carta_aceptacion_path', 2048)->nullable()
                ->comment('Carta de aceptación de la empresa');
            $table->string('informe_final_path', 2048)->nullable()
                ->comment('Informe final del estudiante');
            $table->string('certificado_empresa_path', 2048)->nullable()
                ->comment('Certificado emitido por la empresa');

            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunitarias');
    }
};
