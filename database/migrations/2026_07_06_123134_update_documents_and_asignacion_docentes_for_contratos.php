<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('file_contrato');
            $table->renameColumn('file_otro', 'file_cedula');
        });

        Schema::table('asignacion_docentes', function (Blueprint $table) {
            $table->string('file_contrato')->nullable()->after('paralelo_id');
        });
    }

    public function down(): void
    {
        Schema::table('asignacion_docentes', function (Blueprint $table) {
            $table->dropColumn('file_contrato');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->renameColumn('file_cedula', 'file_otro');
            $table->string('file_contrato')->nullable();
        });
    }
};
