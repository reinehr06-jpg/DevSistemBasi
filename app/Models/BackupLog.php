<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    protected $fillable = [
        'system_id',
        'server_id',
        'status',
        'message',
        'file_path',
        'file_size',
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
}