@extends('layouts.app-dark')

@section('title', 'Times')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Times</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Gerencie os times da equipe</p>
        </div>
        <button onclick="openTeamModal()" style="background: var(--success); color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            + Novo Time
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
    @forelse($teams as $team)
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div>
                <h3 style="color: white; font-size: 18px; font-weight: 600;">{{ $team->name }}</h3>
                <p style="color: var(--text-muted); font-size: 12px;">{{ $team->developers_count }} membros</p>
            </div>
            <span class="{{ $team->active ? 'badge-online' : 'badge-offline' }}">{{ $team->active ? 'Ativo' : 'Inativo' }}</span>
        </div>
        
        @if($team->description)
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 16px;">{{ $team->description }}</p>
        @endif
        
        @if($team->manager)
        <div style="margin-bottom: 16px; padding: 12px; background: var(--bg-surface); border-radius: 8px;">
            <p style="color: var(--text-muted); font-size: 12px;">Manager</p>
            <p style="color: white;">{{ $team->manager->name }}</p>
        </div>
        @endif
        
        <div style="display: flex; gap: 8px;">
            <button onclick="editTeam({{ $team->id }}, '{{ $team->name }}', {{ $team->manager_id }}, '{{ $team->description }}')" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">
                Editar
            </button>
            <button onclick="deleteTeam({{ $team->id }})" style="flex: 1; background: #ef4444; color: white; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">
                ✕
            </button>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: var(--bg-card); border-radius: 16px;">
        <p style="font-size: 48px; margin-bottom: 16px;">👥</p>
        <h3 style="color: white; font-size: 20px; margin-bottom: 8px;">Nenhum time encontrado</h3>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Crie seu primeiro time</p>
    </div>
    @endforelse
</div>

<div id="teamModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 450px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="teamModalTitle" style="color: white; font-size: 20px; font-weight: 600;">Novo Time</h3>
            <button onclick="closeTeamModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 24px;">&times;</button>
        </div>
        
        <form id="teamForm" method="POST" action="{{ route('developers.teams.store') }}" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome *</label>
                <input type="text" name="name" id="teamName" required style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            </div>
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Manager</label>
                <select name="manager_id" id="teamManager" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    <option value="">Selecione</option>
                </select>
            </div>
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Descrição</label>
                <textarea name="description" id="teamDescription" rows="3" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;"></textarea>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Salvar</button>
                <button type="button" onclick="closeTeamModal()" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
.badge-online { background: rgba(34,197,94,0.15); color: #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
.badge-offline { background: rgba(239,68,68,0.15); color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
</style>

<script>
function openTeamModal() {
    document.getElementById('teamModal').style.display = 'flex';
    document.getElementById('teamModalTitle').textContent = 'Novo Time';
    document.getElementById('teamForm').action = '{{ route("developers.teams.store") }}';
    document.getElementById('teamName').value = '';
    document.getElementById('teamDescription').value = '';
}

function editTeam(id, name, managerId, description) {
    document.getElementById('teamModal').style.display = 'flex';
    document.getElementById('teamModalTitle').textContent = 'Editar Time';
    document.getElementById('teamForm').action = '/developers/teams/' + id;
    document.getElementById('teamForm').innerHTML = '@csrf @method("PUT")' +
        '<div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome *</label><input type="text" name="name" value="' + name + '" required style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;"></div>' +
        '<div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Manager</label><select name="manager_id" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;"><option value="">Selecione</option></select></div>' +
        '<div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Descrição</label><textarea name="description" rows="3" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">' + (description || '') + '</textarea></div>' +
        '<div style="display: flex; gap: 12px;"><button type="submit" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Salvar</button><button type="button" onclick="closeTeamModal()" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button></div>';
}

function closeTeamModal() {
    document.getElementById('teamModal').style.display = 'none';
}

function deleteTeam(id) {
    if (!confirm('Tem certeza que deseja excluir este time?')) return;
    fetch('/developers/teams/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => { location.reload(); })
    .catch(e => alert('Erro: ' + e.message));
}
</script>
@endsection