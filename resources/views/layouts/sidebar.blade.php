<aside class="sidebar">
    <div class="p-6">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-neon-green rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">D</span>
            </div>
            <div>
                <h1 class="text-white font-bold text-lg">Dev Manager</h1>
                <p class="text-gray-400 text-xs">Sistema de Gestão</p>
            </div>
        </div>

        <nav class="space-y-1">
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">
                📊 Dashboard
            </div>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-white/10 transition {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : '' }}">
                <span>📊</span>
                <span>Painel Geral</span>
            </a>

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 mt-6 px-3">
                🏢 Sistemas
            </div>

            @foreach($systems ?? \App\Models\System::where('active', true)->get() as $system)
            <div class="system-item px-3 py-2 rounded-lg" style="border-left-color: {{ $system->color }}">
                <div class="flex items-center justify-between">
                    <button onclick="toggleSubmenu('system-{{ $system->id }}')" class="flex items-center gap-3 text-gray-300 hover:text-white w-full">
                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $system->color }}"></span>
                        <span class="text-sm truncate">{{ $system->name }}</span>
                    </button>
                </div>
                <div id="system-{{ $system->id }}" class="hidden pl-6 mt-1 space-y-1">
                    <a href="{{ route('dev-tasks.index', ['system_id' => $system->id]) }}" class="block px-3 py-1.5 text-xs text-gray-400 hover:text-white rounded">
                        📂 Desenvolvimento
                    </a>
                    <a href="{{ route('bugs.index', ['system_id' => $system->id]) }}" class="block px-3 py-1.5 text-xs text-gray-400 hover:text-white rounded">
                        🐞 Bugs
                    </a>
                    <a href="{{ route('servers.by-system', $system->id) }}" class="block px-3 py-1.5 text-xs text-gray-400 hover:text-white rounded">
                        🖥️ Server
                    </a>
                </div>
            </div>
            @endforeach

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 mt-6 px-3">
                🔧 Global
            </div>

            <a href="{{ route('dev-tasks.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-white/10 transition">
                <span>📂</span>
                <span>Todas as Tarefas</span>
            </a>
            <a href="{{ route('bugs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-white/10 transition">
                <span>🐞</span>
                <span>Todos os Bugs</span>
            </a>
            <a href="{{ route('deploy.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-white/10 transition">
                <span>🚀</span>
                <span>Deploy</span>
            </a>
            <a href="{{ route('servers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-white/10 transition">
                <span>🖥️</span>
                <span>Servidores</span>
            </a>
            <a href="{{ route('integrations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-300 hover:bg-white/10 transition">
                <span>🔗</span>
                <span>Integrações</span>
            </a>
        </nav>
    </div>

    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2 w-full text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition">
                <span>🚪</span>
                <span>Sair</span>
            </button>
        </form>
    </div>
</aside>

<script>
    function toggleSubmenu(id) {
        const el = document.getElementById(id);
        el.classList.toggle('hidden');
    }
</script>