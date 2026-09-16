@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Pengaturan Aplikasi</h1>
                <p class="text-xs text-slate-400">Preferensi tampilan sistem & informasi aplikasi</p>
            </div>
        </div>
    </div>

    <!-- Form Parameter Pengaturan Sistem (Engine) -->
    <div class="farm-card p-5 sm:p-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
            <div class="w-10 h-10 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center font-bold text-lg">
                ⚙
            </div>
            <div>
                <h2 class="text-sm sm:text-base font-bold text-slate-800">Parameter Pengaturan Sistem</h2>
                <p class="text-xs text-slate-400">Acuan konversi tray, berat telur, dan batas performa Hen Day (HD)</p>
            </div>
        </div>

        <form method="POST" action="{{ route('master.settings.update') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- 1. Isi Tray -->
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-1.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Isi Tray</label>
                            <span class="text-[11px] text-slate-400">Konversi butir → tray</span>
                        </div>
                    </div>
                    <div class="relative flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-maroon-800/20 focus-within:border-maroon-800">
                        <input type="number" step="1" min="1" name="isi_tray" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['isi_tray'] ?? '30')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm font-bold text-slate-800 border-none outline-none text-right">
                        <span class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border-l border-slate-200">butir</span>
                    </div>
                </div>

                <!-- 2. Berat Telur -->
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-1.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Berat Telur</label>
                            <span class="text-[11px] text-slate-400">Acuan berat per butir</span>
                        </div>
                    </div>
                    <div class="relative flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-maroon-800/20 focus-within:border-maroon-800">
                        <input type="number" step="0.001" min="0.001" name="berat_telur" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['berat_telur'] ?? '0.06')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm font-bold text-slate-800 border-none outline-none text-right">
                        <span class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border-l border-slate-200">kg</span>
                    </div>
                </div>

                <!-- 2b. Berat Karung Pakan -->
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-1.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Berat Karung Pakan</label>
                            <span class="text-[11px] text-slate-400">Konversi kg → karung</span>
                        </div>
                    </div>
                    <div class="relative flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-maroon-800/20 focus-within:border-maroon-800">
                        <input type="number" step="0.5" min="1" name="berat_per_karung" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['berat_per_karung'] ?? '50')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm font-bold text-slate-800 border-none outline-none text-right">
                        <span class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border-l border-slate-200">kg/krg</span>
                    </div>
                </div>

                <!-- 3. HD Target -->
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-1.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">HD Target</label>
                            <span class="text-[11px] text-slate-400">Target performa produksi</span>
                        </div>
                    </div>
                    <div class="relative flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-maroon-800/20 focus-within:border-maroon-800">
                        <input type="number" step="0.1" min="0" max="100" name="hd_target" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['hd_target'] ?? '95')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm font-bold text-slate-800 border-none outline-none text-right">
                        <span class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border-l border-slate-200">%</span>
                    </div>
                </div>

                <!-- 4. HD Warning -->
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-1.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">HD Warning</label>
                            <span class="text-[11px] text-slate-400">Batas peringatan waspada</span>
                        </div>
                    </div>
                    <div class="relative flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-maroon-800/20 focus-within:border-maroon-800">
                        <input type="number" step="0.1" min="0" max="100" name="hd_warning" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['hd_warning'] ?? '90')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm font-bold text-slate-800 border-none outline-none text-right">
                        <span class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border-l border-slate-200">%</span>
                    </div>
                </div>

                <!-- 5. HD Minimum -->
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-1.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">HD Minimum</label>
                            <span class="text-[11px] text-slate-400">Batas kritis minimum performa</span>
                        </div>
                    </div>
                    <div class="relative flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-maroon-800/20 focus-within:border-maroon-800">
                        <input type="number" step="0.1" min="0" max="100" name="hd_minimum" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['hd_minimum'] ?? '88')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm font-bold text-slate-800 border-none outline-none text-right">
                        <span class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border-l border-slate-200">%</span>
                    </div>
                </div>

                <!-- 6. Reject Maksimum -->
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-1.5">
                    <div class="flex justify-between items-start">
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Reject Maksimum</label>
                            <span class="text-[11px] text-slate-400">Batas toleransi telur reject</span>
                        </div>
                    </div>
                    <div class="relative flex items-center bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs focus-within:ring-2 focus-within:ring-maroon-800/20 focus-within:border-maroon-800">
                        <input type="number" step="0.1" min="0" max="100" name="reject_maximum" value="{{ preg_replace('/[^0-9.]/', '', str_replace(',', '.', $systemSettings['reject_maximum'] ?? '2')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm font-bold text-slate-800 border-none outline-none text-right">
                        <span class="px-3 py-2 text-xs font-bold text-slate-500 bg-slate-100 border-l border-slate-200">%</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('master.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all active:scale-95 flex items-center gap-1.5">
                    Kembali
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition-all active:scale-95 flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Parameter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Informasi Lingkungan & Status Aplikasi -->
    <div class="farm-card p-5 sm:p-6 space-y-5">
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Tema Warna Antarmuka</h3>
                    <p class="text-xs text-slate-400">Tema default yang diterapkan pada seluruh modul</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-maroon-800 border-2 border-white shadow-xs"></span>
                    <span class="text-xs font-bold text-maroon-800">Merah Marun & Putih</span>
                </div>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Versi Aplikasi</h3>
                    <p class="text-xs text-slate-400">Nochi Farm Input System</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-slate-100 text-slate-700">
                    v1.0.0 Stable
                </span>
            </div>

            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Koneksi Database Bersama</h3>
                    <p class="text-xs text-slate-400">Terintegrasi dengan sistem penjualan & gudang Nochi Farm</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-700">Terhubung (MySQL nochifram)</span>
                </div>
            </div>
        </div>

        <div class="pt-2 flex justify-start">
            <a href="{{ route('master.info-farm') }}" class="px-5 py-2.5 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs font-bold shadow-md transition-all active:scale-95">
                Buka Pengaturan Info Farm & Tampilan
            </a>
        </div>
    </div>

</div>
@endsection
