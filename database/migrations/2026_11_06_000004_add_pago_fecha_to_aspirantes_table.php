<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fecha en que el aspirante pagó la matrícula (la que consta en el comprobante).
 * pago_verificado_at sigue siendo cuándo lo confirmó secretaría.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->date('pago_fecha')->nullable()->after('pago_metodo');
        });
    }

    public function down(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropColumn('pago_fecha');
        });
    }
};
