<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->string('pago_metodo', 30)->nullable()->after('pago_monto');
            $table->timestamp('pago_verificado_at')->nullable()->after('pago_metodo');
            // Pago generado en matriculación con el abono de admisión: evita aplicarlo dos veces
            $table->foreignId('pago_aplicado_id')->nullable()->after('pago_verificado_at')
                ->constrained('pagos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pago_aplicado_id');
            $table->dropColumn(['pago_metodo', 'pago_verificado_at']);
        });
    }
};
