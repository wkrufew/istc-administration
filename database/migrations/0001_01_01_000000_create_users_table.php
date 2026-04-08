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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();

            //$table->string('code')->nullable();//este es nuevo para el codigo del estudiante
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('cedula')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('matricula_numero')->nullable();
            $table->string('padre')->nullable();
            $table->string('madre')->nullable();
            $table->string('tutor')->nullable(); // Tutor legal

            $table->string('nacionalidad')->nullable(); //AUTO IDENTIFICACION ETNICA ESPECIFICAR EN LAS VISTAS
            $table->enum('genero', ['Masculino', 'Femenino', 'Otro'])->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('telefono_emergencia')->nullable();
            $table->string('contacto_emergencia')->nullable();
            $table->string('tipo_sangre')->nullable();
            $table->text('observaciones_medicas')->nullable();

            $table->boolean('discapacidad')->nullable(); // UN RADIO BUTTON - CARGAR CERTIFICADO CON CONDICION
            $table->string('discapacidad_descripcion')->nullable();
            $table->string('certificado_discapacidad_path', 2048)->nullable();
            //DATOS PARA FACTURA
            $table->boolean('is_facturador')->default(true);
            $table->string('fact_nombre')->nullable(); //
            $table->string('fact_documento')->nullable();
            $table->string('fact_correo')->nullable();
            $table->string('fact_direccion')->nullable();
            $table->string('fact_telefono')->nullable();

            //datos por agregar
            $table->string('etnia')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('bono_dh')->nullable();
            $table->boolean('is_beca')->default(false);
            $table->string('beca_motivo')->nullable();
            $table->string('beca_porcentaje')->nullable();
            $table->string('beca_monto')->nullable();
            $table->string('ingresos_hogar')->nullable();
            $table->string('miembros_hogar')->nullable();



            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
