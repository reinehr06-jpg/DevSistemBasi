<?php

namespace App\Services;

use App\Models\System;
use App\Models\Server;
use App\Models\DevTask;
use App\Models\Bug;
use App\Models\Alert;
use Illuminate\Support\Facades\DB;

class SystemScoreService
{
    public static function calculate(System $system): array
    {
        $score = 100;
        $factors = [];
        $suggestions = [];

        $servers = $system->servers;
        
        foreach ($servers as $server) {
            $serverScore = self::calculateServerScore($server);
            $score -= (100 - $serverScore);
            
            if ($serverScore < 70) {
                $suggestions[] = "Servidor '{$server->name}' com performance abaixo do ideal";
            }
        }

        $openBugs = Bug::where('system_id', $system->id)
            ->where('status', '!=', 'resolved')
            ->count();
        
        if ($openBugs > 0) {
            $penalty = min($openBugs * 2, 20);
            $score -= $penalty;
            $suggestions[] = "{$openBugs} bugs abertos afetando a qualidade";
        }

        $activeTasks = DevTask::where('system_id', $system->id)
            ->where('status', '!=', 'completed')
            ->count();

        $recentDeploys = $servers->filter(function ($s) {
            return $s->last_deploy && $s->last_deploy->diffInHours() < 24;
        })->count();

        if ($recentDeploys > 0) {
            $unstableDeploys = $servers->filter(function ($s) {
                return $s->last_deploy_status === 'failed';
            })->count();
            
            if ($unstableDeploys > 0) {
                $score -= 10;
                $suggestions[] = "Deploy instável detectado nas últimas 24h";
            }
        }

        $alerts = Alert::where('system_id', $system->id)
            ->where('status', 'triggered')
            ->count();
        
        if ($alerts > 0) {
            $score -= min($alerts * 3, 15);
            $suggestions[] = "{$alerts} alertas ativos precisam de atenção";
        }

        $score = max(0, min(100, $score));

        $status = match (true) {
            $score >= 90 => 'excellent',
            $score >= 75 => 'good',
            $score >= 50 => 'warning',
            default => 'critical',
        };

        return [
            'score' => $score,
            'status' => $status,
            'factors' => [
                'servers_count' => $servers->count(),
                'open_bugs' => $openBugs,
                'active_tasks' => $activeTasks,
                'active_alerts' => $alerts,
                'recent_deploys' => $recentDeploys,
            ],
            'suggestions' => $suggestions,
        ];
    }

    public static function calculateServerScore(Server $server): int
    {
        $score = 100;

        if ($server->cpu_usage > 80) {
            $score -= 20;
        } elseif ($server->cpu_usage > 60) {
            $score -= 10;
        }

        if ($server->ram_usage > 80) {
            $score -= 20;
        } elseif ($server->ram_usage > 60) {
            $score -= 10;
        }

        if ($server->disk_usage > 90) {
            $score -= 25;
        } elseif ($server->disk_usage > 75) {
            $score -= 10;
        }

        if ($server->status === 'offline') {
            $score -= 30;
        } elseif ($server->status === 'manutencao') {
            $score -= 10;
        }

        if ($server->last_deploy_status === 'failed') {
            $score -= 15;
        }

        return max(0, $score);
    }

    public static function getHealthReport(): array
    {
        $systems = System::with('servers')->get();
        
        $report = [];
        
        foreach ($systems as $system) {
            $report[] = [
                'system' => $system->name,
                ...self::calculate($system),
            ];
        }

        $avgScore = collect($report)->avg('score') ?? 0;

        return [
            'total_systems' => $systems->count(),
            'average_score' => round($avgScore),
            'systems' => $report,
        ];
    }
}