@extends('layouts.app-dark')

@section('title', 'Fluxos AI')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Fluxos de Automação AI</h2>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
            + Novo Fluxo
        </button>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($flows as $flow)
        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded text-xs bg-blue-500/20 text-blue-400">
                        {{ $flow->trigger }}
                    </span>
                    <span class="w-2 h-2 rounded-full {{ $flow->is_active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                    @if($flow->system)
                    <span class="text-gray-500 text-sm">{{ $flow->system->name }}</span>
                    @endif
                </div>
                <span class="text-gray-500 text-sm">
                    {{ $flow->steps->count() }} passos | {{ $flow->success_rate }}% sucesso
                </span>
            </div>
            
            <h3 class="text-lg font-semibold text-white">{{ $flow->name }}</h3>
            
            @if($flow->steps->count() > 0)
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($flow->steps as $step)
                <span class="px-2 py-1 bg-slate-700 rounded text-xs text-gray-400">
                    {{ $step->step_order }}. {{ $step->agent->name }} ({{ $step->agent->role }})
                </span>
                @endforeach
            </div>
            @else
            <div class="mt-3 text-sm text-yellow-400">
                ⚠️ Fluxo sem passos configurados
            </div>
            @endif
            
            <div class="mt-4 flex gap-2">
                <form method="POST" action="{{ route('ai-orchestrator.flows.run', $flow) }}">
                    @csrf
                    <button type="submit" class="bg-green-500/20 hover:bg-green-500/30 text-green-400 py-1 px-3 rounded text-sm">
                        ▶ Executar
                    </button>
                </form>
                <button onclick="addStepModal('{{ $flow->id }}')" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                    + Passo
                </button>
                <form method="POST" action="{{ route('ai-orchestrator.flows.update', $flow) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_active" value="{{ $flow->is_active ? '0' : '1' }}">
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                        {{ $flow->is_active ? 'Desativar' : 'Ativar' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('ai-orchestrator.flows.destroy', $flow) }}">
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
            Nenhum fluxo configurado
        </div>
        @endforelse
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold text-white mb-4">Novo Fluxo AI</h3>
        
        <form method="POST" action="{{ route('ai-orchestrator.flows.store') }}" class="space-y-4">
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
                <label class="block text-sm text-gray-400 mb-2">Sistema (opcional)</label>
                <select name="system_id" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    <option value="">Todos os sistemas</option>
                    @foreach($systems as $system)
                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Timeout (minutos)</label>
                <input type="number" name="timeout_minutes" value="5" min="1" max="60" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
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

<div id="stepModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl p-6 w-full max-w-lg">
        <h3 class="text-lg font-semibold text-white mb-4">Adicionar Passo</h3>
        <p class="text-gray-400 text-sm mb-4">Selecione a ordem e o agente para este passo.</p>
        
        <form method="POST" id="stepForm" class="space-y-4">
            @csrf
            
            <input type="hidden" name="flow_id" id="stepFlowId">
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Ordem do Passo</label>
                <input type="number" name="step_order" value="1" min="1" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Agente</label>
                <select name="agent_id" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
                    @foreach($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->role }})</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Se der erro</label>
                <select name="on_error" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    <option value="continue">Continuar para próximo passo</option>
                    <option value="stop">Parar execução</option>
                    <option value="retry">Tentar novamente</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition">
                    Adicionar
                </button>
                <button type="button" onclick="document.getElementById('stepModal').classList.add('hidden')" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white py-2 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addStepModal(flowId) {
    document.getElementById('stepFlowId').value = flowId;
    document.getElementById('stepForm').action = '/ai-orchestrator/flows/' + flowId + '/step';
    document.getElementById('stepModal').classList.remove('hidden');
}
</script>
@endsection