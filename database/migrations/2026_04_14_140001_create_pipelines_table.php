<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('stages')->nullable();
            $table->boolean('auto_deploy')->default(false);
            $table->boolean('ia_approval')->default(true);
            $table->string('repository_url')->nullable();
            $table->string('deploy_branch')->default('main');
            $table->string('ia_agent')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipelines');
    }
};