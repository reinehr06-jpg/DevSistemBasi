<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Environment extends Model
{
    protected $fillable = [
        'system_id',
        'name',
        'slug',
        'type',
        'color',
        'is_default',
        'description',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    public static function getAvailableTypes(): array
    {
        return [
            'development' => 'Development',
            'staging' => 'Staging',
            'production' => 'Production',
            'testing' => 'Testing',
        ];
    }

    public static function getAvailableColors(): array
    {
        return [
            'development' => '#3b82f6',
            'staging' => '#f59e0b',
            'production' => '#ef4444',
            'testing' => '#8b5cf6',
        ];
    }
}