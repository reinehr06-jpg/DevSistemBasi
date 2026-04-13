<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DevSistem') }} - @yield('title', 'Dashboard')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            premium: {
                                bg: '#0a0a0f',
                                surface: '#12121a',
                                card: '#16161f',
                                border: '#1e1e2e',
                                hover: '#1a1a24',
                            },
                            accent: {
                                primary: '#6366f1',
                                secondary: '#8b5cf6',
                                success: '#10b981',
                                warning: '#f59e0b',
                                danger: '#ef4444',
                                info: '#06b6d4',
                            },
                            neon: {
                                green: '#22c55e',
                                blue: '#3b82f6',
                                purple: '#a855f7',
                                cyan: '#06b6d4',
                            }
                        },
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'sans-serif'],
                        },
                        boxShadow: {
                            'glow': '0 0 20px rgba(99, 102, 241, 0.15)',
                            'glow-sm': '0 0 10px rgba(99, 102, 241, 0.1)',
                            'card': '0 4px 20px rgba(0, 0, 0, 0.3)',
                        },
                        animation: {
                            'fade-in': 'fadeIn 0.3s ease-out',
                            'slide-up': 'slideUp 0.3s ease-out',
                            'slide-in': 'slideIn 0.2s ease-out',
                            'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                            'glow': 'glow 2s ease-in-out infinite alternate',
                        },
                        keyframes: {
                            fadeIn: {
                                '0%': { opacity: '0' },
                                '100%': { opacity: '1' },
                            },
                            slideUp: {
                                '0%': { transform: 'translateY(10px)', opacity: '0' },
                                '100%': { transform: 'translateY(0)', opacity: '1' },
                            },
                            slideIn: {
                                '0%': { transform: 'translateX(-10px)', opacity: '0' },
                                '100%': { transform: 'translateX(0)', opacity: '1' },
                            },
                            glow: {
                                '0%': { boxShadow: '0 0 5px rgba(99, 102, 241, 0.2)' },
                                '100%': { boxShadow: '0 0 20px rgba(99, 102, 241, 0.4)' },
                            }
                        }
                    }
                }
            }
        </script>

        <style>
            * {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }

            :root {
                --premium-bg: #0a0a0f;
                --premium-surface: #12121a;
                --premium-card: #16161f;
                --premium-border: #1e1e2e;
                --premium-hover: #1a1a24;
                --accent-primary: #6366f1;
                --accent-success: #10b981;
                --accent-warning: #f59e0b;
                --accent-danger: #ef4444;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                background: var(--premium-bg);
                color: #f1f5f9;
                min-height: 100vh;
            }

            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: var(--premium-surface);
            }

            ::-webkit-scrollbar-thumb {
                background: var(--premium-border);
                border-radius: 4px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #2a2a3a;
            }

            /* Glassmorphism Sidebar */
            .glass-sidebar {
                background: linear-gradient(180deg, rgba(18, 18, 26, 0.95) 0%, rgba(10, 10, 15, 0.98) 100%);
                backdrop-filter: blur(20px);
                border-right: 1px solid var(--premium-border);
            }

            /* Card Styles */
            .premium-card {
                background: linear-gradient(135deg, var(--premium-card) 0%, var(--premium-surface) 100%);
                border: 1px solid var(--premium-border);
                border-radius: 16px;
                transition: all 0.3s ease;
            }

            .premium-card:hover {
                border-color: rgba(99, 102, 241, 0.3);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(99, 102, 241, 0.1);
                transform: translateY(-2px);
            }

            /* Stats Card */
            .stats-card {
                background: linear-gradient(135deg, var(--premium-card) 0%, rgba(99, 102, 241, 0.05) 100%);
                border: 1px solid var(--premium-border);
                border-radius: 16px;
                position: relative;
                overflow: hidden;
            }

            .stats-card::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 100px;
                height: 100px;
                background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
                border-radius: 50%;
                transform: translate(30%, -30%);
            }

            /* Gradient Text */
            .gradient-text {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            /* Button Styles */
            .btn-primary {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                color: white;
                padding: 12px 24px;
                border-radius: 10px;
                font-weight: 600;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
            }

            .btn-secondary {
                background: transparent;
                color: #94a3b8;
                padding: 12px 24px;
                border-radius: 10px;
                font-weight: 500;
                transition: all 0.3s ease;
                border: 1px solid var(--premium-border);
                cursor: pointer;
            }

            .btn-secondary:hover {
                background: var(--premium-hover);
                color: #f1f5f9;
                border-color: #2a2a3a;
            }

            /* Status Indicators */
            .status-indicator {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                display: inline-block;
            }

            .status-online {
                background: #10b981;
                box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            }

            .status-offline {
                background: #ef4444;
                box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
            }

            .status-warning {
                background: #f59e0b;
                box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
            }

            .status-maintenance {
                background: #6366f1;
                box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
            }

            /* Pulse Animation */
            .pulse-online {
                animation: pulse-online 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes pulse-online {
                0%, 100% {
                    opacity: 1;
                    box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
                }
                50% {
                    opacity: 0.7;
                    box-shadow: 0 0 20px rgba(16, 185, 129, 0.8);
                }
            }

            /* Input Styles */
            .premium-input {
                background: var(--premium-surface);
                border: 1px solid var(--premium-border);
                border-radius: 10px;
                color: #f1f5f9;
                padding: 12px 16px;
                font-size: 14px;
                transition: all 0.3s ease;
                width: 100%;
            }

            .premium-input:focus {
                outline: none;
                border-color: rgba(99, 102, 241, 0.5);
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            }

            .premium-input::placeholder {
                color: #64748b;
            }

            /* Badge Styles */
            .badge {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .badge-success {
                background: rgba(16, 185, 129, 0.15);
                color: #10b981;
                border: 1px solid rgba(16, 185, 129, 0.2);
            }

            .badge-warning {
                background: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
                border: 1px solid rgba(245, 158, 11, 0.2);
            }

            .badge-danger {
                background: rgba(239, 68, 68, 0.15);
                color: #ef4444;
                border: 1px solid rgba(239, 68, 68, 0.2);
            }

            .badge-info {
                background: rgba(99, 102, 241, 0.15);
                color: #6366f1;
                border: 1px solid rgba(99, 102, 241, 0.2);
            }

            .badge-purple {
                background: rgba(139, 92, 246, 0.15);
                color: #8b5cf6;
                border: 1px solid rgba(139, 92, 246, 0.2);
            }

            /* Progress Bar */
            .progress-bar {
                height: 6px;
                background: var(--premium-border);
                border-radius: 3px;
                overflow: hidden;
            }

            .progress-fill {
                height: 100%;
                border-radius: 3px;
                transition: width 0.5s ease;
            }

            .progress-low {
                background: linear-gradient(90deg, #10b981, #34d399);
            }

            .progress-medium {
                background: linear-gradient(90deg, #f59e0b, #fbbf24);
            }

            .progress-high {
                background: linear-gradient(90deg, #ef4444, #f87171);
            }

            /* Table Styles */
            .premium-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }

            .premium-table thead th {
                background: var(--premium-surface);
                color: #64748b;
                font-weight: 600;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 16px;
                border-bottom: 1px solid var(--premium-border);
                text-align: left;
            }

            .premium-table tbody td {
                padding: 16px;
                border-bottom: 1px solid var(--premium-border);
                color: #f1f5f9;
                font-size: 14px;
            }

            .premium-table tbody tr {
                transition: all 0.2s ease;
            }

            .premium-table tbody tr:hover {
                background: var(--premium-hover);
            }

            /* Modal */
            .modal-backdrop {
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(4px);
            }

            .modal-content {
                background: linear-gradient(135deg, var(--premium-card) 0%, var(--premium-surface) 100%);
                border: 1px solid var(--premium-border);
                border-radius: 20px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            }

            /* Sidebar Menu Item */
            .menu-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 16px;
                border-radius: 10px;
                color: #94a3b8;
                transition: all 0.2s ease;
                cursor: pointer;
                text-decoration: none;
            }

            .menu-item:hover {
                background: var(--premium-hover);
                color: #f1f5f9;
            }

            .menu-item.active {
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(139, 92, 246, 0.1) 100%);
                color: #f1f5f9;
                border-left: 3px solid #6366f1;
            }

            .menu-item.active .menu-icon {
                color: #6366f1;
            }

            .menu-icon {
                font-size: 20px;
                width: 24px;
                text-align: center;
            }

            /* Tabs */
            .tab-item {
                padding: 12px 20px;
                color: #64748b;
                font-weight: 500;
                border-bottom: 2px solid transparent;
                transition: all 0.2s ease;
                cursor: pointer;
            }

            .tab-item:hover {
                color: #f1f5f9;
            }

            .tab-item.active {
                color: #6366f1;
                border-bottom-color: #6366f1;
            }

            /* Page Transitions */
            .page-enter {
                animation: pageEnter 0.3s ease-out;
            }

            @keyframes pageEnter {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Tooltip */
            .tooltip {
                position: relative;
            }

            .tooltip::after {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                padding: 6px 12px;
                background: var(--premium-card);
                border: 1px solid var(--premium-border);
                border-radius: 6px;
                font-size: 12px;
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all 0.2s ease;
                z-index: 100;
            }

            .tooltip:hover::after {
                opacity: 1;
                visibility: visible;
                bottom: calc(100% + 8px);
            }

            /* Loading Skeleton */
            .skeleton {
                background: linear-gradient(90deg, var(--premium-surface) 25%, var(--premium-hover) 50%, var(--premium-surface) 75%);
                background-size: 200% 100%;
                animation: skeleton 1.5s ease-in-out infinite;
                border-radius: 8px;
            }

            @keyframes skeleton {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }

            /* Stagger Animation */
            .stagger-1 { animation-delay: 0.05s; }
            .stagger-2 { animation-delay: 0.1s; }
            .stagger-3 { animation-delay: 0.15s; }
            .stagger-4 { animation-delay: 0.2s; }
            .stagger-5 { animation-delay: 0.25s; }

            /* Toast Notification */
            .toast {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                background: var(--premium-card);
                border: 1px solid var(--premium-border);
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
                z-index: 9999;
                animation: slideInRight 0.3s ease-out;
            }

            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>

        @yield('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-screen">
            @include('layouts.sidebar')
            
            <div class="flex-1 flex flex-col min-h-screen ml-[280px]">
                @include('layouts.header')
                
                <main class="flex-1 p-6 mt-16 page-enter">
                    @yield('content')
                </main>
            </div>
        </div>

        @if(session('success'))
        <div class="toast toast-success" id="successToast">
            <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="toast toast-error" id="errorToast">
            <div class="flex items-center gap-3">
                <span class="text-xl">❌</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <script>
            // Auto-hide toasts
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    const toasts = document.querySelectorAll('.toast');
                    toasts.forEach(toast => {
                        toast.style.animation = 'slideOutRight 0.3s ease-in forwards';
                        setTimeout(() => toast.remove(), 300);
                    });
                }, 4000);
            });

            // Sidebar toggle for mobile
            function toggleSidebar() {
                document.querySelector('.sidebar').classList.toggle('-translate-x-full');
            }
        </script>

        @yield('scripts')
    </body>
</html>