<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias_arrastradas', function (Blueprint $table) {
            $table->unique(['user_id', 'materia_id'], 'materias_arrastradas_user_materia_unique');
        });
    }

    public function down(): void
    {
        Schema::table('materias_arrastradas', function (Blueprint $table) {
            $table->dropUnique('materias_arrastradas_user_materia_unique');
        });
    }
};
