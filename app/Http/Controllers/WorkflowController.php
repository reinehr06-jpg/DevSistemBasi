<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflows = Workflow::with('system')->orderBy('priority', 'desc')->get();
        $triggers = ['webhook' => 'Webhook', 'schedule' => 'Agendado', 'manual' => 'Manual', 'event' => 'Evento'];
        return view('workflows.index', compact('workflows', 'triggers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger' => 'required|string|max:255',
            'system_id' => 'nullable|exists:systems,id',
            'conditions' => 'nullable|array',
            'actions' => 'nullable|array',
            'priority' => 'nullable|integer|min:0|max:100',
            'active' => 'nullable|boolean',
        ]);

        Workflow::create($validated);
        return back()->with('success', 'Workflow criado com sucesso');
    }

    public function update(Request $request, Workflow $workflow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'trigger' => 'required|string|max:255',
            'system_id' => 'nullable|exists:systems,id',
            'conditions' => 'nullable|array',
            'actions' => 'nullable|array',
            'priority' => 'nullable|integer|min:0|max:100',
            'active' => 'nullable|boolean',
        ]);

        $workflow->update($validated);
        return back()->with('success', 'Workflow atualizado com sucesso');
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();
        return back()->with('success', 'Workflow deletado com sucesso');
    }

    public function toggle(Workflow $workflow)
    {
        $workflow->update(['active' => !$workflow->active]);
        return back();
    }
}