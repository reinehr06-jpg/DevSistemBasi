@extends('layouts.app-dark')

@section('title', $developer->user->name)

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 24px;">
                {{ substr($developer->user->name, 0, 1) }}
            </div>
            <div>
                <h1 style="font-size: 24px; font-weight: 700; color: white;">{{ $developer->user->name }}</h1>
                <p style="color: var(--text-muted);">{{ $developer->user->email }}</p>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('developers.edit', $developer->id) }}" style="background: var(--primary); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none;">Editar</a>
            <button onclick="resetPassword()" style="background: var(--hover); color: var(--text-gray); padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer;">Resetar Senha</button>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; text-align: center;">
        <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Score</p>
        <p style="color: white; font-size: 28px; font-weight: 700;">{{ $developer->score }}</p>
    </div>
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; text-align: center;">
        <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Tasks Concluídas</p>
        <p style="color: var(--success); font-size: 28px; font-weight: 700;">{{ $completedTasks }}</p>
    </div>
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; text-align: center;">
        <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Em Andamento</p>
        <p style="color: var(--primary); font-size: 28px; font-weight: 700;">{{ $inProgressTasks }}</p>
    </div>
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; text-align: center;">
        <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Bugs Criados</p>
        <p style="color: #ef4444; font-size: 28px; font-weight: 700;">{{ $bugsCreated }}</p>
    </div>
</div>

<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; margin-bottom: 24px;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color);">
        <h3 style="color: white; font-size: 16px; font-weight: 600;">Informações</h3>
    </div>
    <div style="padding: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
        <div>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Cargo</p>
            <p style="color: white;">{{ $developer->cargo }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Stack Principal</p>
            <p style="color: white;">{{ implode(', ', $developer->stack_primary ?? []) }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Time</p>
            <p style="color: white;">{{ $developer->team?->name ?? 'Sem time' }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Modo de Trabalho</p>
            <p style="color: white;">{{ ucfirst($developer->work_mode) }}</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Horas/Dia</p>
            <p style="color: white;">{{ $developer->hours_per_day }}h</p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 4px;">Status</p>
            <span style="color: {{ $developer->active ? '#22c55e' : '#ef4444' }};">{{ $developer->active ? 'Ativo' : 'Inativo' }}</span>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color);">
            <h3 style="color: white; font-size: 16px; font-weight: 600;">Tasks Recentes</h3>
        </div>
        <div style="padding: 12px;">
            @forelse($tasks as $task)
            <div style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                <p style="color: white; font-size: 14px;">{{ $task->title }}</p>
                <p style="color: var(--text-muted); font-size: 12px;">{{ $task->status }} • {{ $task->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p style="padding: 20px; text-align: center; color: var(--text-muted);">Nenhuma task</p>
            @endforelse
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color);">
            <h3 style="color: white; font-size: 16px; font-weight: 600;">Bugs Recentes</h3>
        </div>
        <div style="padding: 12px;">
            @forelse($bugs as $bug)
            <div style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                <p style="color: white; font-size: 14px;">{{ $bug->title }}</p>
                <p style="color: var(--text-muted); font-size: 12px;">{{ $bug->status }} • {{ $bug->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p style="padding: 20px; text-align: center; color: var(--text-muted);">Nenhum bug</p>
            @endforelse
        </div>
    </div>
</div>

<script>
function resetPassword() {
    if (!confirm('Deseja resetar a senha deste desenvolvedor?')) return;
    alert('Funcionalidade em desenvolvimento');
}
</script>
@endsection