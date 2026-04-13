@extends('layouts.app-dark')

@section('title', 'Servidor - ' . $system->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full" style="background-color: {{ $system->color }}"></span>
            <h2 class="text-xl font-semibold text-white">{{ $system->name }}</h2>
        </div>
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white">← Voltar</a>
    </div>

    @forelse($servers as $server)
    <div class="status-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <span class="w-4 h-4 rounded-full {{ $server->status === 'online' ? 'bg-green-500' : ($server->status === 'offline' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                <h3 class="text-lg font-semibold text-white">{{ $server->name }}</h3>
            </div>
            <span class="text-gray-400">{{ $server->ip }}</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">CPU</p>
                <p class="text-2xl font-bold {{ $server->cpu_usage > 80 ? 'text-red-400' : 'text-white' }}">{{ $server->cpu_usage }}%</p>
            </div>
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">RAM</p>
                <p class="text-2xl font-bold {{ $server->ram_usage > 80 ? 'text-red-400' : 'text-white' }}">{{ $server->ram_usage }}%</p>
            </div>
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">Disco</p>
                <p class="text-2xl font-bold {{ $server->disk_usage > 80 ? 'text-red-400' : 'text-white' }}">{{ $server->disk_usage }}%</p>
            </div>
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">Status</p>
                <p class="text-2xl font-bold text-white capitalize">{{ $server->status }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">Branch</p>
                <p class="text-white">{{ $server->branch }}</p>
            </div>
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">Último Commit</p>
                <p class="text-white text-sm truncate">{{ $server->last_commit ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">Último Deploy</p>
                <p class="text-white">{{ $server->last_deploy?->diffForHumans() ?? 'N/A' }}</p>
            </div>
            <div class="bg-slate-800/50 rounded-lg p-4">
                <p class="text-gray-400 text-xs">Último Backup</p>
                <p class="text-white">{{ $server->last_backup?->diffForHumans() ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <form method="POST" action="{{ route('servers.backup', $server) }}">
                @csrf
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition">
                    💾 Backup
                </button>
            </form>
            <form method="POST" action="{{ route('deploy.execute', $server) }}">
                @csrf
                <input type="hidden" name="action" value="pull">
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition">
                    🚀 Deploy
                </button>
            </form>
            <form method="POST" action="{{ route('deploy.execute', $server) }}">
                @csrf
                <input type="hidden" name="action" value="restart">
                <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg transition">
                    🔄 Reiniciar
                </button>
            </form>
            <a href="{{ route('servers.show', $server) }}" class="block text-center bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg transition">
                📊 Detalhes
            </a>
        </div>
    </div>
    @empty
    <div class="status-card rounded-xl p-8 text-center">
        <p class="text-gray-400">Nenhum servidor configurado para este sistema</p>
    </div>
    @endforelse
</div>
@endsection