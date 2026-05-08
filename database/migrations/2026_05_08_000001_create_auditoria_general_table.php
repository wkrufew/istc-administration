<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_general', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type', 100);
            $table->unsignedBigInteger('auditable_id');
            $table->string('evento', 20);
            $table->json('valores_antes')->nullable();
            $table->json('valores_despues')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id'], 'idx_audit_target');
            $table->index('user_id', 'idx_audit_user');
            $table->index('created_at', 'idx_audit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_general');
    }
};
