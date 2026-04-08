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
        Schema::create('auditoria_calificacions', function (Blueprint $table) {
            $table->id();
            $table->string('campo_modificado', 100)->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->string('motivo_modificacion', 255)->nullable()->comment('Motivo de la modificación, si aplica');

            $table->unsignedBigInteger('calificacion_id');
            $table->foreign('calificacion_id')->references('id')->on('calificacions')->onDelete('cascade');

            $table->unsignedBigInteger('docente_id');
            $table->foreign('docente_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('fecha_modificacion')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_calificacions');
    }
};
