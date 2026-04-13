<?php

namespace App\Services;

use App\Models\AIExecution;
use App\Models\Bug;
use App\Models\TimelineEvent;
use App\Models\System;
use App\Models\Server;
use Illuminate\Support\Facades\Log;

class AIResultProcessor
{
    public function process(array $result, AIExecution $execution): void
    {
        Log::info("AIResultProcessor: Processing result for execution {$execution->id}");

        if (isset($result['is_bug']) && $result['is_bug'] === true) {
            $this->createBugFromResult($result, $execution);
        }

        if (isset($result['actions']) && is_array($result['actions'])) {
            $this->executeActions($result['actions'], $execution);
        }

        if (isset($result['notifications']) && is_array($result['notifications'])) {
            $this->sendNotifications($result['notifications'], $result);
        }

        if (isset($result['timeline'])) {
            $this->createTimelineEvents($result['timeline'], $execution);
        }

        Log::info("AIResultProcessor: Result processed for execution {$execution->id}");
    }

    protected function createBugFromResult(array $result, AIExecution $execution): void
    {
        $systemId = $result['system_id'] ?? $execution->system_id;
        
        if (!$systemId) {
            Log::warning("AIResultProcessor: Cannot create bug - no system_id");
            return;
        }

        $bug = Bug::create([
            'system_id' => $systemId,
            'title' => $result['title'] ?? 'Bug detectado pela IA',
            'description' => $this->formatBugDescription($result),
            'severity' => $result['severity'] ?? 'medium',
            'status' => 'open',
            'source' => 'ai_orchestrator',
            'ai_execution_id' => $execution->id,
        ]);

        Log::info("AIResultProcessor: Created bug {$bug->id} from AI execution {$execution->id}");

        if ($execution->server) {
            TimelineEvent::log(
                $execution->server,
                'ai_bug_detected',
                'Bug detectado pela IA',
                $result['title'] ?? null,
                ['bug_id' => $bug->id, 'confidence' => $result['confidence_level'] ?? null]
            );
        }

        NotificationService::send(
            'dashboard',
            "🤖 Bug detectado automaticamente: {$bug->title}",
            [
                'type' => 'warning',
                'title' => 'AI Detection',
                'bug_id' => $bug->id,
                'severity' => $bug->severity,
            ]
        );
    }

    protected function formatBugDescription(array $result): string
    {
        $description = $result['description'] ?? '';
        
        if (isset($result['analysis'])) {
            $description .= "\n\n## Análise da IA\n\n";
            $description .= $result['analysis'];
        }
        
        if (isset($result['recommendation'])) {
            $description .= "\n\n## Recomendação\n\n";
            $description .= $result['recommendation'];
        }

        if (isset($result['input_summary'])) {
            $description .= "\n\n## Dados de Entrada\n\n";
            $description .= json_encode($result['input_summary'], JSON_PRETTY_PRINT);
        }

        return $description;
    }

    protected function executeActions(array $actions, AIExecution $execution): void
    {
        foreach ($actions as $action) {
            $type = $action['type'] ?? null;
            $config = $action['config'] ?? [];

            match ($type) {
                'restart' => $this->handleRestart($config, $execution),
                'backup' => $this->handleBackup($config, $execution),
                'rollback' => $this->handleRollback($config, $execution),
                'notify' => $this->handleNotify($config, $execution),
                'scale' => $this->handleScale($config, $execution),
                default => Log::warning("AIResultProcessor: Unknown action type: {$type}"),
            };
        }
    }

    protected function handleRestart(array $config, AIExecution $execution): void
    {
        if ($execution->system) {
            IntegrationService::restart($execution->system, $config);
            Log::info("AIResultProcessor: Restart executed for system {$execution->system_id}");
        }
    }

    protected function handleBackup(array $config, AIExecution $execution): void
    {
        if ($execution->system) {
            IntegrationService::backup($execution->system, $config);
            Log::info("AIResultProcessor: Backup executed for system {$execution->system_id}");
        }
    }

    protected function handleRollback(array $config, AIExecution $execution): void
    {
        Log::info("AIResultProcessor: Rollback requested", $config);
    }

    protected function handleNotify(array $config, AIExecution $execution): void
    {
        $channel = $config['channel'] ?? 'dashboard';
        $message = $config['message'] ?? 'AI action completed';
        
        NotificationService::send($channel, $message, $config);
    }

    protected function handleScale(array $config, AIExecution $execution): void
    {
        Log::info("AIResultProcessor: Scale requested", $config);
    }

    protected function sendNotifications(array $notifications, array $result): void
    {
        foreach ($notifications as $notification) {
            $channel = $notification['channel'] ?? 'dashboard';
            $message = $notification['message'] ?? 'AI processing completed';
            
            NotificationService::send($channel, $message, array_merge($notification, $result));
        }
    }

    protected function createTimelineEvents(array $events, AIExecution $execution): void
    {
        if ($execution->server) {
            foreach ($events as $event) {
                TimelineEvent::log(
                    $execution->server,
                    $event['type'] ?? 'ai_event',
                    $event['title'] ?? 'AI Event',
                    $event['description'] ?? null,
                    $event['metadata'] ?? []
                );
            }
        }
    }
}