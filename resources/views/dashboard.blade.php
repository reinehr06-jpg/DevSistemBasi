@extends('layouts.app-dark')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Visão geral da sua infraestrutura</p>
    </div>
    <div style="color: var(--text-muted); font-size: 14px;">
        <span id="live-clock"></span>
    </div>
</div>

<div class="stats-grid" id="stats-grid">
    <div class="stat-card">
        <p class="stat-label">Tarefas</p>
        <p class="stat-value" id="stat-pending">{{ $stats['pending_tasks'] }}</p>
        <p class="stat-sub" id="stat-progress" style="color: var(--warning);">{{ $stats['in_progress_tasks'] }} em andamento</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Bugs</p>
        <p class="stat-value" id="stat-bugs">{{ $stats['total_bugs'] }}</p>
        <p class="stat-sub" id="stat-open-bugs" style="color: var(--danger);">{{ $stats['open_bugs'] }} abertos</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Servidores</p>
        <p class="stat-value"><span id="stat-online">{{ $stats['online_servers'] }}</span>/<span id="stat-total">{{ $stats['total_servers'] }}</span></p>
        <p class="stat-sub" style="color: var(--success);">online</p>
    </div>
    <div class="stat-card">
        <p class="stat-label">Finalizadas</p>
        <p class="stat-value" id="stat-finished">{{ $stats['finished_tasks'] }}</p>
        <p class="stat-sub" style="color: var(--text-muted);">concluídas</p>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="card-title" style="margin: 0;">Servidores em Tempo Real</h2>
        <span style="font-size: 12px; color: var(--success); display: flex; align-items: center; gap: 6px;">
            <span style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; animation: pulse 2s infinite;"></span>
            Ao vivo
        </span>
    </div>
    <div id="servers-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
        @forelse($servers as $server)
        <div class="server-card" data-id="{{ $server->id }}" style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; transition: all 0.3s ease;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="status-dot" style="width: 10px; height: 10px; border-radius: 50%; background: {{ $server->status === 'online' ? 'var(--success)' : ($server->status === 'offline' ? 'var(--danger)' : 'var(--warning)') }}; box-shadow: 0 0 8px {{ $server->status === 'online' ? 'var(--success)' : ($server->status === 'offline' ? 'var(--danger)' : 'var(--warning)') }};"></span>
                    <span style="color: white; font-weight: 600; font-size: 15px;">{{ $server->name }}</span>
                </div>
                <span class="badge-{{ $server->status }} badge">{{ $server->status }}</span>
            </div>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 16px;">{{ $server->ip }}</p>
            
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                    <span style="color: var(--text-muted);">CPU</span>
                    <span class="cpu-text" style="color: {{ $server->cpu_usage > 80 ? 'var(--danger)' : ($server->cpu_usage > 60 ? 'var(--warning)' : 'var(--success)') }}; font-weight: 600;">{{ $server->cpu_usage }}%</span>
                </div>
                <div class="bar-bg" style="background: var(--border-color); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div class="cpu-bar" style="height: 100%; width: {{ $server->cpu_usage }}%; background: {{ $server->cpu_usage > 80 ? 'var(--danger)' : ($server->cpu_usage > 60 ? 'var(--warning)' : 'var(--success)') }}; border-radius: 4px; transition: width 0.5s ease, background 0.3s ease;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                    <span style="color: var(--text-muted);">RAM</span>
                    <span class="ram-text" style="color: {{ $server->ram_usage > 80 ? 'var(--danger)' : ($server->ram_usage > 60 ? 'var(--warning)' : 'var(--success)') }}; font-weight: 600;">{{ $server->ram_usage }}%</span>
                </div>
                <div class="bar-bg" style="background: var(--border-color); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div class="ram-bar" style="height: 100%; width: {{ $server->ram_usage }}%; background: {{ $server->ram_usage > 80 ? 'var(--danger)' : ($server->ram_usage > 60 ? 'var(--warning)' : 'var(--success)') }}; border-radius: 4px; transition: width 0.5s ease, background 0.3s ease;"></div>
                </div>
            </div>
            
            @if($server->disk_usage)
            <div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px;">
                    <span style="color: var(--text-muted);">DISK</span>
                    <span class="disk-text" style="color: {{ $server->disk_usage > 90 ? 'var(--danger)' : ($server->disk_usage > 75 ? 'var(--warning)' : 'var(--success)') }}; font-weight: 600;">{{ $server->disk_usage }}%</span>
                </div>
                <div class="bar-bg" style="background: var(--border-color); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div class="disk-bar" style="height: 100%; width: {{ $server->disk_usage }}%; background: {{ $server->disk_usage > 90 ? 'var(--danger)' : ($server->disk_usage > 75 ? 'var(--warning)' : 'var(--success)') }}; border-radius: 4px; transition: width 0.5s ease, background 0.3s ease;"></div>
                </div>
            </div>
            @endif
            
            @if($server->system)
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-color); font-size: 12px;">
                <span style="width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; background: {{ $server->system->color }};"></span>
                {{ $server->system->name }}
            </div>
            @endif
        </div>
        @empty
        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">
            <p style="font-size: 32px; margin-bottom: 12px;">🖥️</p>
            <p>Nenhum servidor configurado</p>
            <a href="{{ route('servers.index') }}" style="color: var(--primary); font-size: 14px; margin-top: 8px; display: inline-block;">Adicionar servidor →</a>
        </div>
        @endforelse
    </div>
</div>

