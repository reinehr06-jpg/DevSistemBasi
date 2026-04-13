<?php

namespace App\Services\Drivers;

use App\Models\Integration;

abstract class IntegrationDriver
{
    protected Integration $integration;
    protected array $config;

    public function __construct(Integration $integration)
    {
        $this->integration = $integration;
        $this->config = $integration->config ?? [];
    }

    abstract public function deploy(array $payload = []): array;
    abstract public function backup(array $payload = []): array;
    abstract public function restart(array $payload = []): array;
    abstract public function logs(int $lines = 100): array;
    abstract public function status(): array;
    abstract public function rollback(string $version = null): array;
    abstract public function migrate(array $payload = []): array;

    protected function success(string $message, array $data = []): array
    {
        $this->integration->markAsUsed();
        return array_merge(['success' => true, 'message' => $message], $data);
    }

    protected function error(string $message, array $data = []): array
    {
        return array_merge(['success' => false, 'message' => $message], $data);
    }

    protected function httpRequest(string $method, string $url, array $data = []): array
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $response = $client->request($method, $url, $data);
            return $this->success('Request successful', [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody(), true)
            ]);
        } catch (\Exception $e) {
            return $this->error('HTTP Request failed: ' . $e->getMessage());
        }
    }

    protected function sshCommand(string $command): array
    {
        try {
            $host = $this->config['ip'] ?? $this->config['host'] ?? null;
            $user = $this->config['ssh_user'] ?? 'root';
            $key = $this->config['ssh_key_path'] ?? null;

            if (!$host) {
                return $this->error('SSH host not configured');
            }

            $ssh = new \phpseclib3\Net_SSH2($host);
            
            if ($key) {
                $keyObj = \phpseclib3\Crypt\PublicKeyLoader::load(file_get_contents($key));
                $ssh->authenticate($user, $keyObj);
            } else {
                $password = $this->config['ssh_password'] ?? null;
                $ssh->authenticate($user, $password);
            }

            if (!$ssh->isConnected()) {
                return $this->error('Failed to connect via SSH');
            }

            $output = $ssh->exec($command);
            $ssh->disconnect();

            return $this->success('Command executed', ['output' => $output]);
        } catch (\Exception $e) {
            return $this->error('SSH Command failed: ' . $e->getMessage());
        }
    }
}