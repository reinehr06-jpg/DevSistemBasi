<?php

namespace App\Jobs;

use App\Jobs\BackupDatabaseJob;
use App\Models\DevTask;
use App\Models\Notification;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;

class DeployProjectJob implements ShouldQueue
{
    use Queueable;

    public bool $backupBeforeDeploy = true;
    public bool $runMigrations = true;
    public bool $clearCache = true;

    public function __construct(
        public Server $server,
        public string $action = 'full',
        public ?DevTask $task = null
    ) {}

    public function handle(): void
    {
        if ($this->backupBeforeDeploy) {
            $this->runBackup();
        }

        $commands = $this->getCommands();

        foreach ($commands as $index => $command) {
            try {
                $result = Process::run($command);
                $success = $result->successful();

                Notification::create([
                    'type' => $success ? 'deploy' : 'erro',
                    'title' => $success ? 'Deploy Concluído' : 'Erro no Deploy',
                    'message' => $command,
                    'payload' => [
                        'server_id' => $this->server->id,
                        'task_id' => $this->task?->id,
                        'command' => $command,
                        'output' => $result->output(),
                    ],
                ]);

                if (!$success) {
                    $this->handleDeployFailure($command, $result->errorOutput());
                    return;
                }
            } catch (\Exception $e) {
                Notification::create([
                    'type' => 'erro',
                    'title' => 'Erro no Deploy',
                    'message' => $e->getMessage(),
                    'payload' => [
                        'server_id' => $this->server->id,
                        'command' => $command,
                        'error' => $e->getMessage(),
                    ],
                ]);
                return;
            }
        }

        $this->server->update([
            'last_deploy' => now(),
        ]);

        if ($this->task) {
            $this->task->update([
                'status' => 'finalizada',
                'finished_at' => now(),
            ]);
        }
    }

    private function runBackup(): void
    {
        try {
            BackupDatabaseJob::dispatchSync($this->server->system, $this->server);
        } catch (\Exception $e) {
            Notification::create([
                'type' => 'erro',
                'title' => 'Erro no Backup',
                'message' => 'Falha ao fazer backup antes do deploy: ' . $e->getMessage(),
                'payload' => ['server_id' => $this->server->id],
            ]);
        }
    }

    private function handleDeployFailure(string $command, string $error): void
    {
        Notification::create([
            'type' => 'erro',
            'title' => 'Deploy Falhou',
            'message' => 'Deploy falhou no servidor: ' . $this->server->name,
            'payload' => [
                'server_id' => $this->server->id,
                'command' => $command,
                'error' => $error,
            ],
        ]);
    }

    private function getCommands(): array
    {
        $ssh = sprintf(
            'ssh %s@%s',
            $this->server->ssh_user,
            $this->server->ip
        );

        $cd = sprintf(
            'cd %s',
            $this->server->deploy_path
        );

        return match ($this->action) {
            'pull' => [
                "{$ssh} '{$cd} && git pull'",
            ],
            'migrate' => [
                "{$ssh} '{$cd} && php artisan migrate --force'",
            ],
            'restart' => [
                "{$ssh} '{$cd} && php artisan queue:restart'",
            ],
            'cache' => [
                "{$ssh} '{$cd} && php artisan cache:clear && php artisan config:clear'",
            ],
            'full' => [
                "{$ssh} '{$cd} && git pull'",
                "{$ssh} '{$cd} && php artisan migrate --force'",
                "{$ssh} '{$cd} && php artisan cache:clear'",
                "{$ssh} '{$cd} && php artisan queue:restart'",
            ],
            default => [
                "{$ssh} '{$cd} && git pull'",
            ],
        };
    }
}