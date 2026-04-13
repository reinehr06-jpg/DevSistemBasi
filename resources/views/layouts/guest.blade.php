<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DevSistemBasi') }} - @yield('title')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        premium: { bg: '#0a0a0f', surface: '#12121a', card: '#16161f', border: '#1e1e2e', hover: '#1a1a24' },
                        accent: { primary: '#6366f1', secondary: '#8b5cf6', success: '#10b981', warning: '#f59e0b', danger: '#ef4444' },
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #12121a 50%, #0a0a0f 100%);
            min-height: 100vh;
            color: #e2e8f0;
        }
        .status-card {
            background: rgba(22, 22, 31, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(30, 30, 46, 0.8);
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>
</body>
</html>