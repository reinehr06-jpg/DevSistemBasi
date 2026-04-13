<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIExecution extends Model
{
    protected $fillable = [
        'flow_id',
        'system_id',
        'server_id',
        'status',
        'input_data',
        'output_data',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(AIFlow::class, 'flow_id');
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AIExecutionLog::class, 'execution_id');
    }

    public function getDurationAttribute(): ?int
    {
        if (!$this->started_at || !$this->finished_at) {
            return null;
        }
        return $this->started_at->diffInMilliseconds($this->finished_at);
    }

    public static function getAvailableStatuses(): array
    {
        return [
            'pending' => 'Pendente',
            'running' => 'Executando',
            'completed' => 'Concluído',
            'failed' => 'Falhou',
            'timeout' => 'Timeout',
        ];
    }
}