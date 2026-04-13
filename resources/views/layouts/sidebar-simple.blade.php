<aside class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="logo-link">
            <div class="logo-icon">D</div>
            <div class="logo-text">
                <h1>DevSistem</h1>
                <p>Infrastructure</p>
            </div>
        </a>
    </div>

    <nav class="nav-section">
        <div class="nav-group">
            <p class="nav-title">Principal</p>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
        </div>

        @if(isset($systems) && $systems->count() > 0)
        <div class="nav-group">
            <p class="nav-title">Sistemas</p>
            @foreach($systems as $system)
            <div class="system-item">
                <a href="javascript:toggleSystem({{ $system->id }})" class="nav-item">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $system->color }};"></span>
                    <span>{{ $system->name }}</span>
                    <span style="margin-left: auto;">▶</span>
                </a>
                <div id="system-{{ $system->id }}" class="submenu" style="display: none; padding-left: 24px;">
                    <a href="{{ route('dev-tasks.index', ['system_id' => $system->id]) }}" class="nav-item">📂 Desenvolvimento</a>
                    <a href="{{ route('bugs.index', ['system_id' => $system->id]) }}" class="nav-item">🐞 Bugs</a>
                    <a href="{{ route('servers.by-system', $system->id) }}" class="nav-item">🖥️ Servidores</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="nav-group">
            <p class="nav-title">Deploy</p>
            <a href="{{ route('deploy.index') }}" class="nav-item {{ request()->routeIs('deploy.*') ? 'active' : '' }}">
                <span>🚀</span>
                <span>Deploy</span>
            </a>
        </div>

        <div class="nav-group">
            <a href="javascript:toggleConfig()" class="nav-item">
                <span>⚙️</span>
                <span>Configurações</span>
                <span style="margin-left: auto;">▶</span>
            </a>
            <div id="config-menu" class="submenu" style="display: none; padding-left: 12px; margin-top: 4px;">
                <div class="nav-group" style="margin-bottom: 8px;">
                    <p class="nav-title" style="margin-bottom: 8px;">Integrações</p>
                    <a href="{{ route('integrations.index') }}" class="nav-item {{ request()->routeIs('integrations.*') ? 'active' : '' }}">🔗 Integrações</a>
                    <a href="{{ route('workflows.index') }}" class="nav-item {{ request()->routeIs('workflows.*') ? 'active' : '' }}">⚡ Workflows</a>
                    <a href="{{ route('alerts.index') }}" class="nav-item {{ request()->routeIs('alerts.*') ? 'active' : '' }}">🔔 Alertas</a>
                </div>
                <div class="nav-group" style="margin-bottom: 8px;">
                    <p class="nav-title" style="margin-bottom: 8px;">Sistema</p>
                    <a href="{{ route('servers.index') }}" class="nav-item {{ request()->routeIs('servers.*') ? 'active' : '' }}">🖥️ Servidores</a>
                    <a href="{{ route('systems.index') }}" class="nav-item {{ request()->routeIs('systems.*') ? 'active' : '' }}">🧬 Novo Sistema</a>
                    <a href="{{ route('dependencies.index') }}" class="nav-item {{ request()->routeIs('dependencies.*') ? 'active' : '' }}">🕸️ Dependências</a>
                </div>
                <div class="nav-group">
                    <p class="nav-title" style="margin-bottom: 8px;">Automação</p>
                    <a href="{{ route('ai-orchestrator.index') }}" class="nav-item {{ request()->routeIs('ai-orchestrator.*') ? 'active' : '' }}">🤖 AI Orchestrator</a>
                </div>
            </div>
        </div>

        <div class="nav-group" style="margin-top: auto; padding-top: 16px;">
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-card); border-radius: 10px;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-weight: 600;">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p style="color: white; font-size: 14px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()?->name ?? 'User' }}</p>
                    <p style="color: var(--text-muted); font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()?->email ?? 'user@email.com' }}</p>
                </div>
            </div>
        </div>
    </nav>
</aside>

<script>
function toggleSystem(id) {
    var el = document.getElementById('system-' + id);
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function toggleConfig() {
    var el = document.getElementById('config-menu');
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>