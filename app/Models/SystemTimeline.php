<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemTimeline extends Model
{
    protected $table = 'system_timeline';

    protected $fillable = [
        'system_id',
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

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(System $system, string $eventType, string $title, string $description = null, array $metadata = [], ?int $userId = null): self
    {
        return self::create([
            'system_id' => $system->id,
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
            'task_created' => 'Task Created',
            'task_completed' => 'Task Completed',
            'bug_created' => 'Bug Created',
            'bug_resolved' => 'Bug Resolved',
            'deploy' => 'Deploy',
            'rollback' => 'Rollback',
            'backup' => 'Backup',
            'integration_added' => 'Integration Added',
            'integration_removed' => 'Integration Removed',
            'settings_changed' => 'Settings Changed',
            'team_member_added' => 'Team Member Added',
            'webhook_triggered' => 'Webhook Triggered',
        ];
    }
}