@extends('layouts.app-dark')

@section('title', $system->name)

@section('content')
<div style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <span style="width: 48px; height: 48px; border-radius: 12px; background: {{ $system->color }};"></span>
            <div>
                <h1 style="font-size: 24px; font-weight: 700; color: white;">{{ $system->name }}</h1>
                <p style="color: var(--text-muted);">/{{ $system->slug }}</p>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <span class="{{ $system->active ? 'badge-online' : 'badge-offline' }}">{{ $system->active ? 'Ativo' : 'Inativo' }}</span>
            <button onclick="openEditModal()" style="background: var(--primary); color: white; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer;">Editar</button>
        </div>
    </div>
</div>

<div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
    <button onclick="showTab('dna')" class="tab-btn active" data-tab="dna" style="background: none; border: none; color: white; padding: 8px 16px; cursor: pointer; border-bottom: 2px solid var(--primary);">🧬 DNA</button>
    <button onclick="showTab('ambientes')" class="tab-btn" data-tab="ambientes" style="background: none; border: none; color: var(--text-muted); padding: 8px 16px; cursor: pointer;">🌍 Ambientes</button>
    <button onclick="showTab('deps')" class="tab-btn" data-tab="deps" style="background: none; border: none; color: var(--text-muted); padding: 8px 16px; cursor: pointer;">🔗 Dependências</button>
</div>

<div id="tab-dna" class="tab-content">
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
            <p style="color: var(--text-muted); font-size: 12px;">Linguagem</p>
            <p style="color: white; font-size: 18px; font-weight: 600;">{{ $system->detected_language ?? '-' }}</p>
        </div>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
            <p style="color: var(--text-muted); font-size: 12px;">Framework</p>
            <p style="color: white; font-size: 18px; font-weight: 600;">{{ $system->detected_framework ?? '-' }}</p>
        </div>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
            <p style="color: var(--text-muted); font-size: 12px;">Versão</p>
            <p style="color: white; font-size: 18px; font-weight: 600;">{{ $system->detected_version ?? '-' }}</p>
        </div>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 16px;">
        <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 8px;">Repositório Git</p>
        <p style="color: var(--primary); word-break: break-all;">{{ $system->repository_url ?? 'Não configurado' }}</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
            <p style="color: var(--text-muted); font-size: 12px;">Banco de Dados</p>
            <p style="color: white; font-size: 16px;">{{ $system->detected_database ?? '-' }}</p>
        </div>
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
            <p style="color: var(--text-muted); font-size: 12px;">Hospedagem</p>
            <p style="color: white; font-size: 16px;">{{ $system->detected_hosting ?? '-' }}</p>
        </div>
    </div>
</div>

<div id="tab-ambientes" class="tab-content" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="color: white; font-size: 16px;">Ambientes</h3>
        <button onclick="openEnvModal()" style="background: var(--success); color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer;">+ Novo</button>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        @foreach(['dev' => 'Desenvolvimento', 'staging' => 'Homologação', 'prod' => 'Produção'] as $env => $label)
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="color: white; font-weight: 600;">{{ $env }}</span>
                <span class="badge-online">Ativo</span>
            </div>
            <p style="color: var(--text-muted); font-size: 12px;">{{ $label }}</p>
            <div style="margin-top: 12px; display: flex; gap: 8px;">
                <button style="background: var(--hover); color: var(--text-gray); padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px;">Editar</button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="tab-deps" class="tab-content" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 style="color: white; font-size: 16px;">Dependências</h3>
        <a href="{{ route('dependencies.index', ['system_id' => $system->id]) }}" style="background: var(--success); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none;">+ Nova</a>
    </div>

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 12px; text-align: left; color: var(--text-muted); font-size: 12px;">Serviço</th>
                    <th style="padding: 12px; text-align: left; color: var(--text-muted); font-size: 12px;">Tipo</th>
                    <th style="padding: 12px; text-align: center; color: var(--text-muted); font-size: 12px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($system->dependencies ?? [] as $dep)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 12px; color: white;">{{ $dep->name }}</td>
                    <td style="padding: 12px; color: var(--text-muted);">{{ $dep->type }}</td>
                    <td style="padding: 12px; text-align: center;"><span class="badge-online">Ativo</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding: 40px; text-align: center; color: var(--text-muted);">Nenhuma dependência</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.badge-online { background: rgba(34,197,94,0.15); color: #22c55e; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
.badge-offline { background: rgba(239,68,68,0.15); color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 12px; }
</style>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.style.borderBottom = 'none';
        el.style.color = 'var(--text-muted)';
    });
    
    document.getElementById('tab-' + tab).style.display = 'block';
    document.querySelector('[data-tab="' + tab + '"]').style.borderBottom = '2px solid var(--primary)';
    document.querySelector('[data-tab="' + tab + '"]').style.color = 'white';
}

function openEnvModal() {
    alert('Funcionalidade em desenvolvimento');
}
</script>
@endsection