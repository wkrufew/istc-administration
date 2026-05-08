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
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();

            /* $table->date('fecha_matricula');
            $table->string('code', 45);
            $table->enum('tipo', ['Nueva', 'Renovacion', 'Arrastre'])->default('Nueva');
            $table->enum('status', ['Borrador', 'Pendiente_Pago', 'Pagada', 'Vencida', 'Cancelada'])->default('Borrador'); //Pagada Parcialmente
            $table->decimal('total_creditos', 8, 2)->default(0);
            $table->decimal('costo_total', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total_pagar', 10, 2)->default(0);
            $table->text('observaciones')->nullable();

            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('periodo_id')->references('id')->on('periodos')->onDelete('cascade');
            $table->foreign('carrera_id')->references('id')->on('carreras')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); */

            $table->string('code', 45)->unique(); // Código único por semestre
            $table->date('fecha_matricula');

            $table->enum('tipo', ['Nueva', 'Renovacion', 'Arrastre'])->default('Nueva');

            $table->enum('estado', [
                'Borrador',
                'Pendiente_Pago',
                'Habilitada',     // Puede asistir y tener notas
                'Cancelada'
            ])->default('Borrador');

            $table->text('observaciones')->nullable();

            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('carrera_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('periodo_id')->references('id')->on('periodos')->onDelete('cascade');
            $table->foreign('carrera_id')->references('id')->on('carreras')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'periodo_id'], 'idx_matriculas_user_periodo');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
