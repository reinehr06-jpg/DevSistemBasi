@extends('layouts.app-new')

@section('title', 'Integrações')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Integrações</h2>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
            + Nova Integração
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($integrations as $integration)
        <div class="status-card rounded-xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="px-2 py-1 rounded text-xs
                    @if($integration->type === 'git') bg-orange-500/20 text-orange-400
                    @elseif($integration->type === 'server') bg-blue-500/20 text-blue-400
                    @elseif($integration->type === 'api') bg-purple-500/20 text-purple-400
                    @elseif($integration->type === 'bitbucket') bg-cyan-500/20 text-cyan-400
                    @else bg-gray-500/20 text-gray-400 @endif">
                    {{ ucfirst($integration->type) }}
                </span>
                <span class="w-2 h-2 rounded-full {{ $integration->active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
            </div>
            <h3 class="text-lg font-semibold text-white">{{ $integration->name }}</h3>
            <p class="text-gray-400 text-sm">{{ $integration->system->name }}</p>
            
            <div class="mt-3 flex gap-2">
                <form method="POST" action="{{ route('integrations.test', $integration) }}">
                    @csrf
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                        Testar
                    </button>
                </form>
                <form method="POST" action="{{ route('integrations.update', $integration) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="active" value="{{ $integration->active ? '0' : '1' }}">
                    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white py-1 px-3 rounded text-sm">
                        {{ $integration->active ? 'Desativar' : 'Ativar' }}
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-8 text-gray-400">
            Nenhuma integração configurada
        </div>
        @endforelse
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-800 rounded-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-white mb-4">Nova Integração</h3>
        
        <form method="POST" action="{{ route('integrations.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Sistema</label>
                <select name="system_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    @foreach($systems as $system)
                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Tipo</label>
                <select name="type" id="integrationType" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white" onchange="toggleConfigFields()">
                    <option value="git">Git</option>
                    <option value="server">Server</option>
                    <option value="api">API</option>
                    <option value="bitbucket">Bitbucket</option>
                    <option value="easypanel">Easypanel</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Nome</label>
                <input type="text" name="name" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white" required>
            </div>
            
            <div id="configFields" class="space-y-3">
                <div id="gitFields" class="config-fields hidden">
                    <input type="text" name="config[repo]" placeholder="git@bitbucket.org:empresa/projeto.git" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    <input type="text" name="config[branch]" placeholder="branch" value="main" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white mt-2">
                </div>
                <div id="serverFields" class="config-fields hidden">
                    <input type="text" name="config[ip]" placeholder="IP do servidor" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    <input type="text" name="config[ssh_user]" placeholder="usuário SSH" value="root" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white mt-2">
                    <input type="text" name="config[deploy_path]" placeholder="/var/www/app" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white mt-2">
                </div>
                <div id="apiFields" class="config-fields hidden">
                    <input type="text" name="config[url]" placeholder="URL da API" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    <input type="text" name="config[token]" placeholder="Token (opcional)" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white mt-2">
                </div>
                <div id="bitbucketFields" class="config-fields hidden">
                    <input type="text" name="config[webhook_url]" placeholder="Webhook URL" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white">
                </div>
                <div id="easypanelFields" class="config-fields hidden">
                    <input type="text" name="config[url]" placeholder="URL do Easypanel" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white">
                    <input type="text" name="config[api_key]" placeholder="API Key" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white mt-2">
                </div>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition">
                    Criar
                </button>
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 bg-slate-700 hover:bg-slate-600 text-white py-2 rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleConfigFields() {
    const type = document.getElementById('integrationType').value;
    document.querySelectorAll('.config-fields').forEach(el => el.classList.add('hidden'));
    document.getElementById(type + 'Fields').classList.remove('hidden');
}
document.getElementById('integrationType').addEventListener('change', toggleConfigFields);
</script>
@endsection