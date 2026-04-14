@extends('layouts.app-new')

@section('title', 'Criar Pipeline')

@section('content')
<div class="premium-bg min-h-screen">
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-white mb-6">Criar Pipeline</h1>

        <form action="{{ route('pipelines.store') }}" method="POST" class="premium-card rounded-xl p-6">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Sistema</label>
                <select name="system_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg text-white px-4 py-2" required>
                    <option value="">Selecione...</option>
                    @foreach($systems as $system)
                        <option value="{{ $system->id }}">{{ $system->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Nome</label>
                <input type="text" name="name" class="w-full bg-gray-800 border border-gray-700 rounded-lg text-white px-4 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Descrição</label>
                <textarea name="description" class="w-full bg-gray-800 border border-gray-700 rounded-lg text-white px-4 py-2" rows="3"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Repositório URL</label>
                <input type="url" name="repository_url" class="w-full bg-gray-800 border border-gray-700 rounded-lg text-white px-4 py-2" placeholder="https://github.com/...">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Branch</label>
                <input type="text" name="deploy_branch" class="w-full bg-gray-800 border border-gray-700 rounded-lg text-white px-4 py-2" value="main">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">Stages</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['git:fetch', 'lint', 'test', 'ia:analyze', 'deploy:dev', 'deploy:staging', 'health', 'deploy:prod'] as $stage)
                        <label class="flex items-center">
                            <input type="checkbox" name="stages[]" value="{{ $stage }}" checked class="mr-2">
                            <span class="text-sm text-gray-300">{{ $stage }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="auto_deploy" value="1" class="mr-2">
                    <span class="text-sm text-gray-300">Auto-deploy</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="ia_approval" value="1" checked class="mr-2">
                    <span class="text-sm text-gray-300">Aprovação IA</span>
                </label>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    Criar Pipeline
                </button>
                <a href="{{ route('pipelines.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection