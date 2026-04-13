<?php

namespace App\Services;

use App\Models\Workflow;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    public static function run(string $trigger, array $data = []): array
    {
        Log::info("WorkflowEngine: Running trigger '{$trigger}'", $data);

        $workflows = Workflow::where('trigger', $trigger)
            ->where('active', true)
            ->orderBy('priority', 'desc')
            ->get();

        if ($workflows->isEmpty()) {
            Log::info("WorkflowEngine: No workflows found for trigger '{$trigger}'");
            return ['success' => true, 'message' => 'No workflows found for this trigger'];
        }

        $results = [];

        foreach ($workflows as $workflow) {
            if ($workflow->shouldRun($trigger, $data)) {
                try {
                    $result = $workflow->execute($data);
                    $results[] = [
                        'workflow' => $workflow->name,
                        'result' => $result,
                    ];
                } catch (\Exception $e) {
                    Log::error("WorkflowEngine: Workflow '{$workflow->name}' failed", [
                        'error' => $e->getMessage(),
                    ]);
                    $results[] = [
                        'workflow' => $workflow->name,
                        'result' => ['success' => false, 'message' => $e->getMessage()],
                    ];
                }
            }
        }

        $allSuccess = collect($results)->every(fn ($r) => $r['result']['success'] ?? false);

        return [
            'success' => $allSuccess,
            'trigger' => $trigger,
            'workflows_executed' => count($results),
            'results' => $results,
        ];
    }

    public static function runForSystem(string $systemId, string $trigger, array $data = []): array
    {
        $data['system_id'] = $systemId;
        return self::run($trigger, $data);
    }

    public static function onTaskCompleted(int $taskId, string $systemId): array
    {
        return self::run('task_completed', [
            'task_id' => $taskId,
            'system_id' => $systemId,
            'completed_at' => now()->toIso8601String(),
        ]);
    }

    public static function onDeployRequested(string $systemId, array $payload = []): array
    {
        return self::run('deploy_requested', array_merge([
            'system_id' => $systemId,
            'requested_at' => now()->toIso8601String(),
        ], $payload));
    }

    public static function onDeployCompleted(string $systemId, array $payload = []): array
    {
        return self::run('deploy_completed', array_merge([
            'system_id' => $systemId,
            'completed_at' => now()->toIso8601String(),
        ], $payload));
    }

    public static function onBackupCompleted(string $systemId): array
    {
        return self::run('backup_completed', [
            'system_id' => $systemId,
            'completed_at' => now()->toIso8601String(),
        ]);
    }

    public static function onServerOffline(string $serverId): array
    {
        $server = \App\Models\Server::find($serverId);
        
        return self::run('server_offline', [
            'server_id' => $serverId,
            'system_id' => $server?->system_id,
            'ip' => $server?->ip,
            'detected_at' => now()->toIso8601String(),
        ]);
    }

    public static function onWebhookReceived(string $systemId, array $payload): array
    {
        return self::run('webhook_received', [
            'system_id' => $systemId,
            'payload' => $payload,
            'received_at' => now()->toIso8601String(),
        ]);
    }
}