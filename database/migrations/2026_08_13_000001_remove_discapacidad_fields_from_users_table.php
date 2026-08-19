<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar archivos físicos antes de borrar los registros del campo
        $paths = DB::table('users')
            ->whereNotNull('certificado_discapacidad_path')
            ->pluck('certificado_discapacidad_path');

        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'discapacidad',
                'discapacidad_descripcion',
                'certificado_discapacidad_path',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('discapacidad')->nullable();
            $table->string('discapacidad_descripcion')->nullable();
            $table->string('certificado_discapacidad_path', 2048)->nullable();
        });
    }
};
