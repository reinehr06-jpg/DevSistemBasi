<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->onDelete('cascade');
            $table->foreignId('server_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('environment', ['dev', 'staging', 'production']);
            $table->enum('status', [
                'pending', 'running', 'waiting_ia', 
                'approved', 'rejected', 'success', 
                'failed', 'cancelled', 'rollback'
            ])->default('pending');
            $table->string('branch')->default('main');
            $table->string('commit_hash')->nullable();
            $table->string('commit_message')->nullable();
            $table->text('changes')->nullable();
            $table->integer('stage_index')->default(0);
            $table->string('current_stage')->nullable();
            $table->json('stages_result')->nullable();
            $table->text('ia_analysis')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_runs');
    }
};