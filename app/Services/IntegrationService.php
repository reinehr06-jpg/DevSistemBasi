<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\System;
use InvalidArgumentException;

class IntegrationService
{
    public static function execute(System $system, string $action, array $payload = []): array
    {
        $integrations = Integration::where('system_id', $system->id)
            ->where('active', true)
            ->get();

        if ($integrations->isEmpty()) {
            return ['success' => false, 'message' => 'No active integrations found for this system'];
        }

        $results = [];
        
        foreach ($integrations as $integration) {
            if (!$integration->canPerform($action)) {
                $results[] = [
                    'integration' => $integration->name,
                    'type' => $integration->type,
                    'result' => ['success' => false, 'message' => "Action '{$action}' not enabled for this integration"],
                ];
                continue;
            }

            $driver = DriverFactory::make($integration->type, $integration);
            
            if (!method_exists($driver, $action)) {
                $results[] = [
                    'integration' => $integration->name,
                    'type' => $integration->type,
                    'result' => ['success' => false, 'message' => "Driver does not support action: {$action}"],
                ];
                continue;
            }

            try {
                $result = $driver->$action($payload);
                $results[] = [
                    'integration' => $integration->name,
                    'type' => $integration->type,
                    'result' => $result,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'integration' => $integration->name,
                    'type' => $integration->type,
                    'result' => ['success' => false, 'message' => $e->getMessage()],
                ];
            }
        }

        $allSuccess = collect($results)->every(fn ($r) => $r['result']['success'] ?? false);
        
        return [
            'success' => $allSuccess,
            'message' => $allSuccess ? 'All integrations executed successfully' : 'Some integrations failed',
            'results' => $results,
        ];
    }

    public static function executeOnIntegration(Integration $integration, string $action, array $payload = []): array
    {
        if (!$integration->active) {
            return ['success' => false, 'message' => 'Integration is not active'];
        }

        if (!$integration->canPerform($action)) {
            return ['success' => false, 'message' => "Action '{$action}' is not enabled for this integration"];
        }

        try {
            $driver = DriverFactory::make($integration->type, $integration);
            
            if (!method_exists($driver, $action)) {
                return ['success' => false, 'message' => "Driver does not support action: {$action}"];
            }

            return $driver->$action($payload);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function deploy(System $system, array $payload = []): array
    {
        return self::execute($system, 'deploy', $payload);
    }

    public static function backup(System $system, array $payload = []): array
    {
        return self::execute($system, 'backup', $payload);
    }

    public static function restart(System $system, array $payload = []): array
    {
        return self::execute($system, 'restart', $payload);
    }

    public static function logs(System $system, int $lines = 100): array
    {
        return self::execute($system, 'logs', ['lines' => $lines]);
    }

    public static function status(System $system): array
    {
        return self::execute($system, 'status');
    }

    public static function testConnection(Integration $integration): array
    {
        try {
            $driver = DriverFactory::make($integration->type, $integration);
            
            if (method_exists($driver, 'testConnection')) {
                return $driver->testConnection();
            }
            
            return $driver->status();
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function getAvailableActions(Integration $integration): array
    {
        return DriverFactory::getAvailableActions($integration->type);
    }

    public static function getRequiredConfig(string $type): array
    {
        return DriverFactory::getRequiredConfig($type);
    }
}