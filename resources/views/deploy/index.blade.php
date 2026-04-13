@extends('layouts.app-dark')

@section('title', 'Deploy')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Deploy de Projetos</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($servers as $server)
        <div class="status-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full {{ $server->status === 'online' ? 'bg-green-500 status-dot' : ($server->status === 'offline' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ $server->name }}</h3>
                        <p class="text-gray-400 text-sm">{{ $server->system->name }}</p>
                    </div>
                </div>
                <span class="text-gray-400 text-sm">{{ $server->ip }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-slate-800/50 rounded-lg p-3 text-center">
                    <p class="text-gray-400 text-xs">CPU</p>
                    <p class="text-xl font-bold text-white">{{ $server->cpu_usage }}%</p>
                </div>
                <div class="bg-slate-800/50 rounded-lg p-3 text-center">
                    <p class="text-gray-400 text-xs">RAM</p>
                    <p class="text-xl font-bold text-white">{{ $server->ram_usage }}%</p>
                </div>
            </div>

            <form method="POST" action="{{ route('deploy.execute', $server) }}" class="space-y-3">
                @csrf
                <button type="submit" name="action" value="pull" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition">
                    Git Pull
                </button>
                <button type="submit" name="action" value="migrate" class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg transition">
                    Migrate
                </button>
                <button type="submit" name="action" value="restart" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg transition">
                    Restart Queue
                </button>
                <button type="submit" name="action" value="full" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition">
                    Deploy Completo
                </button>
            </form>
        </div>
        @empty
        <div class="col-span-2 text-center py-8 text-gray-400">
            Nenhum servidor configurado
        </div>
        @endforelse
    </div>
</div>
@endsection