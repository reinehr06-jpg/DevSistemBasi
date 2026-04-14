<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    protected $fillable = [
        'system_id',
        'server_id',
        'destination_id',
        'status',
        'message',
        'file_path',
        'file_size',
        'destination_type',
        'upload_status',
        'upload_message',
        's3_key',
        'remote_path_uploaded',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(BackupDestination::class);
    }
}