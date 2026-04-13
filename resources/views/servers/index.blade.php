@extends('layouts.app-dark')

@section('title', 'Servidores')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Servidores</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Gerencie sua infraestrutura</p>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" style="background: var(--success); color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            + Novo Servidor
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
    @forelse($servers as $server)
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; transition: all 0.3s ease;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(168,85,247,0.2)); display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 24px;">🖥️</span>
                </div>
                <div>
                    <h3 style="color: white; font-size: 16px; font-weight: 600;">{{ $server->name }}</h3>
                    <p style="color: var(--text-muted); font-size: 12px;">{{ $server->ip }}</p>
                </div>
            </div>
            <span class="badge badge-{{ $server->status }}" style="font-size: 12px;">
                <span class="status-dot" style="width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; margin-right: 6px;"></span>
                {{ ucfirst($server->status) }}
            </span>
        </div>
        
        @if($server->system)
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $server->system->color }};"></span>
            <span style="color: var(--text-muted); font-size: 14px;">{{ $server->system->name }}</span>
        </div>
        @endif
        
        <div style="margin-bottom: 16px;">
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">CPU</span>
                    <span class="text-{{ $server->cpu_usage > 80 ? 'danger' : ($server->cpu_usage > 60 ? 'warning' : 'success') }}" style="font-weight: 600;">{{ $server->cpu_usage }}%</span>
                </div>
                <div style="background: var(--border-color); height: 6px; border-radius: 3px; overflow: hidden;">
                    <div class="bar-fill bar-{{ $server->cpu_usage > 80 ? 'high' : ($server->cpu_usage > 60 ? 'medium' : 'low') }}" style="height: 100%; width: {{ $server->cpu_usage }}%;"></div>
                </div>
            </div>
            
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">RAM</span>
                    <span class="text-{{ $server->ram_usage > 80 ? 'danger' : ($server->ram_usage > 60 ? 'warning' : 'success') }}" style="font-weight: 600;">{{ $server->ram_usage }}%</span>
                </div>
                <div style="background: var(--border-color); height: 6px; border-radius: 3px; overflow: hidden;">
                    <div class="bar-fill bar-{{ $server->ram_usage > 80 ? 'high' : ($server->ram_usage > 60 ? 'medium' : 'low') }}" style="height: 100%; width: {{ $server->ram_usage }}%;"></div>
                </div>
            </div>
            
            @if($server->disk_usage)
            <div style="margin-top: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                    <span style="color: var(--text-muted);">Disco</span>
                    <span class="text-{{ $server->disk_usage > 90 ? 'danger' : ($server->disk_usage > 75 ? 'warning' : 'success') }}" style="font-weight: 600;">{{ $server->disk_usage }}%</span>
                </div>
                <div style="background: var(--border-color); height: 6px; border-radius: 3px; overflow: hidden;">
                    <div class="bar-fill bar-{{ $server->disk_usage > 90 ? 'high' : ($server->disk_usage > 75 ? 'medium' : 'low') }}" style="height: 100%; width: {{ $server->disk_usage }}%;"></div>
                </div>
            </div>
            @endif
        </div>
        
        @if($server->branch || $server->last_deploy)
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">
            @if($server->branch)
            <div style="display: flex; align-items: center; gap: 4px;">
                <span>🌿</span>
                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $server->branch }}</span>
            </div>
            @endif
            @if($server->last_deploy)
            <div>Deploy: {{ $server->last_deploy->diffForHumans() }}</div>
            @endif
        </div>
        @endif
        
        <div style="display: flex; gap: 8px; padding-top: 16px; border-top: 1px solid var(--border-color);">
            <a href="{{ route('servers.show', $server) }}" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 10px; border-radius: 8px; text-align: center; font-size: 14px; text-decoration: none;">Detalhes</a>
            <form method="POST" action="{{ route('servers.backup', $server) }}" style="flex: 1;">
                @csrf
                <button type="submit" style="width: 100%; background: var(--hover); color: var(--text-gray); padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">💾 Backup</button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: var(--bg-card); border-radius: 16px;">
        <p style="font-size: 48px; margin-bottom: 16px;">🖥️</p>
        <h3 style="color: white; font-size: 20px; margin-bottom: 8px;">Nenhum servidor encontrado</h3>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Adicione seu primeiro servidor</p>
        <button onclick="document.getElementById('createModal').style.display='flex'" style="background: var(--success); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            Adicionar Servidor
        </button>
    </div>
    @endforelse
