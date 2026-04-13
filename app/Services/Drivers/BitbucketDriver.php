<?php

namespace App\Services\Drivers;

use App\Models\Integration;

class BitbucketDriver extends IntegrationDriver
{
    protected string $workspace;
    protected string $repo;
    protected string $username;
    protected string $appPassword;

    public function __construct(Integration $integration)
    {
        parent::__construct($integration);
        $this->workspace = $this->config['workspace'] ?? '';
        $this->repo = $this->config['repo'] ?? '';
        $this->username = $this->config['username'] ?? '';
        $this->appPassword = $this->config['app_password'] ?? '';
    }

    protected function getClient(): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client([
            'auth' => [$this->username, $this->appPassword],
            'headers' => ['Accept' => 'application/json'],
        ]);
    }

    public function deploy(array $payload = []): array
    {
        if (!$this->workspace || !$this->repo) {
            return $this->error('Workspace or Repository not configured');
        }

        $branch = $payload['branch'] ?? 'main';

        try {
            $client = $this->getClient();
            
            $response = $client->get("https://api.bitbucket.org/2.0/repositories/{$this->workspace}/{$this->repo}");
            $data = json_decode($response->getBody(), true);

            return $this->success('Repository information retrieved', [
                'name' => $data['name'] ?? '',
                'branch' => $branch,
                'url' => $data['links']['html']['href'] ?? '',
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to get repository: ' . $e->getMessage());
        }
    }

    public function backup(array $payload = []): array
    {
        return $this->error('Use SSH or EasyPanel driver for backup operations');
    }

    public function restart(array $payload = []): array
    {
        return $this->error('Use SSH or EasyPanel driver for restart operations');
    }

    public function logs(int $lines = 100): array
    {
        if (!$this->workspace || !$this->repo) {
            return $this->error('Workspace or Repository not configured');
        }

        try {
            $client = $this->getClient();
            
            $response = $client->get("https://api.bitbucket.org/2.0/repositories/{$this->workspace}/{$this->repo}/commits", [
                'query' => ['pagelen' => $lines],
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            $commits = array_map(function ($commit) {
                return [
                    'hash' => $commit['hash'],
                    'message' => $commit['message'],
                    'author' => $commit['author']['raw'],
                    'date' => $commit['date'],
                ];
            }, $data['values'] ?? []);

            return $this->success('Commits retrieved', ['commits' => $commits]);
        } catch (\Exception $e) {
            return $this->error('Failed to get commits: ' . $e->getMessage());
        }
    }

    public function status(): array
    {
        if (!$this->workspace || !$this->repo) {
            return $this->error('Workspace or Repository not configured');
        }

        try {
            $client = $this->getClient();
            
            $response = $client->get("https://api.bitbucket.org/2.0/repositories/{$this->workspace}/{$this->repo}");
            $data = json_decode($response->getBody(), true);

            return $this->success('Repository status retrieved', [
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? '',
                'language' => $data['language'] ?? '',
                'updated' => $data['updated_on'] ?? '',
                'url' => $data['links']['html']['href'] ?? '',
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to get status: ' . $e->getMessage());
        }
    }

    public function rollback(string $version = null): array
    {
        return $this->error('Use SSH driver for rollback operations');
    }

    public function migrate(array $payload = []): array
    {
        return $this->error('Use SSH or EasyPanel driver for migration operations');
    }

    public function getBranches(): array
    {
        if (!$this->workspace || !$this->repo) {
            return $this->error('Workspace or Repository not configured');
        }

        try {
            $client = $this->getClient();
            
            $response = $client->get("https://api.bitbucket.org/2.0/repositories/{$this->workspace}/{$this->repo}/refs/branches");
            $data = json_decode($response->getBody(), true);
            
            $branches = array_keys($data['values'] ?? []);

            return $this->success('Branches retrieved', ['branches' => $branches]);
        } catch (\Exception $e) {
            return $this->error('Failed to get branches: ' . $e->getMessage());
        }
    }

    public function getWebhooks(): array
    {
        if (!$this->workspace || !$this->repo) {
            return $this->error('Workspace or Repository not configured');
        }

        try {
            $client = $this->getClient();
            
            $response = $client->get("https://api.bitbucket.org/2.0/repositories/{$this->workspace}/{$this->repo}/webhooks");
            $data = json_decode($response->getBody(), true);

            return $this->success('Webhooks retrieved', ['webhooks' => $data['values'] ?? []]);
        } catch (\Exception $e) {
            return $this->error('Failed to get webhooks: ' . $e->getMessage());
        }
    }

    public function createWebhook(string $url, string $events = 'push'): array
    {
        if (!$this->workspace || !$this->repo) {
            return $this->error('Workspace or Repository not configured');
        }

        try {
            $client = $this->getClient();
            
            $response = $client->post("https://api.bitbucket.org/2.0/repositories/{$this->workspace}/{$this->repo}/webhooks", [
                'json' => [
                    'url' => $url,
                    'events' => [$events],
                    'active' => true,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            return $this->success('Webhook created', ['webhook' => $data]);
        } catch (\Exception $e) {
            return $this->error('Failed to create webhook: ' . $e->getMessage());
        }
    }
}