<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contadores que nunca retroceden: un código anulado no vuelve a usarse
        Schema::create('secuencias', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 40)->unique();
            $table->unsignedBigInteger('valor')->default(0);
            $table->timestamps();
        });

        // Arranca en el número más alto ya usado (matrículas vigentes y anuladas)
        $codigos = DB::table('matriculas')->where('code', 'like', 'ISTC-MATRICULA-%')->pluck('code')
            ->merge(
                Schema::hasTable('auditoria_anulaciones_matricula')
                    ? DB::table('auditoria_anulaciones_matricula')->where('matricula_code', 'like', 'ISTC-MATRICULA-%')->pluck('matricula_code')
                    : []
            );

        $ultimo = $codigos
            ->map(fn($code) => preg_match('/^ISTC-MATRICULA-(\d+)$/', $code, $m) ? (int) $m[1] : 0)
            ->max() ?? 0;

        DB::table('secuencias')->insert([
            'clave'      => 'matricula',
            'valor'      => $ultimo,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('secuencias');
    }
};
