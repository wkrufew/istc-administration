<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dias_no_lectivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('nombre');
            $table->enum('tipo', ['feriado', 'suspension'])->default('feriado');
            $table->enum('alcance', ['global', 'horario'])->default('global');
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->cascadeOnDelete();
            $table->foreignId('creado_por_id')->constrained('users');
            $table->timestamps();

            $table->index(['periodo_id', 'fecha']);
            $table->index(['horario_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dias_no_lectivos');
    }
};
