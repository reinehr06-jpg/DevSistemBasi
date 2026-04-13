<?php

namespace App\Services\Drivers;

use App\Models\Integration;

class ApiDriver extends IntegrationDriver
{
    public function deploy(array $payload = []): array
    {
        return $this->httpRequest('POST', $this->config['deploy_url'] ?? '', $payload);
    }

    public function backup(array $payload = []): array
    {
        return $this->httpRequest('POST', $this->config['backup_url'] ?? '', $payload);
    }

    public function restart(array $payload = []): array
    {
        return $this->httpRequest('POST', $this->config['restart_url'] ?? '', $payload);
    }

    public function logs(int $lines = 100): array
    {
        return $this->httpRequest('GET', $this->config['logs_url'] ?? '', ['query' => ['lines' => $lines]]);
    }

    public function status(): array
    {
        return $this->httpRequest('GET', $this->config['status_url'] ?? '');
    }

    public function rollback(string $version = null): array
    {
        return $this->httpRequest('POST', $this->config['rollback_url'] ?? '', ['version' => $version]);
    }

    public function migrate(array $payload = []): array
    {
        return $this->httpRequest('POST', $this->config['migrate_url'] ?? '', $payload);
    }

    public function testConnection(): array
    {
        return $this->status();
    }
}