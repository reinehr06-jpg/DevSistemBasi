<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIExecutionLog extends Model
{
    protected $table = 'ai_execution_logs';

    protected $fillable = [
        'execution_id',
        'agent_id',
        'input',
        'output',
        'duration_ms',
        'success',
    ];

    protected $casts = [
        'success' => 'boolean',
        'duration_ms' => 'integer',
    ];

    public function execution(): BelongsTo
    {
        return $this->belongsTo(AIExecution::class, 'execution_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AIAgent::class, 'agent_id');
    }
}