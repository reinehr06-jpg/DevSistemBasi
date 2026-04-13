<?php

namespace App\Services\Drivers;

use App\Models\Integration;

class SSHDriver extends IntegrationDriver
{
    protected string $host;
    protected string $user;
    protected ?string $keyPath;
    protected ?string $password;
    protected string $deployPath;

    public function __construct(Integration $integration)
    {
        parent::__construct($integration);
        $this->host = $this->config['ip'] ?? $this->config['host'] ?? '';
        $this->user = $this->config['ssh_user'] ?? 'root';
        $this->keyPath = $this->config['ssh_key_path'] ?? null;
        $this->password = $this->config['ssh_password'] ?? null;
        $this->deployPath = $this->config['deploy_path'] ?? '/var/www/html';
    }

    protected function getConnection(): \phpseclib3\Net_SSH2
    {
        $ssh = new \phpseclib3\Net_SSH2($this->host);
        
        if ($this->keyPath && file_exists($this->keyPath)) {
            $keyObj = \phpseclib3\Crypt\PublicKeyLoader::load(file_get_contents($this->keyPath));
            $ssh->authenticate($this->user, $keyObj);
        } elseif ($this->password) {
            $ssh->authenticate($this->user, $this->password);
        } else {
            throw new \Exception('No authentication method configured');
        }

        if (!$ssh->isConnected()) {
            throw new \Exception('Failed to connect via SSH');
        }

        return $ssh;
    }

    public function deploy(array $payload = []): array
    {
        try {
            $ssh = $this->getConnection();
            
            $gitUrl = $payload['git_url'] ?? $this->config['git_url'] ?? null;
            $branch = $payload['branch'] ?? 'main';
            
            if (!$gitUrl) {
                return $this->error('Git URL not configured');
            }

            $commands = [
                "cd {$this->deployPath}",
                "git pull {$gitUrl} {$branch}",
                "composer install --no-dev --optimize-autoloader",
                "php artisan migrate --force",
                "php artisan config:cache",
                "php artisan route:cache",
            ];

            $output = [];
            foreach ($commands as $cmd) {
                $result = $ssh->exec($cmd);
                $output[] = $cmd . ' -> ' . trim($result);
            }

            $ssh->disconnect();

            return $this->success('Deploy completed successfully', ['commands' => $output]);
        } catch (\Exception $e) {
            return $this->error('Deploy failed: ' . $e->getMessage());
        }
    }

    public function backup(array $payload = []): array
    {
        try {
            $ssh = $this->getConnection();
            
            $dbName = $payload['database'] ?? $this->config['database_name'] ?? null;
            $backupPath = $payload['path'] ?? '/backups';
            
            if (!$dbName) {
                return $this->error('Database name not configured');
            }

            $timestamp = date('Y-m-d_His');
            $filename = "backup_{$timestamp}.sql";
            
            $commands = [
                "mkdir -p {$backupPath}",
                "mysqldump -u {$this->config['db_user'] ?? 'root'} -p'{$this->config['db_password']}' {$dbName} > {$backupPath}/{$filename}",
                "gzip {$backupPath}/{$filename}",
            ];

            $output = [];
            foreach ($commands as $cmd) {
                $ssh->exec($cmd);
            }

            $ssh->exec("ls -lh {$backupPath}/*.sql.gz | tail -1");
            $result = $ssh->getLastOutput();

            $ssh->disconnect();

            return $this->success('Backup completed', ['file' => $result]);
        } catch (\Exception $e) {
            return $this->error('Backup failed: ' . $e->getMessage());
        }
    }

    public function restart(array $payload = []): array
    {
        try {
            $ssh = $this->getConnection();
            
            $service = $payload['service'] ?? $this->config['service'] ?? 'php-fpm';
            
            $commands = [
                "systemctl restart {$service}",
                "systemctl status {$service} --no-pager",
            ];

            $output = [];
            foreach ($commands as $cmd) {
                $output[] = $ssh->exec($cmd);
            }

            $ssh->disconnect();

            return $this->success('Service restarted', ['output' => implode("\n", $output)]);
        } catch (\Exception $e) {
            return $this->error('Restart failed: ' . $e->getMessage());
        }
    }

    public function logs(int $lines = 100): array
    {
        try {
            $ssh = $this->getConnection();
            
            $logPath = $payload['path'] ?? $this->config['log_path'] ?? storage_path('logs');
            $logFile = $payload['file'] ?? 'laravel.log';
            
            $command = "tail -n {$lines} {$logPath}/{$logFile}";
            $output = $ssh->exec($command);
            
            $ssh->disconnect();

            return $this->success('Logs retrieved', ['logs' => $output]);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve logs: ' . $e->getMessage());
        }
    }

    public function status(): array
    {
        try {
            $ssh = $this->getConnection();
            
            $commands = [
                'uptime',
                'df -h',
                'free -m',
                'systemctl status ' . ($this->config['service'] ?? 'nginx') . ' --no-pager',
            ];

            $output = [];
            foreach ($commands as $cmd) {
                $output[] = $cmd . ":\n" . $ssh->exec($cmd);
            }

            $ssh->disconnect();

            return $this->success('Server status retrieved', ['status' => implode("\n\n", $output)]);
        } catch (\Exception $e) {
            return $this->error('Failed to get status: ' . $e->getMessage());
        }
    }

    public function rollback(string $version = null): array
    {
        try {
            $ssh = $this->getConnection();
            
            if (!$version) {
                $ssh->exec("cd {$this->deployPath} && git log --oneline -10");
                $output = $ssh->getLastOutput();
                return $this->error('Please specify version to rollback to. Available commits:', ['commits' => $output]);
            }

            $ssh->exec("cd {$this->deployPath} && git checkout {$version}");
            $ssh->exec("composer install --no-dev --optimize-autoloader");
            $ssh->exec("php artisan migrate --force");

            $ssh->disconnect();

            return $this->success('Rollback to ' . $version . ' completed');
        } catch (\Exception $e) {
            return $this->error('Rollback failed: ' . $e->getMessage());
        }
    }

    public function migrate(array $payload = []): array
    {
        try {
            $ssh = $this->getConnection();
            
            $force = $payload['force'] ? '--force' : '';
            $seed = $payload['seed'] ? '--seed' : '';
            
            $commands = [
                "cd {$this->deployPath} && php artisan migrate {$force}",
                $seed ? "cd {$this->deployPath} && php artisan db:seed {$force}" : '',
            ];

            $output = [];
            foreach ($commands as $cmd) {
                if ($cmd) {
                    $output[] = $ssh->exec($cmd);
                }
            }

            $ssh->disconnect();

            return $this->success('Migrations executed', ['output' => implode("\n", $output)]);
        } catch (\Exception $e) {
            return $this->error('Migration failed: ' . $e->getMessage());
        }
    }

    public function execute(string $command): array
    {
        try {
            $ssh = $this->getConnection();
            $output = $ssh->exec($command);
            $ssh->disconnect();

            return $this->success('Command executed', ['output' => $output]);
        } catch (\Exception $e) {
            return $this->error('Command failed: ' . $e->getMessage());
        }
    }
}