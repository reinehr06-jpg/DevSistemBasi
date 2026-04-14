<?php

namespace App\Services;

use App\Models\System;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RepositoryDetector
{
    private array $detected = [
        'language' => null,
        'framework' => null,
        'database' => null,
        'version' => null,
        'hosting' => null,
        'server' => null,
    ];

    public function detect(System $system): array
    {
        if (!$system->repository_url) {
            return $this->detected;
        }

        $repoUrl = $this->normalizeUrl($system->repository_url);
        
        try {
            if ($this->isGithub($repoUrl)) {
                return $this->detectFromGithub($repoUrl);
            } elseif ($this->isBitbucket($repoUrl)) {
                return $this->detectFromBitbucket($repoUrl);
            }
        } catch (\Exception $e) {
            Log::error("Failed to detect repository: " . $e->getMessage());
        }

        return $this->detected;
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        
        if (preg_match('/^git@(.+):(.+)\.git$/', $url, $matches)) {
            $host = $matches[1];
            $repo = $matches[2];
            if ($host === 'github.com') {
                return "https://github.com/{$repo}.git";
            } elseif ($host === 'bitbucket.org') {
                return "https://bitbucket.org/{$repo}.git";
            }
        }
        
        if (preg_match('/^(https?:\/\/)?(.+?)(\.git)?$/', $url, $matches)) {
            return rtrim($matches[0], '.git');
        }
        
        return $url;
    }

    private function isGithub(string $url): bool
    {
        return str_contains($url, 'github.com');
    }

    private function isBitbucket(string $url): bool
    {
        return str_contains($url, 'bitbucket.org');
    }

    private function detectFromGithub(string $url): array
    {
        preg_match('/github\.com\/([^\/]+)\/([^\/]+)/', $url, $matches);
        
        if (empty($matches)) {
            return $this->detected;
        }

        $owner = $matches[1];
        $repo = str_replace('.git', '', $matches[2]);

        $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}";
        $response = Http::withHeaders(['User-Agent' => 'DevManager/1.0'])->get($apiUrl);

        if (!$response->successful()) {
            return $this->detected;
        }

        $repoData = $response->json();
        $defaultBranch = $repoData['default_branch'] ?? 'main';

        $contentsUrl = "https://api.github.com/repos/{$owner}/{$repo}/contents?ref={$defaultBranch}";
        $contents = Http::withHeaders(['User-Agent' => 'DevManager/1.0'])->get($contentsUrl)->json();

        $this->detectFromFiles($contents);
        $this->detectHostingFromFiles($contents);
        $this->detectFromComposer($owner, $repo);
        $this->detectFromPackage($owner, $repo);
        $this->detectFromRequirements($owner, $repo);
        $this->detectFromDockerfile($owner, $repo);

        return $this->detected;
    }

    private function detectFromBitbucket(string $url): array
    {
        preg_match('/bitbucket\.org\/([^\/]+)\/([^\/]+)/', $url, $matches);
        
        if (empty($matches)) {
            return $this->detected;
        }

        $owner = $matches[1];
        $repo = str_replace('.git', '', $matches[2]);

        $apiUrl = "https://api.bitbucket.org/2.0/repositories/{$owner}/{$repo}";
        $response = Http::get($apiUrl);

        if (!$response->successful()) {
            return $this->detected;
        }

        $mainBranch = $response->json()['mainbranch']['name'] ?? 'main';

        $srcUrl = "https://api.bitbucket.org/2.0/repositories/{$owner}/{$repo}/src/{$mainBranch}";
        $srcResponse = Http::get($srcUrl)->json();

        if (isset($srcResponse['values'])) {
            $this->detectFromFiles($srcResponse['values']);
            $this->detectHostingFromFiles($srcResponse['values']);
        }

        $this->detectFromFileContent($owner, $repo, 'composer.json');
        $this->detectFromFileContent($owner, $repo, 'package.json');
        $this->detectFromFileContent($owner, $repo, 'requirements.txt');
        $this->detectFromFileContent($owner, $repo, 'Dockerfile');

        return $this->detected;
    }

    private function detectFromFiles(array $files): void
    {
        $fileNames = array_column($files, 'name');

        if (in_array('composer.json', $fileNames)) {
            $this->detected['language'] = 'PHP';
        } elseif (in_array('package.json', $fileNames)) {
            $this->detected['language'] = 'Node.js';
        } elseif (in_array('requirements.txt', $fileNames) || in_array('pyproject.toml', $fileNames) || in_array('setup.py', $fileNames)) {
            $this->detected['language'] = 'Python';
        } elseif (in_array('go.mod', $fileNames)) {
            $this->detected['language'] = 'Go';
        } elseif (in_array('Gemfile', $fileNames)) {
            $this->detected['language'] = 'Ruby';
        }

        if (in_array('Dockerfile', $fileNames)) {
            $this->detectDatabaseFromDocker($files);
        }
    }

    private function detectFromComposer(string $owner, string $repo): void
    {
        $url = "https://raw.githubusercontent.com/{$owner}/{$repo}/main/composer.json";
        $response = Http::withHeaders(['User-Agent' => 'DevManager/1.0'])->get($url);

        if ($response->successful()) {
            $composer = $response->json();
            $require = $composer['require'] ?? [];

            if (isset($require['laravel/framework'])) {
                $this->detected['framework'] = 'Laravel';
                $this->detected['version'] = $require['php'] ?? null;
            } elseif (isset($require['symfony/framework-bundle'])) {
                $this->detected['framework'] = 'Symfony';
            }

            foreach (array_keys($require) as $package) {
                if (str_contains($package, 'laravel')) {
                    $this->detected['framework'] = 'Laravel';
                }
            }

            foreach (['mysql', 'pgsql', 'postgres', 'mariadb'] as $db) {
                if (isset($require[$db]) || isset($require['doctrine/dbal'])) {
                    $this->detected['database'] = strtoupper($db);
                    if ($db === 'pgsql' || $db === 'postgres') {
                        $this->detected['database'] = 'PostgreSQL';
                    }
                }
            }
        }
    }

    private function detectFromPackage(string $owner, string $repo): void
    {
        $url = "https://raw.githubusercontent.com/{$owner}/{$repo}/main/package.json";
        $response = Http::withHeaders(['User-Agent' => 'DevManager/1.0'])->get($url);

        if ($response->successful()) {
            $package = $response->json();
            $dependencies = array_merge($package['dependencies'] ?? [], $package['devDependencies'] ?? []);

            foreach (array_keys($dependencies) as $dep) {
                if (in_array($dep, ['express', 'fastify', 'koa', 'nestjs'])) {
                    $this->detected['framework'] = ucfirst($dep);
                }
                if ($dep === 'redis') {
                    $this->detected['database'] = 'Redis';
                }
            }

            if (isset($package['engines']['node'])) {
                $this->detected['version'] = $package['engines']['node'];
            }
        }
    }

    private function detectFromRequirements(string $owner, string $repo): void
    {
        $url = "https://raw.githubusercontent.com/{$owner}/{$repo}/main/requirements.txt";
        $response = Http::withHeaders(['User-Agent' => 'DevManager/1.0'])->get($url);

        if ($response->successful()) {
            $content = $response->body();

            if (preg_match('/(django|flask|fastapi|python-flask|python-django)/i', $content)) {
                $this->detected['framework'] = 'Django';
            }

            if (preg_match('/(psycopg2|pg|postgres)/i', $content)) {
                $this->detected['database'] = 'PostgreSQL';
            } elseif (preg_match('/(mysqlclient|pymysql)/i', $content)) {
                $this->detected['database'] = 'MySQL';
            } elseif (preg_match('/(redis)/i', $content)) {
                $this->detected['database'] = 'Redis';
            }
        }
    }

    private function detectFromDockerfile(string $owner, string $repo): void
    {
        $url = "https://raw.githubusercontent.com/{$owner}/{$repo}/main/Dockerfile";
        $response = Http::withHeaders(['User-Agent' => 'DevManager/1.0'])->get($url);

        if ($response->successful()) {
            $content = $response->body();
            $this->detectDatabaseFromDockerContent($content);
        }
    }

    private function detectFromFileContent(string $owner, string $repo, string $filename): void
    {
        $url = "https://api.bitbucket.org/2.0/repositories/{$owner}/{$repo}/src/main/{$filename}";
        $response = Http::get($url);

        if ($response->successful()) {
            $content = $response->body();

            if ($filename === 'composer.json') {
                $composer = json_decode($content, true);
                if ($composer) {
                    if (isset($composer['require']['laravel/framework'])) {
                        $this->detected['framework'] = 'Laravel';
                        $this->detected['language'] = 'PHP';
                    }
                }
            } elseif ($filename === 'package.json') {
                $package = json_decode($content, true);
                if ($package) {
                    $this->detected['language'] = 'Node.js';
                }
            } elseif ($filename === 'Dockerfile') {
                $this->detectDatabaseFromDockerContent($content);
            }
        }
    }

    private function detectDatabaseFromDocker(array $files): void
    {
        foreach ($files as $file) {
            if ($file['name'] === 'Dockerfile') {
                $url = $file['links']['self']['href'] ?? null;
                if ($url) {
                    $content = Http::get($url)->body();
                    $this->detectDatabaseFromDockerContent($content);
                }
            }
        }
    }

    private function detectDatabaseFromDockerContent(string $content): void
    {
        if (preg_match('/(mysql|postgres|mariadb|mongodb|redis)/i', $content, $matches)) {
            $db = strtolower($matches[1]);
            match ($db) {
                'mysql' => $this->detected['database'] = 'MySQL',
                'postgres', 'pgsql' => $this->detected['database'] = 'PostgreSQL',
                'mariadb' => $this->detected['database'] = 'MariaDB',
                'mongodb' => $this->detected['database'] = 'MongoDB',
                'redis' => $this->detected['database'] = 'Redis',
                default => null,
            };
        }

        if (preg_match('/(nginx|apache|httpd|caddy)/i', $content, $matches)) {
            $this->detected['server'] = strtoupper($matches[1]);
        }

        if (preg_match('/(php-fpm|gunicorn|unicorn|puma|passenger|uvicorn)/i', $content, $matches)) {
            $this->detected['server'] = strtoupper($matches[1]);
        }
    }

    private function detectHostingFromFiles(array $files): void
    {
        $fileNames = array_column($files, 'name');

        if (in_array('vercel.json', $fileNames)) {
            $this->detected['hosting'] = 'Vercel';
        } elseif (in_array('netlify.toml', $fileNames)) {
            $this->detected['hosting'] = 'Netlify';
        } elseif (in_array('firebase.json', $fileNames)) {
            $this->detected['hosting'] = 'Firebase';
        } elseif (in_array('Procfile', $fileNames)) {
            $this->detected['hosting'] = 'Heroku';
        } elseif (in_array('app.yaml', $fileNames)) {
            $this->detected['hosting'] = 'Google Cloud';
        } elseif (in_array('cloudformation.yaml', $fileNames) || in_array('cloudformation.yml', $fileNames)) {
            $this->detected['hosting'] = 'AWS';
        } elseif (in_array('.ebextensions', $fileNames) || in_array('elasticbeanstalk', $fileNames)) {
            $this->detected['hosting'] = 'AWS Beanstalk';
        }
    }
}