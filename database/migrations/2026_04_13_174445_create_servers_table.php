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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('ip');
            $table->string('ssh_user');
            $table->string('ssh_key_path')->nullable();
            $table->string('deploy_path');
            $table->string('database_name')->nullable();
            $table->enum('status', ['online', 'offline', 'manutencao'])->default('online');
            $table->float('cpu_usage')->default(0);
            $table->float('ram_usage')->default(0);
            $table->float('disk_usage')->default(0);
            $table->string('branch')->default('main');
            $table->string('last_commit')->nullable();
            $table->timestamp('last_deploy')->nullable();
            $table->timestamp('last_backup')->nullable();
            $table->string('project_version')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
