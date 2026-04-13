<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    protected $fillable = [
        'name',
        'trigger',
        'conditions',
        'actions',
        'active',
        'priority',
        'last_run_at',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'active' => 'boolean',
        'priority' => 'integer',
        'last_run_at' => 'datetime',
    ];

    public function shouldRun(string $trigger, array $data): bool
    {
        if ($this->trigger !== $trigger) {
            return false;
        }

        if (!$this->active) {
            return false;
        }

        $conditions = $this->conditions ?? [];
        
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $key => $value) {
            if (!isset($data[$key]) || $data[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    public function execute(array $data): array
    {
        $this->update(['last_run_at' => now()]);
        
        $actions = $this->actions ?? [];
        
        if (empty($actions)) {
            return ['success' => true, 'message' => 'No actions to execute'];
        }

        $results = [];
        
        foreach ($actions as $action) {
            $actionType = $action['type'] ?? null;
            $actionConfig = $action['config'] ?? [];
            
            $result = match ($actionType) {
                'deploy' => \App\Services\IntegrationService::deploy(
                    \App\Models\System::find($data['system_id'] ?? null),
                    array_merge($actionConfig, $data)
                ),
                'backup' => \App\Services\IntegrationService::backup(
                    \App\Models\System::find($data['system_id'] ?? null),
                    array_merge($actionConfig, $data)
                ),
                'restart' => \App\Services\IntegrationService::restart(
                    \App\Models\System::find($data['system_id'] ?? null),
                    array_merge($actionConfig, $data)
                ),
                'notify' => \App\Services\NotificationService::send(
                    $actionConfig['channel'] ?? 'dashboard',
                    $actionConfig['message'] ?? 'Workflow executed',
                    $data
                ),
                'http' => \App\Services\IntegrationService::execute(
                    \App\Models\System::find($data['system_id'] ?? null),
                    'http',
                    array_merge($actionConfig, $data)
                ),
                default => ['success' => false, 'message' => "Unknown action: {$actionType}"],
            };

            $results[] = [
                'action' => $actionType,
                'result' => $result,
            ];
        }

        $allSuccess = collect($results)->every(fn ($r) => $r['result']['success'] ?? false);

        return [
            'success' => $allSuccess,
            'workflow' => $this->name,
            'results' => $results,
        ];
    }

    public static function getAvailableTriggers(): array
    {
        return [
            'task_completed' => 'Task Completed',
            'deploy_requested' => 'Deploy Requested',
            'backup_completed' => 'Backup Completed',
            'backup_failed' => 'Backup Failed',
            'deploy_completed' => 'Deploy Completed',
            'deploy_failed' => 'Deploy Failed',
            'server_offline' => 'Server Offline',
            'server_online' => 'Server Online',
            'webhook_received' => 'Webhook Received',
            'git_push' => 'Git Push',
        ];
    }

    public static function getAvailableActions(): array
    {
        return [
            'deploy' => 'Deploy',
            'backup' => 'Backup',
            'restart' => 'Restart',
            'notify' => 'Send Notification',
            'http' => 'HTTP Request',
        ];
    }
}