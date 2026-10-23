<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rastro de auditoría: cuándo, quién y por qué se desactivó una beca o un convenio
        foreach (['becas_aplicadas', 'convenios_aplicados'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->timestamp('desactivado_at')->nullable()->after('is_active');
                $table->foreignId('desactivado_por')->nullable()->after('desactivado_at')
                    ->constrained('users')->nullOnDelete();
                $table->string('motivo_desactivacion', 40)->nullable()->after('desactivado_por');
                $table->text('detalle_desactivacion')->nullable()->after('motivo_desactivacion');
            });
        }

        // Qué convenio descontó cada obligación (igual que beca_aplicada_id)
        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            $table->foreignId('convenio_aplicado_id')->nullable()->after('beca_aplicada_id')
                ->constrained('convenios_aplicados')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('obligaciones_financieras', function (Blueprint $table) {
            $table->dropConstrainedForeignId('convenio_aplicado_id');
        });

        foreach (['becas_aplicadas', 'convenios_aplicados'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropConstrainedForeignId('desactivado_por');
                $table->dropColumn(['desactivado_at', 'motivo_desactivacion', 'detalle_desactivacion']);
            });
        }
    }
};
