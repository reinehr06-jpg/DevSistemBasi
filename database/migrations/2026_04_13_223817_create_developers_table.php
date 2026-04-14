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
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('cargo')->default('Junior');
            $table->integer('experience_years')->default(0);
            $table->json('stack_primary')->nullable();
            $table->json('stack_secondary')->nullable();
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('hours_per_day')->default(8);
            $table->decimal('cost_per_hour', 10, 2)->nullable();
            $table->string('timezone')->default('America/Sao_Paulo');
            $table->enum('work_mode', ['remoto', 'hibrido', 'presencial'])->default('remoto');
            $table->boolean('ai_monitoring')->default(true);
            $table->enum('ai_level', ['basico', 'completo'])->default('basico');
            $table->string('role')->default('developer');
            $table->integer('score')->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->integer('bugs_created')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};
