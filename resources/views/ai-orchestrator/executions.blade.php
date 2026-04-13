@extends('layouts.app-dark')

@section('title', 'Execuções AI')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Execuções de Fluxos AI</h2>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('ai-orchestrator.executions', ['status' => '']) }}" class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-green-500' : 'bg-slate-700' }} text-white">
            Todas
        </a>
        <a href="{{ route('ai-orchestrator.executions', ['status' => 'running']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'running' ? 'bg-yellow-500' : 'bg-slate-700' }} text-white">
            Executando
        </a>
        <a href="{{ route('ai-orchestrator.executions', ['status' => 'completed']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'completed' ? 'bg-green-500' : 'bg-slate-700' }} text-white">
            Concluídas
        </a>
        <a href="{{ route('ai-orchestrator.executions', ['status' => 'failed']) }}" class="px-4 py-2 rounded-lg {{ request('status') === 'failed' ? 'bg-red-500' : 'bg-slate-700' }} text-white">
            Falhou
        </a>
    </div>

    <div class="space-y-3">
        @forelse($executions as $execution)
        <div class="status-card rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full 
                        @if($execution->status === 'completed') bg-green-500
                        @elseif($execution->status === 'running') bg-yellow-500 animate-pulse
                        @elseif($execution->status === 'failed') bg-red-500
                        @else bg-gray-500 @endif"></span>
                    <div>
                        <h4 class="font-semibold text-white">{{ $execution->flow->name ?? 'Fluxo #' . $execution->flow_id }}</h4>
                        <p class="text-sm text-gray-400">
                            {{ $execution->system?->name ?? 'Sistema #' . $execution->system_id }}
                            @if($execution->server) / {{ $execution->server->name }} @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-2 py-1 rounded text-xs
                        @if($execution->status === 'completed') bg-green-500/20 text-green-400
                        @elseif($execution->status === 'running') bg-yellow-500/20 text-yellow-400
                        @elseif($execution->status === 'failed') bg-red-500/20 text-red-400
                        @else bg-gray-500/20 text-gray-400 @endif">
                        {{ $execution->status }}
                    </span>
                    @if($execution->duration)
                    <span class="text-gray-500 text-sm">{{ $execution->duration }}ms</span>
                    @endif
                    <span class="text-gray-500 text-sm">{{ $execution->created_at->diffForHumans() }}</span>
                    <a href="{{ route('ai-orchestrator.executions.show', $execution) }}" class="text-blue-400 hover:text-blue-300">
                        Ver →
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            Nenhuma execução encontrada
        </div>
        @endforelse
    </div>

    <div class="pagination">
        {{ $executions->links() }}
    </div>
</div>
@endsection