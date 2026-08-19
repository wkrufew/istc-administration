<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cohorte_id')->constrained('cohortes')->cascadeOnDelete();

            $table->enum('estado', [
                'pendiente',
                'proceso',
                'verificacion',
                'aprobado',
                'rechazado',
                'matriculado',
            ])->default('pendiente');

            $table->text('motivo_rechazo')->nullable();
            $table->text('observacion_general')->nullable();

            // ── Documento: Cédula ──────────────────────────────────────────
            $table->string('cedula_path', 2048)->nullable();
            $table->enum('cedula_estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('cedula_observacion')->nullable();

            // ── Documento: Título de bachiller ────────────────────────────
            $table->string('bachiller_path', 2048)->nullable();
            $table->enum('bachiller_estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('bachiller_observacion')->nullable();

            // ── Documento: Habilitante (exonera al bachiller) ─────────────
            // Para estudiantes de colegio que aún no tienen título de bachiller
            $table->string('habilitante_path', 2048)->nullable();
            $table->enum('habilitante_estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('habilitante_observacion')->nullable();

            // ── Pago de matrícula ─────────────────────────────────────────
            $table->decimal('pago_monto', 8, 2)->nullable();
            $table->string('pago_comprobante_path', 2048)->nullable();
            $table->enum('pago_estado', ['pendiente', 'verificado', 'rechazado'])->default('pendiente');
            $table->text('pago_observacion')->nullable();

            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aspirantes');
    }
};
