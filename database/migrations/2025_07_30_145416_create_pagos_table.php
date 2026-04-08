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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            /* $table->string('numero_comprobante')->unique(); //numero de comprobante de pago //BORRAR LO UNICO
            $table->string('codigo_referencia')->nullable(); //dato pasarela de pago
            $table->decimal('monto', 10, 2);
            $table->enum('concepto', ['MATRICULA', 'MATERIA', 'SUPLETORIO', 'CERTIFICADO', 'OTROS'])->default('MATERIA');
            $table->enum('tipo_pago', ['Matricula', 'Arrastre', 'Semestral', 'Mensualidad', 'Certificado', 'Otros'])->default('Matricula'); //CUOTA SEMESTRAL
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia', 'Deposito', 'Payphone'])->default('Transferencia');
            $table->enum('estado', ['Pendiente', 'Procesando', 'Aprobado', 'Rechazado', 'Reembolsado'])->default('Pendiente');
            $table->datetime('fecha_pago');
            $table->datetime('fecha_vencimiento')->nullable();
            $table->integer('numero_cuota')->nullable();
            $table->text('descripcion')->nullable();
            $table->json('datos_gateway')->nullable(); // Para datos del gateway de pago
            $table->string('comprobante_path')->nullable(); //para la foto del comprobante
            $table->foreignId('matricula_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); */
            //$table->foreignId('obligacion_id')->constrained('obligaciones_financieras')->onDelete('cascade');

            $table->string('numero_comprobante')->unique();
            $table->string('codigo_referencia')->nullable();

            $table->foreignId('obligacion_id')
                ->constrained('obligaciones_financieras')
                ->cascadeOnDelete();

            $table->decimal('monto', 10, 2);

            $table->enum('metodo_pago', [
                'Efectivo',
                'Tarjeta',
                'Transferencia',
                'Deposito',
                'Payphone'
            ]);

            $table->enum('estado', [
                'Pendiente',
                'Procesando',
                'Aprobado',
                'Rechazado',
                'Reembolsado'
            ])->default('Pendiente');

            $table->integer('numero_cuota')->nullable();

            $table->dateTime('fecha_pago');

            $table->json('datos_gateway')->nullable();
            $table->string('comprobante_path')->nullable();
            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
