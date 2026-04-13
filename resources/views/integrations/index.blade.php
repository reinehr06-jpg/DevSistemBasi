@extends('layouts.app-dark')

@section('title', 'Integrações')

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: white;">Integrações</h1>
            <p style="color: var(--text-muted); margin-top: 4px;">Gerencie suas conexões externas</p>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" style="background: var(--primary); color: white; padding: 12px 20px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            + Nova Integração
        </button>
    </div>
</div>

<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px;">
    <a href="{{ route('integrations.index') }}" style="padding: 8px 16px; border-radius: 8px; {{ !request('type') ? 'background: var(--primary); color: white;' : 'background: var(--bg-card); color: var(--text-gray);' }} text-decoration: none; font-size: 14px;">Todas</a>
    @foreach(['easypanel', 'ssh', 'git', 'bitbucket', 'api'] as $type)
    <a href="{{ route('integrations.index', ['type' => $type]) }}" style="padding: 8px 16px; border-radius: 8px; {{ request('type') === $type ? 'background: var(--primary); color: white;' : 'background: var(--bg-card); color: var(--text-gray);' }} text-decoration: none; font-size: 14px;">{{ ucfirst($type) }}</a>
    @endforeach
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
    @forelse($integrations as $integration)
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    @if($integration->type === 'easypanel')⚡
                    @elseif($integration->type === 'ssh')🖥️
                    @elseif($integration->type === 'git')📦
                    @elseif($integration->type === 'bitbucket')🔩
                    @else🔌
                    @endif
                </div>
                <div>
                    <h3 style="color: white; font-weight: 600;">{{ $integration->name }}</h3>
                    <p style="color: var(--text-muted); font-size: 12px;">{{ ucfirst($integration->type) }}</p>
                </div>
            </div>
            <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $integration->active ? 'var(--success)' : 'var(--text-muted)' }};"></span>
        </div>

        @if($integration->system)
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $integration->system->color }};"></span>
            <span style="color: var(--text-muted); font-size: 14px;">{{ $integration->system->name }}</span>
        </div>
        @endif

        <div style="display: flex; gap: 8px; padding-top: 16px; border-top: 1px solid var(--border-color);">
            <button onclick="testIntegration('{{ $integration->id }}')" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">Testar</button>
            <button style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px;">Editar</button>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 60px; background: var(--bg-card); border-radius: 16px;">
        <p style="font-size: 48px; margin-bottom: 16px;">🔗</p>
        <h3 style="color: white; font-size: 20px; margin-bottom: 8px;">Nenhuma integração encontrada</h3>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Comece adicionando sua primeira integração</p>
        <button onclick="document.getElementById('createModal').style.display='flex'" style="background: var(--primary); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">
            Criar Integração
        </button>
    </div>
    @endforelse
</div>

<!-- Create Modal -->
<div id="createModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="color: white; font-size: 20px; font-weight: 600;">Nova Integração</h3>
            <button onclick="document.getElementById('createModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 24px;">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('integrations.store') }}" style="padding: 20px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Sistema</label>
                <select name="system_id" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
                    <option value="">Selecione um sistema</option>
                    @foreach($systems as $system)
                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Tipo</label>
                <select name="type" id="integrationType" onchange="toggleConfigFields()" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
                    <option value="easypanel">EasyPanel</option>
                    <option value="ssh">SSH/Server</option>
                    <option value="git">Git</option>
                    <option value="bitbucket">Bitbucket</option>
                    <option value="api">API</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; color: var(--text-muted); font-size: 14px; margin-bottom: 8px;">Nome</label>
                <input type="text" name="name" placeholder="Minha Integração" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" required>
            </div>
            
            <div id="configFields">
                <div id="easypanelFields">
                    <input type="text" name="config[url]" placeholder="URL do EasyPanel" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white; margin-bottom: 8px;">
                    <input type="text" name="config[api_key]" placeholder="API Key" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; padding-top: 12px;">
                <button type="submit" style="flex: 1; background: var(--primary); color: white; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600;">Criar</button>
                <button type="button" onclick="document.getElementById('createModal').style.display='none'" style="flex: 1; background: var(--hover); color: var(--text-gray); padding: 14px; border-radius: 10px; border: none; cursor: pointer;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleConfigFields() {
    // Simplified - just show all fields for now
}
function testIntegration(id) {
    alert('Teste em développement');
}
</script>
@endsection