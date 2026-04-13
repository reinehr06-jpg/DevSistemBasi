@extends('layouts.app-dark')

@section('title', 'Workflows')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Workflows</h2>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
            + Novo Workflow
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($workflows as $workflow)
        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded text-xs bg-blue-500/20 text-blue-400">
                        {{ $workflow->trigger }}
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $workflow->active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                </div>
                <span class="text-gray-500 text-sm">Prioridade: {{ $workflow->priority }}</span>
            </div>
            
            <h3 class="text-lg font-semibold text-white">{{ $workflow->name }}</h3>
            
            @if($workflow->conditions)
            <div class="mt-2 text-sm text-gray-400">
                <strong>Condições:</strong> {{ json_encode($workflow->conditions) }}
            </div>
            @endif
            
            @if($workflow->actions)
            <div class="mt-2 text-sm text-gray-400">
                <strong>Ações:</strong> {{ implode(', ', array_column($workflow->actions, 'type')) }}
            </div>
            @endif
            
            @if($workflow->last_run_at)
            <div class="mt-2 text-xs text-gray-500">
                Última execução: {{ $workflow->last_run_at->diffForHumans() }}
            </div>
            @endif
            
            <div class="mt-3 flex gap-2">
                <form method="POST" action="{{ route('workflows.test', $workflow) }}">
                    @csrf
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                        Testar
                    </button>
                </form>
                <form method="POST" action="{{ route('workflows.update', $workflow) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active" value="{{ $workflow->active ? '0' : '1' }}">
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                        {{ $workflow->active ? 'Desativar' : 'Ativar' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('workflows.destroy', $workflow) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 py-1 px-3 rounded text-sm">
                        Excluir
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            Nenhum workflow configurado
        </div>
        @endforelse
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold text-white mb-4">Novo Workflow</h3>
        
        <form method="POST" action="{{ route('workflows.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Nome</label>
                <input type="text" name="name" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Trigger</label>
                <select name="trigger" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    @foreach($triggers as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Condições (JSON)</label>
                <textarea name="conditions" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white text-sm" rows="2" placeholder='{"system": "basileia_finance"}'></textarea>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Ações (JSON)</label>
                <textarea name="actions" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white text-sm" rows="3" placeholder='[{"type": "deploy", "config": {}}, {"type": "backup", "config": {}}]'></textarea>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Prioridade</label>
                <input type="number" name="priority" value="0" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
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