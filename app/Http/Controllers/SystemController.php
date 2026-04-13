<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function index()
    {
        $systems = System::orderBy('name')->get();
        return view('systems.index', compact('systems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:systems,slug',
            'color' => 'nullable|string|max:255',
        ]);

        System::create($validated);
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