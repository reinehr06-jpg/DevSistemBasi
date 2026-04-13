@extends('layouts.app-dark')

@section('title', 'Agentes IA')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Agentes de IA</h2>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
            + Novo Agente
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($agents as $agent)
        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="px-2 py-1 rounded text-xs
                    @if($agent->type === 'openai') bg-yellow-500/20 text-yellow-400
                    @elseif($agent->type === 'anthropic') bg-purple-500/20 text-purple-400
                    @elseif($agent->type === 'ollama') bg-blue-500/20 text-blue-400
                    @else bg-gray-500/20 text-gray-400 @endif">
                    {{ strtoupper($agent->type) }}
                </span>
                <span class="w-2 h-2 rounded-full {{ $agent->is_active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
            </div>
            
            <h3 class="text-lg font-semibold text-white">{{ $agent->name }}</h3>
            <p class="text-gray-400 text-sm mb-2">{{ $agent->role }}</p>
            
            @if($agent->model)
            <div class="text-xs text-gray-500 mb-3">Model: {{ $agent->model }}</div>
            @endif
            
            <div class="text-xs text-gray-500 mb-3">
                Timeout: {{ $agent->timeout_seconds }}s | Max Tokens: {{ $agent->max_tokens }}
            </div>
            
            <div class="flex gap-2 mt-3">
                <button onclick="editAgent({{ $agent }})" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                    Editar
                </button>
                <form method="POST" action="{{ route('ai-orchestrator.agents.toggle', $agent) }}">
                    @csrf
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                        {{ $agent->is_active ? 'Desativar' : 'Ativar' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('ai-orchestrator.agents.destroy', $agent) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 py-1 px-3 rounded text-sm">
                        Excluir
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-8 text-gray-400">
            Nenhum agente configurado
        </div>
        @endforelse
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-white mb-4">Novo Agente IA</h3>
        
        <form method="POST" action="{{ route('ai-orchestrator.agents.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Nome</label>
                <input type="text" name="name" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Tipo</label>
                    <select name="type" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Role</label>
                    <select name="role" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        @foreach($roles as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Endpoint (URL)</label>
                    <input type="url" name="endpoint" placeholder="http://localhost:11434" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Model</label>
                    <input type="text" name="model" placeholder="llama3, gpt-4, etc" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Prompt do Agente</label>
                <textarea name="prompt" rows="6" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white text-sm" placeholder="Cole o prompt aqui..."></textarea>
                <p class="text-xs text-gray-500 mt-1">Se vazio, usará o prompt padrão baseado no role</p>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Timeout (segundos)</label>
                    <input type="number" name="timeout_seconds" value="30" min="5" max="300" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Max Tokens</label>
                    <input type="number" name="max_tokens" value="4000" min="100" max="32000" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition">
                    Criar
                </button>
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white py-2 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection