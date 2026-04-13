<?php

namespace App\Http\Controllers;

use App\Jobs\DeployProjectJob;
use App\Models\Notification;
use App\Models\Server;
use Illuminate\Http\Request;

class DeployController extends Controller
{
    public function index()
    {
        $servers = Server::with('system')->get();
        return view('deploy.index', compact('servers'));
    }

    public function deploy(Request $request, Server $server)
    {
        $validated = $request->validate([
            'action' => 'required|in:pull,migrate,restart,full',
        ]);

        DeployProjectJob::dispatch($server, $validated['action']);

        Notification::create([
            'type' => 'deploy',
            'title' => 'Deploy Iniciando',
            'message' => "Deploy {$validated['action']} iniciado no servidor {$server->name}",
            'payload' => [
                'server_id' => $server->id,
                'action' => $validated['action'],
            ],
        ]);

        return redirect()->route('deploy.index')->with('success', 'Deploy iniciado com sucesso!');
    }

    public function status(Server $server)
    {
        return response()->json([
            'server' => $server->fresh(),
            'status' => $server->status,
            'cpu_usage' => $server->cpu_usage,
            'ram_usage' => $server->ram_usage,
        ]);
    }
}