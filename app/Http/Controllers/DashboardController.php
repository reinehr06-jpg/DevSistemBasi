<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\DevTask;
use App\Models\Notification;
use App\Models\Server;
use App\Models\System;
use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $systems = System::where('active', true)->get();

        $tasksByStatus = DevTask::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $bugsByStatus = Bug::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $servers = Server::with('system')->get();

        $recentNotifications = Notification::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeLog = WorkLog::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        $todayWorkLogs = WorkLog::whereDate('start_time', today())
            ->whereNotNull('end_time')
            ->get();

        $totalMinutes = 0;
        foreach ($todayWorkLogs as $log) {
            $totalMinutes += $log->start_time->diffInMinutes($log->end_time);
        }
        $hoursWorked = floor($totalMinutes / 60);
        $minutesWorked = $totalMinutes % 60;

        $stats = [
            'total_tasks' => DevTask::count(),
            'pending_tasks' => DevTask::where('status', 'pendente')->count(),
            'in_progress_tasks' => DevTask::where('status', 'em_andamento')->count(),
            'finished_tasks' => DevTask::where('status', 'finalizada')->count(),
            'total_bugs' => Bug::count(),
            'open_bugs' => Bug::where('status', 'aberto')->count(),
            'total_servers' => Server::count(),
            'online_servers' => Server::where('status', 'online')->count(),
        ];

        return view('dashboard', compact(
            'systems',
            'stats',
            'tasksByStatus',
            'bugsByStatus',
            'servers',
            'recentNotifications',
            'activeLog',
            'hoursWorked',
            'minutesWorked'
        ));
    }

    public function apiStats()
    {
        $stats = [
            'total_tasks' => DevTask::count(),
            'pending_tasks' => DevTask::where('status', 'pendente')->count(),
            'in_progress_tasks' => DevTask::where('status', 'em_andamento')->count(),
            'finished_tasks' => DevTask::where('status', 'finalizada')->count(),
            'total_bugs' => Bug::count(),
            'open_bugs' => Bug::where('status', 'aberto')->count(),
            'total_servers' => Server::count(),
            'online_servers' => Server::where('status', 'online')->count(),
        ];

        return response()->json($stats);
    }
}