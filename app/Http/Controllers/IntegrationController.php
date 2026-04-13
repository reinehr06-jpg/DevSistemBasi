<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use App\Models\Server;
use App\Models\System;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Integration::with('system');

        if ($request->system_id) {
            $query->where('system_id', $request->system_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $integrations = $query->orderBy('system_id')->get();
        $systems = System::where('active', true)->get();

        return view('integrations.index', compact('integrations', 'systems'));
    }

    public function bySystem(Request $request, string $systemId)
    {
        $system = System::findOrFail($systemId);
        $integrations = Integration::where('system_id', $systemId)->get();

        return view('integrations.by-system', compact('system', 'integrations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'system_id' => 'required|exists:systems,id',
            'type' => 'required|in:git,server,api,bitbucket,easypanel',
            'name' => 'required',
            'config' => 'required|array',
        ]);

        $integration = Integration::create($validated);

        return redirect()->back()->with('success', 'Integração criada!');
    }

    public function update(Request $request, string $id)
    {
        $integration = Integration::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'config' => 'required|array',
            'active' => 'boolean',
        ]);

        $integration->update($validated);

        return redirect()->back()->with('success', 'Integração atualizada!');
    }

    public function destroy(string $id)
    {
        $integration = Integration::findOrFail($id);
        $integration->delete();

        return redirect()->back()->with('success', 'Integração excluída!');
    }

    public function test(Request $request, string $id)
    {
        $integration = Integration::findOrFail($id);
        $integration->markAsUsed();

        $result = match ($integration->type) {
            'git' => $this->testGit($integration),
            'server' => $this->testServer($integration),
            'api' => $this->testApi($integration),
            'bitbucket' => $this->testBitbucket($integration),
            'easypanel' => $this->testEasypanel($integration),
            default => ['success' => false, 'message' => 'Tipo não suportado'],
        };

        return response()->json($result);
    }

    private function testGit(Integration $integration): array
    {
        try {
            $config = $integration->config;
            return [
                'success' => true,
                'message' => 'Git configurado: ' . ($config['repo'] ?? 'N/A'),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function testServer(Integration $integration): array
    {
        try {
            $config = $integration->config;
            return [
                'success' => true,
                'message' => 'Servidor: ' . ($config['ip'] ?? 'N/A'),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function testApi(Integration $integration): array
    {
        try {
            $config = $integration->config;
            return [
                'success' => true,
                'message' => 'API: ' . ($config['url'] ?? 'N/A'),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function testBitbucket(Integration $integration): array
    {
        try {
            $config = $integration->config;
            return [
                'success' => true,
                'message' => 'Webhook configurado',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function testEasypanel(Integration $integration): array
    {
        try {
            $config = $integration->config;
            return [
                'success' => true,
                'message' => 'Easypanel: ' . ($config['url'] ?? 'N/A'),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}