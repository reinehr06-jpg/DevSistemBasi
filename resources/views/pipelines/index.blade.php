@extends('layouts.app-new')

@section('title', 'Pipelines')

@section('content')
<div class="premium-bg min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Pipelines CI/CD</h1>
            <a href="{{ route('pipelines.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                + Novo Pipeline
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($pipelines as $pipeline)
                <div class="premium-card rounded-xl p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $pipeline->name }}</h3>
                            <p class="text-sm text-gray-400">{{ $pipeline->system->name ?? 'Sem sistema' }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $pipeline->active ? 'bg-green-900 text-green-200' : 'bg-gray-700 text-gray-300' }}">
                            {{ $pipeline->active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-400 mb-4">{{ $pipeline->description }}</p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($pipeline->stages ?? [] as $stage)
                            <span class="px-2 py-1 text-xs bg-gray-800 text-gray-300 rounded">
                                {{ $stage }}
                            </span>
                        @endforeach
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('pipelines.show', $pipeline) }}" class="text-indigo-400 hover:text-indigo-300">Ver</a>
                        <form action="{{ route('pipelines.run', $pipeline) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-400 hover:text-green-300 ml-4">Executar</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-400">Nenhum pipeline encontrado.</p>
                    <a href="{{ route('pipelines.create') }}" class="text-indigo-400 hover:text-indigo-300">Criar primeiro pipeline</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection