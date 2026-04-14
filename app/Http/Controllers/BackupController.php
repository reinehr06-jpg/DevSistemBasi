<?php

namespace App\Http\Controllers;

use App\Models\BackupDestination;
use App\Models\BackupLog;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class BackupController extends Controller
{
    public function index()
    {
        $recentBackups = BackupLog::with(['system', 'destination'])
            ->orderBy('started_at', 'desc')
            ->limit(50)
            ->get();

        $failedBackups = BackupLog::with(['system', 'destination'])
            ->whereIn('status', ['error'])
            ->orWhereIn('upload_status', ['error'])
            ->where('started_at', '>=', Date::now()->subDays(1))
            ->get();

        $systemsWithIssues = $failedBackups->pluck('system_id')->unique()->count();
        
        $destinations = BackupDestination::withCount('backupLogs')
            ->get();

        return view('backup.index', compact(
            'recentBackups',
            'failedBackups',
            'systemsWithIssues',
            'destinations'
        ));
    }

    public function status()
    {
        $today = Date::now()->startOfDay();
        
        $stats = [
            'total' => BackupLog::where('started_at', '>=', $today)->count(),
            'success' => BackupLog::where('started_at', '>=', $today)
                ->where('status', 'success')
                ->count(),
            'error' => BackupLog::where('started_at', '>=', $today)
                ->where(function ($query) {
                    $query->where('status', 'error')
                        ->orWhere('upload_status', 'error');
                })->count(),
        ];

        $failedBackups = BackupLog::with(['system', 'destination'])
            ->where(function ($query) {
                $query->where('status', 'error')
                    ->orWhere('upload_status', 'error');
            })
            ->where('started_at', '>=', $today)
            ->get();

        return response()->json([
            'stats' => $stats,
            'failed_backups' => $failedBackups->map(fn($b) => [
                'system' => $b->system?->name,
                'destination' => $b->destination?->name,
                'message' => $b->message ?? $b->upload_message,
                'started_at' => $b->started_at->toIso8601String(),
            ]),
        ]);
    }

    public function systems()
    {
        $systems = System::with(['backupLogs' => function ($query) {
            $query->orderBy('started_at', 'desc')->limit(1);
        }])->get();

        return view('backup.systems', compact('systems'));
    }

    public function destinations()
    {
        $destinations = BackupDestination::withCount('backupLogs')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backup.destinations', compact('destinations'));
    }

    public function storeDestination(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:s3,mac',
            'host' => 'nullable|string',
            'port' => 'nullable|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'private_key_path' => 'nullable|string',
            'remote_path' => 'nullable|string',
            'bucket' => 'nullable|string',
            'region' => 'nullable|string',
            'access_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'retention_days' => 'required|integer',
            'active' => 'boolean',
        ]);

        $destination = BackupDestination::create($validated);
        
        return response()->json(['message' => 'Destino criado com sucesso', 'destination' => $destination]);
    }

    public function updateDestination(Request $request, BackupDestination $destination)
    {
        $validated = $request->validate([
            'name' => 'string',
            'type' => 'in:s3,mac',
            'host' => 'string',
            'port' => 'integer',
            'username' => 'string',
            'password' => 'string',
            'private_key_path' => 'string',
            'remote_path' => 'string',
            'bucket' => 'string',
            'region' => 'string',
            'access_key' => 'string',
            'secret_key' => 'string',
            'retention_days' => 'integer',
            'active' => 'boolean',
        ]);

        $destination->update($validated);
        
        return response()->json(['message' => 'Destino atualizado com sucesso', 'destination' => $destination]);
    }

    public function destroyDestination(BackupDestination $destination)
    {
        $destination->delete();
        
        return response()->json(['message' => 'Destino removido com sucesso']);
    }
}