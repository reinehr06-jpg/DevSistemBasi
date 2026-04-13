@extends('layouts.app-dark')

@section('title', 'Config AI')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-white">Configurações do AI Orchestrator</h2>
    </div>

    <div class="status-card rounded-xl p-6">
        <form method="POST" action="{{ route('ai-orchestrator.config.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">URL do Serviço Python (FastAPI)</label>
                <input type="url" name="ai_url" value="{{ $config['ai_url'] }}" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;" placeholder="http://localhost:8000">
                <p class="text-xs text-gray-500 mt-1">Endereço onde o serviço Python está rodando</p>
            </div>
            
            <div>
                <label class="block text-sm text-gray-400 mb-2">Timeout (segundos)</label>
                <input type="number" name="timeout" value="{{ $config['timeout'] }}" min="10" max="300" style="width: 100%; padding: 12px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                <p class="text-xs text-gray-500 mt-1">Tempo máximo de espera por resposta da IA</p>
            </div>
            
            <div class="flex items-center gap-3">
                <input type="checkbox" name="enabled" id="enabled" {{ $config['enabled'] ? 'checked' : '' }} class="w-4 h-4 rounded bg-slate-900 border-slate-700">
                <label for="enabled" class="text-white">Ativar AI Orchestrator</label>
            </div>
            
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                Salvar Configurações
            </button>
        </form>
    </div>

    <div class="status-card rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Instalação do Serviço Python</h3>
        
        <div class="space-y-4 text-sm text-gray-300">
            <div>
                <h4 class="text-white font-semibold mb-2">1. Criar diretório</h4>
                <pre class="bg-slate-900 p-3 rounded text-xs">mkdir -p /opt/ai-orchestrator
cd /opt/ai-orchestrator</pre>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-2">2. Criar requirements.txt</h4>
                <pre class="bg-slate-900 p-3 rounded text-xs">fastapi==0.109.0
uvicorn==0.27.0
langgraph==0.0.20
langchain==0.1.0
langchain-ollama==0.0.1
pydantic==2.5.3
python-dotenv==1.0.0
httpx==0.26.0</pre>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-2">3. Criar main.py</h4>
                <pre class="bg-slate-900 p-3 rounded text-xs"># Copiar o arquivo ai-service/main.py do projeto</pre>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-2">4. Instalar e rodar</h4>
                <pre class="bg-slate-900 p-3 rounded text-xs">pip install -r requirements.txt
uvicorn main:app --host 0.0.0.0 --port 8000</pre>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-2">5. Rodar em background com PM2</h4>
                <pre class="bg-slate-900 p-3 rounded text-xs">pm2 start "uvicorn main:app --host 0.0.0.0 --port 8000" --name ai-orchestrator
pm2 save</pre>
            </div>
        </div>
    </div>

    <div class="status-card rounded-xl p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Testar Instalação</h3>
        <div class="flex gap-3">
            <button onclick="testAIConnection()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition">
                🔄 Testar Conexão
            </button>
            <button onclick="document.getElementById('testResult').classList.remove('hidden')" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition">
                📋 Verificar Health
            </button>
        </div>
        <div id="testResult" class="hidden mt-4 p-4 rounded bg-slate-900 text-gray-300">
            Resultado aparecer aqui...
        </div>
    </div>
</div>

<script>
function testAIConnection() {
    fetch('{{ route("ai-orchestrator.test") }}')
        .then(res => res.json())
        .then(data => {
            alert(data.success ? '✅ Conectado!' : '❌ Erro: ' + data.message);
        });
}
</script>
@endsection