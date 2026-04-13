<aside class="sidebar fixed left-0 top-0 h-screen w-[280px] glass-sidebar z-40 flex flex-col">
    <!-- Logo Section -->
    <div class="p-6 border-b border-[#1e1e2e]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg group-hover:shadow-indigo-500/30 transition-all duration-300">
                <span class="text-white font-bold text-xl">D</span>
            </div>
            <div>
                <h1 class="text-white font-bold text-lg tracking-tight">DevSistem</h1>
                <p class="text-slate-500 text-xs">Infrastructure</p>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
        <!-- Main Menu -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Principal
            </p>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="menu-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>

        <!-- Systems Section -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Sistemas
            </p>
            <div class="space-y-1">
                @foreach($systems ?? \App\Models\System::where('active', true)->get() as $system)
                <div class="group">
                    <button onclick="toggleSystemMenu('system-{{ $system->id }}')" class="menu-item w-full justify-between group-hover:bg-[#1a1a24]">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $system->color }}"></span>
                            <span class="text-sm truncate">{{ $system->name }}</span>
                        </div>
                        <span class="text-slate-500 text-xs group-hover:text-white transition-transform" id="arrow-system-{{ $system->id }}">
                            ‣
                        </span>
                    </button>
                    <div id="system-{{ $system->id }}" class="hidden pl-8 mt-1 space-y-1">
                        <a href="{{ route('dev-tasks.index', ['system_id' => $system->id]) }}" class="menu-item text-sm py-2">
                            <span class="text-slate-500">📂</span>
                            <span>Desenvolvimento</span>
                        </a>
                        <a href="{{ route('bugs.index', ['system_id' => $system->id]) }}" class="menu-item text-sm py-2">
                            <span class="text-slate-500">🐞</span>
                            <span>Bugs</span>
                        </a>
                        <a href="{{ route('servers.by-system', $system->id) }}" class="menu-item text-sm py-2">
                            <span class="text-slate-500">🖥️</span>
                            <span>Servidores</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Integration Section -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Integrações
            </p>
            <div class="space-y-1">
                <a href="{{ route('integrations.index') }}" class="menu-item {{ request()->routeIs('integrations.*') ? 'active' : '' }}">
                    <span class="menu-icon">🔗</span>
                    <span>Integrações</span>
                </a>
                <a href="{{ route('workflows.index') }}" class="menu-item {{ request()->routeIs('workflows.*') ? 'active' : '' }}">
                    <span class="menu-icon">⚡</span>
                    <span>Workflows</span>
                </a>
                <a href="{{ route('alerts.index') }}" class="menu-item {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                    <span class="menu-icon">🔔</span>
                    <span>Alertas</span>
                </a>
            </div>
        </div>

        <!-- Settings Section -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Configurações
            </p>
            <div class="space-y-1">
                <a href="{{ route('systems.index') }}" class="menu-item {{ request()->routeIs('systems.*') ? 'active' : '' }}">
                    <span class="menu-icon">🧬</span>
                    <span>Novo Sistema</span>
                </a>
                <a href="{{ route('dependencies.index') }}" class="menu-item {{ request()->routeIs('dependencies.*') ? 'active' : '' }}">
                    <span class="menu-icon">🕸️</span>
                    <span>Dependências</span>
                </a>
                <a href="{{ route('ai-orchestrator.index') }}" class="menu-item {{ request()->routeIs('ai-orchestrator.*') ? 'active' : '' }}">
                    <span class="menu-icon">🤖</span>
                    <span>AI Orchestrator</span>
                </a>
                <a href="{{ route('dev-tasks.index') }}" class="menu-item {{ request()->routeIs('dev-tasks.*') ? 'active' : '' }}">
                    <span class="menu-icon">📋</span>
                    <span>Tarefas</span>
                </a>
                <a href="{{ route('bugs.index') }}" class="menu-item {{ request()->routeIs('bugs.*') ? 'active' : '' }}">
                    <span class="menu-icon">🐛</span>
                    <span>Bugs</span>
                </a>
                <a href="{{ route('deploy.index') }}" class="menu-item {{ request()->routeIs('deploy.*') ? 'active' : '' }}">
                    <span class="menu-icon">🚀</span>
                    <span>Deploy</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- User Section -->
    <div class="p-4 border-t border-[#1e1e2e]">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#16161f] hover:bg-[#1a1a24] transition-colors cursor-pointer">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                <span class="text-white font-semibold">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ auth()->user()?->name ?? 'User' }}</p>
                <p class="text-slate-500 text-xs truncate">{{ auth()->user()?->email ?? 'user@email.com' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-2 rounded-lg hover:bg-slate-700 text-slate-400 hover:text-white transition-colors" title="Sair">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    function toggleSystemMenu(id) {
        const menu = document.getElementById(id);
        const arrow = document.getElementById('arrow-' + id);
        
        menu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-90');
    }
</script>