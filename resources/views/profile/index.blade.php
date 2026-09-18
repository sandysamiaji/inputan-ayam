@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">

    <!-- Top Navigation Header (Sesuai Mockup Layar 2) -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">Profil</h1>
                <p class="text-xs sm:text-sm text-slate-400">Informasi akun petugas kandang</p>
            </div>
        </div>
    </div>

    <!-- KARTU PROFIL UTAMA (Sesuai Mockup Gambar Layar 2) -->
    <div class="farm-card p-6 sm:p-8 text-center sm:text-left relative overflow-hidden">
        <!-- Background decorative accent -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-maroon-800 via-rose-600 to-amber-500"></div>

        <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-6">
            
            <!-- Avatar Petugas (Sesuai Mockup) -->
            <div class="relative shrink-0">
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-gradient-to-br from-emerald-100 to-teal-50 border-4 border-white shadow-xl flex items-center justify-center overflow-hidden">
                    <!-- SVG Avatar Petugas Kandang dengan Topi -->
                    <svg class="w-20 h-20 text-emerald-800" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Head -->
                        <circle cx="50" cy="45" r="20" fill="#fed7aa"/>
                        <!-- Cap / Topi Petugas -->
                        <path d="M30 38 C30 24, 70 24, 70 38 Z" fill="#047857"/>
                        <path d="M22 38 Q50 32 78 38 L82 42 Q50 36 18 42 Z" fill="#065f46"/>
                        <!-- Badge Topi -->
                        <polygon points="50,27 53,33 47,33" fill="#facc15"/>
                        <!-- Body / Seragam Hijau -->
                        <path d="M20 95 C20 70, 32 65, 50 65 C68 65, 80 70, 80 95 Z" fill="#059669"/>
                        <!-- Kerah Seragam -->
                        <path d="M42 65 L50 78 L58 65 Z" fill="#047857"/>
                        <circle cx="50" cy="85" r="2" fill="#ffffff"/>
                    </svg>
                </div>
                <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white shadow-xs"></span>
            </div>

            <!-- Detail Identitas Petugas -->
            <div class="space-y-2 flex-1">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama</span>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-800">
                        {{ $user ? $user->name : 'Petugas Kandang' }}
                    </h2>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Username</span>
                    <p class="text-sm font-bold text-slate-700">
                        {{ $user ? $user->username : 'petugas01' }}
                    </p>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Role / Jabatan</span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-maroon-50 text-maroon-800 border border-maroon-200 mt-0.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-maroon-800"></i>
                        {{ $user && $user->role === 'admin' ? 'Administrator Farm' : 'Petugas Kandang' }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- MENU AKSI PROFIL (Sesuai Mockup Gambar Layar 2) -->
    <div class="farm-card divide-y divide-slate-100 overflow-hidden">
        
        <!-- 1. Ubah Password -->
        <button onclick="openModalChangePassword()" class="w-full p-4 sm:p-5 text-left flex items-center justify-between hover:bg-slate-50 transition-colors group active:scale-99">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 group-hover:bg-rose-50 text-slate-600 group-hover:text-maroon-800 flex items-center justify-center transition-colors">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 group-hover:text-maroon-800 transition-colors">Ubah Password</h3>
                    <p class="text-xs text-slate-400">Ganti kata sandi akun petugas</p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-slate-400 group-hover:text-maroon-700 transition-colors"></i>
        </button>

        <!-- 2. Tentang Aplikasi -->
        <button onclick="openModalAboutApp()" class="w-full p-4 sm:p-5 text-left flex items-center justify-between hover:bg-slate-50 transition-colors group active:scale-99">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 group-hover:bg-rose-50 text-slate-600 group-hover:text-maroon-800 flex items-center justify-center transition-colors">
                    <i data-lucide="info" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 group-hover:text-maroon-800 transition-colors">Tentang Aplikasi</h3>
                    <p class="text-xs text-slate-400">Informasi sistem & versi Nochi Farm Input</p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-slate-400 group-hover:text-maroon-700 transition-colors"></i>
        </button>

        <!-- 3. Logout -->
        <form method="POST" action="{{ route('profile.logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari aplikasi?');">
            @csrf
            <button type="submit" class="w-full p-4 sm:p-5 text-left flex items-center justify-between hover:bg-rose-50/60 transition-colors group active:scale-99">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-rose-600">Logout</h3>
                        <p class="text-xs text-rose-400">Keluar dari sesi akun saat ini</p>
                    </div>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-rose-400"></i>
            </button>
        </form>

    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL UBAH PASSWORD -->
<!-- ========================================================================= -->
<div id="modalChangePassword" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-maroon-800 flex items-center justify-center">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Ubah Password Akun</h3>
            </div>
            <button onclick="closeModalChangePassword()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('profile.update-password') }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Password Lama *</label>
                <input type="password" name="current_password" required placeholder="Masukkan password lama" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru *</label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password Baru *</label>
                <input type="password" name="password_confirmation" required minlength="6" placeholder="Ulangi password baru" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Password Baru
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TENTANG APLIKASI -->
<!-- ========================================================================= -->
<div id="modalAboutApp" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 text-center space-y-4">
        
        <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center mx-auto shadow-lg border-2 border-slate-200 overflow-hidden p-1">
            <img src="{{ asset('images/logo-nochi.png') }}" alt="Nochi Farm Logo" class="w-full h-full object-contain rounded-full bg-white">
        </div>

        <div>
            <h3 class="text-base sm:text-lg font-black text-slate-900">NOCHI FARM</h3>
            <span class="text-xs font-bold text-maroon-800">Peternak Telur & Manajemen Kandang</span>
            <p class="text-xs text-slate-400 mt-1">Versi 1.0.0 (Production Stable)</p>
        </div>

        <div class="p-3.5 bg-slate-50 rounded-2xl text-xs text-slate-600 leading-relaxed text-left border border-slate-100">
            Aplikasi pencatatan operasional harian terintegrasi Nochi Farm: mencakup pencatatan produksi telur harian, konsumsi pakan, pemantauan mortalitas, timbang bobot ayam, riwayat vaksinasi, manajemen pergudangan, serta analitik rekapan performa kandang.
        </div>

        <button onclick="closeModalAboutApp()" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
            Tutup
        </button>

    </div>
</div>

@push('scripts')
<script>
    function openModalChangePassword() {
        const modal = document.getElementById('modalChangePassword');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalChangePassword() {
        const modal = document.getElementById('modalChangePassword');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    function openModalAboutApp() {
        const modal = document.getElementById('modalAboutApp');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalAboutApp() {
        const modal = document.getElementById('modalAboutApp');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }
</script>
@endpush
@endsection
