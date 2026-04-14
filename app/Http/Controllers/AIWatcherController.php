<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\Bug;
use App\Models\BackupLog;
use Illuminate\Http\Request;

class AIWatcherController extends Controller
{
    public function index(Request $request)
    {
        $systems = System::where('active', true)->get();
        
        $recentBugs = Bug::orderBy('created_at', 'desc')->limit(10)->get();
        $activeSystems = $systems->count();
        
        $healthScores = $systems->map(function($sys) {
            $bugs = Bug::where('system_id', $sys->id)->where('status', 'aberto')->count();
            $score = $bugs > 0 ? max(0, 100 - ($bugs * 10)) : 100;
            return ['system' => $sys->name, 'score' => $score, 'bugs' => $bugs];
        });

        return view('ai-watcher.index', compact('systems', 'recentBugs', 'activeSystems', 'healthScores'));
    }

    public function config()
    {
        $systems = System::where('active', true)->get();
        return view('ai-watcher.config', compact('systems'));
    }

    public function storeConfig(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|exists:systems,id',
            'type' => 'required|in:logs,api,database,performance',
            'interval' => 'required|integer|min:1',
            'sensitivity' => 'required|in:baixa,media,alta',
        ]);

        return back()->with('success', 'Watcher configurado');
    }

    public function logs(Request $request)
    {
        $logs = Bug::orderBy('created_at', 'desc')
            ->where('ai_detected', true)
            ->limit(50)
            ->get();

        return view('ai-watcher.logs', compact('logs'));
    }
}