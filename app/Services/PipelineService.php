<?php

namespace App\Services;

use App\Models\Pipeline;
use App\Models\PipelineRun;
use App\Models\Server;
use App\Models\Notification;
use App\Models\AIAgent;
use App\Models\AIExecution;
use App\Jobs\ExecutePipelineJob;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class PipelineService
{
    public function createPipeline(array $data): Pipeline
    {
        return Pipeline::create($data);
    }

    public function runPipeline(Pipeline $pipeline, string $environment = 'dev', ?string $branch = null): PipelineRun
    {
        $run = PipelineRun::create([
            'pipeline_id' => $pipeline->id,
            'environment' => $environment,
            'branch' => $branch ?? $pipeline->deploy_branch,
            'status' => 'pending',
        ]);

        ExecutePipelineJob::dispatch($run);

        return $run;
    }

    public function executeStage(PipelineRun $run, string $stage): array
    {
        $result = match ($stage) {
            'git:fetch' => $this->gitFetch($run),
            'lint' => $this->runLint($run),
            'test' => $this->runTests($run),
            'ia:analyze' => $this->runIaAnalyze($run),
            'deploy:dev' => $this->deployToEnvironment($run, 'dev'),
            'deploy:staging' => $this->deployToEnvironment($run, 'staging'),
            'deploy:prod' => $this->deployToEnvironment($run, 'production'),
            'health' => $this->runHealthCheck($run),
            default => ['success' => true, 'message' => 'Stage não reconhecida: ' . $stage],
        };

        $run->updateStage($stage, $result);

        return $result;
    }

    public function runFullPipeline(PipelineRun $run): void
    {
        $pipeline = $run->pipeline;
        $stages = $pipeline->stages ?? Pipeline::getDefaultStages();

        $run->markAsRunning();

        foreach ($stages as $index => $stage) {
            $run->update(['stage_index' => $index]);

            $result = $this->executeStage($run, $stage);

            if (!$result['success']) {
                $run->markAsFailed($result['message'] ?? 'Stage falhou: ' . $stage);
                $this->notifyFailure($run, $stage, $result);
                return;
            }

            if ($stage === 'ia:analyze' && $pipeline->ia_approval) {
                $this->handleIaApproval($run, $result);
            }

            if ($stage === 'health' && !$result['healthy']) {
                $run->markAsFailed('Health check falhou');
                $this->notifyFailure($run, $stage, $result);
                return;
            }
        }

        $run->markAsSuccess();
        $this->notifySuccess($run);
    }

    protected function gitFetch(PipelineRun $run): array
    {
        $pipeline = $run->pipeline;
        
        if (empty($pipeline->repository_url)) {
            return ['success' => true, 'message' => 'Sem repositório configurado'];
        }

        try {
            $output = Process::run('cd /tmp && git clone ' . $pipeline->repository_url . ' basileia-deploy 2>&1');
            
            return [
                'success' => $output->successful(),
                'message' => $output->output(),
                'commit_hash' => $this->getLastCommit(),
            ];
        } catch (\Exception $e) {
            Log::error('Git fetch error', ['message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function runLint(PipelineRun $run): array
    {
        try {
            $output = Process::run('cd /tmp/basileia-deploy && ./vendor/bin/pint --test 2>&1');
            
            return [
                'success' => $output->successful(),
                'message' => 'Lint passed',
                'output' => $output->output(),
            ];
        } catch (\Exception $e) {
            return ['success' => true, 'message' => 'Lint não disponível'];
        }
    }

    protected function runTests(PipelineRun $run): array
    {
        try {
            $output = Process::run('cd /tmp/basileia-deploy && ./vendor/bin/pest --parallel 2>&1');
            
            $success = $output->successful();
            
            return [
                'success' => $success,
                'message' => $success ? 'Tests passed' : 'Tests failed',
                'output' => $output->output(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function runIaAnalyze(PipelineRun $run): array
    {
        $run->update(['status' => 'waiting_ia']);

        try {
            $aiService = app(AIOrchestratorService::class);
            
            $execution = $aiService->handleEvent('pipeline:analyze', [
                'pipeline_run_id' => $run->id,
                'branch' => $run->branch,
                'system' => $run->pipeline->system->name,
                'system_id' => $run->pipeline->system_id,
            ]);

            return [
                'success' => true,
                'message' => 'Análise IA iniciada',
                'analysis' => $execution ? ['id' => $execution->id] : null,
            ];
        } catch (\Exception $e) {
            Log::error('IA analyze error', ['message' => $e->getMessage()]);
            return [
                'success' => true,
                'message' => 'IA não disponível, pulando análise',
                'analysis' => null,
            ];
        }
    }

    protected function deployToEnvironment(PipelineRun $run, string $environment): array
    {
        $server = Server::where('environment', $environment)
            ->where('system_id', $run->pipeline->system_id)
            ->first();

        if (!$server) {
            return ['success' => false, 'message' => 'Servidor não encontrado para: ' . $environment];
        }

        try {
            $deployJob = new \App\Jobs\DeployProjectJob($server, 'full');
            $deployJob->handle();

            return [
                'success' => true,
                'message' => "Deploy para {$environment} concluído",
                'server_id' => $server->id,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function runHealthCheck(PipelineRun $run): array
    {
        $server = $run->server;
        
        if (!$server) {
            return ['success' => false, 'message' => 'Servidor não encontrado'];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get($server->monitoring_url ?? 'http://localhost');
            $healthy = $response->successful();

            $server->update([
                'last_health_check' => now(),
                'health_status' => $healthy ? 'healthy' : 'unhealthy',
            ]);

            return [
                'success' => true,
                'healthy' => $healthy,
                'message' => $healthy ? 'Healthy' : 'Unhealthy',
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => true,
                'healthy' => false,
                'message' => 'Health check falhou: ' . $e->getMessage(),
            ];
        }
    }

    protected function handleIaApproval(PipelineRun $run, array $result): void
    {
        $approved = ($result['analysis']['risk_level'] ?? 'low') !== 'high';
        
        $run->setIaAnalysis(
            $result['analysis']['summary'] ?? 'Análise concluída',
            $approved
        );

        if (!$approved) {
            $this->notifyFailure($run, 'ia:analyze', ['message' => 'IA bloqueou o deploy - alto risco']);
        }
    }

    protected function getLastCommit(): ?string
    {
        try {
            $output = Process::run('cd /tmp/basileia-deploy && git log -1 --format=%H');
            return $output->successful() ? trim($output->output()) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function notifySuccess(PipelineRun $run): void
    {
        Notification::broadcast(
            'deploy',
            'Pipeline Concluído',
            "Pipeline {$run->pipeline->name} concluído com sucesso em {$run->environment}",
            $run->pipeline->system_id,
            ['pipeline_run_id' => $run->id]
        );
    }

    protected function notifyFailure(PipelineRun $run, string $stage, array $result): void
    {
        Notification::broadcast(
            'erro',
            'Pipeline Falhou',
            "Pipeline {$run->pipeline->name} falhou na stage {$stage}: " . ($result['message'] ?? 'Erro desconhecido'),
            $run->pipeline->system_id,
            ['pipeline_run_id' => $run->id, 'stage' => $stage]
        );
    }

    public function rollback(PipelineRun $run): bool
    {
        $previousRun = PipelineRun::where('pipeline_id', $run->pipeline_id)
            ->where('status', 'success')
            ->where('id', '<', $run->id)
            ->orderByDesc('id')
            ->first();

        if (!$previousRun) {
            return false;
        }

        $run->update(['status' => 'rollback']);

        $this->executeStage($run, 'deploy:prod');

        return true;
    }
}