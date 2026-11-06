<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Último envío de credenciales al portal de admisión (bienvenida o reenvío por secretaría)
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->timestamp('credenciales_enviadas_at')->nullable()->after('estado');
            $table->foreignId('credenciales_enviadas_por')->nullable()->after('credenciales_enviadas_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('credenciales_enviadas_por');
            $table->dropColumn('credenciales_enviadas_at');
        });
    }
};
