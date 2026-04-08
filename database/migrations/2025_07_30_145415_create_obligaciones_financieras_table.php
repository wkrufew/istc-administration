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
        Schema::create('obligaciones_financieras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('matricula_id')->nullable()->constrained()->cascadeOnDelete();

            $table->enum('tipo', [
                'MATRICULA',
                'COLEGIATURA',
                'ARRASTRE',
                'MULTA',
                'OTROS'
            ]);

            // 🔹 Monto base
            $table->decimal('monto_original', 10, 2);

            // 🔹 Descuento aplicado
            $table->decimal('descuento', 10, 2)->default(0);

            // 🔹 Monto final real a pagar
            $table->decimal('monto_final', 10, 2);

            $table->enum('estado', [
                'Pendiente',
                'Parcial',
                'Pagado',
                'Vencido'
            ])->default('Pendiente');

            $table->date('fecha_vencimiento')->nullable();
            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obligaciones_financieras');
    }
};
