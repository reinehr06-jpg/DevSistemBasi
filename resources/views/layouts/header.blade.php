<header class="bg-slate-900/50 backdrop-blur border-b border-white/10">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-4">
            <h2 class="text-xl font-semibold text-white">@yield('title', 'Dashboard')</h2>
        </div>
        
        <div class="flex items-center gap-4">
            <button class="p-2 text-gray-400 hover:text-white">
                <span class="text-xl">🔔</span>
            </button>
            
            <div class="flex items-center gap-3 pl-4 border-l border-white/10">
                <div class="text-right">
                    <p class="text-sm text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">Desenvolvedor</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center">
                    <span class="text-white font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
        </div>
    </div>
</header>