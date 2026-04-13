<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\Server;
use App\Models\System;
use Illuminate\Support\Facades\Log;

class AlertService
{
    public static function evaluateMetrics(System $system, array $metrics): array
    {
        $triggered = [];

        $rules = AlertRule::where('system_id', $system->id)
            ->where('enabled', true)
            ->get();

        foreach ($rules as $rule) {
            if ($rule->evaluate($metrics)) {
                $existingAlert = Alert::where('alert_rule_id', $rule->id)
                    ->where('status', 'triggered')
                    ->first();

                if (!$existingAlert) {
                    $alert = self::triggerAlert($rule, $system, null, $metrics);
                    $triggered[] = $alert;
                }
            }
        }

        return $triggered;
    }

    public static function evaluateServerMetrics(Server $server, array $metrics): array
    {
        $triggered = [];

        $rules = AlertRule::where('server_id', $server->id)
            ->where('enabled', true)
            ->get();

        foreach ($rules as $rule) {
            if ($rule->evaluate($metrics)) {
                $existingAlert = Alert::where('alert_rule_id', $rule->id)
                    ->where('status', 'triggered')
                    ->first();

                if (!$existingAlert) {
                    $alert = self::triggerAlert($rule, $server->system, $server, $metrics);
                    $triggered[] = $alert;
                }
            }
        }

        return $triggered;
    }

    public static function triggerAlert(AlertRule $rule, ?System $system, ?Server $server, array $metrics): Alert
    {
        $alert = Alert::create([
            'alert_rule_id' => $rule->id,
            'system_id' => $system?->id,
            'server_id' => $server?->id,
            'title' => $rule->name,
            'message' => self::buildAlertMessage($rule, $metrics),
            'severity' => $rule->severity,
            'status' => 'triggered',
            'metric_snapshot' => $metrics,
            'triggered_at' => now(),
        ]);

        self::executeAlertActions($rule, $alert);

        Log::warning('Alert triggered', [
            'alert' => $alert->id,
            'rule' => $rule->name,
            'system' => $system?->name,
            'server' => $server?->name,
        ]);

        return $alert;
    }

    protected static function buildAlertMessage(AlertRule $rule, array $metrics): string
    {
        $value = $metrics[$rule->metric] ?? 'N/A';
        $threshold = $rule->threshold;
        
        return "Alerta: {$rule->name} - {$rule->metric} está em {$value} (limite: {$threshold})";
    }

    protected static function executeAlertActions(AlertRule $rule, Alert $alert): void
    {
        $actions = $rule->actions ?? [];
        
        foreach ($actions as $action) {
            $type = $action['type'] ?? null;
            $config = $action['config'] ?? [];

            match ($type) {
                'notification' => NotificationService::send(
                    $config['channel'] ?? 'dashboard',
                    $alert->title . ': ' . $alert->message,
                    ['type' => $alert->severity, 'alert_id' => $alert->id]
                ),
                'webhook' => self::sendWebhook($config['url'] ?? '', $alert),
                'auto_restart' => self::handleAutoRestart($alert),
                'auto_backup' => self::handleAutoBackup($alert),
                'ai_flow' => self::triggerAIFlow($alert),
                default => null,
            };
        }
    }

    protected static function triggerAIFlow(Alert $alert): void
    {
        if (!config('services.ai_orchestrator.enabled', false)) {
            return;
        }

        try {
            $data = [
                'system_id' => $alert->system_id,
                'server_id' => $alert->server_id,
                'alert_id' => $alert->id,
                'alert_title' => $alert->title,
                'alert_message' => $alert->message,
                'alert_severity' => $alert->severity,
                'metric_snapshot' => $alert->metric_snapshot,
                'triggered_at' => $alert->triggered_at->toIso8601String(),
            ];

            app(\App\Services\AIOrchestratorService::class)->handleEvent('alert_triggered', $data);
            
            Log::info('AI Flow triggered from alert', ['alert_id' => $alert->id]);
        } catch (\Exception $e) {
            Log::error('Failed to trigger AI flow from alert', ['error' => $e->getMessage()]);
        }
    }

    protected static function sendWebhook(string $url, Alert $alert): void
    {
        if (empty($url)) return;

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $client->post($url, [
                'json' => [
                    'alert' => $alert->toArray(),
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Alert webhook failed', ['error' => $e->getMessage()]);
        }
    }

    protected static function handleAutoRestart(Alert $alert): void
    {
        if ($alert->server && $alert->severity === 'critical') {
            IntegrationService::restart($alert->server->system);
            Log::info('Auto-restart triggered for server', ['server_id' => $alert->server->id]);
        }
    }

    protected static function handleAutoBackup(Alert $alert): void
    {
        if ($alert->severity === 'critical') {
            IntegrationService::backup($alert->system);
            Log::info('Auto-backup triggered for system', ['system_id' => $alert->system->id]);
        }
    }

    public static function resolveStaleAlerts(): int
    {
        $staleAlerts = Alert::where('status', 'triggered')
            ->where('triggered_at', '<', now()->subHours(24))
            ->get();

        foreach ($staleAlerts as $alert) {
            $alert->resolve();
        }

        return $staleAlerts->count();
    }
}