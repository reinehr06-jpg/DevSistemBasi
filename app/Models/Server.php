<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    protected $fillable = [
        'system_id',
        'name',
        'ip',
        'ssh_user',
        'ssh_key_path',
        'deploy_path',
        'status',
        'cpu_usage',
        'ram_usage',
        'disk_usage',
        'branch',
        'last_commit',
        'last_deploy',
        'last_backup',
        'project_version',
        'database_name',
        'uptime',
        'php_version',
        'last_deploy_status',
        'deploy_count',
        'failed_deploys',
    ];

    protected $casts = [
        'cpu_usage' => 'float',
        'ram_usage' => 'float',
        'disk_usage' => 'float',
        'last_deploy' => 'datetime',
        'last_backup' => 'datetime',
        'deploy_count' => 'integer',
        'failed_deploys' => 'integer',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(Integration::class, 'system_id', 'system_id');
    }

    public function backupLogs(): HasMany
    {
        return $this->hasMany(BackupLog::class);
    }

    public function getDeploySuccessRateAttribute(): int
    {
        if ($this->deploy_count === 0) return 100;
        return round((($this->deploy_count - $this->failed_deploys) / $this->deploy_count) * 100);
    }
}