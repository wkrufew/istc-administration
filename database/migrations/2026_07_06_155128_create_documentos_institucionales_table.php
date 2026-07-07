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
        Schema::create('documentos_institucionales', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20)->unique(); // silabo|rubrica|acta|guia|otro
            $table->string('nombre', 100);        // nombre visible (libre para 'otro')
            $table->string('path');               // ruta en storage/public
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_institucionales');
    }
};
