@extends('layouts.app-new')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Tarefas Pendentes</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['pending_tasks'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                    <span class="text-2xl">📋</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                <span class="text-xs text-gray-400">Em andamento: {{ $stats['in_progress_tasks'] }}</span>
            </div>
        </div>

        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Bugs Abertos</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['open_bugs'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-red-500/20 flex items-center justify-center">
                    <span class="text-2xl">🐞</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-xs text-gray-400">Total: {{ $stats['total_bugs'] }}</span>
            </div>
        </div>

        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Tarefas Concluídas</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['finished_tasks'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-xs text-gray-400">Total: {{ $stats['total_tasks'] }}</span>
            </div>
        </div>

        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Servidores Online</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['online_servers'] }}/{{ $stats['total_servers'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <span class="text-2xl">🖥️</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500 status-dot"></span>
                <span class="text-xs text-gray-400">Monitoramento ativo</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 status-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white">📊 Visão Geral</h3>
                <select class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-1 text-sm text-white">
                    <option>Últimos 7 dias</option>
                    <option>Últimos 30 dias</option>
                    <option>Últimos 90 dias</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($systems as $system)
                <div class="bg-slate-800/50 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $system->color }}"></span>
                        <span class="text-xs text-gray-400 truncate">{{ $system->name }}</span>
                    </div>
                    <p class="text-xl font-bold text-white">
                        {{ \App\Models\DevTask::where('system_id', $system->id)->count() }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>

        <div class="status-card rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">⏱️ Expediente</h3>
            
            @if($activeLog)
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></span>
                    <span class="text-red-400 font-medium">Em expediente</span>
                </div>
                <p class="text-sm text-gray-400 mt-2">Iniciado às {{ $activeLog->start_time->format('H:i') }}</p>
                
                <form method="POST" action="{{ route('work-logs.update', $activeLog) }}" class="mt-3">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg transition">
                        Encerrar Expediente
                    </button>
                </form>
            </div>
            @else
            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                    <span class="text-gray-400">Fora do expediente</span>
                </div>
                
                <form method="POST" action="{{ route('work-logs.store') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition">
                        Iniciar Expediente
                    </button>
                </form>
            </div>
            @endif
            
            <div class="text-center">
                <p class="text-gray-400 text-sm">Hoje:</p>
                <p class="text-2xl font-bold text-white">{{ $hoursWorked }}h {{ $minutesWorked }}min</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="status-card rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">🖥️ Servidores</h3>
            
            <div class="space-y-3">
                @foreach($servers as $server)
                <div class="flex items-center justify-between bg-slate-800/50 rounded-lg p-3">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ $server->status === 'online' ? 'bg-green-500 status-dot' : ($server->status === 'offline' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                        <div>
                            <p class="text-white font-medium">{{ $server->name }}</p>
                            <p class="text-gray-400 text-xs">{{ $server->ip }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">CPU: {{ $server->cpu_usage }}%</p>
                        <p class="text-xs text-gray-400">RAM: {{ $server->ram_usage }}%</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="status-card rounded-xl p-6">
            <h3 class="text-lg font-semibold text-white mb-4">🔔 Notificações Recentes</h3>
            
            <div class="space-y-3">
                @forelse($recentNotifications as $notification)
                <div class="flex items-start gap-3 p-3 bg-slate-800/50 rounded-lg">
                    <span class="text-lg">
                        @if($notification->type === 'git')
                                Git
                            @elseif($notification->type === 'deploy')
                                Deploy
                            @elseif($notification->type === 'erro')
                                Erro
                            @else
                                Sistema
                            @endif
                    </span>
                    <div class="flex-1">
                        <p class="text-white text-sm">{{ $notification->title }}</p>
                        <p class="text-gray-400 text-xs">{{ $notification->message }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-center py-4">Nenhuma notificação</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection