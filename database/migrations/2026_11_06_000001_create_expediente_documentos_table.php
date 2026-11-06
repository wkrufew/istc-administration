<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expediente único de documentos de estudiantes y docentes.
 *
 * Una fila por cada archivo subido: al reemplazar un documento la fila anterior queda con
 * vigente = false (historial). Los contratos se ligan a su asignación docente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 40);                       // ver ExpedienteDocumento::TIPOS
            $table->string('descripcion')->nullable();          // para el tipo "otro"
            $table->foreignId('asignacion_docente_id')->nullable()->constrained('asignacion_docentes')->nullOnDelete();
            $table->string('variante', 20)->nullable();         // contratos: base | firmado

            $table->string('archivo_path')->nullable();         // null en un documento solicitado aún no subido
            $table->string('disco', 20)->default('local');      // public (archivos anteriores) | local (privado)
            $table->string('nombre_original')->nullable();

            $table->string('estado', 20)->default('pendiente'); // pendiente | aprobado | rechazado | solicitado
            $table->text('observacion')->nullable();
            $table->string('origen', 20);                       // admision | secretaria | estudiante | docente
            $table->string('fuente', 40)->nullable();           // de dónde se importó (migración / traspaso)
            $table->boolean('vigente')->default(true);

            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('subido_at')->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tipo', 'vigente']);
            $table->index(['asignacion_docente_id', 'vigente']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_documentos');
    }
};
