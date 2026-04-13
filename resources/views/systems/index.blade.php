@extends('layouts.app-dark')

@section('title', 'Sistemas')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Sistemas</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Gerencie seus sistemas e repositórios</p>
        </div>
        <button onclick="openCreateModal()" style="background: var(--success); color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            + Novo Sistema
        </button>
    </div>
</div>

<div style="grid: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; display: grid;">
    @forelse($systems as $system)
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="width: 16px; height: 16px; border-radius: 50%; background: {{ $system->color }};"></span>
                <div>
                    <h3 style="color: white; font-size: 18px; font-weight: 600;">{{ $system->name }}</h3>
                    <p style="color: var(--text-muted); font-size: 12px;">/{{ $system->slug }}</p>
                </div>
            </div>
            <span class="badge-{{ $system->active ? 'online' : 'offline' }} badge" style="font-size: 12px;">
                {{ $system->active ? 'Ativo' : 'Inativo' }}
            </span>
        </div>
        
        @if($system->repository_url)
        <div style="margin-bottom: 16px; padding: 12px; background: var(--bg-surface); border-radius: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <span style="font-size: 12px; color: var(--text-muted);">Repositório</span>
                @if($system->auto_detected)
                <span style="font-size: 11px; color: var(--success); background: rgba(34,197,94,0.15); padding: 2px 6px; border-radius: 4px;">✓ Auto</span>
                @endif
            </div>
            <p style="font-size: 13px; color: var(--primary); word-break: break-all;">{{ $system->repository_url }}</p>
            @if($system->detected_language || $system->detected_framework)
            <div style="display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap;">
                @if($system->detected_language)
                <span style="font-size: 11px; background: var(--primary); padding: 3px 8px; border-radius: 4px;">{{ $system->detected_language }}</span>
                @endif
                @if($system->detected_framework)
                <span style="font-size: 11px; background: #10b981; padding: 3px 8px; border-radius: 4px;">{{ $system->detected_framework }}</span>
                @endif
                @if($system->detected_database)
                <span style="font-size: 11px; background: #8b5cf6; padding: 3px 8px; border-radius: 4px;">{{ $system->detected_database }}</span>
                @endif
            </div>
            @endif
        </div>
        @else
        <div style="margin-bottom: 16px; padding: 12px; background: var(--bg-surface); border-radius: 10px; border: 1px dashed var(--border-color);">
            <p style="font-size: 12px; color: var(--text-muted);">Nenhum repositório configurado</p>
        </div>
        @endif
        
        <div style="display: flex; gap: 8px;">
            <button onclick="openEditModal({{ $system->id }}, '{{ $system->name }}', '{{ $system->slug }}', '{{ $system->color }}', '{{ $system->repository_url }}', {{ $system->active ? 'true' : 'false' }})" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">
                Editar
            </button>
            <button onclick="detectRepo({{ $system->id }})" style="flex: 1; background: var(--primary); color: white; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">
                ↻ Detectar
            </button>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: var(--bg-card); border-radius: 16px;">
        <p style="font-size: 48px; margin-bottom: 16px;">🏗️</p>
        <h3 style="color: white; font-size: 20px; margin-bottom: 8px;">Nenhum sistema encontrado</h3>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Crie seu primeiro sistema</p>
        <button onclick="openCreateModal()" style="background: var(--success); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            Criar Sistema
        </button>
    </div>
    @endforelse
</div>

<!-- Create Modal -->
<div id="createModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 450px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="color: white; font-size: 20px; font-weight: 600;">Novo Sistema</h3>
            <button onclick="closeModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 24px;">&times;</button>
        </div>
        
        <form id="systemForm" method="POST" action="{{ route('systems.store') }}" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome *</label>
                <input type="text" name="name" id="systemName" placeholder="Meu Sistema" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Slug *</label>
                <input type="text" name="slug" id="systemSlug" placeholder="meu-sistema" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Cor</label>
                <input type="color" name="color" id="systemColor" value="#6366f1" style="width: 100%; height: 45px; padding: 4px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer;">
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Repositório Git (URL)</label>
                <input type="text" name="repository_url" id="systemRepo" placeholder="https://github.com/user/repo.git" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" onpaste="setTimeout(detectRepoFromForm, 100)">
                <div id="detectResult2" style="display: none; margin-top: 8px; padding: 10px; background: var(--bg-surface); border-radius: 8px;">
                    <span style="font-size: 12px; color: var(--success);">✓ Detectado automaticamente:</span>
                    <div id="detectTags2" style="display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap;"></div>
                </div>
            </div>
            
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="active" id="systemActive" checked style="width: 18px; height: 18px;">
                <span style="color: var(--text-gray);">Sistema ativo</span>
            </label>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Criar</button>
                <button type="button" onclick="closeModal()" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
