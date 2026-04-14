<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Services\RepositoryDetector;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function index(Request $request)
    {
        $query = System::withCount('servers');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->status !== '' && $request->status !== null) {
            $query->where('active', $request->status == '1');
        }

        if ($request->language) {
            $query->where('detected_language', $request->language);
        }

        $systems = $query->orderBy('name')->paginate(20);

        return view('systems.index', compact('systems'));
    }

    public function show(System $system)
    {
        $system->load('servers', 'dependencies');

        return view('systems.show', compact('system'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:systems,slug',
            'color' => 'nullable|string|max:255',
            'repository_url' => 'nullable|string|max:500',
        ]);

        $system = System::create($validated);

        if ($system->repository_url) {
            $detector = new RepositoryDetector();
            $detected = $detector->detect($system);
            
            if ($detected['language'] || $detected['framework'] || $detected['database']) {
                $system->update([
                    'detected_language' => $detected['language'],
                    'detected_framework' => $detected['framework'],
                    'detected_database' => $detected['database'],
                    'detected_version' => $detected['version'] ?? null,
                    'detected_hosting' => $detected['hosting'] ?? null,
                    'detected_server' => $detected['server'] ?? null,
                    'auto_detected' => true,
                ]);
            }
        }

        return back()->with('success', 'Sistema criado com sucesso');
    }

    public function update(Request $request, System $system)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:systems,slug,' . $system->id,
            'color' => 'nullable|string|max:255',
            'repository_url' => 'nullable|string|max:500',
            'active' => 'nullable|boolean',
        ]);

        $system->update($validated);
        return back()->with('success', 'Sistema atualizado com sucesso');
    }

    public function destroy(System $system)
    {
        $system->delete();
        return back()->with('success', 'Sistema deletado com sucesso');
    }
}