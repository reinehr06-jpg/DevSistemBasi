<?php

namespace App\Services\Drivers;

use App\Models\Integration;

class GitDriver extends IntegrationDriver
{
    public function deploy(array $payload = []): array
    {
        $repo = $this->config['repo'] ?? null;
        $branch = $payload['branch'] ?? 'main';
        
        if (!$repo) {
            return $this->error('Repository not configured');
        }

        try {
            $tempDir = sys_get_temp_dir() . '/git_' . time();
            $commands = [
                "git clone {$repo} {$tempDir}",
                "cd {$tempDir} && git checkout {$branch}",
            ];

            $output = [];
            foreach ($commands as $cmd) {
                exec($cmd . ' 2>&1', $output, $return);
            }

            if ($return !== 0) {
                return $this->error('Git operations failed: ' . implode("\n", $output));
            }

            if (is_dir($tempDir)) {
                exec("rm -rf {$tempDir}");
            }

            return $this->success('Repository checked out to ' . $branch, ['branch' => $branch]);
        } catch (\Exception $e) {
            return $this->error('Git operation failed: ' . $e->getMessage());
        }
    }

    public function backup(array $payload = []): array
    {
        return $this->error('Use SSH driver for backup operations');
    }

    public function restart(array $payload = []): array
    {
        return $this->error('Use SSH driver for restart operations');
    }

    public function logs(int $lines = 100): array
    {
        $repo = $this->config['repo'] ?? null;
        
        if (!$repo) {
            return $this->error('Repository not configured');
        }

        try {
            $tempDir = sys_get_temp_dir() . '/git_log_' . time();
            exec("git clone --depth 50 {$repo} {$tempDir} 2>&1", $output);
            
            if (!is_dir($tempDir . '/.git')) {
                return $this->error('Invalid git repository');
            }

            exec("cd {$tempDir} && git log --oneline -{$lines}", $commits);
            
            exec("rm -rf {$tempDir}");

            return $this->success('Git logs retrieved', ['commits' => $commits]);
        } catch (\Exception $e) {
            return $this->error('Failed to get git logs: ' . $e->getMessage());
        }
    }

    public function status(): array
    {
        $repo = $this->config['repo'] ?? null;
        
        if (!$repo) {
            return $this->error('Repository not configured');
        }

        try {
            $tempDir = sys_get_temp_dir() . '/git_status_' . time();
            exec("git clone {$repo} {$tempDir} 2>&1", $output);
            
            if (!is_dir($tempDir . '/.git')) {
                return $this->error('Invalid git repository');
            }

            $branch = trim(shell_exec("cd {$tempDir} && git branch --show-current"));
            $commit = trim(shell_exec("cd {$tempDir} && git rev-parse HEAD"));
            
            exec("rm -rf {$tempDir}");

            return $this->success('Git status retrieved', [
                'branch' => $branch,
                'commit' => $commit,
                'repo' => $repo,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to get git status: ' . $e->getMessage());
        }
    }

    public function rollback(string $version = null): array
    {
        return $this->error('Use SSH driver for rollback operations');
    }

    public function migrate(array $payload = []): array
    {
        return $this->error('Use SSH driver for migration operations');
    }

    public function getBranches(): array
    {
        $repo = $this->config['repo'] ?? null;
        
        if (!$repo) {
            return $this->error('Repository not configured');
        }

        try {
            $tempDir = sys_get_temp_dir() . '/git_branches_' . time();
            exec("git ls-remote --heads {$repo}", $output);
            
            $branches = [];
            foreach ($output as $line) {
                if (preg_match('/refs\/heads\/(.+)$/', $line, $matches)) {
                    $branches[] = trim($matches[1]);
                }
            }

            return $this->success('Branches retrieved', ['branches' => $branches]);
        } catch (\Exception $e) {
            return $this->error('Failed to get branches: ' . $e->getMessage());
        }
    }
}