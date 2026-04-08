<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracion_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('gateway'); // paypal, stripe, etc.
            $table->string('nombre_mostrar');
            $table->json('configuracion'); // claves, tokens, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('es_sandbox')->default(true);
            $table->decimal('comision_porcentaje', 5, 2)->default(0);
            $table->decimal('comision_fija', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_pagos');
    }
};