<div class="card">
    <h2 class="card-title">Atividade Recente</h2>
    <div id="notifications-list">
        @forelse($recentNotifications as $notification)
        <div class="notification-item" style="padding: 12px 0; border-bottom: 1px solid var(--border-color); animation: fadeIn 0.3s ease;">
            <p style="color: white; font-size: 14px;">{{ $notification->message }}</p>
            <p style="color: var(--text-muted); font-size: 12px; margin-top: 4px;">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
        @empty
        <p style="color: var(--text-muted); text-align: center; padding: 20px;">Nenhuma atividade recente</p>
        @endforelse
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes valueChange {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); color: var(--primary); }
    100% { transform: scale(1); }
}
.value-changed {
    animation: valueChange 0.3s ease;
}
.status-dot {
    animation: pulse 2s infinite;
}
.server-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
</style>

<script>
let previousStats = {
    pending: {{ $stats['pending_tasks'] }},
    bugs: {{ $stats['total_bugs'] }},
    openBugs: {{ $stats['open_bugs'] }},
    online: {{ $stats['online_servers'] }},
    finished: {{ $stats['finished_tasks'] }}
};

let previousServers = {};
@foreach($servers as $server)
previousServers[{{ $server->id }}] = { cpu: {{ $server->cpu_usage }}, ram: {{ $server->ram_usage }}, disk: {{ $server->disk_usage ?? 0 }} };
@endforeach

function updateClock() {
    const now = new Date();
    const options = { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    document.getElementById('live-clock').textContent = now.toLocaleDateString('pt-BR', options);
}

function animateValue(element, oldVal, newVal) {
    if (oldVal !== newVal) {
        element.classList.add('value-changed');
        setTimeout(() => element.classList.remove('value-changed'), 300);
    }
    element.textContent = newVal;
}

function getColorClass(value, type) {
    if (type === 'cpu') {
        if (value > 80) return 'var(--danger)';
        if (value > 60) return 'var(--warning)';
        return 'var(--success)';
    }
    if (type === 'ram' || type === 'disk') {
        if (value > 90) return 'var(--danger)';
        if (value > 75) return 'var(--warning)';
        return 'var(--success)';
    }
    return 'var(--success)';
}

function updateDashboard() {
    fetch('{{ route("dashboard.api") }}')
        .then(response => response.json())
        .then(data => {
            animateValue(document.getElementById('stat-pending'), previousStats.pending, data.pending_tasks);
            animateValue(document.getElementById('stat-bugs'), previousStats.bugs, data.total_bugs);
            animateValue(document.getElementById('stat-open-bugs'), previousStats.open_bugs, data.open_bugs);
            animateValue(document.getElementById('stat-online'), previousStats.online, data.online_servers);
            animateValue(document.getElementById('stat-finished'), previousStats.finished, data.finished_tasks);
            
            document.getElementById('stat-progress').textContent = data.in_progress_tasks + ' em andamento';
            
            previousStats.pending = data.pending_tasks;
            previousStats.bugs = data.total_bugs;
            previousStats.openBugs = data.open_bugs;
            previousStats.online = data.online_servers;
            previousStats.finished = data.finished_tasks;
        })
        .catch(error => console.error('Error fetching stats:', error));
}

function updateServers() {
    fetch('{{ route("servers.api") }}')
        .then(response => response.json())
        .then(servers => {
            servers.forEach(server => {
                const card = document.querySelector(`.server-card[data-id="${server.id}"]`);
                if (!card) return;
                
                const cpuBar = card.querySelector('.cpu-bar');
                const cpuText = card.querySelector('.cpu-text');
                const ramBar = card.querySelector('.ram-bar');
                const ramText = card.querySelector('.ram-text');
                const diskBar = card.querySelector('.disk-bar');
                const diskText = card.querySelector('.disk-text');
                const statusDot = card.querySelector('.status-dot');
                const badge = card.querySelector('[class^="badge-"]');
                
                if (cpuBar) {
                    cpuBar.style.width = server.cpu_usage + '%';
                    cpuBar.style.background = getColorClass(server.cpu_usage, 'cpu');
                    cpuText.textContent = server.cpu_usage + '%';
                    cpuText.style.color = getColorClass(server.cpu_usage, 'cpu');
                }
                
                if (ramBar) {
                    ramBar.style.width = server.ram_usage + '%';
                    ramBar.style.background = getColorClass(server.ram_usage, 'ram');
                    ramText.textContent = server.ram_usage + '%';
                    ramText.style.color = getColorClass(server.ram_usage, 'ram');
                }
                
                if (diskBar && server.disk_usage) {
                    diskBar.style.width = server.disk_usage + '%';
                    diskBar.style.background = getColorClass(server.disk_usage, 'disk');
                    diskText.textContent = server.disk_usage + '%';
                    diskText.style.color = getColorClass(server.disk_usage, 'disk');
                }
                
                if (statusDot) {
                    const color = server.status === 'online' ? 'var(--success)' : (server.status === 'offline' ? 'var(--danger)' : 'var(--warning)');
                    statusDot.style.background = color;
                    statusDot.style.boxShadow = `0 0 8px ${color}`;
                }
                
                if (badge) {
                    badge.className = 'badge badge-' + server.status;
                    badge.textContent = server.status;
                }
            });
        })
        .catch(error => console.error('Error fetching servers:', error));
}

setInterval(updateDashboard, 5000);
setInterval(updateServers, 3000);

updateClock();
setInterval(updateClock, 1000);
</script>
@endsection