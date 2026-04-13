<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Integration extends Model
{
    protected $fillable = [
        'system_id',
        'type',
        'name',
        'config',
        'active',
        'last_used_at',
    ];

    protected $casts = [
        'config' => 'array',
        'active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function actions()
    {
        return $this->hasMany(IntegrationAction::class);
    }

    public function enabledActions(): array
    {
        return $this->actions()->where('enabled', true)->pluck('action')->toArray();
    }

    public function canPerform(string $action): bool
    {
        return in_array($action, $this->enabledActions());
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}