.badge-online { background: rgba(34,197,94,0.15); color: #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
.badge-offline { background: rgba(239,68,68,0.15); color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
</style>

<script>
let editingId = null;

function openCreateModal() {
    editingId = null;
    document.getElementById('modalTitle').textContent = 'Novo Sistema';
    document.getElementById('systemForm').action = '{{ route("systems.store") }}';
    document.getElementById('systemForm').innerHTML = `
        @csrf
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome *</label><input type="text" name="name" id="systemName" placeholder="Meu Sistema" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required></div>
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Slug *</label><input type="text" name="slug" id="systemSlug" placeholder="meu-sistema" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required></div>
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Cor</label><input type="color" name="color" id="systemColor" value="#6366f1" style="width: 100%; height: 45px; padding: 4px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer;"></div>
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Repositório Git (URL)</label><input type="text" name="repository_url" id="systemRepo" placeholder="https://github.com/user/repo.git" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;"></div>
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="active" id="systemActive" checked style="width: 18px; height: 18px;"><span style="color: var(--text-gray);">Sistema ativo</span></label>
        <div style="display: flex; gap: 12px;"><button type="submit" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Criar</button><button type="button" onclick="closeModal()" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button></div>
    `;
    document.getElementById('createModal').style.display = 'flex';
}

function openEditModal(id, name, slug, color, repo, active) {
    editingId = id;
    document.getElementById('modalTitle').textContent = 'Editar Sistema';
    document.getElementById('systemForm').action = '/systems/' + id;
    document.getElementById('systemForm').innerHTML = `
        @csrf
        @method('PUT')
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome *</label><input type="text" name="name" id="systemName" value="${name}" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required></div>
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Slug *</label><input type="text" name="slug" id="systemSlug" value="${slug}" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required></div>
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Cor</label><input type="color" name="color" id="systemColor" value="${color}" style="width: 100%; height: 45px; padding: 4px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer;"></div>
        <div><label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Repositório Git (URL)</label><input type="text" name="repository_url" id="systemRepo" value="${repo || ''}" placeholder="https://github.com/user/repo.git" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;"></div>
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;"><input type="checkbox" name="active" id="systemActive" ${active ? 'checked' : ''} style="width: 18px; height: 18px;"><span style="color: var(--text-gray);">Sistema ativo</span></label>
        <div style="display: flex; gap: 12px;"><button type="submit" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Salvar</button><button type="button" onclick="closeModal()" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button></div>
    `;
    document.getElementById('createModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('createModal').style.display = 'none';
}

function detectRepo(systemId) {
    if (!confirm('Deseja detectar as configurações do repositório automaticamente?')) return;
    
    fetch('/systems/' + systemId + '/detect', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Detectado: ' + [data.detected.language, data.detected.framework, data.detected.database].filter(Boolean).join(', '));
            location.reload();
        } else {
            alert(data.error || 'Erro ao detectar');
        }
    })
    .catch(e => alert('Erro: ' + e.message));
}

function detectRepoFromForm() {
    const repoUrl = document.getElementById('systemRepo').value;
    if (!repoUrl || repoUrl.length < 10) return;
    
    const systemId = editingId || 1;
    
    fetch('{{ route("system-profiles.detect") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ system_id: systemId, repository_url: repoUrl })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.detected) {
            const result = document.getElementById('detectResult2');
            const tags = document.getElementById('detectTags2');
            result.style.display = 'block';
            tags.innerHTML = '';
            if (data.detected.language) tags.innerHTML += '<span style="background: var(--primary); padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + data.detected.language + '</span>';
            if (data.detected.framework) tags.innerHTML += '<span style="background: #10b981; padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + data.detected.framework + '</span>';
            if (data.detected.database) tags.innerHTML += '<span style="background: #8b5cf6; padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + data.detected.database + '</span>';
            if (data.detected.version) tags.innerHTML += '<span style="background: #f59e0b; padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + data.detected.version + '</span>';
        }
    })
    .catch(e => { console.log('Erro:', e); });
}

document.getElementById('systemName')?.addEventListener('input', function() {
    const slug = this.value.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-');
    const slugInput = document.getElementById('systemSlug');
    if (slugInput && !editingId) slugInput.value = slug;
});
</script>
@endsection