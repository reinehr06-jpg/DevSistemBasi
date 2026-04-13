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
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->onDelete('cascade');
            $table->foreignId('server_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['success', 'error', 'running'])->default('running');
            $table->string('message')->nullable();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
