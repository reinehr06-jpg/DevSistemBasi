<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Models\PipelineRun;
use App\Models\Server;
use App\Services\PipelineService;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function __construct(
        protected PipelineService $pipelineService
    ) {}

    public function index(Request $request)
    {
        $pipelines = Pipeline::with(['system', 'runs' => function ($query) {
            $query->limit(5);
        }])->get();

        return view('pipelines.index', compact('pipelines'));
    }

    public function create()
    {
        $systems = \App\Models\System::where('active', true)->get();
        return view('pipelines.create', compact('systems'));
    }

    public function show(Pipeline $pipeline)
    {
        $pipeline->load(['system', 'runs.server', 'runs.user']);
        
        return view('pipelines.show', compact('pipeline'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|exists:systems,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'stages' => 'nullable|array',
            'auto_deploy' => 'boolean',
            'ia_approval' => 'boolean',
            'repository_url' => 'nullable|url',
            'deploy_branch' => 'nullable|string',
            'ia_agent' => 'nullable|string',
        ]);

        $validated['stages'] = $validated['stages'] ?? Pipeline::getDefaultStages();
        
        $pipeline = $this->pipelineService->createPipeline($validated);

        return redirect()->route('pipelines.show', $pipeline)->with('success', 'Pipeline criado!');
    }

    public function update(Request $request, Pipeline $pipeline)
    {
        $validated = $request->validate([
            'name' => 'string',
            'description' => 'nullable|string',
            'stages' => 'nullable|array',
            'auto_deploy' => 'boolean',
            'ia_approval' => 'boolean',
            'repository_url' => 'nullable|url',
            'deploy_branch' => 'nullable|string',
            'ia_agent' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $pipeline->update($validated);

        return back()->with('success', 'Pipeline atualizado!');
    }

    public function destroy(Pipeline $pipeline)
    {
        $pipeline->delete();
        return redirect()->route('pipelines.index')->with('success', 'Pipeline excluído!');
    }

    public function run(Request $request, Pipeline $pipeline)
    {
        $validated = $request->validate([
            'environment' => 'in:dev,staging,production',
            'branch' => 'nullable|string',
        ]);

        $run = $this->pipelineService->runPipeline(
            $pipeline,
            $validated['environment'] ?? 'dev',
            $validated['branch'] ?? null
        );

        return back()->with('success', 'Pipeline iniciado!', ['run_id' => $run->id]);
    }

    public function runs(Pipeline $pipeline)
    {
        $runs = $pipeline->runs()
            ->with(['server', 'user'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('pipelines.runs', compact('pipeline', 'runs'));
    }

    public function runDetail(PipelineRun $run)
    {
        $run->load(['pipeline.system', 'server', 'user']);

        return view('pipelines.run-detail', compact('run'));
    }

    public function cancel(PipelineRun $run)
    {
        if ($run->isPending() || $run->isRunning()) {
            $run->update([
                'status' => 'cancelled',
                'finished_at' => now(),
            ]);
            return back()->with('success', 'Pipeline cancelado!');
        }

        return back()->with('error', 'Não é possível cancelar este pipeline');
    }

    public function rollback(PipelineRun $run)
    {
        $success = $this->pipelineService->rollback($run);
        
        if ($success) {
            return back()->with('success', 'Rollback iniciado!');
        }

        return back()->with('error', 'Rollback não disponível');
    }

    public function api(Request $request)
    {
        $pipelines = Pipeline::with(['system', 'latestRun'])
            ->where('active', true)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'system' => $p->system->name,
                'last_run' => $p->latestRun?->only(['id', 'status', 'environment', 'created_at']),
            ]);

        return response()->json(['pipelines' => $pipelines]);
    }

    public function apiRun(Request $request, Pipeline $pipeline)
    {
        $run = $this->pipelineService->runPipeline(
            $pipeline,
            $request->environment ?? 'dev',
            $request->branch
        );

        return response()->json([
            'run_id' => $run->id,
            'status' => $run->status,
            'environment' => $run->environment,
        ]);
    }
}