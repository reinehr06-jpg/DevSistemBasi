<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EasyPanelService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $serverId;

    public function __construct()
    {
        $this->baseUrl = config('services.easypanel.url', env('EASYPANEL_URL'));
        $this->apiKey = config('services.easypanel.key', env('EASYPANEL_API_KEY'));
        $this->serverId = config('services.easypanel.server_id', env('EASYPANEL_SERVER_ID'));
    }

    public function getServerMetrics(string $serverId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/api/servers/{$serverId}/metrics");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('EasyPanel API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->getFallbackMetrics();
        } catch (\Exception $e) {
            Log::error('EasyPanel exception', ['message' => $e->getMessage()]);
            return $this->getFallbackMetrics();
        }
    }

    public function getServerLogs(string $serverId, int $lines = 100): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/api/servers/{$serverId}/logs", [
                'lines' => $lines,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['logs' => [], 'error' => $response->status()];
        } catch (\Exception $e) {
            Log::error('EasyPanel logs error', ['message' => $e->getMessage()]);
            return ['logs' => [], 'error' => $e->getMessage()];
        }
    }

    public function getServerStatus(string $serverId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/api/servers/{$serverId}");

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'unknown', 'error' => $response->status()];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    public function deploy(string $serverId, string $branch = 'main'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/servers/{$serverId}/deploy", [
                'branch' => $branch,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'error' => $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function restartService(string $serverId, string $service = 'php'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/servers/{$serverId}/services/{$service}/restart");

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'error' => $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getAllServers(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/api/servers");

            if ($response->successful()) {
                return $response->json();
            }

            return ['servers' => [], 'error' => $response->status()];
        } catch (\Exception $e) {
            return ['servers' => [], 'error' => $e->getMessage()];
        }
    }

    public function getServices(string $serverId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get("{$this->baseUrl}/api/servers/{$serverId}/services");

            if ($response->successful()) {
                return $response->json();
            }

            return ['services' => [], 'error' => $response->status()];
        } catch (\Exception $e) {
            return ['services' => [], 'error' => $e->getMessage()];
        }
    }

    public function runCommand(string $serverId, string $command): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/api/servers/{$serverId}/exec", [
                'command' => $command,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'output' => '', 'error' => $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'output' => '', 'error' => $e->getMessage()];
        }
    }

    protected function getFallbackMetrics(): array
    {
        return [
            'cpu' => ['usage' => 0, 'cores' => 4],
            'ram' => ['used' => 0, 'total' => 8192, 'usage' => 0],
            'disk' => ['used' => 0, 'total' => 100, 'usage' => 0],
            'uptime' => 0,
            'network' => ['in' => 0, 'out' => 0],
        ];
    }

    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->apiKey);
    }
}