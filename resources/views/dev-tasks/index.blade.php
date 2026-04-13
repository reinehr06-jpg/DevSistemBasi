@extends('layouts.app-dark')

@section('title', 'Desenvolvimento')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Tarefas de Desenvolvimento</h2>
        <a href="{{ route('dev-tasks.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
            + Nova Tarefa
        </a>
    </div>

    <div class="status-card rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Sistema</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Prioridade</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($tasks as $task)
                <tr class="hover:bg-slate-800/50">
                    <td class="px-6 py-4">
                        <span class="w-2 h-2 rounded-full inline-block mr-2" style="background-color: {{ $task->system->color }}"></span>
                        <span class="text-white">{{ $task->system->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-white">{{ $task->title }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs 
                            @if($task->type === 'front') bg-blue-500/20 text-blue-400
                            @elseif($task->type === 'back') bg-purple-500/20 text-purple-400
                            @else bg-yellow-500/20 text-yellow-400 @endif">
                            {{ ucfirst($task->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs
                            @if($task->priority === 'urgente') bg-red-500/20 text-red-400
                            @elseif($task->priority === 'alta') bg-orange-500/20 text-orange-400
                            @elseif($task->priority === 'media') bg-yellow-500/20 text-yellow-400
                            @else bg-gray-500/20 text-gray-400 @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs
                            @if($task->status === 'finalizada') bg-green-500/20 text-green-400
                            @elseif($task->status === 'em_andamento') bg-blue-500/20 text-blue-400
                            @elseif($task->status === 'cancelada') bg-red-500/20 text-red-400
                            @else bg-gray-500/20 text-gray-400 @endif">
                            {{ ucfirst($task->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('dev-tasks.show', $task) }}" class="text-gray-400 hover:text-white">Ver</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        Nenhuma tarefa encontrada
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $tasks->links() }}
    </div>
</div>
@endsection