<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convenios_aplicados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tipo_convenio_id')->constrained('tipos_convenio');
            $table->foreignId('periodo_id')->nullable()->constrained('periodos')->nullOnDelete()
                ->comment('Periodo desde el que aplica; null = aplica desde la fecha de inicio sin restricción de periodo');
            $table->decimal('porcentaje_aplicado', 5, 2)
                ->comment('Porcentaje efectivo negociado, puede diferir del porcentaje_defecto del tipo');
            $table->text('motivo')->nullable()
                ->comment('Descripción del acuerdo o razón del convenio');
            $table->string('documento_path', 2048)->nullable()
                ->comment('Ruta del documento de respaldo (resolución, acuerdo firmado, etc.)');
            $table->text('observacion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable()
                ->comment('Null = vigente hasta revocación manual. Para tipo_alcance semestral se auto-vence al finalizar el periodo.');
            $table->boolean('is_active')->default(true);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convenios_aplicados');
    }
};
