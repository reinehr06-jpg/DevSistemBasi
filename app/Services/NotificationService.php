<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public static function send(string $channel, string $message, array $data = []): array
    {
        try {
            return match ($channel) {
                'dashboard' => self::sendToDashboard($message, $data),
                'email' => self::sendEmail($message, $data),
                'webhook' => self::sendWebhook($message, $data),
                default => ['success' => false, 'message' => "Unknown channel: {$channel}"],
            };
        } catch (\Exception $e) {
            Log::error('NotificationService failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function sendToDashboard(string $message, array $data = []): array
    {
        $users = User::all();
        
        $notifications = [];
        foreach ($users as $user) {
            $notification = $user->notifications()->create([
                'type' => $data['type'] ?? 'info',
                'title' => $data['title'] ?? 'Notification',
                'message' => $message,
                'data' => $data,
            ]);
            $notifications[] = $notification->id;
        }

        return ['success' => true, 'notifications' => $notifications];
    }

    public static function sendEmail(string $message, array $data = []): array
    {
        $to = $data['to'] ?? null;
        $subject = $data['subject'] ?? 'Notification';
        
        if (!$to) {
            return ['success' => false, 'message' => 'No recipient specified'];
        }

        // Would use Laravel Mail facade here
        Log::info('Email notification', ['to' => $to, 'subject' => $subject, 'message' => $message]);

        return ['success' => true, 'sent_to' => $to];
    }

    public static function sendWebhook(string $message, array $data = []): array
    {
        $url = $data['url'] ?? null;
        
        if (!$url) {
            return ['success' => false, 'message' => 'No webhook URL specified'];
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $client->post($url, [
                'json' => [
                    'message' => $message,
                    'data' => $data,
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            return ['success' => true, 'sent_to' => $url];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function notifyDeploy(string $systemId, string $status, array $data = []): array
    {
        return self::send('dashboard', "Deploy {$status} for system", [
            'type' => $status === 'success' ? 'success' : 'error',
            'title' => 'Deploy Notification',
            'system_id' => $systemId,
            'status' => $status,
            ...$data,
        ]);
    }

    public static function notifyBackup(string $systemId, string $status, array $data = []): array
    {
        return self::send('dashboard', "Backup {$status} for system", [
            'type' => $status === 'success' ? 'success' : 'error',
            'title' => 'Backup Notification',
            'system_id' => $systemId,
            'status' => $status,
            ...$data,
        ]);
    }

    public static function notifyServerAlert(string $serverId, string $alertType, array $data = []): array
    {
        return self::send('dashboard', "Server alert: {$alertType}", [
            'type' => 'warning',
            'title' => 'Server Alert',
            'server_id' => $serverId,
            'alert_type' => $alertType,
            ...$data,
        ]);
    }
}