<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_anulaciones_matricula', function (Blueprint $table) {
            $table->id();
            $table->string('matricula_code');
            $table->string('matricula_tipo', 50);
            $table->unsignedBigInteger('estudiante_id');
            $table->string('estudiante_nombre');
            $table->string('periodo_code');
            $table->string('carrera_nombre');
            $table->json('detalle');
            $table->unsignedBigInteger('eliminado_por');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_anulaciones_matricula');
    }
};
