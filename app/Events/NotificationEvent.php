<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $type,
        public string $title,
        public string $message,
        public ?int $userId = null,
        public ?int $systemId = null,
        public array $payload = []
    ) {}

    public function createNotification(): Notification
    {
        return Notification::create([
            'user_id' => $this->userId,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'system_id' => $this->systemId,
            'payload' => $this->payload,
            'read' => false,
        ]);
    }
}