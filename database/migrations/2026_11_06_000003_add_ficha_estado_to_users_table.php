<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Revisión de la ficha del estudiante por secretaría.
 * ficha_estado: null (sin revisar) | en_revision | con_observaciones | aprobada.
 * "Incompleta" no se guarda: se calcula por los campos obligatorios que faltan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ficha_estado', 20)->nullable()->after('estado_academico');
            $table->text('ficha_observacion')->nullable()->after('ficha_estado');
            $table->foreignId('ficha_revisada_por')->nullable()->after('ficha_observacion')->constrained('users')->nullOnDelete();
            $table->timestamp('ficha_revisada_at')->nullable()->after('ficha_revisada_por');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ficha_revisada_por');
            $table->dropColumn(['ficha_estado', 'ficha_observacion', 'ficha_revisada_at']);
        });
    }
};
