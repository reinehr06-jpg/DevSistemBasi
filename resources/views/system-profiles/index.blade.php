@extends('layouts.app-dark')

@section('title', 'DNA Sistemas')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">DNA dos Sistemas</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Perfis técnicos de cada sistema</p>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" style="background: var(--success); color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            + Novo Perfil
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
    @forelse($profiles as $profile)
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="width: 12px; height: 12px; border-radius: 50%; background: {{ $profile->system->color ?? '#6b7280' }};"></span>
                <span style="color: var(--text-muted); font-size: 14px;">{{ $profile->language ?? 'N/A' }}</span>
            </div>
            @if($profile->system->auto_detected)
            <span style="font-size: 11px; color: var(--success); background: rgba(34,197,94,0.15); padding: 4px 8px; border-radius: 4px;">✓ Auto-detectado</span>
            @endif
        </div>
        <h3 style="color: white; font-size: 18px; font-weight: 600; margin-bottom: 12px;">{{ $profile->system->name }}</h3>
        
        @if($profile->system->repository_url)
        <div style="margin-bottom: 12px; padding: 8px; background: var(--bg-surface); border-radius: 8px;">
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">Repositório</p>
            <p style="font-size: 13px; color: var(--primary); word-break: break-all;">{{ $profile->system->repository_url }}</p>
        </div>
        @endif
        
        <div style="display: flex; flex-direction: column; gap: 8px; font-size: 14px;">
            @if($profile->framework)
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Framework:</span>
                <span style="color: white;">{{ $profile->framework }}</span>
            </div>
            @endif
            @if($profile->php_version)
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">PHP:</span>
                <span style="color: white;">{{ $profile->php_version }}</span>
            </div>
            @endif
            @if($profile->node_version)
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Node:</span>
                <span style="color: white;">{{ $profile->node_version }}</span>
            </div>
            @endif
            @if($profile->database)
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Banco:</span>
                <span style="color: white;">{{ $profile->database }}</span>
            </div>
            @endif
        </div>
        
        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; gap: 8px;">
            <button onclick="editProfile({{ $profile->id }})" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 8px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">Editar</button>
            <button onclick="deleteProfile({{ $profile->id }})" style="flex: 1; background: rgba(239,68,68,0.2); color: #ef4444; padding: 8px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">Excluir</button>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: var(--bg-card); border-radius: 16px;">
        <p style="font-size: 48px; margin-bottom: 16px;">🧬</p>
        <h3 style="color: white; font-size: 20px; margin-bottom: 8px;">Nenhum perfil encontrado</h3>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Crie o perfilDNA do seu primeiro sistema</p>
        <button onclick="document.getElementById('createModal').style.display='flex'" style="background: var(--success); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            Criar Perfil
        </button>
    </div>
    @endforelse
</div>

