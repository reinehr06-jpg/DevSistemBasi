<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineEvent extends Model
{
    protected $table = 'server_timeline';

    protected $fillable = [
        'server_id',
        'event_type',
        'title',
        'description',
        'metadata',
        'user_id',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(Server $server, string $eventType, string $title, string $description = null, array $metadata = [], ?int $userId = null): self
    {
        return self::create([
            'server_id' => $server->id,
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'user_id' => $userId ?? auth()->id(),
            'occurred_at' => now(),
        ]);
    }

    public static function getEventTypes(): array
    {
        return [
            'deploy' => 'Deploy',
            'rollback' => 'Rollback',
            'backup' => 'Backup',
            'restore' => 'Restore',
            'restart' => 'Restart',
            'config_change' => 'Config Change',
            'error' => 'Error',
            'alert' => 'Alert',
            'status_change' => 'Status Change',
            'metric_spike' => 'Metric Spike',
            'migrate' => 'Migration',
            'scale' => 'Scale',
        ];
    }
}