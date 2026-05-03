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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index()->comment('instituto|whatsapp|smtp|documentos|notificaciones');
            $table->string('key', 100)->unique()->comment('Clave única p.e.: instituto.nombre_largo');
            $table->text('value')->nullable();
            $table->string('type', 30)->default('text')->comment('text|textarea|password|boolean|image|email|select');
            $table->string('label', 150);
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
