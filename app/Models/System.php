<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class System extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function devTasks(): HasMany
    {
        return $this->hasMany(DevTask::class);
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class);
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }
}