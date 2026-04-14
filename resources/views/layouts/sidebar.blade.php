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
        <!-- 1.0 Dashboard -->
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

        <!-- 2.0 Desenvolvedores -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Desenvolvedores
            </p>
            <div class="space-y-1">
                <button onclick="toggleSystemMenu('menu-devs')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">👥</span>
                        <span>2.1 Lista</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-devs">▸</span>
                </button>
                <div id="menu-devs" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('developers.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Todos os Devs</span>
                    </a>
                    <a href="{{ route('developers.create') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Novo Dev</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-desempenho')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">📊</span>
                        <span>2.2 Desempenho</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-desempenho">▸</span>
                </button>
                <div id="menu-desempenho" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('developers.performance') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📈</span>
                        <span>Métricas</span>
                    </a>
                    <a href="{{ route('developers.ranking') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">🏆</span>
                        <span>Ranking</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-times')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">👥</span>
                        <span>2.3 Times</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-times">▸</span>
                </button>
                <div id="menu-times" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('developers.teams') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Lista de Times</span>
                    </a>
                    <a href="{{ route('developers.teams') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Criar Time</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-bugs')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🐛</span>
                        <span>2.4 Bugs</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-bugs">▸</span>
                </button>
                <div id="menu-bugs" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('bugs.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Todos os Bugs</span>
                    </a>
                    <a href="{{ route('bugs.create') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Novo Bug</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-sprints')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🏃</span>
                        <span>2.5 Sprints</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-sprints">▸</span>
                </button>
                <div id="menu-sprints" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Ativas</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">✅</span>
                        <span>Concluídas</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3.0 Sistemas -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Sistemas
            </p>
            <div class="space-y-1">
                <button onclick="toggleSystemMenu('menu-syslist')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">📋</span>
                        <span>3.1 Lista</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-syslist">▸</span>
                </button>
                <div id="menu-syslist" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('systems.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Todos</span>
                    </a>
                    <a href="{{ route('systems.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Novo</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-dna')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🧬</span>
                        <span>3.2 DNA</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-dna">▸</span>
                </button>
                <div id="menu-dna" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('system-profiles.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Profiles</span>
                    </a>
                    <a href="{{ route('dependencies.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">🕸️</span>
                        <span>Dependências</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-envs')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🌍</span>
                        <span>3.3 Ambientes</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-envs">▸</span>
                </button>
                <div id="menu-envs" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">dev</span>
                        <span>Desenvolvimento</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">staging</span>
                        <span>Homologação</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">prod</span>
                        <span>Produção</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-srv')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🖥️</span>
                        <span>3.4 Servidores</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-srv">▸</span>
                </button>
                <div id="menu-srv" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('servers.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Lista</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📊</span>
                        <span>Monitoramento</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-deploy')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🚀</span>
                        <span>3.5 Deploy</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-deploy">▸</span>
                </button>
                <div id="menu-deploy" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('deploy.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Histórico</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">↺</span>
                        <span>Rollback</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-backups')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">💾</span>
                        <span>3.6 Backups</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-backups">▸</span>
                </button>
                <div id="menu-backups" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Lista</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📅</span>
                        <span>Agendamentos</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-integrations')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🔗</span>
                        <span>3.7 Integrações</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-integrations">▸</span>
                </button>
                <div id="menu-integrations" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('integrations.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Lista</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Nova</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-workflows')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">⚡</span>
                        <span>3.8 Workflows</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-workflows">▸</span>
                </button>
                <div id="menu-workflows" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('workflows.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Lista</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">▶️</span>
                        <span>Execuções</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-alerts')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🔔</span>
                        <span>3.9 Alertas</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-alerts">▸</span>
                </button>
                <div id="menu-alerts" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('alerts.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Ativos</span>
                    </a>
                    <a href="{{ route('alerts.rules') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">⚙️</span>
                        <span>Regras</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📜</span>
                        <span>Histórico</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-integrations')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🔗</span>
                        <span>3.4 Integrações</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-integrations">▸</span>
                </button>
                <div id="menu-integrations" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('integrations.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Todas</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Nova</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-workflows')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">⚡</span>
                        <span>3.5 Workflows</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-workflows">▸</span>
                </button>
                <div id="menu-workflows" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('workflows.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Todos</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Novo</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-alerts')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🔔</span>
                        <span>3.6 Alertas</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-alerts">▸</span>
                </button>
                <div id="menu-alerts" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('alerts.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Ativos</span>
                    </a>
                    <a href="{{ route('alerts.rules') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">⚙️</span>
                        <span>Regras</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4.0 AI Core -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                AI Core
            </p>
            <div class="space-y-1">
                <button onclick="toggleSystemMenu('menu-orch')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🧠</span>
                        <span>4.1 Orchestrator</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-orch">▸</span>
                </button>
                <div id="menu-orch" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('ai-orchestrator.index') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('ai-orchestrator.agents') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">👥</span>
                        <span>Agentes</span>
                    </a>
                    <a href="{{ route('ai-orchestrator.flows') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">🔀</span>
                        <span>Pipelines</span>
                    </a>
                    <a href="{{ route('ai-orchestrator.executions') }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📜</span>
                        <span>Logs</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-watcher')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">👁️</span>
                        <span>4.2 AI Watcher</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-watcher">▸</span>
                </button>
                <div id="menu-watcher" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">⚙️</span>
                        <span>Configurar</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Logs</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-predict')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🔮</span>
                        <span>4.3 Previsões</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-predict">▸</span>
                </button>
                <div id="menu-predict" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">⚙️</span>
                        <span>Configurar</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-memory')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">💾</span>
                        <span>4.4 Memória IA</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-memory">▸</span>
                </button>
                <div id="menu-memory" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Gerenciar</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">🧠</span>
                        <span>Aprendizado</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📊</span>
                        <span>Estatísticas</span>
                    </a>
                </div>

                <button onclick="toggleSystemMenu('menu-actions')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">⚡</span>
                        <span>4.5 Ações Auto</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-actions">▸</span>
                </button>
                <div id="menu-actions" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Lista</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">➕</span>
                        <span>Nova Ação</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📜</span>
                        <span>Execuções</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 5.0 Configurações -->
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Configurações
            </p>
            <div class="space-y-1">
                <button onclick="toggleSystemMenu('menu-notifications')" class="menu-item w-full justify-between">
                    <div class="flex items-center gap-3">
                        <span class="menu-icon">🔔</span>
                        <span>5.1 Notificações</span>
                    </div>
                    <span class="text-xs transition-transform" id="arrow-menu-notifications">▸</span>
                </button>
                <div id="menu-notifications" class="hidden pl-6 mt-1 space-y-1">
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📧</span>
                        <span>Email</span>
                    </a>
                    <a href="#" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📱</span>
                        <span>Push</span>
                    </a>
                </div>

                <a href="#" class="menu-item">
                    <span class="menu-icon">👥</span>
                    <span>5.2 Usuários</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="menu-item">
                    <span class="menu-icon">⚙️</span>
                    <span>Meu Perfil</span>
                </a>
            </div>
        </div>

        <!-- Sistemas Ativos (lista dinâmica) -->
        @if($systems ?? \App\Models\System::where('active', true)->count() > 0)
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 px-3">
                Sistemas
            </p>
            <div class="space-y-1">
                @foreach($systems ?? \App\Models\System::where('active', true)->get() as $system)
                <button onclick="toggleSystemMenu('system-{{ $system->id }}')" class="menu-item w-full justify-between group-hover:bg-[#1a1a24]">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $system->color }}"></span>
                        <span class="text-sm truncate">{{ $system->name }}</span>
                    </div>
                    <span class="text-slate-500 text-xs group-hover:text-white transition-transform" id="arrow-system-{{ $system->id }}">▸</span>
                </button>
                <div id="system-{{ $system->id }}" class="hidden pl-8 mt-1 space-y-1">
                    <a href="{{ route('dev-tasks.index', ['system_id' => $system->id]) }}" class="menu-item text-sm py-2">
                        <span class="text-slate-500">📋</span>
                        <span>Tarefas</span>
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
                @endforeach
            </div>
        </div>
        @endif
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
        
        if (menu) {
            menu.classList.toggle('hidden');
            if (arrow) {
                arrow.classList.toggle('rotate-90');
            }
        }
    }
</script>