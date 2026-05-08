<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calificacions', function (Blueprint $table) {
            $table->id();

            $table->float('insumo1')->nullable();
            $table->float('insumo2')->nullable();
            $table->float('insumo3')->nullable();
            $table->float('insumo4')->nullable();
            $table->float('insumo5')->nullable();
            $table->float('promedio_insumos')->nullable();
            $table->float('examen_parcial')->nullable();
            $table->float('examen_final')->nullable();
            $table->float('nota_final')->nullable();
            $table->float('nota_suspenso')->nullable();
            $table->enum('estado_final', ['Aprobado', 'Reprobado', 'Retirado', 'Incompleto', 'Suspenso_Pendiente'])->nullable();
            $table->boolean('es_arrastre')->default(false);
            $table->integer('numero_intento')->default(1);
            $table->boolean('es_borrador')->default(false);

            $table->unsignedBigInteger('detalle_matricula_id');
            $table->unsignedBigInteger('docente_id');
            $table->foreign('detalle_matricula_id')->references('id')->on('detalle_matriculas')->onDelete('cascade');
            $table->foreign('docente_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['detalle_matricula_id', 'numero_intento'], 'idx_cali_detalle_intento');
            $table->index('estado_final', 'idx_cali_estado_final');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE calificacions ADD CONSTRAINT chk_notas_rango CHECK (
            (insumo1 IS NULL OR (insumo1 >= 0 AND insumo1 <= 10)) AND
            (insumo2 IS NULL OR (insumo2 >= 0 AND insumo2 <= 10)) AND
            (insumo3 IS NULL OR (insumo3 >= 0 AND insumo3 <= 10)) AND
            (insumo4 IS NULL OR (insumo4 >= 0 AND insumo4 <= 10)) AND
            (insumo5 IS NULL OR (insumo5 >= 0 AND insumo5 <= 10)) AND
            (examen_parcial IS NULL OR (examen_parcial >= 0 AND examen_parcial <= 10)) AND
            (examen_final IS NULL OR (examen_final >= 0 AND examen_final <= 10)) AND
            (nota_final IS NULL OR (nota_final >= 0 AND nota_final <= 10)) AND
            (nota_suspenso IS NULL OR (nota_suspenso >= 0 AND nota_suspenso <= 10))
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificacions');
    }
};
