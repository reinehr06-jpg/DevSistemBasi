<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            $table->string('repository_url')->nullable()->after('icon');
            $table->string('detected_language')->nullable()->after('repository_url');
            $table->string('detected_framework')->nullable()->after('detected_language');
            $table->string('detected_database')->nullable()->after('detected_framework');
            $table->string('detected_version')->nullable()->after('detected_framework');
            $table->boolean('auto_detected')->default(false)->after('detected_version');
        });
    }

    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            $table->dropColumn(['repository_url', 'detected_language', 'detected_framework', 'detected_database', 'detected_version', 'auto_detected']);
        });
    }
};