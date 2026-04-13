@extends('layouts.app-dark')

@section('title', 'AI Orchestrator')
@section('breadcrumb')
<span class="text-slate-500">/</span>
<span class="text-slate-400">AI Orchestrator</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-purple-500/20">
                <span class="text-2xl">🤖</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">AI Orchestrator</h1>
                <p class="text-slate-400">Automação inteligente com IA</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-[#12121a] border border-[#1e1e2e]">
                <span class="w-2 h-2 rounded-full {{ $stats['connected'] ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                <span class="text-sm text-slate-400">{{ $stats['connected'] ? 'Online' : 'Offline' }}</span>
            </div>
            <button onclick="testConnection()" class="btn-secondary text-sm py-2">
                🔄 Testar
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stats-card premium-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Fluxos Ativos</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $stats['active_flows'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center">
                    <span class="text-2xl">⚡</span>
                </div>
            </div>
        </div>

        <div class="stats-card premium-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Agentes Ativos</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $stats['active_agents'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
                    <span class="text-2xl">🧠</span>
                </div>
            </div>
        </div>

        <div class="stats-card premium-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Execuções Hoje</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ $recentExecutions->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-500/20 flex items-center justify-center">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
        </div>

        <div class="stats-card premium-card p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Status</p>
                    <p class="text-3xl font-bold mt-2 {{ $stats['connected'] ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $stats['connected'] ? 'Online' : 'Offline' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $stats['connected'] ? 'bg-emerald-500/20' : 'bg-red-500/20' }} flex items-center justify-center">
                    <span class="text-2xl">{{ $stats['connected'] ? '✅' : '❌' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex gap-2 border-b border-[#1e1e2e] pb-2">
        <a href="{{ route('ai-orchestrator.index') }}" class="tab-item {{ request()->routeIs('ai-orchestrator.index') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('ai-orchestrator.agents') }}" class="tab-item {{ request()->routeIs('ai-orchestrator.agents*') ? 'active' : '' }}">Agentes</a>
        <a href="{{ route('ai-orchestrator.flows') }}" class="tab-item {{ request()->routeIs('ai-orchestrator.flows*') ? 'active' : '' }}">Fluxos</a>
        <a href="{{ route('ai-orchestrator.executions') }}" class="tab-item {{ request()->routeIs('ai-orchestrator.executions*') ? 'active' : '' }}">Execuções</a>
        <a href="{{ route('ai-orchestrator.config') }}" class="tab-item {{ request()->routeIs('ai-orchestrator.config*') ? 'active' : '' }}">Config</a>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('ai-orchestrator.agents') }}" class="premium-card p-4 hover:border-indigo-500/30 group cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center group-hover:bg-indigo-500/30 transition-colors">
                    <span class="text-xl">➕</span>
                </div>
                <span class="text-white font-medium">Novo Agente</span>
            </div>
        </a>
        
        <a href="{{ route('ai-orchestrator.flows') }}" class="premium-card p-4 hover:border-purple-500/30 group cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                    <span class="text-xl">🔄</span>
                </div>
                <span class="text-white font-medium">Novo Fluxo</span>
            </div>
        </a>
        
        <a href="{{ route('ai-orchestrator.executions') }}" class="premium-card p-4 hover:border-cyan-500/30 group cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center group-hover:bg-cyan-500/30 transition-colors">
                    <span class="text-xl">📜</span>
                </div>
                <span class="text-white font-medium">Ver Execuções</span>
            </div>
        </a>
        
        <div class="premium-card p-4 hover:border-amber-500/30 group cursor-pointer" onclick="testConnection()">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center group-hover:bg-amber-500/30 transition-colors">
                    <span class="text-xl">🔧</span>
                </div>
                <span class="text-white font-medium">Testar AI</span>
            </div>
        </div>
    </div>

    <!-- Recent Executions -->
    <div class="premium-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-white">Execuções Recentes</h3>
            <a href="{{ route('ai-orchestrator.executions') }}" class="text-sm text-indigo-400 hover:text-indigo-300">Ver todas →</a>
        </div>
        
        <div class="space-y-3">
            @forelse($recentExecutions as $execution)
            <div class="flex items-center justify-between p-4 rounded-xl bg-[#12121a] border border-[#1e1e2e] hover:border-indigo-500/30 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center
                        @if($execution->status === 'completed') bg-emerald-500/20
                        @elseif($execution->status === 'running') bg-amber-500/20 animate-pulse
                        @else bg-red-500/20 @endif">
                        <span class="text-xl">
                            @if($execution->status === 'completed') ✅
                            @elseif($execution->status === 'running') ⏳
                            @else ❌
                            @endif
                        </span>
                    </div>
                    <div>
                        <p class="text-white font-medium">{{ $execution->flow->name ?? 'Fluxo #' . $execution->flow_id }}</p>
                        <p class="text-slate-500 text-xs">{{ $execution->system?->name ?? 'Sistema #' . $execution->system_id }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="badge {{ $execution->status === 'completed' ? 'badge-success' : ($execution->status === 'running' ? 'badge-warning' : 'badge-danger') }}">
                        {{ $execution->status }}
                    </span>
                    <span class="text-slate-500 text-xs">{{ $execution->created_at->diffForHumans() }}</span>
                    <a href="{{ route('ai-orchestrator.executions.show', $execution) }}" class="text-indigo-400 hover:text-indigo-300">
                        →
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-slate-500">
                <span class="text-4xl mb-2 block">🤖</span>
                <p>Nenhuma execução recente</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Available Triggers -->
    <div class="premium-card p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Triggers Disponíveis</h3>
        <div class="flex flex-wrap gap-2">
            @foreach(['error_detected', 'alert_triggered', 'deploy_completed', 'deploy_failed', 'backup_completed', 'server_offline', 'metrics_received', 'webhook_received'] as $trigger)
            <span class="px-3 py-1.5 rounded-lg bg-[#12121a] border border-[#1e1e2e] text-sm text-slate-400">
                {{ $trigger }}
            </span>
            @endforeach
        </div>
    </div>
</div>

<script>
function testConnection() {
    fetch('{{ route("ai-orchestrator.test") }}')
        .then(res => res.json())
        .then(data => {
            alert(data.success ? '✅ Conexão OK!' : '❌ Erro: ' + data.message);
        })
        .catch(err => alert('❌ Erro de conexão'));
}
</script>
@endsection