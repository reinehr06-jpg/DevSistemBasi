<?php

namespace App\Http\Controllers;

use App\Models\AIAgent;
use App\Models\AIFlow;
use App\Models\AIExecution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AIOrchestratorController extends Controller
{
    public function index()
    {
        $agents = AIAgent::all();
        $flows = AIFlow::with('agent')->get();
        $recentExecutions = AIExecution::with('flow')->orderBy('created_at', 'desc')->limit(10)->get();
        return view('ai-orchestrator.index', compact('agents', 'flows', 'recentExecutions'));
    }

    public function agents()
    {
        $agents = AIAgent::all();
        return view('ai-orchestrator.agents', compact('agents'));
    }

    public function storeAgent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:openai,anthropic,local',
            'model' => 'required|string|max:255',
            'api_key' => 'nullable|string',
            'config' => 'nullable|array',
            'active' => 'nullable|boolean',
        ]);

        AIAgent::create($validated);
        return back()->with('success', 'Agente criado com sucesso');
    }

    public function updateAgent(Request $request, AIAgent $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:openai,anthropic,local',
            'model' => 'required|string|max:255',
            'api_key' => 'nullable|string',
            'config' => 'nullable|array',
            'active' => 'nullable|boolean',
        ]);

        $agent->update($validated);
        return back()->with('success', 'Agente atualizado com sucesso');
    }

    public function destroyAgent(AIAgent $agent)
    {
        $agent->delete();
        return back()->with('success', 'Agente deletado com sucesso');
    }

    public function flows()
    {
        $flows = AIFlow::with('agent')->get();
        $agents = AIAgent::where('active', true)->get();
        return view('ai-orchestrator.flows', compact('flows', 'agents'));
    }

    public function storeFlow(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'agent_id' => 'required|exists:ai_agents,id',
            'trigger' => 'required|string|max:255',
            'prompt' => 'required|string',
            'steps' => 'nullable|array',
            'active' => 'nullable|boolean',
        ]);

        AIFlow::create($validated);
        return back()->with('success', 'Fluxo criado com sucesso');
    }

    public function updateFlow(Request $request, AIFlow $flow)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'agent_id' => 'required|exists:ai_agents,id',
            'trigger' => 'required|string|max:255',
            'prompt' => 'required|string',
            'steps' => 'nullable|array',
            'active' => 'nullable|boolean',
        ]);

        $flow->update($validated);
        return back()->with('success', 'Fluxo atualizado com sucesso');
    }

    public function destroyFlow(AIFlow $flow)
    {
        $flow->delete();
        return back()->with('success', 'Fluxo deletado com sucesso');
    }

    public function runFlow(AIFlow $flow)
    {
        $execution = AIExecution::create([
            'flow_id' => $flow->id,
            'status' => 'running',
            'input' => [],
            'output' => [],
            'started_at' => now(),
        ]);
        return back()->with('success', 'Execução #' . $execution->id . ' iniciada');
    }

    public function executions()
    {
        $executions = AIExecution::with('flow')->orderBy('created_at', 'desc')->paginate(20);
        return view('ai-orchestrator.executions', compact('executions'));
    }

    public function executionDetail(AIExecution $execution)
    {
        $execution->load(['flow', 'logs']);
        return view('ai-orchestrator.execution-detail', compact('execution'));
    }

    public function config()
    {
        return view('ai-orchestrator.config');
    }
}