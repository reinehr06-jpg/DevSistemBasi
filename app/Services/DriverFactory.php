<?php

namespace App\Services;

use App\Services\Drivers\ApiDriver;
use App\Services\Drivers\BitbucketDriver;
use App\Services\Drivers\EasyPanelDriver;
use App\Services\Drivers\GitDriver;
use App\Services\Drivers\IntegrationDriver;
use App\Services\Drivers\SSHDriver;
use InvalidArgumentException;

class DriverFactory
{
    public static function make(string $type, \App\Models\Integration $integration): IntegrationDriver
    {
        return match ($type) {
            'easypanel' => new EasyPanelDriver($integration),
            'ssh', 'server' => new SSHDriver($integration),
            'git' => new GitDriver($integration),
            'bitbucket' => new BitbucketDriver($integration),
            'api' => new ApiDriver($integration),
            default => throw new InvalidArgumentException("Unknown driver type: {$type}"),
        };
    }

    public static function getAvailableActions(string $type): array
    {
        return match ($type) {
            'easypanel' => ['deploy', 'backup', 'restart', 'logs', 'status', 'rollback', 'migrate'],
            'ssh', 'server' => ['deploy', 'backup', 'restart', 'logs', 'status', 'rollback', 'migrate'],
            'git' => ['deploy', 'logs', 'status'],
            'bitbucket' => ['deploy', 'logs', 'status'],
            'api' => ['deploy', 'backup', 'restart', 'logs', 'status', 'rollback', 'migrate'],
            default => [],
        };
    }

    public static function getRequiredConfig(string $type): array
    {
        return match ($type) {
            'easypanel' => ['url', 'api_key', 'site_name'],
            'ssh', 'server' => ['ip', 'ssh_user', 'deploy_path'],
            'git' => ['repo'],
            'bitbucket' => ['workspace', 'repo', 'username', 'app_password'],
            'api' => ['status_url'],
            default => [],
        };
    }
}