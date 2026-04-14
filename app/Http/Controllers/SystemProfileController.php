<?php

namespace App\Http\Controllers;

use App\Models\SystemProfile;
use App\Models\System;
use App\Services\RepositoryDetector;
use Illuminate\Http\Request;

class SystemProfileController extends Controller
{
    public function index()
    {
        $profiles = SystemProfile::with('system')->get();
        $systems = System::where('active', true)->get();
        return view('system-profiles.index', compact('profiles', 'systems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|exists:systems,id',
            'language' => 'required|string|max:255',
            'framework' => 'nullable|string|max:255',
            'database' => 'nullable|string|max:255',
            'php_version' => 'nullable|string|max:255',
            'node_version' => 'nullable|string|max:255',
            'dependencies' => 'nullable|array',
            'auto_deploy' => 'nullable|boolean',
        ]);

        SystemProfile::create($validated);
        return back()->with('success', 'Perfil criado com sucesso');
    }

    public function detect(Request $request)
    {
        $url = $request->input('repository_url');
        
        if (!$url) {
            return response()->json(['error' => 'URL do repositório é obrigatória'], 422);
        }

        $system = System::find($request->input('system_id'));
        
        if (!$system) {
            return response()->json(['error' => 'Sistema não encontrado'], 404);
        }

        $detector = new RepositoryDetector();
        
        $system->repository_url = $url;
        $system->save();

        $detected = $detector->detect($system);

        $system->update([
            'repository_url' => $url,
            'detected_language' => $detected['language'],
            'detected_framework' => $detected['framework'],
            'detected_database' => $detected['database'],
            'detected_version' => $detected['version'],
            'detected_hosting' => $detected['hosting'],
            'detected_server' => $detected['server'],
            'auto_detected' => true,
        ]);

        return response()->json([
            'success' => true,
            'detected' => $detected,
        ]);
    }

    public function detectFromSystem(System $system)
    {
        if (!$system->repository_url) {
            return response()->json(['error' => 'Sistema não tem URL do repositório configurada'], 422);
        }

        $detector = new RepositoryDetector();
        $detected = $detector->detect($system);

        $system->update([
            'detected_language' => $detected['language'],
            'detected_framework' => $detected['framework'],
            'detected_database' => $detected['database'],
            'detected_version' => $detected['version'],
            'detected_hosting' => $detected['hosting'],
            'detected_server' => $detected['server'],
            'auto_detected' => true,
        ]);

        return response()->json([
            'success' => true,
            'detected' => $detected,
            'system' => $system->fresh(),
        ]);
    }

    public function update(Request $request, SystemProfile $profile)
    {
        $validated = $request->validate([
            'system_id' => 'required|exists:systems,id',
            'language' => 'required|string|max:255',
            'framework' => 'nullable|string|max:255',
            'database' => 'nullable|string|max:255',
            'php_version' => 'nullable|string|max:255',
            'node_version' => 'nullable|string|max:255',
            'dependencies' => 'nullable|array',
            'auto_deploy' => 'nullable|boolean',
        ]);

        $profile->update($validated);
        return back()->with('success', 'Perfil atualizado com sucesso');
    }

    public function destroy(SystemProfile $profile)
    {
        $profile->delete();
        return back()->with('success', 'Perfil deletado com sucesso');
    }
}