</div>

<!-- Create Modal -->
<div id="createModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 450px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="color: white; font-size: 20px; font-weight: 600;">Novo Servidor</h3>
            <button onclick="document.getElementById('createModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 24px;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('servers.store') }}" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Sistema</label>
                <select name="system_id" id="serverSystemSelect" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required onchange="loadSystemRepo(this.value)">
                    <option value="">Selecione um sistema</option>
                    @foreach(\App\Models\System::where('active', true)->get() as $system)
                    <option value="{{ $system->id }}" data-repo="{{ $system->repository_url }}">{{ $system->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome</label>
                <input type="text" name="name" placeholder="Production Server" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">IP</label>
                    <input type="text" name="ip" placeholder="192.168.1.100" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Usuário SSH</label>
                    <input type="text" name="ssh_user" value="root" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
                </div>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Caminho Deploy</label>
                <input type="text" name="deploy_path" placeholder="/var/www/html" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Banco (opcional)</label>
                <input type="text" name="database_name" placeholder="app_db" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Repositório Git (URL)</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="serverRepoUrl" name="repository_url" placeholder="https://github.com/user/repo.git" style="flex: 1; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                    <button type="button" onclick="detectServerRepo()" style="background: var(--primary); color: white; padding: 12px 16px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">↻</button>
                </div>
                <div id="detectResult" style="display: none; margin-top: 8px; padding: 10px; background: var(--bg-card); border-radius: 8px;">
                    <span style="font-size: 12px; color: var(--text-muted);">Detectado:</span>
                    <div id="detectTags" style="display: flex; gap: 6px; margin-top: 6px; flex-wrap: wrap;"></div>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; padding-top: 12px;">
                <button type="submit" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Criar</button>
                <button type="button" onclick="document.getElementById('createModal').style.display='none'" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
.badge-online { background: rgba(34,197,94,0.15); color: #22c55e; }
.badge-offline { background: rgba(239,68,68,0.15); color: #ef4444; }
.badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
.badge { padding: 6px 12px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; }
.status-dot { animation: pulse 2s infinite; }
.bar-fill { transition: width 0.5s ease; border-radius: 3px; }
.bar-low { background: #22c55e; }
.bar-medium { background: #f59e0b; }
.bar-high { background: #ef4444; }
.text-success { color: #22c55e; }
.text-warning { color: #f59e0b; }
.text-danger { color: #ef4444; }
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<script>
function loadSystemRepo(systemId) {
    const select = document.getElementById('serverSystemSelect');
    const option = select.options[select.selectedIndex];
    const repoUrl = option?.getAttribute('data-repo');
    document.getElementById('serverRepoUrl').value = repoUrl || '';
}

function detectServerRepo() {
    const select = document.getElementById('serverSystemSelect');
    const systemId = select?.value;
    const repoUrl = document.getElementById('serverRepoUrl').value;
    
    if (!systemId) {
        alert('Selecione um sistema primeiro');
        return;
    }
    if (!repoUrl) {
        alert('Cole a URL do repositório ou selecione um sistema com repo configurado');
        return;
    }
    
    const btn = document.querySelector('button[onclick="detectServerRepo()"]');
    btn.innerHTML = '⏳';
    
    fetch('{{ route("system-profiles.detect") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ system_id: systemId, repository_url: repoUrl })
    })
    .then(r => r.json())
    .then(data => {
        btn.innerHTML = '↻';
        if (data.success && data.detected) {
            const result = document.getElementById('detectResult');
            const tags = document.getElementById('detectTags');
            result.style.display = 'block';
            tags.innerHTML = '';
            if (data.detected.language) tags.innerHTML += '<span style="background: var(--primary); padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + data.detected.language + '</span>';
            if (data.detected.framework) tags.innerHTML += '<span style="background: #10b981; padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + data.detected.framework + '</span>';
            if (data.detected.database) tags.innerHTML += '<span style="background: #8b5cf6; padding: 4px 8px; border-radius: 4px; font-size: 11px;">' + data.detected.database + '</span>';
        }
    })
    .catch(e => {
        btn.innerHTML = '↻';
        alert('Erro: ' + e.message);
    });
}
</script>
@endsection