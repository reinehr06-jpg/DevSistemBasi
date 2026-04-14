<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BackupDestination extends Model
{
    protected $fillable = [
        'name',
        'type',
        'host',
        'port',
        'username',
        'password',
        'private_key_path',
        'remote_path',
        'bucket',
        'region',
        'access_key',
        'secret_key',
        'retention_days',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'port' => 'integer',
        'retention_days' => 'integer',
    ];

    public function backupLogs(): HasMany
    {
        return $this->hasMany(BackupLog::class);
    }
}