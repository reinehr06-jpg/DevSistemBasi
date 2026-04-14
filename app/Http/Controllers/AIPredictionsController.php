<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Http\Request;

class AIPredictionsController extends Controller
{
    public function index(Request $request)
    {
        $systems = System::where('active', true)->get();
        
        $predictions = collect([
            ['system' => 'Sistema Exemplo', 'type' => 'cpu', 'prediction' => '85%', 'risk' => 'high', 'date' => now()->addDays(2)],
            ['system' => 'Sistema Exemplo', 'type' => 'bug', 'prediction' => '3 bugs', 'risk' => 'medium', 'date' => now()->addDays(5)],
        ]);

        return view('ai-predictions.index', compact('systems', 'predictions'));
    }

    public function config(Request $request)
    {
        $systems = System::where('active', true)->get();
        return view('ai-predictions.config', compact('systems'));
    }
}