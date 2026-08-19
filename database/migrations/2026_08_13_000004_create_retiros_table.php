<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha_retiro');
            $table->text('motivo')->nullable();
            $table->boolean('recargo_cobrado')->default(false)
                ->comment('El recargo del 10% sobre costo_carrera se cobra solo una vez por estudiante');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retiros');
    }
};
