<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - NOCHI FARM Inputan Kandang</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
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

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(145deg, #38040d 0%, #680d1e 35%, #800020 70%, #9e192f 100%);
            min-height: 100vh;
        }

        /* Ambient Glow Effect */
        .ambient-glow-1 {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(244, 63, 94, 0.25) 0%, rgba(128, 0, 32, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(40px);
        }

        .ambient-glow-2 {
            position: absolute;
            bottom: -10%;
            left: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(251, 113, 133, 0.2) 0%, rgba(82, 11, 22, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(50px);
        }

        /* Pure White Glass Card */
        .login-card {
            background: #ffffff;
            box-shadow: 0 25px 50px -12px rgba(40, 4, 10, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.2);
            border-radius: 28px;
        }

        /* Custom Input Focus */
        .custom-input:focus {
            outline: none;
            border-color: #800020;
            box-shadow: 0 0 0 4px rgba(128, 0, 32, 0.12);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-maroon-800 selection:text-white">

    <!-- Ambient Glowing Background Elements -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="w-full max-w-md relative z-10 my-auto">
        
        <!-- Header Branding & Farm Icon -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white shadow-2xl border-4 border-rose-100/50 p-2 mb-3.5 transform hover:scale-105 transition-transform duration-300">
                <!-- Chicken / Egg Emblem SVG -->
                <svg viewBox="0 0 24 24" class="w-12 h-12 fill-maroon-800" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C8.13 2 5 6.48 5 12c0 4.42 3.13 8 7 8s7-3.58 7-8c0-5.52-3.13-10-7-10zm-1 5c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm3.5 8c-.83 0-1.5-.67-1.5-1.5S13.67 12 14.5 12s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" opacity="0.3"/>
                    <path d="M18.88 10.37c-.36-.93-1.07-1.68-2-2.11l-.88-.41.34-.91c.42-1.12.22-2.39-.53-3.32-.76-.94-1.95-1.46-3.16-1.38-.85.06-1.65.43-2.26 1.05l-.65.66-.7-.61C8.29 2.68 7.23 2.36 6.16 2.45c-1.39.12-2.61.94-3.23 2.19-.61 1.23-.49 2.69.32 3.8l.58.8-.93.38c-1.19.49-2.03 1.56-2.25 2.84-.21 1.26.24 2.53 1.18 3.39l.23.21C2.57 18.06 6.94 22 12 22s9.43-3.94 9.94-5.94l.23-.21c.94-.86 1.39-2.13 1.18-3.39-.22-1.28-1.06-2.35-2.25-2.84l-.22-.09zM12 20c-3.87 0-7-3.58-7-8 0-3.35 1.4-6.4 3.4-8.08.7.67 1.63 1.08 2.6 1.08h2c.97 0 1.9-.41 2.6-1.08C17.6 5.6 19 8.65 19 12c0 4.42-3.13 8-7 8z"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-wide drop-shadow-md">
                NOCHI FARM
            </h1>
            <p class="text-xs sm:text-sm font-medium text-rose-200 mt-0.5 tracking-wider uppercase">
                Sistem Input & Manajemen Kandang
            </p>
        </div>

        <!-- Main Login Card (Pure White with Maroon Accents) -->
        <div class="login-card p-7 sm:p-9 relative">

            <!-- Card Header Badge -->
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-800">Silakan Masuk</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gunakan akun terdaftar di database farm</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0 border border-rose-100 shadow-xs">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Flash Error Notification -->
            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-start gap-2.5 shadow-xs animate-shake">
                    <i data-lucide="alert-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600 shrink-0 mt-0.5"></i>
                    <div class="font-medium leading-relaxed">{{ session('error') }}</div>
                </div>
            @endif

            <!-- Flash Success Notification -->
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-2.5 shadow-xs">
                    <i data-lucide="check-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                    <div class="font-medium leading-relaxed">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4 sm:space-y-5" autocomplete="on">
                @csrf

                <!-- Input Username / Email -->
                <div>
                    <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Username / Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4 text-maroon-700"></i>
                        </div>
                        <input type="text" 
                               name="login" 
                               id="login" 
                               value="{{ old('login') }}" 
                               required 
                               autofocus
                               placeholder="Masukkan username atau email" 
                               class="custom-input w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all">
                    </div>
                    @error('login')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4 text-maroon-700"></i>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               placeholder="Masukkan password akun" 
                               class="custom-input w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all">
                        
                        <!-- Toggle Password Visibility Button -->
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-maroon-800 focus:outline-none transition-colors"
                                title="Tampilkan/Sembunyikan Password">
                            <i id="passwordToggleIcon" data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" 
                               name="remember" 
                               id="remember" 
                               {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 text-maroon-800 border-slate-300 rounded focus:ring-maroon-800 focus:ring-offset-0 transition-colors">
                        <span class="text-xs text-slate-600 group-hover:text-slate-900 font-medium select-none">
                            Ingat Saya di Perangkat Ini
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-maroon-900 via-maroon-800 to-rose-700 hover:from-maroon-950 hover:to-rose-800 text-white font-bold text-sm rounded-xl shadow-lg shadow-maroon-900/30 hover:shadow-maroon-900/40 active:scale-[0.99] transition-all flex items-center justify-center gap-2 group">
                    <span>Masuk ke Sistem</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- Quick Account Info / Help Box -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400 font-medium">
                    Hak akses pengguna diatur langsung oleh 
                    <span class="font-bold text-maroon-800">Admin Farm</span>
                </p>
            </div>

        </div>

        <!-- Footer Copyright -->
        <p class="text-center text-xs text-rose-200/80 mt-6 font-medium">
            &copy; {{ date('Y') }} NOCHI FARM. Hak Cipta Dilindungi.
        </p>

    </div>

    <script>
        // Inisialisasi icon Lucide
        lucide.createIcons();

        // Fungsi Toggle Visibility Password
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
