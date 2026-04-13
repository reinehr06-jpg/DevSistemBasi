<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AlertRule;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $alerts = Alert::with('system')->orderBy('created_at', 'desc')->paginate(20);
        return view('alerts.index', compact('alerts'));
    }

    public function rules()
    {
        $rules = AlertRule::with('system')->get();
        return view('alerts.rules', compact('rules'));
    }

    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'system_id' => 'nullable|exists:systems,id',
            'type' => 'required|string|max:255',
            'condition' => 'required|string',
            'threshold' => 'nullable|numeric',
            'severity' => 'required|in:info,warning,error,critical,emergency',
            'notification_channels' => 'nullable|array',
            'active' => 'nullable|boolean',
        ]);

        AlertRule::create($validated);
        return back()->with('success', 'Regra criada com sucesso');
    }

    public function updateRule(Request $request, AlertRule $rule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'system_id' => 'nullable|exists:systems,id',
            'type' => 'required|string|max:255',
            'condition' => 'required|string',
            'threshold' => 'nullable|numeric',
            'severity' => 'required|in:info,warning,error,critical,emergency',
            'notification_channels' => 'nullable|array',
            'active' => 'nullable|boolean',
        ]);

        $rule->update($validated);
        return back()->with('success', 'Regra atualizada com sucesso');
    }

    public function destroyRule(AlertRule $rule)
    {
        $rule->delete();
        return back()->with('success', 'Regra deletada com sucesso');
    }

    public function acknowledge(Alert $alert)
    {
        $alert->update(['acknowledged' => true, 'acknowledged_at' => now(), 'acknowledged_by' => auth()->user()->id]);
        return back();
    }
}