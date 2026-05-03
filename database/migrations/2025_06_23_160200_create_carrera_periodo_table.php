<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
      /**
       * Tabla pivot que vincula una carrera a un período lectivo.
       *
       * Permite que cada carrera tenga su propio período activo con fechas
       * independientes. Si fecha_inicio / fecha_fin son null, se usan las
       * del período base (periodos.*).
       */
      public function up(): void
      {
            Schema::create('carrera_periodo', function (Blueprint $table) {
                  $table->id();

                  $table->foreignId('carrera_id')
                        ->constrained('carreras')
                        ->onDelete('cascade');

                  $table->foreignId('periodo_id')
                        ->constrained('periodos')
                        ->onDelete('cascade');

                  // Fechas propias por carrera — si son null se usan las del período base
                  $table->date('fecha_inicio')->nullable()
                        ->comment('Override de periodos.fecha_inicio para esta carrera');
                  $table->date('fecha_fin')->nullable()
                        ->comment('Override de periodos.fecha_fin para esta carrera');
                  $table->date('fecha_limite_matricula')->nullable()
                        ->comment('Override de periodos.fecha_limite_matricula para esta carrera');
                  $table->date('fecha_limite_pago')->nullable()
                        ->comment('Override de periodos.fecha_limite_pago para esta carrera');

                  // Período activo para esta carrera específica
                  $table->boolean('is_current')->default(false)
                        ->comment('Período activo para esta carrera. Solo uno puede ser true por carrera.');

                  $table->boolean('is_active')->default(true);

                  $table->timestamps();

                  // Una carrera no puede estar dos veces en el mismo período
                  $table->unique(['carrera_id', 'periodo_id'], 'unique_carrera_periodo');
            });
      }

      public function down(): void
      {
            Schema::dropIfExists('carrera_periodo');
      }
};
