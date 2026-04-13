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
        Schema::create('dev_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('documentation')->nullable();
            $table->string('prototype_url')->nullable();
            $table->enum('type', ['front', 'back', 'ia'])->default('front');
            $table->enum('priority', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('status', ['pendente', 'em_andamento', 'finalizada', 'cancelada'])->default('pendente');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dev_tasks');
    }
};
