@extends('layouts.app-dark')

@section('title', 'Bugs')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Bugs Reportados</h2>
        <a href="{{ route('bugs.create') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
            + Reportar Bug
        </a>
    </div>

    <div class="status-card rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Sistema</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Severidade</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($bugs as $bug)
                <tr class="hover:bg-slate-800/50">
                    <td class="px-6 py-4">
                        <span class="w-2 h-2 rounded-full inline-block mr-2" style="background-color: {{ $bug->system->color }}"></span>
                        <span class="text-white">{{ $bug->system->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-white">{{ $bug->title }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs
                            @if($bug->severity === 'critico') bg-red-500/20 text-red-400
                            @elseif($bug->severity === 'alto') bg-orange-500/20 text-orange-400
                            @elseif($bug->severity === 'medio') bg-yellow-500/20 text-yellow-400
                            @else bg-gray-500/20 text-gray-400 @endif">
                            {{ ucfirst($bug->severity) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs
                            @if($bug->status === 'resolvido') bg-green-500/20 text-green-400
                            @elseif($bug->status === 'fechado') bg-gray-500/20 text-gray-400
                            @elseif($bug->status === 'em_andamento') bg-blue-500/20 text-blue-400
                            @else bg-red-500/20 text-red-400 @endif">
                            {{ ucfirst($bug->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('bugs.show', $bug) }}" class="text-gray-400 hover:text-white">Ver</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        Nenhum bug encontrado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $bugs->links() }}
    </div>
</div>
@endsection