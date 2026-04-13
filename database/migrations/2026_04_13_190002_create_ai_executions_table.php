<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flow_id')->constrained('ai_flows')->onDelete('cascade');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_executions');
    }
};