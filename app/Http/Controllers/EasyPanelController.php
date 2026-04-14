<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Services\EasyPanelService;
use Illuminate\Http\Request;

class EasyPanelController extends Controller
{
    public function __construct(
        protected EasyPanelService $easyPanel
    ) {}

    public function servers()
    {
        if (!$this->easyPanel->isConfigured()) {
            return response()->json(['error' => 'EasyPanel não configurado'], 400);
        }

        $servers = $this->easyPanel->getAllServers();
        
        return view('easypanel.servers', compact('servers'));
    }

    public function apiServers()
    {
        if (!$this->easyPanel->isConfigured()) {
            return response()->json(['servers' => []]);
        }

        $servers = $this->easyPanel->getAllServers();
        return response()->json($servers);
    }

    public function metrics(Request $request, string $serverId)
    {
        $metrics = $this->easyPanel->getServerMetrics($serverId);
        
        if ($request->wantsJson()) {
            return response()->json($metrics);
        }

        $server = Server::findOrFail($serverId);
        return view('easypanel.metrics', compact('server', 'metrics'));
    }

    public function logs(Request $request, string $serverId)
    {
        $lines = $request->input('lines', 100);
        $logs = $this->easyPanel->getServerLogs($serverId, $lines);

        if ($request->wantsJson()) {
            return response()->json($logs);
        }

        $server = Server::findOrFail($serverId);
        return view('easypanel.logs', compact('server', 'logs'));
    }

    public function logsStream(string $serverId)
    {
        $lines = 50;
        
        return response()->stream(function () use ($serverId, $lines) {
            while (true) {
                $logs = $this->easyPanel->getServerLogs($serverId, $lines);
                echo "data: " . json_encode($logs) . "\n\n";
                ob_flush();
                flush();
                sleep(2);
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function status(string $serverId)
    {
        $status = $this->easyPanel->getServerStatus($serverId);
        return response()->json($status);
    }

    public function deploy(Request $request, string $serverId)
    {
        $validated = $request->validate([
            'branch' => 'nullable|string',
        ]);

        $result = $this->easyPanel->deploy($serverId, $validated['branch'] ?? 'main');

        return response()->json($result);
    }

    public function restart(Request $request, string $serverId)
    {
        $validated = $request->validate([
            'service' => 'nullable|string',
        ]);

        $result = $this->easyPanel->restartService($serverId, $validated['service'] ?? 'php');

        return response()->json($result);
    }

    public function services(string $serverId)
    {
        $services = $this->easyPanel->getServices($serverId);
        return response()->json($services);
    }

    public function exec(Request $request, string $serverId)
    {
        $validated = $request->validate([
            'command' => 'required|string',
        ]);

        $result = $this->easyPanel->runCommand($serverId, $validated['command']);

        return response()->json($result);
    }

    public function syncServers()
    {
        if (!$this->easyPanel->isConfigured()) {
            return response()->json(['error' => 'EasyPanel não configurado'], 400);
        }

        $easypanelServers = $this->easyPanel->getAllServers();
        
        $synced = 0;
        foreach ($easypanelServers['servers'] ?? [] as $epServer) {
            Server::updateOrCreate(
                ['easypanel_id' => $epServer['id']],
                [
                    'name' => $epServer['name'],
                    'ip' => $epServer['ip'] ?? '0.0.0.0',
                    'easypanel_id' => $epServer['id'],
                    'status' => $epServer['status'] ?? 'online',
                ]
            );
            $synced++;
        }

        return response()->json(['synced' => $synced]);
    }
}