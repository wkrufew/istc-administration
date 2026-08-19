<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Discapacidad — versión mejorada de los campos eliminados en 2026_08_13
            $table->boolean('tiene_discapacidad')->default(false)->after('observaciones_medicas');
            $table->string('tipo_discapacidad')->nullable()->after('tiene_discapacidad');
            $table->unsignedTinyInteger('porcentaje_discapacidad')->nullable()->after('tipo_discapacidad');
            $table->string('nro_conadis')->nullable()->after('porcentaje_discapacidad');
            $table->string('certificado_discapacidad_path', 2048)->nullable()->after('nro_conadis');

            // Sexo biológico (la ficha SENESCYT lo pide separado de género)
            $table->string('sexo')->nullable()->after('genero');

            // Pueblo o nacionalidad (solo para etnia indígena)
            $table->string('pueblo_nacionalidad')->nullable()->after('etnia');

            // Lugar de nacimiento
            $table->string('provincia_nacimiento')->nullable()->after('fecha_nacimiento');
            $table->string('canton_nacimiento')->nullable()->after('provincia_nacimiento');

            // Residencia
            $table->string('pais_residencia')->nullable()->after('address');
            $table->string('provincia_residencia')->nullable()->after('pais_residencia');
            $table->string('canton_residencia')->nullable()->after('provincia_residencia');

            // Colegio de procedencia
            $table->string('tipo_colegio')->nullable()->after('miembros_hogar');
            $table->string('nombre_colegio')->nullable()->after('tipo_colegio');

            // Datos socioeconómicos
            $table->string('empleo_ingresos')->nullable()->after('ocupacion');
            $table->string('formacion_padre')->nullable()->after('padre');
            $table->string('formacion_madre')->nullable()->after('madre');

            // Contacto de emergencia — parentesco faltaba
            $table->string('parentesco_emergencia')->nullable()->after('contacto_emergencia');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tiene_discapacidad',
                'tipo_discapacidad',
                'porcentaje_discapacidad',
                'nro_conadis',
                'certificado_discapacidad_path',
                'sexo',
                'pueblo_nacionalidad',
                'provincia_nacimiento',
                'canton_nacimiento',
                'pais_residencia',
                'provincia_residencia',
                'canton_residencia',
                'tipo_colegio',
                'nombre_colegio',
                'empleo_ingresos',
                'formacion_padre',
                'formacion_madre',
                'parentesco_emergencia',
            ]);
        });
    }
};
