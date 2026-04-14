@extends('layouts.app-new')

@section('title', 'EasyPanel - Servidores')

@section('content')
<div class="premium-bg min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">EasyPanel - Servidores</h1>
            <form action="{{ route('easypanel.servers.sync') }}" method="POST">
                @csrf
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                    Sincronizar
                </button>
            </form>
        </div>

        <div class="bg-blue-900/50 border border-blue-500 text-blue-200 px-4 py-3 rounded mb-4">
            <p>Configure <code>EASYPANEL_URL</code> e <code>EASYPANEL_API_KEY</code> no .env</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="premium-card rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white">CPU</h3>
                <p class="text-gray-400">Monitoramento em tempo real</p>
            </div>
            <div class="premium-card rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white">Memória RAM</h3>
                <p class="text-gray-400">Uso de memória</p>
            </div>
            <div class="premium-card rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white">Disco</h3>
                <p class="text-gray-400">Espaço em disco</p>
            </div>
        </div>
    </div>
</div>
@endsection