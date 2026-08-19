<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matriculas', function (Blueprint $table) {
            $table->unsignedTinyInteger('num_cuotas_arancel')->default(1)->after('tipo')
                ->comment('Número de cuotas en que se divide el arancel (para el estado de cuenta)');
        });
    }

    public function down(): void
    {
        Schema::table('matriculas', function (Blueprint $table) {
            $table->dropColumn('num_cuotas_arancel');
        });
    }
};
