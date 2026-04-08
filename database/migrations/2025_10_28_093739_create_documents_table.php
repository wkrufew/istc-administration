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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('file_curriculum')->nullable(); //hoja de vida
            $table->string('file_senescyt')->nullable(); //titulo senescyt
            $table->string('file_contrato')->nullable(); //hoja de vida
            $table->string('file_otro')->nullable(); //otro archivo

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); //cascade si es que se elimina al usuario se elimine el archivo

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
