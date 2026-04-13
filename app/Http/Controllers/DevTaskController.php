<?php

namespace App\Http\Controllers;

use App\Models\DevTask;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = DevTask::with(['system', 'user']);

        if ($request->system_id) {
            $query->where('system_id', $request->system_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('dev-tasks.index', compact('tasks'));
    }

    public function create()
    {
        $systems = System::where('active', true)->get();
        return view('dev-tasks.create', compact('systems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'system_id' => 'required|exists:systems,id',
            'documentation' => 'required',
            'prototype_url' => 'required',
            'type' => 'required|in:front,back,ia',
            'priority' => 'required|in:baixa,media,alta,urgente',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pendente';

        DevTask::create($validated);

        return redirect()->route('dev-tasks.index')->with('success', 'Tarefa criada com sucesso!');
    }

    public function show(DevTask $devTask)
    {
        $devTask->load(['system', 'user']);
        return view('dev-tasks.show', compact('devTask'));
    }

    public function edit(DevTask $devTask)
    {
        $systems = System::where('active', true)->get();
        return view('dev-tasks.edit', compact('devTask', 'systems'));
    }

    public function update(Request $request, DevTask $devTask)
    {
        $validated = $request->validate([
            'title' => 'required',
            'system_id' => 'required|exists:systems,id',
            'documentation' => 'required',
            'prototype_url' => 'required',
            'type' => 'required|in:front,back,ia',
            'priority' => 'required|in:baixa,media,alta,urgente',
            'status' => 'required|in:pendente,em_andamento,finalizada,cancelada',
        ]);

        $oldStatus = $devTask->status;
        $devTask->update($validated);

        if ($oldStatus !== 'em_andamento' && $validated['status'] === 'em_andamento') {
            $devTask->update(['started_at' => now()]);
        }

        if ($validated['status'] === 'finalizada' && $oldStatus !== 'finalizada') {
            $devTask->update(['finished_at' => now()]);
        }

        return redirect()->route('dev-tasks.index')->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function destroy(DevTask $devTask)
    {
        $devTask->delete();
        return redirect()->route('dev-tasks.index')->with('success', 'Tarefa excluída com sucesso!');
    }
}