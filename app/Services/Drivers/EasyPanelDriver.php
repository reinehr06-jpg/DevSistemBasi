<?php

namespace App\Services\Drivers;

use App\Models\Integration;

class EasyPanelDriver extends IntegrationDriver
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct(Integration $integration)
    {
        parent::__construct($integration);
        $this->baseUrl = rtrim($this->config['url'] ?? $this->config['host'] ?? '', '/');
        $this->apiKey = $this->config['api_key'] ?? '';
    }

    public function deploy(array $payload = []): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        $siteName = $payload['site_name'] ?? $this->config['site_name'] ?? null;
        
        if (!$siteName) {
            return $this->error('Site name not configured');
        }

        $branch = $payload['branch'] ?? 'main';
        $gitUrl = $payload['git_url'] ?? $this->config['git_url'] ?? null;

        if (!$gitUrl) {
            return $this->error('Git URL not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 120]);
            
            $response = $client->post("{$this->baseUrl}/api/v1/sites/{$siteName}/deploy", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'git_url' => $gitUrl,
                    'branch' => $branch,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            return $this->success('Deploy initiated successfully', [
                'deploy_id' => $data['id'] ?? null,
                'status' => $data['status'] ?? 'initiated',
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            return $this->error('Deploy failed: ' . ($response['message'] ?? $e->getMessage()));
        } catch (\Exception $e) {
            return $this->error('Deploy failed: ' . $e->getMessage());
        }
    }

    public function backup(array $payload = []): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        $siteName = $payload['site_name'] ?? $this->config['site_name'] ?? null;
        
        if (!$siteName) {
            return $this->error('Site name not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 60]);
            
            $response = $client->post("{$this->baseUrl}/api/v1/sites/{$siteName}/backup", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            return $this->success('Backup initiated successfully', [
                'backup_id' => $data['id'] ?? null,
            ]);
        } catch (\Exception $e) {
            return $this->error('Backup failed: ' . $e->getMessage());
        }
    }

    public function restart(array $payload = []): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        $siteName = $payload['site_name'] ?? $this->config['site_name'] ?? null;
        
        if (!$siteName) {
            return $this->error('Site name not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            
            $response = $client->post("{$this->baseUrl}/api/v1/sites/{$siteName}/restart", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            return $this->success('Service restarted successfully');
        } catch (\Exception $e) {
            return $this->error('Restart failed: ' . $e->getMessage());
        }
    }

    public function logs(int $lines = 100): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        $siteName = $this->config['site_name'] ?? null;
        
        if (!$siteName) {
            return $this->error('Site name not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            
            $response = $client->get("{$this->baseUrl}/api/v1/sites/{$siteName}/logs", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
                'query' => ['lines' => $lines],
            ]);

            $data = json_decode($response->getBody(), true);
            
            return $this->success('Logs retrieved', [
                'logs' => $data['logs'] ?? $data ?? '',
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve logs: ' . $e->getMessage());
        }
    }

    public function status(): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        $siteName = $this->config['site_name'] ?? null;
        
        if (!$siteName) {
            return $this->error('Site name not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            
            $response = $client->get("{$this->baseUrl}/api/v1/sites/{$siteName}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            return $this->success('Site status retrieved', [
                'name' => $data['name'] ?? $siteName,
                'status' => $data['status'] ?? 'unknown',
                'php_version' => $data['php_version'] ?? null,
                'laravel_version' => $data['laravel_version'] ?? null,
                'last_deploy' => $data['last_deploy_at'] ?? null,
                'url' => $data['url'] ?? null,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to get status: ' . $e->getMessage());
        }
    }

    public function rollback(string $version = null): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        $siteName = $this->config['site_name'] ?? null;
        
        if (!$siteName) {
            return $this->error('Site name not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 60]);
            
            $response = $client->post("{$this->baseUrl}/api/v1/sites/{$siteName}/rollback", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
                'json' => $version ? ['version' => $version] : [],
            ]);

            return $this->success('Rollback initiated successfully');
        } catch (\Exception $e) {
            return $this->error('Rollback failed: ' . $e->getMessage());
        }
    }

    public function migrate(array $payload = []): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        $siteName = $this->config['site_name'] ?? null;
        
        if (!$siteName) {
            return $this->error('Site name not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 120]);
            
            $response = $client->post("{$this->baseUrl}/api/v1/sites/{$siteName}/migrate", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'force' => $payload['force'] ?? false,
                    'seed' => $payload['seed'] ?? false,
                ],
            ]);

            return $this->success('Migrations executed successfully');
        } catch (\Exception $e) {
            return $this->error('Migration failed: ' . $e->getMessage());
        }
    }

    public function testConnection(): array
    {
        if (!$this->baseUrl || !$this->apiKey) {
            return $this->error('EasyPanel URL or API Key not configured');
        }

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            
            $response = $client->get("{$this->baseUrl}/api/v1/me", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            
            return $this->success('Connection successful', [
                'user' => $data['email'] ?? $data['name'] ?? 'Unknown',
            ]);
        } catch (\Exception $e) {
            return $this->error('Connection failed: ' . $e->getMessage());
        }
    }
}