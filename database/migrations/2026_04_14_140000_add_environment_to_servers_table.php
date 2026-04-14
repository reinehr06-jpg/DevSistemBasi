<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->enum('environment', ['dev', 'staging', 'production'])->default('production')->after('status');
            $table->string('easypanel_id')->nullable()->after('ip');
            $table->string('monitoring_url')->nullable()->after('deploy_path');
            $table->boolean('auto_deploy')->default(false)->after('monitoring_url');
            $table->integer('deploy_count')->default(0)->after('deploy_count');
            $table->timestamp('last_health_check')->nullable()->after('last_deploy');
            $table->string('health_status')->nullable()->after('last_health_check');
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn([
                'environment',
                'easypanel_id',
                'monitoring_url',
                'auto_deploy',
                'last_health_check',
                'health_status',
            ]);
        });
    }
};