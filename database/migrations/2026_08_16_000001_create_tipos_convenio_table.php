<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_convenio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('porcentaje_defecto', 5, 2)
                ->comment('Porcentaje de descuento por defecto (0-100). Puede sobreescribirse al asignar al estudiante.');
            $table->enum('tipo_alcance', ['anual', 'semestral'])
                ->default('anual')
                ->comment('anual = aplica en todos los semestres mientras esté vigente; semestral = solo el semestre actual del periodo asignado');
            $table->boolean('requiere_documento')->default(false)
                ->comment('Si es true, debe subirse un documento de respaldo al asignar el convenio al estudiante');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_convenio');
    }
};
