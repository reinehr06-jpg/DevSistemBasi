<?php

namespace App\Http\Controllers;

use App\Models\ServerDependency;
use Illuminate\Http\Request;

class DependencyController extends Controller
{
    public function index()
    {
        $dependencies = ServerDependency::with(['server', 'dependency'])->get();
        return view('dependencies.index', compact('dependencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'dependency_id' => 'required|exists:servers,id',
            'type' => 'required|in:git,database,api,service',
            'status' => 'nullable|in:pending,active,inactive,error',
        ]);

        ServerDependency::create($validated);
        return back()->with('success', 'Dependência criada com sucesso');
    }

    public function update(Request $request, ServerDependency $dependency)
    {
        $validated = $request->validate([
            'server_id' => 'required|exists:servers,id',
            'dependency_id' => 'required|exists:servers,id',
            'type' => 'required|in:git,database,api,service',
            'status' => 'nullable|in:pending,active,inactive,error',
        ]);

        $dependency->update($validated);
        return back()->with('success', 'Dependência atualizada com sucesso');
    }

    public function destroy(ServerDependency $dependency)
    {
        $dependency->delete();
        return back()->with('success', 'Dependência deletada com sucesso');
    }
}