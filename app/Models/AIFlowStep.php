<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIFlowStep extends Model
{
    protected $table = 'ai_flow_steps';

    protected $fillable = [
        'flow_id',
        'agent_id',
        'step_order',
        'condition',
        'is_optional',
        'on_error',
    ];

    protected $casts = [
        'condition' => 'array',
        'is_optional' => 'boolean',
    ];

    public function flow(): BelongsTo
    {
        return $this->belongsTo(AIFlow::class, 'flow_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AIAgent::class, 'agent_id');
    }

    public function evaluateCondition(array $context): bool
    {
        $condition = $this->condition;
        
        if (empty($condition)) {
            return true;
        }

        foreach ($condition as $key => $value) {
            if (!isset($context[$key]) || $context[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}