<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NOCHI FARM - Peternak Telur</title>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            50: '#fdf2f4',
                            100: '#fbe6e9',
                            200: '#f7d0d6',
                            300: '#f0aab5',
                            400: '#e5788a',
                            500: '#d34d64',
                            600: '#b8324b',
                            700: '#9b243b',
                            800: '#800020', // Primary deep maroon
                            900: '#6d1323',
                            950: '#400610',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js CDN for Analytics & Trends -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            -webkit-tap-highlight-color: transparent;
        }

        .hidden { display: none !important; }
        @media (min-width: 640px) {
            .sm\:flex { display: flex !important; }
            .sm\:hidden { display: none !important; }
        }
        @media (min-width: 768px) {
            .md\:flex { display: flex !important; }
            .md\:hidden { display: none !important; }
        }

        /* Deep Maroon Gradient */
        .bg-maroon-gradient {
            background: linear-gradient(135deg, #520b16 0%, #800020 50%, #991b1b 100%);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Card styling */
        .farm-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .farm-card-interactive {
            cursor: pointer;
        }
        .farm-card-interactive:hover {
            border-color: #fbcfe8;
            box-shadow: 0 8px 20px -4px rgba(128, 0, 32, 0.08);
            transform: translateY(-1px);
        }
        .farm-card-interactive:active {
            transform: scale(0.99);
        }

        /* Modal backdrop animation */
        .modal-active {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }

        .modal-content-active {
            transform: translateY(0) !important;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Header (Fully Responsive for Mobile & Desktop) -->
    <header class="bg-maroon-gradient text-white shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18">
                
                <!-- Brand Logo & Title -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white flex items-center justify-center shadow-md border-2 border-maroon-200 text-maroon-800 font-bold overflow-hidden p-1 group-hover:scale-105 transition-transform">
                            <!-- Chicken / Egg SVG -->
                            <svg viewBox="0 0 24 24" class="w-7 h-7 fill-maroon-800" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13 2 5 6.48 5 12c0 4.42 3.13 8 7 8s7-3.58 7-8c0-5.52-3.13-10-7-10zm-1 5c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm3.5 8c-.83 0-1.5-.67-1.5-1.5S13.67 12 14.5 12s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" opacity="0.3"/>
                                <path d="M18.88 10.37c-.36-.93-1.07-1.68-2-2.11l-.88-.41.34-.91c.42-1.12.22-2.39-.53-3.32-.76-.94-1.95-1.46-3.16-1.38-.85.06-1.65.43-2.26 1.05l-.65.66-.7-.61C8.29 2.68 7.23 2.36 6.16 2.45c-1.39.12-2.61.94-3.23 2.19-.61 1.23-.49 2.69.32 3.8l.58.8-.93.38c-1.19.49-2.03 1.56-2.25 2.84-.21 1.26.24 2.53 1.18 3.39l.23.21C2.57 18.06 6.94 22 12 22s9.43-3.94 9.94-5.94l.23-.21c.94-.86 1.39-2.13 1.18-3.39-.22-1.28-1.06-2.35-2.25-2.84l-.22-.09zM12 20c-3.87 0-7-3.58-7-8 0-3.35 1.4-6.4 3.4-8.08.7.67 1.63 1.08 2.6 1.08h2c.97 0 1.9-.41 2.6-1.08C17.6 5.6 19 8.65 19 12c0 4.42-3.13 8-7 8z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-base sm:text-lg font-extrabold tracking-wide leading-tight text-white block">
                                NOCHI FARM
                            </span>
                            <span class="text-xs text-rose-200 font-medium tracking-wide block">Peternak Telur</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links (Hidden on Mobile, Visible on Desktop md:) -->
                <nav class="hidden md:flex items-center gap-1.5 bg-black/15 p-1 rounded-xl backdrop-blur-sm border border-white/10">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('warehouse.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('warehouse.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="warehouse" class="w-4 h-4"></i>
                        <span>Gudang</span>
                    </a>
                    <a href="{{ route('input.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('input.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Input</span>
                    </a>
                    <a href="{{ route('rekap.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('rekap.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                        <span>Rekap</span>
                    </a>
                    <a href="{{ route('master.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('master.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                        <span>Master</span>
                    </a>
                </nav>

                <!-- Right Action Bar: Notification & User Profile -->
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    <!-- Notification Bell -->
                    <button onclick="toggleNotificationModal()" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 transition-all flex items-center justify-center text-white relative border border-white/10" title="Notifikasi Kandang">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-amber-400 rounded-full ring-2 ring-maroon-800"></span>
                    </button>

                    <!-- User Profile Info (Shown on Desktop) -->
                    <a href="{{ route('profile.index') }}" class="hidden sm:flex items-center gap-2.5 pl-2 border-l border-white/20 hover:opacity-90 transition-opacity">
                        <div class="w-9 h-9 rounded-xl bg-rose-950/60 border border-white/20 flex items-center justify-center text-white font-bold text-xs uppercase shadow-inner">
                            <i data-lucide="user-check" class="w-4 h-4 text-rose-200"></i>
                        </div>
                        <div class="text-left leading-tight">
                            <span class="text-xs font-bold text-white block">{{ isset($user) && $user ? $user->name : (auth()->user()->name ?? 'Petugas') }}</span>
                            <span class="text-[10px] text-rose-200 font-medium block">Petugas Kandang</span>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </header>

    <!-- Flash Message Notification -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-3">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm shadow-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center gap-3 text-sm shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Content Slot (Expands smoothly on desktop) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5 pb-24 md:pb-10">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (Sesuai Mockup Nochi Farm: Dashboard, Gudang, Input, Rekap, Master) -->
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[440px] z-40 bg-white border-t border-slate-200 shadow-2xl">
        <div class="grid grid-cols-5 py-2 px-1 text-center items-center">
            <!-- 1. Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('dashboard') ? 'text-maroon-800 font-extrabold' : 'text-slate-500 hover:text-maroon-700 font-medium' }} transition-transform active:scale-90">
                <div class="relative">
                    <i data-lucide="home" class="w-5 h-5 stroke-[2.2]"></i>
                    @if(request()->routeIs('dashboard'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3.5 h-1 bg-maroon-800 rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Dashboard</span>
            </a>

            <!-- 2. Gudang -->
            <a href="{{ route('warehouse.index') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('warehouse.*') ? 'text-maroon-800 font-extrabold' : 'text-slate-500 hover:text-maroon-700 font-medium' }} transition-transform active:scale-90">
                <div class="relative">
                    <i data-lucide="warehouse" class="w-5 h-5 stroke-[2.2]"></i>
                    @if(request()->routeIs('warehouse.*'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3.5 h-1 bg-maroon-800 rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Gudang</span>
            </a>

            <!-- 3. Input (Sesuai Mockup ＋ Input Kandang) -->
            <a href="{{ route('input.index') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('input.*') ? 'text-maroon-800 font-extrabold' : 'text-slate-500 hover:text-maroon-700 font-medium' }} transition-transform active:scale-90 group">
                <div class="w-7 h-7 rounded-lg {{ request()->routeIs('input.*') ? 'bg-maroon-800 text-white shadow-md' : 'bg-rose-50 border border-rose-200 text-maroon-800' }} flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                    <span class="text-base font-black leading-none">＋</span>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight font-bold {{ request()->routeIs('input.*') ? 'text-maroon-800' : 'text-slate-600' }}">Input</span>
            </a>

            <!-- 4. Rekap -->
            <a href="{{ route('rekap.index') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('rekap.*') ? 'text-maroon-800 font-extrabold' : 'text-slate-500 hover:text-maroon-700 font-medium' }} transition-transform active:scale-90">
                <div class="relative">
                    <i data-lucide="clipboard-list" class="w-5 h-5 stroke-[2.2]"></i>
                    @if(request()->routeIs('rekap.*'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3.5 h-1 bg-maroon-800 rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Rekap</span>
            </a>

            <!-- 5. Master -->
            <a href="{{ route('master.index') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('master.*') ? 'text-maroon-800 font-extrabold' : 'text-slate-500 hover:text-maroon-700 font-medium' }} transition-transform active:scale-90">
                <div class="relative">
                    <i data-lucide="layout-grid" class="w-5 h-5 stroke-[2.2]"></i>
                    @if(request()->routeIs('master.*'))
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3.5 h-1 bg-maroon-800 rounded-full"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Master</span>
            </a>
        </div>
    </nav>

    <!-- Notification Modal / Drawer -->
    <div id="notificationModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-2xl p-5 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm sm:text-base">Notifikasi Kandang</h3>
                </div>
                <button onclick="toggleNotificationModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="py-4 space-y-3">
                <div class="p-3 rounded-xl bg-rose-50/60 border border-rose-100 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="egg" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Pencatatan Telur Hari Ini</p>
                        <p class="text-[11px] text-slate-500">Pastikan seluruh telur Blok A, B, dan C telah dihitung dan disimpan.</p>
                        <span class="text-[10px] text-maroon-700 font-medium">Hari ini, 06:30</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-100 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="wheat" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Jadwal Pakan Sore</p>
                        <p class="text-[11px] text-slate-500">Pemberian pakan konsentrat layer dijadwalkan pukul 15:30.</p>
                        <span class="text-[10px] text-amber-700 font-medium">Hari ini, 15:30</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- General Toast Alert -->
    <div id="infoToast" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-slate-900/90 text-white text-xs sm:text-sm px-5 py-2.5 rounded-full shadow-xl backdrop-blur-sm opacity-0 invisible transition-all duration-300 pointer-events-none max-w-[90%] text-center">
        <span id="infoToastText">Info</span>
    </div>

    <!-- Script Initialize -->
    <script>
        lucide.createIcons();

        function toggleNotificationModal() {
            const modal = document.getElementById('notificationModal');
            const content = modal.querySelector('div');
            if (modal.classList.contains('modal-active')) {
                modal.classList.remove('modal-active');
                content.classList.remove('modal-content-active');
            } else {
                modal.classList.add('modal-active');
                content.classList.add('modal-content-active');
            }
        }

        function showInfoToast(msg) {
            const toast = document.getElementById('infoToast');
            const toastText = document.getElementById('infoToastText');
            toastText.textContent = msg;
            toast.classList.add('modal-active');
            setTimeout(() => {
                toast.classList.remove('modal-active');
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>
</html>
