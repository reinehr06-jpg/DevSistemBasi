<?php

namespace App\Http\Controllers;

use App\Jobs\BackupDatabaseJob;
use App\Models\BackupLog;
use App\Models\Server;
use App\Models\System;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        $query = Server::with('system');

        if ($request->system_id) {
            $query->where('system_id', $request->system_id);
        }

        $servers = $query->orderBy('system_id')->get();

        if ($request->wantsJson()) {
            return response()->json($servers);
        }

        return view('servers.index', compact('servers'));
    }

    public function show(Request $request, string $id)
    {
        $server = Server::with('system')->findOrFail($id);
        
        $backupLogs = BackupLog::where('server_id', $id)
            ->orWhere(function ($query) use ($server) {
                $query->where('system_id', $server->system_id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'server' => $server,
                'backup_logs' => $backupLogs,
            ]);
        }

        return view('servers.show', compact('server', 'backupLogs'));
    }

    public function bySystem(Request $request, string $systemId)
    {
        $system = System::findOrFail($systemId);
        $servers = Server::where('system_id', $systemId)->get();

        return view('servers.by-system', compact('system', 'servers'));
    }

    public function updateMetrics(Request $request, string $id)
    {
        $server = Server::findOrFail($id);

        $server->update([
            'cpu_usage' => $request->cpu ?? $server->cpu_usage,
            'ram_usage' => $request->ram ?? $server->ram_usage,
            'disk_usage' => $request->disk ?? $server->disk_usage,
            'status' => $request->status ?? $server->status,
        ]);

        if ($request->branch) {
            $server->update(['branch' => $request->branch]);
        }

        if ($request->last_commit) {
            $server->update(['last_commit' => $request->last_commit]);
        }

        return response()->json(['success' => true]);
    }

    public function apiUpdate(Request $request)
    {
        $ip = $request->ip_address ?? $request->ip;
        
        $server = Server::where('ip', $ip)->first();

        if (!$server) {
            return response()->json(['error' => 'Server not found'], 404);
        }

        $server->update([
            'cpu_usage' => $request->cpu ?? 0,
            'ram_usage' => $request->ram ?? 0,
            'disk_usage' => $request->disk ?? 0,
            'branch' => $request->branch ?? $server->branch,
            'last_commit' => $request->commit ?? $server->last_commit,
            'project_version' => $request->version ?? $server->project_version,
            'status' => $request->status ?? 'online',
        ]);

        return response()->json(['success' => true]);
    }

    public function manualBackup(Request $request, string $id)
    {
        $server = Server::findOrFail($id);
        $system = $server->system;

        BackupDatabaseJob::dispatch($system, $server);

        return redirect()->back()->with('success', 'Backup iniciado!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|exists:systems,id',
            'name' => 'required',
            'ip' => 'required|ip',
            'ssh_user' => 'required',
            'deploy_path' => 'required',
            'database_name' => 'nullable',
        ]);

        Server::create($validated);

        return redirect()->route('servers.index')->with('success', 'Servidor criado!');
    }

    public function update(Request $request, string $id)
    {
        $server = Server::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required',
            'ip' => 'required|ip',
            'ssh_user' => 'required',
            'deploy_path' => 'required',
            'database_name' => 'nullable',
            'status' => 'in:online,offline,manutencao',
        ]);

        $server->update($validated);

        return redirect()->route('servers.index')->with('success', 'Servidor atualizado!');
    }

    public function destroy(string $id)
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return redirect()->route('servers.index')->with('success', 'Servidor excluído!');
    }
}