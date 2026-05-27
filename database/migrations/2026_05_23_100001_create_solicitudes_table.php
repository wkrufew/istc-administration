<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tipo_solicitud_id')->constrained('tipos_solicitudes');
            $table->text('descripcion');
            $table->enum('estado', [
                'pendiente',
                'aprobada',
                'rechazada',
                'pendiente_pago',
                'pagada',
                'en_proceso',
                'entregada',
                'cancelada',
            ])->default('pendiente');
            $table->decimal('precio_aplicado', 8, 2)->default(0.00);
            $table->text('notas_admin')->nullable();
            $table->foreignId('procesado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
