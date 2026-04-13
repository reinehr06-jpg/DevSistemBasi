<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Dev Manager') }} - @yield('title', 'Dashboard')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --neon-green: #22c55e;
                --neon-red: #ef4444;
                --sidebar-width: 280px;
            }
            
            * {
                font-family: 'Figtree', sans-serif;
            }
            
            .sidebar {
                width: var(--sidebar-width);
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
                z-index: 50;
                overflow-y: auto;
            }
            
            .main-content {
                margin-left: var(--sidebar-width);
                min-height: 100vh;
                background: #0f172a;
            }
            
            .system-item {
                transition: all 0.3s ease;
                border-left: 3px solid transparent;
            }
            
            .system-item:hover {
                background: rgba(255, 255, 255, 0.1);
                border-left-color: var(--neon-green);
            }
            
            .system-item.active {
                background: rgba(34, 197, 94, 0.2);
                border-left-color: var(--neon-green);
            }
            
            .status-card {
                background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                border: 1px solid #334155;
                transition: all 0.3s ease;
            }
            
            .status-card:hover {
                border-color: #22c55e;
                box-shadow: 0 0 20px rgba(34, 197, 94, 0.2);
            }
            
            .status-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            
            .neon-green { color: #22c55e; }
            .neon-red { color: #ef4444; }
            .neon-blue { color: #3b82f6; }
            .neon-purple { color: #a855f7; }
            .neon-yellow { color: #eab308; }
            .neon-cyan { color: #06b6d4; }
            
            .bg-neon-green { background-color: #22c55e; }
            .bg-neon-red { background-color: #ef4444; }
        </style>
        
        @yield('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen">
            @include('layouts.sidebar')
            
            <div class="main-content flex-1">
                @include('layouts.header')
                
                <main class="p-6">
                    @yield('content')
                </main>
            </div>
        </div>
        
        @yield('scripts')
    </body>
</html>