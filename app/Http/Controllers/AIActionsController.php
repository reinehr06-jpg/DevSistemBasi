<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Http\Request;

class AIActionsController extends Controller
{
    public function index(Request $request)
    {
        $actions = collect([
            ['name' => 'Criar task ao detectar bug', 'trigger' => 'bug_detected', 'action' => 'create_task', 'status' => 'active'],
            ['name' => 'Notificar erro crítico', 'trigger' => 'error_critical', 'action' => 'notify', 'status' => 'active'],
        ]);

        return view('ai-actions.index', compact('actions'));
    }

    public function create()
    {
        $systems = System::where('active', true)->get();
        return view('ai-actions.create', compact('systems'));
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Ação configurada');
    }

    public function logs(Request $request)
    {
        $logs = collect([]);
        return view('ai-actions.logs', compact('logs'));
    }
}
