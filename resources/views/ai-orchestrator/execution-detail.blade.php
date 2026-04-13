@extends('layouts.app-dark')

@section('title', 'Execução #' . $execution->id)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-white">Execução #{{ $execution->id }}</h2>
            <p class="text-gray-400">{{ $execution->flow->name ?? 'Fluxo #' . $execution->flow_id }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded text-sm
                @if($execution->status === 'completed') bg-green-500/20 text-green-400
                @elseif($execution->status === 'running') bg-yellow-500/20 text-yellow-400
                @elseif($execution->status === 'failed') bg-red-500/20 text-red-400
                @else bg-gray-500/20 text-gray-400 @endif">
                {{ $execution->status }}
            </span>
            @if($execution->status === 'running')
            <form method="POST" action="{{ route('ai-orchestrator.executions.cancel', $execution) }}">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded-lg text-sm">
                    Cancelar
                </button>
            </form>
            @endif
            <a href="{{ route('ai-orchestrator.executions') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-1 rounded-lg text-sm">
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="status-card rounded-xl p-4">
            <p class="text-gray-400 text-xs">Sistema</p>
            <p class="text-white font-semibold">{{ $execution->system?->name ?? 'N/A' }}</p>
        </div>
        <div class="status-card rounded-xl p-4">
            <p class="text-gray-400 text-xs">Servidor</p>
            <p class="text-white font-semibold">{{ $execution->server?->name ?? 'N/A' }}</p>
        </div>
        <div class="status-card rounded-xl p-4">
            <p class="text-gray-400 text-xs">Duração</p>
            <p class="text-white font-semibold">{{ $execution->duration ? $execution->duration . 'ms' : 'N/A' }}</p>
        </div>
    </div>

    @if($execution->input_data)
    <div class="status-card rounded-xl p-4">
        <h3 class="text-lg font-semibold text-white mb-3">Dados de Entrada</h3>
        <pre class="text-sm text-gray-300 overflow-x-auto">{{ json_encode($execution->input_data, JSON_PRETTY_PRINT) }}</pre>
    </div>
    @endif

    @if($execution->output_data)
    <div class="status-card rounded-xl p-4">
        <h3 class="text-lg font-semibold text-white mb-3">Resultado</h3>
        <pre class="text-sm text-gray-300 overflow-x-auto">{{ json_encode($execution->output_data, JSON_PRETTY_PRINT) }}</pre>
    </div>
    @endif

    @if($execution->error_message)
    <div class="status-card rounded-xl p-4 border-l-4 border-red-500">
        <h3 class="text-lg font-semibold text-red-400 mb-3">Erro</h3>
        <pre class="text-sm text-red-300 overflow-x-auto">{{ $execution->error_message }}</pre>
    </div>
    @endif

    @if($execution->logs->count() > 0)
    <div class="status-card rounded-xl p-4">
        <h3 class="text-lg font-semibold text-white mb-3">Logs de Execução</h3>
        <div class="space-y-3">
            @foreach($execution->logs as $log)
            <div class="border-b border-slate-700 pb-3 last:border-0">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-white font-semibold">{{ $log->agent->name ?? 'Agente #' . $log->agent_id }}</span>
                    <span class="text-gray-500 text-xs">
                        @if($log->duration_ms){{ $log->duration_ms }}ms | @endif
                        {{ $log->created_at->format('H:i:s') }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-gray-500">Input:</span>
                        <pre class="text-gray-400 mt-1">{{ Str::limit($log->input ?? '', 200) }}</pre>
                    </div>
                    <div>
                        <span class="text-gray-500">Output:</span>
                        <pre class="text-gray-400 mt-1">{{ Str::limit($log->output ?? '', 200) }}</pre>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="status-card rounded-xl p-4">
        <h3 class="text-lg font-semibold text-white mb-3">Timeline</h3>
        <div class="text-sm text-gray-400">
            <p>Criado: {{ $execution->created_at }}</p>
            @if($execution->started_at)<p>Iniciado: {{ $execution->started_at }}</p>@endif
            @if($execution->finished_at)<p>Finalizado: {{ $execution->finished_at }}</p>@endif
        </div>
    </div>
</div>
@endsection