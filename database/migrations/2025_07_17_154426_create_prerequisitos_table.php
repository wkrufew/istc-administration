<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prerequisitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('prerequisito_id')->constrained('materias')->onDelete('cascade');

            $table->boolean('es_obligatorio')->default(true);

            $table->timestamps();

            // Evitar duplicados
            $table->unique(['materia_id', 'prerequisito_id'], 'unique_prerequisito');
        });
        // Agregar restricción CHECK (solo funciona si tu base usa InnoDB con soporte CHECK)
        DB::statement('ALTER TABLE prerequisitos ADD CONSTRAINT chk_no_self_prerequisito CHECK (materia_id != prerequisito_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prerequisitos');
    }
};
