<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertRule extends Model
{
    protected $fillable = [
        'system_id',
        'server_id',
        'name',
        'metric',
        'condition',
        'threshold',
        'duration_minutes',
        'severity',
        'enabled',
        'actions',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'threshold' => 'float',
        'duration_minutes' => 'integer',
        'actions' => 'array',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public static function getAvailableMetrics(): array
    {
        return [
            'cpu_usage' => 'CPU Usage (%)',
            'ram_usage' => 'RAM Usage (%)',
            'disk_usage' => 'Disk Usage (%)',
            'response_time' => 'Response Time (ms)',
            'error_rate' => 'Error Rate (%)',
            'request_count' => 'Request Count',
            'queue_size' => 'Queue Size',
            'connections' => 'Database Connections',
        ];
    }

    public static function getAvailableConditions(): array
    {
        return ['>', '>=', '<', '<=', '==', '!='];
    }

    public static function getAvailableSeverities(): array
    {
        return ['info', 'warning', 'critical', 'emergency'];
    }

    public function evaluate(array $metrics): bool
    {
        $value = $metrics[$this->metric] ?? null;
        
        if ($value === null) {
            return false;
        }

        return match ($this->condition) {
            '>' => $value > $this->threshold,
            '>=' => $value >= $this->threshold,
            '<' => $value < $this->threshold,
            '<=' => $value <= $this->threshold,
            '==' => $value == $this->threshold,
            '!=' => $value != $this->threshold,
            default => false,
        };
    }
}