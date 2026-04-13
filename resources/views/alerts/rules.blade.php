@extends('layouts.app-dark')

@section('title', 'Regras de Alerta')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Regras de Alerta</h2>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
            + Nova Regra
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($rules as $rule)
        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <span class="px-2 py-1 rounded text-xs 
                        @if($rule->severity === 'critical' || $rule->severity === 'emergency') bg-red-500/20 text-red-400
                        @elseif($rule->severity === 'warning') bg-yellow-500/20 text-yellow-400
                        @else bg-blue-500/20 text-blue-400 @endif">
                        {{ $rule->severity }}
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $rule->enabled ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                </div>
                <span class="text-gray-500 text-sm">
                    {{ $rule->metric }} {{ $rule->condition }} {{ $rule->threshold }}
                </span>
            </div>
            
            <h3 class="text-lg font-semibold text-white">{{ $rule->name }}</h3>
            
            @if($rule->system)
            <div class="mt-2 text-sm text-gray-400">
                Sistema: {{ $rule->system->name }}
            </div>
            @endif
            
            @if($rule->duration_minutes > 0)
            <div class="mt-1 text-sm text-gray-500">
                Duração: {{ $rule->duration_minutes }} minutos
            </div>
            @endif
            
            <div class="mt-3 flex gap-2">
                <form method="POST" action="{{ route('alerts.rules.update', $rule) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="enabled" value="{{ $rule->enabled ? '0' : '1' }}">
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                        {{ $rule->enabled ? 'Desativar' : 'Ativar' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('alerts.rules.destroy', $rule) }}">
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
            Nenhuma regra configurada
        </div>
        @endforelse
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold text-white mb-4">Nova Regra de Alerta</h3>
        
        <form method="POST" action="{{ route('alerts.rules.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Nome</label>
                <input type="text" name="name" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Métrica</label>
                    <select name="metric" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        @foreach($metrics as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Condição</label>
                    <select name="condition" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        @foreach($conditions as $cond)
                        <option value="{{ $cond }}">{{ $cond }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Limite</label>
                    <input type="number" name="threshold" step="0.1" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Severidade</label>
                    <select name="severity" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        @foreach($severities as $sev)
                        <option value="{{ $sev }}">{{ ucfirst($sev) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Duração (minutos)</label>
                    <input type="number" name="duration_minutes" value="0" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Sistema (opcional)</label>
                <select name="system_id" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    <option value="">Todos os sistemas</option>
                    @foreach($systems as $system)
                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                    @endforeach
                </select>
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