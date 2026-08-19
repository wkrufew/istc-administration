<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_beca', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->enum('categoria', [
                'Excelencia Academica',
                'Socioeconomica',
                'Discapacidad',
                'Deportista',
                'Artistica',
                'Pueblos y Nacionalidades',
                'Migrante Retornado',
                'Emergente',
            ]);
            $table->decimal('porcentaje_descuento', 5, 2)
                ->comment('Porcentaje de descuento sobre el arancel (0-100)');
            $table->text('descripcion')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_beca');
    }
};
