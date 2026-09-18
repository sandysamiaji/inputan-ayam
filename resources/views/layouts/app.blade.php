<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NOCHI FARM - Peternak Telur</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nochi.png') }}">

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
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white flex items-center justify-center shadow-md border-2 border-white text-maroon-800 font-bold overflow-hidden p-0.5 group-hover:scale-105 transition-transform shrink-0">
                            <img src="{{ asset('images/logo-nochi.png') }}" alt="Nochi Farm Logo" class="w-full h-full object-contain rounded-full bg-white">
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
                    @if(!auth()->check() || auth()->user()->canAccess('menu_dashboard'))
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    @if(!auth()->check() || auth()->user()->canAccess('menu_warehouse'))
                    <a href="{{ route('warehouse.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('warehouse.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="warehouse" class="w-4 h-4"></i>
                        <span>Gudang</span>
                    </a>
                    @endif

                    @if(!auth()->check() || auth()->user()->canAccess('menu_input'))
                    <a href="{{ route('input.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('input.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Input</span>
                    </a>
                    @endif

                    @if(!auth()->check() || auth()->user()->canAccess('menu_rekap'))
                    <a href="{{ route('rekap.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('rekap.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                        <span>Rekap</span>
                    </a>
                    @endif

                    @if(!auth()->check() || auth()->user()->canAccess('menu_master'))
                    <a href="{{ route('master.index') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold {{ request()->routeIs('master.*') ? 'bg-white text-maroon-900 shadow-sm' : 'text-rose-100 hover:text-white hover:bg-white/10' }} transition-all">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                        <span>Master</span>
                    </a>
                    @endif
                </nav>

                <!-- Right Action Bar: Notification & User Profile -->
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('master.permissions') }}" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-400/20 hover:bg-amber-400/30 text-amber-200 border border-amber-300/30 text-xs font-bold transition-all" title="Kelola Hak Akses Pengguna">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-amber-300"></i>
                                <span>Hak Akses</span>
                            </a>
                        @endif
                    @endauth

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
                            <span class="text-xs font-bold text-white block">{{ auth()->user()->username ?? auth()->user()->name ?? 'Petugas' }}</span>
                            <span class="text-[10px] text-rose-200 font-medium block">{{ auth()->user() && auth()->user()->role === 'admin' ? 'Administrator' : 'Petugas Kandang' }}</span>
                        </div>
                    </a>

                    <!-- Logout Button -->
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                            @csrf
                            <button type="submit" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 hover:bg-rose-900/80 active:scale-95 transition-all flex items-center justify-center text-rose-200 hover:text-white border border-white/10" title="Keluar / Logout">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    @endauth

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

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm shadow-sm space-y-1 mt-2">
                <div class="flex items-center gap-2 font-bold text-rose-900">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <span>Terdapat kesalahan pada input:</span>
                </div>
                <ul class="list-disc list-inside pl-7 text-xs text-rose-700 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Slot (Expands smoothly on desktop) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-5 pb-24 md:pb-10">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (Dynamic Permission-Aware) -->
    @php
        $showDashboard = !auth()->check() || auth()->user()->canAccess('menu_dashboard');
        $showWarehouse = !auth()->check() || auth()->user()->canAccess('menu_warehouse');
        $showInput = !auth()->check() || auth()->user()->canAccess('menu_input');
        $showRekap = !auth()->check() || auth()->user()->canAccess('menu_rekap');
        $showMaster = !auth()->check() || auth()->user()->canAccess('menu_master');
        $navCount = ($showDashboard ? 1 : 0) + ($showWarehouse ? 1 : 0) + ($showInput ? 1 : 0) + ($showRekap ? 1 : 0) + ($showMaster ? 1 : 0);
        if ($navCount < 1) $navCount = 1;
    @endphp
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[440px] z-40 bg-white border-t border-slate-200 shadow-2xl">
        <div class="grid grid-cols-{{ $navCount }} py-2 px-1 text-center items-center">
            @if($showDashboard)
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
            @endif

            @if($showWarehouse)
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
            @endif

            @if($showInput)
            <!-- 3. Input (Sesuai Mockup ＋ Input Kandang) -->
            <a href="{{ route('input.index') }}" class="flex flex-col items-center justify-center py-1 {{ request()->routeIs('input.*') ? 'text-maroon-800 font-extrabold' : 'text-slate-500 hover:text-maroon-700 font-medium' }} transition-transform active:scale-90 group">
                <div class="w-7 h-7 rounded-lg {{ request()->routeIs('input.*') ? 'bg-maroon-800 text-white shadow-md' : 'bg-rose-50 border border-rose-200 text-maroon-800' }} flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                    <span class="text-base font-black leading-none">＋</span>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight font-bold {{ request()->routeIs('input.*') ? 'text-maroon-800' : 'text-slate-600' }}">Input</span>
            </a>
            @endif

            @if($showRekap)
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
            @endif

            @if($showMaster)
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
            @endif
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
