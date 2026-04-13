<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->onDelete('cascade');
            $table->string('language');
            $table->string('framework')->nullable();
            $table->string('database')->nullable();
            $table->string('php_version')->nullable();
            $table->string('node_version')->nullable();
            $table->json('dependencies')->nullable();
            $table->boolean('auto_deploy')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_profiles');
    }
};