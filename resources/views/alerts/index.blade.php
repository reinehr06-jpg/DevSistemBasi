@extends('layouts.app-dark')

@section('title', 'Alertas')
@section('breadcrumb')
<span class="text-slate-500">/</span>
<span class="text-slate-400">Alertas</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Alertas</h1>
            <p class="text-slate-400 mt-1">Monitoramento e notificações</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('alerts.rules') }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Regras
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="premium-card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center">
                <span class="text-xl">🚨</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ \App\Models\Alert::where('status', 'triggered')->count() }}</p>
                <p class="text-slate-500 text-sm">Ativos</p>
            </div>
        </div>
        <div class="premium-card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                <span class="text-xl">👀</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ \App\Models\Alert::where('status', 'acknowledged')->count() }}</p>
                <p class="text-slate-500 text-sm">Reconhecidos</p>
            </div>
        </div>
        <div class="premium-card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                <span class="text-xl">✅</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ \App\Models\Alert::where('status', 'resolved')->count() }}</p>
                <p class="text-slate-500 text-sm">Resolvidos</p>
            </div>
        </div>
        <div class="premium-card p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center">
                <span class="text-xl">📊</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ \App\Models\Alert::count() }}</p>
                <p class="text-slate-500 text-sm">Total</p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('alerts.index') }}" class="tab-item {{ !request('status') ? 'active' : '' }}">Todos</a>
        <a href="{{ route('alerts.index', ['status' => 'triggered']) }}" class="tab-item {{ request('status') === 'triggered' ? 'active' : '' }}">
            <span class="text-red-400">●</span> Ativos
        </a>
        <a href="{{ route('alerts.index', ['status' => 'acknowledged']) }}" class="tab-item {{ request('status') === 'acknowledged' ? 'active' : '' }}">
            <span class="text-amber-400">●</span> Reconhecidos
        </a>
        <a href="{{ route('alerts.index', ['status' => 'resolved']) }}" class="tab-item {{ request('status') === 'resolved' ? 'active' : '' }}">
            <span class="text-emerald-400">●</span> Resolvidos
        </a>
    </div>

    <!-- Alerts List -->
    <div class="space-y-3">
        @forelse($alerts as $alert)
        <div class="premium-card p-4 border-l-4 
            @if($alert->severity === 'critical' || $alert->severity === 'emergency') border-l-red-500
            @elseif($alert->severity === 'warning') border-l-amber-500
            @else border-l-indigo-500 @endif">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center
                        @if($alert->severity === 'critical' || $alert->severity === 'emergency') bg-red-500/20
                        @elseif($alert->severity === 'warning') bg-amber-500/20
                        @else bg-indigo-500/20 @endif">
                        <span class="text-xl">
                            @if($alert->severity === 'critical' || $alert->severity === 'emergency') 🚨
                            @elseif($alert->severity === 'warning') ⚠️
                            @else ℹ️
                            @endif
                        </span>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">{{ $alert->title }}</h3>
                        <p class="text-slate-400 text-sm mt-1">{{ $alert->message }}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-slate-500">
                            @if($alert->system)
                            <span>🏢 {{ $alert->system->name }}</span>
                            @endif
                            @if($alert->server)
                            <span>🖥️ {{ $alert->server->name }}</span>
                            @endif
                            <span>{{ $alert->triggered_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="badge 
                        @if($alert->severity === 'critical' || $alert->severity === 'emergency') badge-danger
                        @elseif($alert->severity === 'warning') badge-warning
                        @else badge-info @endif">
                        {{ $alert->severity }}
                    </span>
                    <span class="badge 
                        @if($alert->status === 'triggered') badge-danger
                        @elseif($alert->status === 'acknowledged') badge-warning
                        @else badge-success @endif">
                        {{ $alert->status }}
                    </span>
                </div>
            </div>
            
            @if($alert->status !== 'resolved')
            <div class="flex gap-2 mt-4 pt-4 border-t border-[#1e1e2e]">
                @if($alert->status === 'triggered')
                <form method="POST" action="{{ route('alerts.acknowledge', $alert) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full btn-secondary text-sm py-2">👀 Reconhecer</button>
                </form>
                @endif
                <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full btn-primary text-sm py-2">✅ Resolver</button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="premium-card p-12 text-center">
            <span class="text-6xl mb-4 block">🔔</span>
            <h3 class="text-xl font-semibold text-white mb-2">Nenhum alerta encontrado</h3>
            <p class="text-slate-400">Tudo tranquilo!</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($alerts->hasPages())
    <div class="flex justify-center">
        {{ $alerts->links() }}
    </div>
    @endif
</div>
@endsection