@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.index') }}#card-standar-produksi" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Standar Produksi, Pakan & Bobot</h1>
                <p class="text-xs text-slate-400">Parameter acuan performa ayam petelur harian</p>
            </div>
        </div>
    </div>

    <!-- Form Standar Operasional -->
    <div class="farm-card p-5 sm:p-6">
        <form method="POST" action="{{ route('master.standards.update') }}" class="space-y-5">
            @csrf

            <!-- 1. Standar Produksi Telur -->
            <div>
                <h3 class="text-xs font-bold text-maroon-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="egg" class="w-4 h-4 text-amber-500"></i>
                    1. Standar Produksi Telur
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Produksi Telur Harian (Peti) *</label>
                        <input type="number" step="1" name="standard_production_egg_crates" value="{{ $standards['standard_production_egg_crates'] }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                        <span class="text-[11px] text-slate-400 mt-1 block">Acuan baseline produksi telur normal per hari</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Hen Day Production (HDP)</label>
                        <div class="relative">
                            <input type="text" readonly value="85.5% - 92.0%" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm font-bold text-slate-600 cursor-not-allowed">
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Persentase ayam bertelur per populasi aktif</span>
                    </div>
                </div>
            </div>

            <!-- 2. Standar Pakan -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-maroon-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="wheat" class="w-4 h-4 text-emerald-600"></i>
                    2. Standar Konsumsi Pakan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kebutuhan Pakan per Ekor (Gram / Hari) *</label>
                        <input type="number" step="0.5" name="standard_feed_gram_per_chicken" value="{{ $standards['standard_feed_gram_per_chicken'] }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                        <span class="text-[11px] text-slate-400 mt-1 block">Standar konsumsi pakan ayam layer umur 20-30 minggu (110 - 120 g)</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Feed Conversion Ratio (FCR)</label>
                        <input type="text" readonly value="2.10 - 2.20" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm font-bold text-slate-600 cursor-not-allowed">
                        <span class="text-[11px] text-slate-400 mt-1 block">Rasio kilogram pakan terhadap kilogram telur</span>
                    </div>
                </div>
            </div>

            <!-- 3. Standar Bobot Badan (BB) -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-maroon-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="scale" class="w-4 h-4 text-blue-600"></i>
                    3. Standar Berat Badan (BB) Ayam Layer
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Bobot Rata-rata (Kg) *</label>
                        <input type="number" step="0.01" name="standard_avg_weight_kg" value="{{ $standards['standard_avg_weight_kg'] }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                        <span class="text-[11px] text-slate-400 mt-1 block">Standar berat badan ayam betina umur 21-25 minggu</span>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Toleransi Bobot (+/- Kg) *</label>
                        <input type="number" step="0.01" name="standard_weight_tolerance" value="{{ $standards['standard_weight_tolerance'] }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                        <span class="text-[11px] text-slate-400 mt-1 block">Deviasi batas wajar sampling timbang bobot</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Standar Performa</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
