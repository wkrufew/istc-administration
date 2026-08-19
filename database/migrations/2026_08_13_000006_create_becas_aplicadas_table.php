<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('becas_aplicadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tipo_beca_id')->constrained('tipos_beca');
            $table->decimal('porcentaje_aplicado', 5, 2)
                ->comment('Porcentaje efectivo en el momento de asignación (puede diferir del tipo si se personaliza)');

            // Campos extra para beca de Discapacidad
            $table->unsignedTinyInteger('porcentaje_discapacidad')->nullable()
                ->comment('Porcentaje de discapacidad del CONADIS (35-100)');
            $table->string('documento_path', 2048)->nullable()
                ->comment('Ruta del certificado o documento de respaldo');

            $table->text('observacion')->nullable();
            $table->date('fecha_asignacion');
            $table->date('fecha_ultima_revision')->nullable()
                ->comment('Última revisión administrativa; la beca es perpetua hasta revocación');
            $table->boolean('is_active')->default(true);
            $table->foreignId('asignado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('becas_aplicadas');
    }
};
