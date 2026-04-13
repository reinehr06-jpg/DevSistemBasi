<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->foreignId('dependency_id')->constrained('servers')->onDelete('cascade');
            $table->enum('type', ['git', 'database', 'api', 'service'])->default('git');
            $table->enum('status', ['pending', 'active', 'inactive', 'error'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_dependencies');
    }
};