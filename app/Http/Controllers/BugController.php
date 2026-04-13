<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BugController extends Controller
{
    public function index(Request $request)
    {
        $query = Bug::with(['system', 'user']);

        if ($request->system_id) {
            $query->where('system_id', $request->system_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->severity) {
            $query->where('severity', $request->severity);
        }

        $bugs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('bugs.index', compact('bugs'));
    }

    public function create()
    {
        $systems = System::where('active', true)->get();
        return view('bugs.create', compact('systems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'system_id' => 'required|exists:systems,id',
            'description' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'severity' => 'required|in:baixo,medio,alto,critico',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('bugs', 'public');
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'aberto';

        Bug::create($validated);

        return redirect()->route('bugs.index')->with('success', 'Bug reportado com sucesso!');
    }

    public function show(Bug $bug)
    {
        $bug->load(['system', 'user']);
        return view('bugs.show', compact('bug'));
    }

    public function edit(Bug $bug)
    {
        $systems = System::where('active', true)->get();
        return view('bugs.edit', compact('bug', 'systems'));
    }

    public function update(Request $request, Bug $bug)
    {
        $validated = $request->validate([
            'title' => 'required',
            'system_id' => 'required|exists:systems,id',
            'description' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'severity' => 'required|in:baixo,medio,alto,critico',
            'status' => 'required|in:aberto,em_andamento,resolvido,fechado',
        ]);

        if ($request->hasFile('image')) {
            if ($bug->image_path) {
                Storage::disk('public')->delete($bug->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('bugs', 'public');
        }

        $oldStatus = $bug->status;
        $bug->update($validated);

        if ($oldStatus !== 'resolvido' && $validated['status'] === 'resolvido') {
            $bug->update(['resolved_at' => now()]);
        }

        return redirect()->route('bugs.index')->with('success', 'Bug atualizado com sucesso!');
    }

    public function destroy(Bug $bug)
    {
        if ($bug->image_path) {
            Storage::disk('public')->delete($bug->image_path);
        }
        $bug->delete();
        return redirect()->route('bugs.index')->with('success', 'Bug excluído com sucesso!');
    }
}