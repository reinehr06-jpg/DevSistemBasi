<?php

namespace App\Http\Controllers;

use App\Models\WorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkLogController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkLog::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date) {
            $query->whereDate('start_time', $request->date);
        }

        $logs = $query->orderBy('start_time', 'desc')->paginate(20);

        return view('work-logs.index', compact('logs'));
    }

    public function store(Request $request)
    {
        $activeLog = WorkLog::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        if ($activeLog) {
            return redirect()->back()->with('error', 'Você já tem um expediente ativo!');
        }

        WorkLog::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Expediente iniciado!');
    }

    public function update(Request $request, WorkLog $workLog)
    {
        if ($workLog->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Você não pode encerrar o expediente de outro usuário!');
        }

        if ($workLog->end_time) {
            return redirect()->back()->with('error', 'Este expediente já foi encerrado!');
        }

        $workLog->update([
            'end_time' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')->with('success', 'Expediente encerrado!');
    }

    public function active()
    {
        $activeLog = WorkLog::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        return response()->json($activeLog);
    }
}