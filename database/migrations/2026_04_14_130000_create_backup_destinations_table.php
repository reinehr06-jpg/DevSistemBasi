<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['s3', 'mac']);
            $table->string('host')->nullable();
            $table->integer('port')->default(22);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('private_key_path')->nullable();
            $table->string('remote_path')->nullable();
            $table->string('bucket')->nullable();
            $table->string('region')->nullable();
            $table->string('access_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->integer('retention_days');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_destinations');
    }
};