<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_logs', function (Blueprint $table) {
            $table->foreignId('destination_id')->nullable()->constrained('backup_destinations')->onDelete('set null');
            $table->string('destination_type')->nullable();
            $table->enum('upload_status', ['pending', 'uploading', 'success', 'error'])->nullable();
            $table->string('upload_message')->nullable();
            $table->string('s3_key')->nullable();
            $table->string('remote_path_uploaded')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('backup_logs', function (Blueprint $table) {
            $table->dropForeign(['destination_id']);
            $table->dropColumn(['destination_type', 'upload_status', 'upload_message', 's3_key', 'remote_path_uploaded']);
        });
    }
};