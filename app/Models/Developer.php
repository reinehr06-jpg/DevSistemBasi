<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Developer extends Model
{
    protected $fillable = [
        'user_id',
        'cargo',
        'experience_years',
        'stack_primary',
        'stack_secondary',
        'team_id',
        'manager_id',
        'hours_per_day',
        'cost_per_hour',
        'timezone',
        'work_mode',
        'ai_monitoring',
        'ai_level',
        'role',
        'score',
        'tasks_completed',
        'bugs_created',
        'active',
    ];

    protected $casts = [
        'stack_primary' => 'array',
        'stack_secondary' => 'array',
        'ai_monitoring' => 'boolean',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function devTasks(): HasMany
    {
        return $this->hasMany(DevTask::class, 'assigned_to');
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class);
    }
}