<!-- Create/Edit Modal -->
<div id="createModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalTitle" style="color: white; font-size: 20px; font-weight: 600;">Novo DNA Sistema</h3>
            <button onclick="closeModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 24px;">&times;</button>
        </div>
        
        <form id="profileForm" method="POST" action="{{ route('system-profiles.store') }}" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Sistema *</label>
                <select id="systemSelect" name="system_id" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required onchange="checkSystemRepo()">
                    <option value="">Selecione um sistema</option>
                    @foreach($systems as $system)
                    <option value="{{ $system->id }}" data-repo="{{ $system->repository_url }}">{{ $system->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div id="repoSection" style="display: none; padding: 16px; background: var(--bg-card); border-radius: 10px; border: 1px dashed var(--border-color);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <p style="color: white; font-weight: 600;">🔗 Repositório Git</p>
                    <button type="button" onclick="detectRepo()" id="detectBtn" style="background: var(--primary); color: white; padding: 8px 14px; border-radius: 8px; border: none; cursor: pointer; font-size: 13px; font-weight: 500;">
                        ↻ Detectar自动
                    </button>
                </div>
                <input type="text" id="repoUrl" name="repository_url" placeholder="https://github.com/user/repo.git" style="width: 100%; padding: 12px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 8px; color: white; font-size: 14px;">
                <div id="detectResult" style="margin-top: 12px; display: none; padding: 12px; background: var(--bg-surface); border-radius: 8px;">
                    <p style="font-size: 13px; margin-bottom: 8px;"><span style="color: var(--text-muted);">Detectado:</span></p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;" id="detectTags"></div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Linguagem *</label>
                    <select name="language" id="languageSelect" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
                        <option value="">Selecione</option>
                        <option value="PHP">PHP</option>
                        <option value="Node.js">Node.js</option>
                        <option value="Python">Python</option>
                        <option value="Go">Go</option>
                        <option value="Ruby">Ruby</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Framework</label>
                    <input type="text" name="framework" id="frameworkInput" placeholder="Laravel, Express..." style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">PHP Version</label>
                    <select name="php_version" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="">Selecione</option>
                        <option value="8.3">PHP 8.3</option>
                        <option value="8.2">PHP 8.2</option>
                        <option value="8.1">PHP 8.1</option>
                        <option value="8.0">PHP 8.0</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Node Version</label>
                    <select name="node_version" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                        <option value="">Selecione</option>
                        <option value="20.x">Node 20</option>
                        <option value="18.x">Node 18</option>
                        <option value="16.x">Node 16</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Banco de Dados</label>
                <select name="database" id="databaseSelect" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    <option value="">Selecione</option>
                    <option value="MySQL">MySQL</option>
                    <option value="PostgreSQL">PostgreSQL</option>
                    <option value="SQLite">SQLite</option>
                    <option value="MongoDB">MongoDB</option>
                    <option value="Redis">Redis</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 12px; padding-top: 12px;">
                <button type="submit" id="submitBtn" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Criar</button>
                <button type="button" onclick="closeModal()" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.detecting { animation: spin 1s linear infinite; }
</style>

<script>
function checkSystemRepo() {
    const select = document.getElementById('systemSelect');
    const option = select.options[select.selectedIndex];
    const repoUrl = option.getAttribute('data-repo');
    const repoSection = document.getElementById('repoSection');
    const repoInput = document.getElementById('repoUrl');
    
    if (option.value) {
        repoSection.style.display = 'block';
        repoInput.value = repoUrl || '';
    } else {
        repoSection.style.display = 'none';
    }
}

function detectRepo() {
    const btn = document.getElementById('detectBtn');
    const systemId = document.getElementById('systemSelect').value;
    const repoUrl = document.getElementById('repoUrl').value;
    
    if (!systemId) {
        alert('Selecione um sistema primeiro');
        return;
    }
    
    if (!repoUrl) {
        alert('Cole a URL do repositório');
        return;
    }
    
    btn.innerHTML = '⏳ Detektando...';
    btn.classList.add('detecting');
    
    fetch('{{ route("system-profiles.detect") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            system_id: systemId,
            repository_url: repoUrl
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = '✓ Detectado!';
        btn.classList.remove('detecting');
        
        if (data.detected) {
            const result = document.getElementById('detectResult');
            const tags = document.getElementById('detectTags');
            result.style.display = 'block';
            
            let html = '';
            if (data.detected.language) html += `<span style="background: var(--primary); padding: 4px 10px; border-radius: 6px; font-size: 12px;">${data.detected.language}</span>`;
            if (data.detected.framework) html += `<span style="background: #10b981; padding: 4px 10px; border-radius: 6px; font-size: 12px;">${data.detected.framework}</span>`;
            if (data.detected.database) html += `<span style="background: #8b5cf6; padding: 4px 10px; border-radius: 6px; font-size: 12px;">${data.detected.database}</span>`;
            if (data.detected.version) html += `<span style="background: #f59e0b; padding: 4px 10px; border-radius: 6px; font-size: 12px;">${data.detected.version}</span>`;
            
            tags.innerHTML = html;
            
            if (data.detected.language) document.getElementById('languageSelect').value = data.detected.language;
            if (data.detected.framework) document.getElementById('frameworkInput').value = data.detected.framework;
            if (data.detected.database) document.getElementById('databaseSelect').value = data.detected.database;
        }
    })
    .catch(error => {
        btn.innerHTML = '↻ Detectar自动';
        btn.classList.remove('detecting');
        alert('Erro ao detectar: ' + error.message);
    });
}

function closeModal() {
    document.getElementById('createModal').style.display = 'none';
}

function editProfile(id) {
    alert('Funcionalidade de edição em desenvolvimento');
}

function deleteProfile(id) {
    if (confirm('Tem certeza que deseja excluir?')) {
        fetch(`/system-profiles/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.reload());
    }
}
</script>
@endsection