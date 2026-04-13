<?php

namespace App\Services;

use App\Models\AIFlow;
use App\Models\AIExecution;
use App\Models\System;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIOrchestratorService
{
    protected string $pythonApiUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->pythonApiUrl = config('services.ai_orchestrator.url', 'http://localhost:8000');
        $this->timeout = config('services.ai_orchestrator.timeout', 60);
    }

    public function handleEvent(string $eventType, array $data): ?AIExecution
    {
        $flow = AIFlow::where('trigger', $eventType)
            ->where('is_active', true)
            ->when(isset($data['system_id']), fn($q) => $q->where('system_id', $data['system_id']))
            ->first();

        if (!$flow) {
            Log::info("AIOrchestrator: No active flow found for trigger: {$eventType}");
            return null;
        }

        return $this->runFlow($flow, $data);
    }

    public function runFlow(AIFlow $flow, array $data): AIExecution
    {
        $execution = AIExecution::create([
            'flow_id' => $flow->id,
            'system_id' => $data['system_id'] ?? null,
            'server_id' => $data['server_id'] ?? null,
            'status' => 'pending',
            'input_data' => $data,
        ]);

        try {
            $execution->update(['status' => 'running', 'started_at' => now()]);

            $response = Http::timeout($this->timeout)->post("{$this->pythonApiUrl}/run-flow", [
                'flow_id' => $flow->id,
                'execution_id' => $execution->id,
                'data' => $data,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                $execution->update([
                    'status' => 'completed',
                    'output_data' => $result,
                    'finished_at' => now(),
                ]);

                app(AIResultProcessor::class)->process($result, $execution);
            } else {
                $execution->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                    'finished_at' => now(),
                ]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $execution->update([
                'status' => 'failed',
                'error_message' => 'AI Service unavailable: ' . $e->getMessage(),
                'finished_at' => now(),
            ]);
            Log::error("AIOrchestrator: Connection failed", ['error' => $e->getMessage()]);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $execution->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);
            Log::error("AIOrchestrator: Request failed", ['error' => $e->getMessage()]);
        }

        return $execution;
    }

    public function runFlowManually(AIFlow $flow, array $data): AIExecution
    {
        $data['triggered_by'] = 'manual';
        return $this->runFlow($flow, $data);
    }

    public function testConnection(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->pythonApiUrl}/health");
            
            if ($response->successful()) {
                return ['success' => true, 'message' => 'Connected to AI service', 'data' => $response->json()];
            }
            
            return ['success' => false, 'message' => 'AI service returned error'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Cannot connect to AI service: ' . $e->getMessage()];
        }
    }

    public function getStatus(): array
    {
        $connection = $this->testConnection();
        
        $activeFlows = AIFlow::where('is_active', true)->count();
        $activeAgents = \App\Models\AIAgent::where('is_active', true)->count();
        
        $recentExecutions = AIExecution::orderBy('created_at', 'desc')->limit(10)->get();
        
        return [
            'connected' => $connection['success'],
            'active_flows' => $activeFlows,
            'active_agents' => $activeAgents,
            'recent_executions' => $recentExecutions->map(fn($e) => [
                'id' => $e->id,
                'flow' => $e->flow->name ?? 'N/A',
                'status' => $e->status,
                'created_at' => $e->created_at->diffForHumans(),
            ]),
        ];
    }

    public function cancelExecution(AIExecution $execution): bool
    {
        if ($execution->status !== 'running') {
            return false;
        }

        try {
            Http::timeout(10)->post("{$this->pythonApiUrl}/cancel", [
                'execution_id' => $execution->id,
            ]);
            
            $execution->update([
                'status' => 'failed',
                'error_message' => 'Cancelled by user',
                'finished_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}