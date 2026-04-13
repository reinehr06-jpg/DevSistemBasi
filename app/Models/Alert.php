<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'alert_rule_id',
        'system_id',
        'server_id',
        'title',
        'message',
        'severity',
        'status',
        'metric_snapshot',
        'triggered_at',
        'resolved_at',
    ];

    protected $casts = [
        'metric_snapshot' => 'array',
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AlertRule::class, 'alert_rule_id');
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public static function getAvailableStatuses(): array
    {
        return ['triggered', 'acknowledged', 'resolved', 'ignored'];
    }

    public function resolve(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function acknowledge(): void
    {
        $this->update(['status' => 'acknowledged']);
    }
}