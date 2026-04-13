<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bug extends Model
{
    protected $fillable = [
        'system_id',
        'user_id',
        'title',
        'description',
        'image_path',
        'severity',
        'status',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}