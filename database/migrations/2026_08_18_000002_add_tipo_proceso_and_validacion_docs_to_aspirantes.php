<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            if (! Schema::hasColumn('aspirantes', 'tipo_proceso')) {
                $table->enum('tipo_proceso', ['regular', 'validacion_conocimientos'])
                      ->default('regular')
                      ->after('cohorte_id');
            }

            if (! Schema::hasColumn('aspirantes', 'hoja_vida_path')) {
                $table->string('hoja_vida_path', 500)->nullable()->after('habilitante_observacion');
            }
            if (! Schema::hasColumn('aspirantes', 'hoja_vida_estado')) {
                $table->enum('hoja_vida_estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente')->after('hoja_vida_path');
            }
            if (! Schema::hasColumn('aspirantes', 'hoja_vida_observacion')) {
                $table->text('hoja_vida_observacion')->nullable()->after('hoja_vida_estado');
            }

            if (! Schema::hasColumn('aspirantes', 'cert_laborales_path')) {
                $table->string('cert_laborales_path', 500)->nullable()->after('hoja_vida_observacion');
            }
            if (! Schema::hasColumn('aspirantes', 'cert_laborales_estado')) {
                $table->enum('cert_laborales_estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente')->after('cert_laborales_path');
            }
            if (! Schema::hasColumn('aspirantes', 'cert_laborales_observacion')) {
                $table->text('cert_laborales_observacion')->nullable()->after('cert_laborales_estado');
            }

            if (! Schema::hasColumn('aspirantes', 'cert_cursos_path')) {
                $table->string('cert_cursos_path', 500)->nullable()->after('cert_laborales_observacion');
            }
            if (! Schema::hasColumn('aspirantes', 'cert_cursos_estado')) {
                $table->enum('cert_cursos_estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente')->after('cert_cursos_path');
            }
            if (! Schema::hasColumn('aspirantes', 'cert_cursos_observacion')) {
                $table->text('cert_cursos_observacion')->nullable()->after('cert_cursos_estado');
            }

            if (! Schema::hasColumn('aspirantes', 'mecanizado_iess_path')) {
                $table->string('mecanizado_iess_path', 500)->nullable()->after('cert_cursos_observacion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aspirantes', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_proceso',
                'hoja_vida_path', 'hoja_vida_estado', 'hoja_vida_observacion',
                'cert_laborales_path', 'cert_laborales_estado', 'cert_laborales_observacion',
                'cert_cursos_path', 'cert_cursos_estado', 'cert_cursos_observacion',
                'mecanizado_iess_path',
            ]);
        });
    }
};
