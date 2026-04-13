@extends('layouts.app-new')

@section('title', 'Nova Tarefa')

@section('content')
<div class="max-w-2xl">
    <div class="status-card rounded-xl p-6">
        <h2 class="text-xl font-semibold text-white mb-6">Nova Tarefa de Desenvolvimento</h2>
        
        <form method="POST" action="{{ route('dev-tasks.store') }}" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Sistema</label>
                <select name="system_id" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    @foreach($systems as $system)
                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                    @endforeach
                </select>
                @error('system_id')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Título</label>
                <input type="text" name="title" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white" required>
                @error('title')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Documentação (URL)</label>
                <input type="url" name="documentation" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white" placeholder="https://..." required>
                @error('documentation')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Protótipo (URL)</label>
                <input type="url" name="prototype_url" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white" placeholder="https://..." required>
                @error('prototype_url')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Tipo</label>
                    <select name="type" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                        <option value="front">Frontend</option>
                        <option value="back">Backend</option>
                        <option value="ia">IA</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Prioridade</label>
                    <select name="priority" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                    Criar Tarefa
                </button>
                <a href="{{ route('dev-tasks.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-2 rounded-lg transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection