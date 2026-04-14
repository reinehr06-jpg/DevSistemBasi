@extends('layouts.app-dark')

@section('title', 'Desenvolvedores')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Desenvolvedores</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Gerencie a equipe de desenvolvimento</p>
        </div>
        <a href="{{ route('developers.create') }}" style="background: var(--success); color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-decoration: none;">
            + Novo Desenvolvedor
        </a>
    </div>
</div>

<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('developers.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Buscar por nome ou email..." value="{{ request('search') }}" style="flex: 1; min-width: 200px; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
        <select name="cargo" style="padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            <option value="">Todos os Cargos</option>
            <option value="Junior" {{ request('cargo') == 'Junior' ? 'selected' : '' }}>Junior</option>
            <option value="Pleno" {{ request('cargo') == 'Pleno' ? 'selected' : '' }}>Pleno</option>
            <option value="Senior" {{ request('cargo') == 'Senior' ? 'selected' : '' }}>Senior</option>
            <option value="Lead" {{ request('cargo') == 'Lead' ? 'selected' : '' }}>Lead</option>
        </select>
        <select name="status" style="padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            <option value="">Todos</option>
            <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
        <button type="submit" style="background: var(--primary); color: white; padding: 12px 20px; border-radius: 8px; border: none; cursor: pointer;">Filtrar</button>
    </form>
</div>

<div style="overflow-x: auto; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <th style="padding: 16px; text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 600;">Nome</th>
                <th style="padding: 16px; text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 600;">Cargo</th>
                <th style="padding: 16px; text-align: left; color: var(--text-muted); font-size: 12px; font-weight: 600;">Stack</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Status</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Score</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Tasks</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Bugs</th>
                <th style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 600;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($developers as $dev)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                            {{ substr($dev->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p style="color: white; font-weight: 500;">{{ $dev->user->name }}</p>
                            <p style="color: var(--text-muted); font-size: 12px;">{{ $dev->user->email }}</p>
                        </div>
                    </div>
                </td>
                <td style="padding: 16px;">
                    <span style="background: var(--primary); padding: 4px 12px; border-radius: 6px; font-size: 12px; color: white;">{{ $dev->cargo }}</span>
                </td>
                <td style="padding: 16px;">
                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                        @foreach(array_slice($dev->stack_primary ?? [], 0, 2) as $stack)
                        <span style="background: var(--bg-surface); padding: 2px 8px; border-radius: 4px; font-size: 11px; color: var(--text-gray);">{{ $stack }}</span>
                        @endforeach
                    </div>
                </td>
                <td style="padding: 16px; text-align: center;">
                    <span class="{{ $dev->active ? 'badge-online' : 'badge-offline' }}" style="font-size: 12px;">
                        {{ $dev->active ? 'Ativo' : 'Inativo' }}
                    </span>
                </td>
                <td style="padding: 16px; text-align: center;">
                    <span style="color: white; font-weight: 600;">{{ $dev->score }}</span>
                </td>
                <td style="padding: 16px; text-align: center;">
                    <span style="color: var(--success);">{{ $dev->tasks_completed }}</span>
                </td>
                <td style="padding: 16px; text-align: center;">
                    <span style="color: #ef4444;">{{ $dev->bugs_created }}</span>
                </td>
                <td style="padding: 16px; text-align: center;">
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <a href="{{ route('developers.show', $dev->id) }}" style="background: var(--hover); color: var(--text-gray); padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">Ver</a>
                        <a href="{{ route('developers.edit', $dev->id) }}" style="background: var(--primary); color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">Editar</a>
                        <button onclick="deleteDeveloper({{ $dev->id }})" style="background: #ef4444; color: white; padding: 8px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px;">✕</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">Nenhum desenvolvedor encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 20px; display: flex; justify-content: center;">
    {{ $developers->links() }}
</div>

<style>
.badge-online { background: rgba(34,197,94,0.15); color: #22c55e; padding: 4px 10px; border-radius: 6px; }
.badge-offline { background: rgba(239,68,68,0.15); color: #ef4444; padding: 4px 10px; border-radius: 6px; }
</style>

<script>
function deleteDeveloper(id) {
    if (!confirm('Tem certeza que deseja desativar este desenvolvedor?')) return;
    
    fetch('/developers/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success || 'Desenvolvedor desativado');
        location.reload();
    })
    .catch(e => alert('Erro: ' + e.message));
}
</script>
@endsection