@extends('layouts.app-new')

@section('title', 'Reportar Bug')

@section('content')
<div class="max-w-2xl">
    <div class="status-card rounded-xl p-6">
        <h2 class="text-xl font-semibold text-white mb-6">Reportar Bug</h2>
        
        <form method="POST" action="{{ route('bugs.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Sistema</label>
                <select name="system_id" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    @foreach($systems as $system)
                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Título</label>
                <input type="text" name="title" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white" required>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Descrição</label>
                <textarea name="description" rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white"></textarea>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Imagem</label>
                <input type="file" name="image" accept="image/*" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Severidade</label>
                <select name="severity" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    <option value="baixo">Baixo</option>
                    <option value="medio">Médio</option>
                    <option value="alto">Alto</option>
                    <option value="critico">Crítico</option>
                </select>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition">
                    Reportar Bug
                </button>
                <a href="{{ route('bugs.index') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-2 rounded-lg transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection