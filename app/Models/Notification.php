<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'system_id',
        'type',
        'title',
        'message',
        'payload',
        'read',
    ];

    protected $casts = [
        'payload' => 'array',
        'read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function markAsRead(): void
    {
        $this->update(['read' => true]);
    }

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public static function notify(
        string $type,
        string $title,
        string $message,
        ?int $userId = null,
        ?int $systemId = null,
        array $payload = []
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'system_id' => $systemId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'payload' => $payload,
            'read' => false,
        ]);
    }

    public static function broadcast(
        string $type,
        string $title,
        string $message,
        ?int $systemId = null,
        array $payload = []
    ): void {
        $users = User::whereHas('role', function ($query) {
            $query->where('slug', '!=', 'viewer');
        })->get();

        foreach ($users as $user) {
            self::notify($type, $title, $message, $user->id, $systemId, $payload);
        }
    }
}