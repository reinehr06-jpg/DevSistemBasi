<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Services\EasyPanelService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorServers extends Command
{
    protected $signature = 'monitor:servers';
    protected $description = 'Coleta métricas dos servidores cadastrados';

    public function __construct(
        protected EasyPanelService $easyPanel
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $servers = Server::whereNotNull('easypanel_id')
            ->where('status', '!=', 'offline')
            ->get();

        if ($servers->isEmpty()) {
            $this->info('Nenhum servidor EasyPanel configurado.');
            return Command::SUCCESS;
        }

        $this->info("Monitorando {$servers->count()} servidores...");

        foreach ($servers as $server) {
            $this->updateServerMetrics($server);
        }

        $this->info('Monitoramento concluído.');
        return Command::SUCCESS;
    }

    protected function updateServerMetrics(Server $server): void
    {
        try {
            $metrics = $this->easyPanel->getServerMetrics($server->easypanel_id);

            $server->update([
                'cpu_usage' => $metrics['cpu']['usage'] ?? $server->cpu_usage,
                'ram_usage' => $metrics['ram']['usage'] ?? $server->ram_usage,
                'disk_usage' => $metrics['disk']['usage'] ?? $server->disk_usage,
                'last_health_check' => now(),
                'health_status' => 'healthy',
            ]);

            $this->line("✓ {$server->name}: CPU {$metrics['cpu']['usage']}%");
        } catch (\Exception $e) {
            Log::error("Monitor error for {$server->name}", ['message' => $e->getMessage()]);
            
            $server->update([
                'health_status' => 'offline',
            ]);
            
            $this->warn("✗ {$server->name}: {$e->getMessage()}");
        }
    }
}