<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->decimal('costo_convalidacion', 8, 2)->nullable()->after('costo_carrera')
                ->comment('Costo arancel semestral para estudiantes con validación de conocimiento');
        });
    }

    public function down(): void
    {
        Schema::table('carreras', function (Blueprint $table) {
            $table->dropColumn('costo_convalidacion');
        });
    }
};
