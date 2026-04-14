@extends('layouts.app-new')

@section('title', $pipeline->name)

@section('content')
<div class="premium-bg min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $pipeline->name }}</h1>
                <p class="text-gray-400">{{ $pipeline->system->name ?? 'Sem sistema' }}</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('pipelines.run', $pipeline) }}" method="POST">
                    @csrf
                    <input type="hidden" name="environment" value="dev">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Executar Pipeline
                    </button>
                </form>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 mb-6">
            <div class="premium-card rounded-xl p-6">
                <h3 class="font-semibold text-white mb-4">Configurações</h3>
                <div class="space-y-2 text-sm text-gray-300">
                    <p><span class="font-medium">Repositório:</span> {{ $pipeline->repository_url ?? 'Não configurado' }}</p>
                    <p><span class="font-medium">Branch:</span> {{ $pipeline->deploy_branch }}</p>
                    <p><span class="font-medium">Auto-deploy:</span> {{ $pipeline->auto_deploy ? 'Sim' : 'Não' }}</p>
                    <p><span class="font-medium">IA Approval:</span> {{ $pipeline->ia_approval ? 'Sim' : 'Não' }}</p>
                </div>
            </div>

            <div class="premium-card rounded-xl p-6">
                <h3 class="font-semibold text-white mb-4">Stages</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($pipeline->stages ?? [] as $stage)
                        <span class="px-3 py-1 text-sm bg-gray-800 text-gray-300 rounded-full">
                            {{ $stage }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="premium-card rounded-xl p-6">
            <h3 class="font-semibold text-white mb-4">Execuções Recentes</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Ambiente</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Branch</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($pipeline->runs as $run)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-300">#{{ $run->id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $run->environment }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $run->status === 'success' ? 'bg-green-900 text-green-200' : 
                                           ($run->status === 'failed' ? 'bg-red-900 text-red-200' : 
                                           ($run->status === 'running' ? 'bg-blue-900 text-blue-200' : 'bg-gray-700 text-gray-300')) }}">
                                        {{ $run->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $run->branch }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $run->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('pipelines.run-detail', $run) }}" class="text-indigo-400 hover:text-indigo-300">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-400">
                                    Nenhuma execução encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection