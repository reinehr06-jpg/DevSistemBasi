<header class="fixed top-0 right-0 left-[280px] z-30 bg-[#0a0a0f]/80 backdrop-blur-xl border-b border-[#1e1e2e]">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Left Section - Breadcrumb & Title -->
        <div class="flex items-center gap-4">
            <button class="lg:hidden p-2 rounded-lg hover:bg-[#1a1a24] text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-white transition-colors">Home</a>
                @yield('breadcrumb')
            </nav>
        </div>

        <!-- Right Section - Actions -->
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="relative hidden md:block">
                <input 
                    type="text" 
                    placeholder="Buscar..." 
                    class="premium-input w-64 pl-10"
                >
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Quick Actions -->
            <div class="flex items-center gap-2">
                <!-- Add New -->
                <button class="p-2 rounded-lg hover:bg-[#1a1a24] text-slate-400 hover:text-white transition-colors" title="Novo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>

                <!-- Notifications -->
                <div class="relative">
                    <button class="p-2 rounded-lg hover:bg-[#1a1a24] text-slate-400 hover:text-white transition-colors" id="notifBtn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.405L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full text-xs flex items-center justify-center text-white font-medium">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown -->
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 premium-card p-2">
                        <div class="flex items-center justify-between p-2 border-b border-[#1e1e2e]">
                            <span class="text-white font-semibold">Notificações</span>
                            <button class="text-xs text-indigo-400 hover:text-indigo-300">Marcar todas como lida</button>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse(\App\Models\Notification::where('user_id', auth()->id())->limit(5)->get() as $notif)
                            <div class="p-3 hover:bg-[#1a1a24] rounded-lg cursor-pointer transition-colors">
                                <div class="flex items-start gap-3">
                                    <span class="text-lg">{{ $notif->type === 'error' ? '❌' : ($notif->type === 'success' ? '✅' : 'ℹ️') }}</span>
                                    <div class="flex-1">
                                        <p class="text-white text-sm">{{ $notif->title }}</p>
                                        <p class="text-slate-500 text-xs mt-1">{{ $notif->message }}</p>
                                        <p class="text-slate-600 text-xs mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="p-4 text-center text-slate-500">
                                Nenhuma notificação
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <a href="{{ route('profile.edit') }}" class="p-2 rounded-lg hover:bg-[#1a1a24] text-slate-400 hover:text-white transition-colors" title="Configurações">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</header>

<script>
    // Toggle notifications dropdown
    document.getElementById('notifBtn')?.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('notifDropdown').classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notifDropdown');
        const btn = document.getElementById('notifBtn');
        if (dropdown && !dropdown.contains(e.target) && btn && !btn.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>