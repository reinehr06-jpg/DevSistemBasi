<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'DevSistemBasi') }} - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg-dark: #0a0a0f;
            --bg-surface: #12121a;
            --bg-card: #16161f;
            --border-color: #1e1e2e;
            --hover: #1a1a24;
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-white: #ffffff;
            --text-gray: #9ca3af;
            --text-muted: #64748b;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-dark);
            color: var(--text-white);
            min-height: 100vh;
        }
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 40;
        }
        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid var(--border-color);
        }
        .logo-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .logo-text h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-white);
        }
        .logo-text p {
            font-size: 12px;
            color: var(--text-muted);
        }
        .nav-section {
            padding: 16px 12px;
            flex: 1;
            overflow-y: auto;
        }
        .nav-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            padding: 0 12px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-gray);
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 4px;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--hover);
            color: var(--text-white);
        }
        .nav-item.active {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 24px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .page-title {
            font-size: 24px;
            font-weight: 700;
        }
        .page-subtitle {
            color: var(--text-muted);
            margin-top: 4px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
        }
        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-top: 8px;
        }
        .stat-sub {
            font-size: 12px;
            margin-top: 8px;
        }
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #5558e8;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        @include('layouts.sidebar', ['systems' => $systems ?? \App\Models\System::where('active', true)->get()])
        
        <main class="main-content">
            @yield('content')
        </main>
    </div>
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
</body>
</html>