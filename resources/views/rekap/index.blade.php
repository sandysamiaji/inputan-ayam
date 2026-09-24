@extends('layouts.app')

@section('content')
<!-- Flatpickr CSS & Custom Theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.flatpickr-calendar { 
    font-family: Inter, system-ui, sans-serif; 
    box-shadow: 0 10px 25px rgba(128, 0, 32, 0.12) !important; 
    border: 1px solid #f2ccd8 !important; 
    border-radius: 16px !important;
}
.flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, 
.flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, 
.flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, 
.flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover {
    background: #800020 !important;
    border-color: #800020 !important;
}
.flatpickr-day.inRange {
    background: #fff0f4 !important;
    border-color: #fff0f4 !important;
    box-shadow: -5px 0 0 #fff0f4, 5px 0 0 #fff0f4 !important;
}
</style>

<div class="space-y-6 max-w-7xl mx-auto">

    <!-- ========================================================================= -->
    <!-- 1. HERO SECTION: TITLE, DATE FILTER & KLOTER SELECTOR -->
    <!-- ========================================================================= -->
    <div class="farm-card p-4 sm:p-6 bg-gradient-to-r from-white via-white to-rose-50/40 border border-slate-200/90 shadow-xs rounded-2xl relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Title & Status -->
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-maroon-800 via-rose-900 to-maroon-900 text-white flex items-center justify-center shadow-md shadow-maroon-900/20 shrink-0">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">REKAP DATA</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Terhubung
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">Laporan komprehensif & ringkasan analitik data kandang peternakan</p>
                </div>
            </div>

            <!-- Filters: Date Range & Kloter Dropdown -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5">
                <!-- Period Selector Capsule -->
                @if(!auth()->check() || auth()->user()->canAccess('rekap_filter_tanggal'))
                <div class="cursor-pointer bg-white hover:bg-slate-50 border border-slate-200/90 hover:border-maroon-300 shadow-2xs hover:shadow-sm transition-all rounded-xl p-2 px-3 flex items-center justify-between gap-2.5 group select-none" 
                     id="travelokaDatePicker" 
                     title="Klik untuk memilih rentang tanggal">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Periode Rekap</div>
                            <div class="text-xs sm:text-sm font-black text-slate-800 whitespace-nowrap">
                                {{ $formattedRange }}
                            </div>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 group-hover:text-maroon-800 transition-colors"></i>
                </div>
                @else
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-2 px-3 flex items-center gap-2 select-none opacity-80 cursor-not-allowed">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Periode Rekap</div>
                        <div class="text-xs sm:text-sm font-black text-slate-700 whitespace-nowrap">{{ $formattedRange }}</div>
                    </div>
                </div>
                @endif

                <!-- Kloter Filter Dropdown -->
                @if(!auth()->check() || auth()->user()->canAccess('rekap_filter'))
                <div class="bg-white border border-slate-200/90 hover:border-maroon-300 shadow-2xs hover:shadow-sm transition-all rounded-xl p-2 px-3 flex items-center justify-between gap-2 group">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Filter Populasi</div>
                            <select onchange="filterKloter(this.value)" class="w-full bg-transparent font-black text-xs sm:text-sm text-slate-800 outline-none cursor-pointer truncate pr-4">
                                <option value="" {{ empty($flockId) ? 'selected' : '' }}>
                                    Semua Kloter ({{ number_format($totalFarmPopulation, 0, ',', '.') }} ekor)
                                </option>
                                @foreach($allFlocks as $flk)
                                    <option value="{{ $flk->id }}" {{ ($flockId == $flk->id) ? 'selected' : '' }}>
                                        {{ $flk->name }} · {{ $flk->code }} ({{ number_format($flk->coops->sum('active_chickens'), 0, ',', '.') }} ekor)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Preset Date Buttons -->
        <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-wrap items-center gap-1.5 sm:gap-2">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mr-1">Preset:</span>
            
            @php
                $presets = [
                    'hari_ini'   => 'Hari Ini',
                    'kemarin'    => 'Kemarin',
                    '7_hari'     => '7 Hari',
                    '30_hari'    => '30 Hari',
                    'bulan_ini'  => 'Bulan Ini',
                    'bulan_lalu' => 'Bulan Lalu',
                ];
            @endphp

            @foreach($presets as $pKey => $pLabel)
                @php
                    $isActive = ($preset === $pKey);
                    $url = route('rekap.index', array_filter([
                        'preset' => $pKey,
                        'flock_id' => $flockId,
                        'tab' => $activeTab
                    ]));
                @endphp
                <a href="{{ $url }}" 
                   class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ $isActive ? 'bg-maroon-800 text-white shadow-xs' : 'bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-maroon-800 border border-slate-200/80 hover:border-maroon-300' }}">
                    {{ $pLabel }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. TAB NAVIGATOR (4 PILIHAN REKAP) -->
    <!-- ========================================================================= -->
    <div>
        <div class="flex items-center justify-between mb-2.5 px-1">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Pilih Modul Rekapitulasi:</span>
            <span class="text-[11px] text-slate-400 font-medium">Data bersifat read-only untuk analisis performa</span>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Tab 1: Produksi -->
            <button type="button" 
                    class="rekap-tab-btn p-3.5 sm:p-4 rounded-2xl border transition-all text-left group flex items-center gap-3.5 cursor-pointer select-none {{ $activeTab === 'produksi' ? 'border-amber-400 bg-amber-50/40 text-amber-900 shadow-xs ring-2 ring-amber-400/20' : 'border-slate-200/90 bg-white hover:border-slate-300 hover:bg-slate-50/70 text-slate-700' }}"
                    data-tab="produksi" 
                    onclick="switchRekapTab('produksi')">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                    🥚
                </div>
                <div class="min-w-0">
                    <div class="text-xs sm:text-sm font-black truncate">Rekap Produksi</div>
                    <div class="text-[11px] font-bold text-slate-500 mt-0.5 truncate">
                        {{ number_format($totalTelurButir, 0, ',', '.') }} Butir • HDP {{ number_format($hdp, 1, ',', '.') }}%
                    </div>
                </div>
            </button>

            <!-- Tab 2: Pakan -->
            <button type="button" 
                    class="rekap-tab-btn p-3.5 sm:p-4 rounded-2xl border transition-all text-left group flex items-center gap-3.5 cursor-pointer select-none {{ $activeTab === 'pakan' ? 'border-emerald-400 bg-emerald-50/40 text-emerald-900 shadow-xs ring-2 ring-emerald-400/20' : 'border-slate-200/90 bg-white hover:border-slate-300 hover:bg-slate-50/70 text-slate-700' }}"
                    data-tab="pakan" 
                    onclick="switchRekapTab('pakan')">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                    🌾
                </div>
                <div class="min-w-0">
                    <div class="text-xs sm:text-sm font-black truncate">Rekap Pakan</div>
                    <div class="text-[11px] font-bold text-slate-500 mt-0.5 truncate">
                        {{ number_format($totalPakanKg, 0, ',', '.') }} Kg Pakan
                    </div>
                </div>
            </button>

            <!-- Tab 3: Mortalitas -->
            <button type="button" 
                    class="rekap-tab-btn p-3.5 sm:p-4 rounded-2xl border transition-all text-left group flex items-center gap-3.5 cursor-pointer select-none {{ $activeTab === 'mortalitas' ? 'border-rose-400 bg-rose-50/40 text-rose-900 shadow-xs ring-2 ring-rose-400/20' : 'border-slate-200/90 bg-white hover:border-slate-300 hover:bg-slate-50/70 text-slate-700' }}"
                    data-tab="mortalitas" 
                    onclick="switchRekapTab('mortalitas')">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                    ☠️
                </div>
                <div class="min-w-0">
                    <div class="text-xs sm:text-sm font-black truncate">Rekap Mortalitas</div>
                    <div class="text-[11px] font-bold text-slate-500 mt-0.5 truncate">
                        {{ number_format($totalMortalitas, 0, ',', '.') }} Ekor ({{ number_format($mortalitasRate, 2, ',', '.') }}%)
                    </div>
                </div>
            </button>

            <!-- Tab 4: Kesehatan -->
            <button type="button" 
                    class="rekap-tab-btn p-3.5 sm:p-4 rounded-2xl border transition-all text-left group flex items-center gap-3.5 cursor-pointer select-none {{ $activeTab === 'kesehatan' ? 'border-purple-400 bg-purple-50/40 text-purple-900 shadow-xs ring-2 ring-purple-400/20' : 'border-slate-200/90 bg-white hover:border-slate-300 hover:bg-slate-50/70 text-slate-700' }}"
                    data-tab="kesehatan" 
                    onclick="switchRekapTab('kesehatan')">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                    💊
                </div>
                <div class="min-w-0">
                    <div class="text-xs sm:text-sm font-black truncate">Rekap Kesehatan</div>
                    <div class="text-[11px] font-bold text-slate-500 mt-0.5 truncate">
                        {{ $totalVaksinKegiatan }} Tindakan Medis
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: REKAP PRODUKSI TELUR -->
    <!-- ========================================================================= -->
    <div id="tab-produksi" style="{{ $activeTab === 'produksi' ? '' : 'display:none;' }}" class="space-y-6">

        <!-- 4 Stat Summary Cards: Produksi, HDP, Reject, Bobot -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            <!-- 1. Total Produksi -->
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Total Produksi</span>
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="egg" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="text-xl sm:text-2xl font-black text-maroon-800 tracking-tight">
                        {{ number_format($totalTelurButir, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">butir</span>
                    </div>
                    <div class="text-xs font-bold text-slate-600 mt-0.5">
                        {{ number_format($totalTelurPeti, 0, ',', '.') }} Peti Telur
                    </div>
                </div>
                <div class="text-[10px] text-slate-400 font-medium pt-1 border-t border-slate-100 flex items-center justify-between">
                    <span>Hasil Kandang</span>
                    <span class="text-emerald-700 font-bold">1 Peti = 10 Kg</span>
                </div>
            </div>

            <!-- 2. HDP -->
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">HDP Rata-rata</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="text-xl sm:text-2xl font-black text-emerald-700 tracking-tight">
                        {{ number_format($hdp, 1, ',', '.') }}<span class="text-xs font-bold text-emerald-600">%</span>
                    </div>
                    <div class="text-xs font-bold text-slate-500 mt-0.5">
                        Hen Day Production
                    </div>
                </div>
                <div class="text-[10px] text-slate-400 font-medium pt-1 border-t border-slate-100 flex items-center justify-between">
                    <span>Target Master</span>
                    <span class="text-emerald-700 font-bold">90.0%</span>
                </div>
            </div>

            <!-- 3. Reject / Pecah -->
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Tingkat Reject</span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="text-xl sm:text-2xl font-black text-rose-700 tracking-tight">
                        {{ number_format($rejectRate, 1, ',', '.') }}<span class="text-xs font-bold text-rose-600">%</span>
                    </div>
                    <div class="text-xs font-bold text-slate-500 mt-0.5">
                        {{ number_format($totalTelurBroken, 0, ',', '.') }} Butir Rusak/Pecah
                    </div>
                </div>
                <div class="text-[10px] text-slate-400 font-medium pt-1 border-t border-slate-100 flex items-center justify-between">
                    <span>Toleransi Max</span>
                    <span class="text-slate-600 font-bold">&lt; 1.5%</span>
                </div>
            </div>

            <!-- 4. Berat Badan -->
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Rata-rata BB Ayam</span>
                    <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i data-lucide="scale" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">
                        {{ number_format($avgBobot, 2, ',', '.') }} <span class="text-xs font-bold text-slate-500">kg/ekor</span>
                    </div>
                    <div class="text-xs font-bold text-slate-500 mt-0.5">
                        Sampel Timbang Mingguan
                    </div>
                </div>
                <div class="text-[10px] text-slate-400 font-medium pt-1 border-t border-slate-100 flex items-center justify-between">
                    <span>Status Evaluasi</span>
                    <span class="text-sky-700 font-bold">Standard</span>
                </div>
            </div>
        </div>

        <!-- STATUS BLOK KANDANG AKTIF (Produksi per Blok & Kloter) -->
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-maroon-800 flex items-center justify-center shadow-2xs">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">STATUS BLOK KANDANG AKTIF</h3>
                        <p class="text-[11px] text-slate-400">Rincian performa produksi telur per blok dan kloter</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 self-start sm:self-auto">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ count($blokRekap) }} Blok Aktif
                </span>
            </div>

            <!-- Ringkasan per Kloter -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                @foreach($flockRekap as $fr)
                    <div class="p-3.5 sm:p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-xs font-black bg-maroon-100 text-maroon-800">{{ $fr['name'] }}</span>
                                <span class="text-xs text-slate-500 font-medium">({{ number_format($fr['chickens'], 0, ',', '.') }} ekor aktif)</span>
                            </div>
                            <span class="text-xs font-extrabold {{ $fr['hdp'] >= 70 ? 'text-emerald-700' : 'text-rose-600' }}">
                                HDP {{ number_format($fr['hdp'], 1, ',', '.') }}%
                            </span>
                        </div>
                        <div class="mt-2 text-base sm:text-lg font-black text-slate-800">
                            {{ number_format($fr['eggs'], 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">butir</span>
                            <span class="text-xs font-bold text-slate-400">• {{ number_format($fr['crates'], 0, ',', '.') }} Peti</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List Blok Kandang Aktif (3 Columns Responsive) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-1">
                @foreach($blokRekap as $br)
                    @php
                        $isBrHdpGood = !isset($br['target_hd']) || (float)$br['hdp'] >= (float)$br['target_hd'];
                    @endphp
                    <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/90 hover:border-maroon-300 hover:shadow-sm transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Header Blok -->
                            <div class="flex items-center justify-between gap-1.5 mb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-sm text-slate-800">{{ $br['name'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9.5px] font-extrabold bg-slate-100 text-slate-600 uppercase">{{ $br['flock_name'] }}</span>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                    {{ $br['chicken_age_weeks'] }} Mgg
                                </span>
                            </div>

                            <!-- Populasi -->
                            <div class="text-[11px] text-slate-400">
                                Populasi: <b class="text-slate-700">{{ number_format($br['active_chickens'], 0, ',', '.') }}</b> / {{ number_format($br['capacity'], 0, ',', '.') }} ekor
                            </div>

                            <!-- Produksi Telur & HDP -->
                            <div class="mt-3 flex items-baseline justify-between">
                                <div>
                                    <div class="text-base font-black text-maroon-800">
                                        {{ number_format($br['eggs'], 0, ',', '.') }} <span class="text-[10px] font-bold text-slate-500">butir</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium">
                                        {{ number_format($br['crates'], 0, ',', '.') }} Peti Telur
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-black {{ $isBrHdpGood ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        HDP {{ number_format($br['hdp'], 1, ',', '.') }}%
                                    </span>
                                    <div class="text-[9.5px] text-slate-400 mt-0.5">
                                        Target: {{ $br['target_hd'] ?? 90 }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3 pt-2 border-t border-slate-100">
                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $isBrHdpGood ? 'bg-emerald-600' : 'bg-maroon-800' }} rounded-full" style="width: {{ min(100, $br['hdp']) }}%;"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Rekap Mingguan per Kloter -->
        @foreach($weeklyRekapsByFlock as $flockRekapData)
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs space-y-3.5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-maroon-700"></span>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">
                        REKAP MINGGUAN — {{ strtoupper($flockRekapData['flock_name']) }}
                    </h3>
                </div>
                <span class="text-xs text-slate-400 font-medium">Pergerakan 4 Minggu Terakhir</span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($flockRekapData['data'] as $w)
                    <div class="p-3.5 sm:p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:bg-white hover:border-slate-300 hover:shadow-xs transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-xs font-black text-maroon-800">Umur M{{ $w['age_week'] }}</span>
                                <span class="text-[9.5px] font-bold text-slate-400">{{ $w['date_range'] }}</span>
                            </div>
                            <div class="text-lg sm:text-xl font-black text-slate-900 mt-2">
                                {{ number_format($w['eggs'], 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-slate-500 font-medium">
                                butir · {{ number_format($w['crates'], 0, ',', '.') }} peti
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200/70 flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-400">Performa:</span>
                            <span class="text-xs font-black {{ $w['hdp'] >= 70 ? 'text-emerald-700' : ($w['hdp'] > 0 ? 'text-amber-700' : 'text-slate-400') }}">
                                HDP {{ number_format($w['hdp'], 1, ',', '.') }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Grafik Tren Telur Masuk vs Keluar -->
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center shadow-2xs">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base" id="chartTitle">GRAFIK TREN TELUR</h3>
                        <p class="text-[11px] text-slate-400">Komparasi harian produksi telur kandang vs penjualan keluar</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <select id="selectChartMetric" onchange="updateChartMetric(this.value)" class="px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 bg-slate-50 hover:bg-white text-slate-700 outline-none cursor-pointer shadow-2xs">
                        <option value="egg_peti" selected>Peti Masuk vs Keluar</option>
                        <option value="egg_kg">Kg Masuk vs Keluar</option>
                        <option value="egg_butir">Butir Masuk vs Keluar</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 relative w-full rounded-2xl bg-slate-50/40 p-2 sm:p-3 border border-slate-100" style="height: 320px;">
                <canvas id="rekapTrendChart"></canvas>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: REKAP PAKAN -->
    <!-- ========================================================================= -->
    <div id="tab-pakan" style="{{ $activeTab === 'pakan' ? '' : 'display:none;' }}" class="space-y-6">

        <!-- Ringkasan Pakan -->
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 mb-4">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shadow-2xs">
                    <i data-lucide="wheat" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">RINGKASAN KONSUMSI PAKAN</h3>
                    <p class="text-[11px] text-slate-400">Akumulasi pemberian pakan kandang pada rentang tanggal terpilih</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200/80 flex flex-col justify-between">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-800 tracking-wider">Total Konsumsi Pakan</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-900 mt-2">
                        {{ number_format($totalPakanKg, 0, ',', '.') }} <span class="text-sm font-bold text-slate-600">Kg</span>
                    </div>
                    <div class="text-xs font-bold text-emerald-800 mt-1">
                        {{ $totalPakanKarungStr }}
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col justify-between">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">Populasi Penerima Pakan</span>
                    <div class="text-2xl sm:text-3xl font-black text-slate-800 mt-2">
                        {{ number_format($activePopulation, 0, ',', '.') }} <span class="text-sm font-bold text-slate-500">ekor</span>
                    </div>
                    <div class="text-xs text-slate-500 font-medium mt-1">
                        Tersebar di {{ count($blokRekap) }} Blok Kandang Aktif
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekap Mingguan Pakan -->
        @foreach($weeklyRekapsByFlock as $flockRekapData)
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs space-y-3.5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">
                        REKAP MINGGUAN PAKAN — {{ strtoupper($flockRekapData['flock_name']) }}
                    </h3>
                </div>
                <span class="text-xs text-slate-400 font-medium">Konsumsi per Umur</span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($flockRekapData['data'] as $w)
                    <div class="p-3.5 sm:p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:bg-white hover:border-slate-300 hover:shadow-xs transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-xs font-black text-emerald-800">Umur M{{ $w['age_week'] }}</span>
                                <span class="text-[9.5px] font-bold text-slate-400">{{ $w['date_range'] }}</span>
                            </div>
                            <div class="text-lg sm:text-xl font-black text-slate-900 mt-2">
                                {{ number_format($w['feed_kg'], 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">kg</span>
                            </div>
                            <div class="text-[11px] text-slate-500 font-medium truncate">
                                {{ $w['feed_karung_str'] }}
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200/70 flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-400">Fase Pakan:</span>
                            <span class="text-xs font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                {{ $w['feed_fase'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Pemakaian per Blok (Aktual vs Master) -->
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shadow-2xs">
                        <i data-lucide="sliders" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">PEMAKAIAN PER BLOK KANDANG</h3>
                        <p class="text-[11px] text-slate-400">Komparasi total input pakan aktual vs standar hitungan master</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($blokRekap as $br)
                    @php
                        $diff = $br['feed_kg'] - $br['master_feed_kg'];
                        $isOver = $diff > 0;
                        $isUnder = $diff < 0;
                        $diffBadge = $isOver ? 'bg-rose-50 text-rose-700 border-rose-200' : ($isUnder ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                        $diffText = $isOver ? '+'.number_format($diff, 1, ',', '.') : ($isUnder ? number_format($diff, 1, ',', '.') : 'Sesuai');
                    @endphp
                    <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200/90 hover:border-emerald-300 hover:shadow-xs transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-black text-sm text-slate-800">{{ $br['name'] }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-600">{{ $br['flock_name'] }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400">
                                Standar Master: <b class="text-slate-700">{{ number_format($br['master_feed_kg'], 1, ',', '.') }} kg</b>
                            </div>
                            <div class="mt-2 text-base font-black text-slate-900">
                                Aktual: {{ number_format($br['feed_kg'], 1, ',', '.') }} <span class="text-xs font-bold text-slate-500">kg</span>
                            </div>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-[10px] font-bold text-slate-400">Selisih Input:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold border {{ $diffBadge }}">
                                {{ $diffText }} {{ $diffText !== 'Sesuai' ? 'kg' : '' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: REKAP MORTALITAS -->
    <!-- ========================================================================= -->
    <div id="tab-mortalitas" style="{{ $activeTab === 'mortalitas' ? '' : 'display:none;' }}" class="space-y-6">

        <!-- Ringkasan Mortalitas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Total Kematian</span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i data-lucide="skull" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="text-2xl sm:text-3xl font-black text-rose-700 tracking-tight">
                        {{ number_format($totalMortalitas, 0, ',', '.') }} <span class="text-sm font-bold text-slate-500">ekor</span>
                    </div>
                    <div class="text-xs font-bold text-slate-500 mt-0.5">
                        Akumulasi Kematian & Afkir
                    </div>
                </div>
                <div class="text-[10px] text-slate-400 font-medium pt-1 border-t border-slate-100 flex items-center justify-between">
                    <span>Populasi Hidup</span>
                    <span class="text-slate-700 font-bold">{{ number_format($activePopulation, 0, ',', '.') }} ekor</span>
                </div>
            </div>

            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Persentase Mortalitas</span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i data-lucide="percent" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="my-2">
                    <div class="text-2xl sm:text-3xl font-black text-rose-700 tracking-tight">
                        {{ number_format($mortalitasRate, 2, ',', '.') }}<span class="text-sm font-bold text-rose-600">%</span>
                    </div>
                    <div class="text-xs font-bold text-slate-500 mt-0.5">
                        Rasio terhadap Populasi Aktif
                    </div>
                </div>
                <div class="text-[10px] text-slate-400 font-medium pt-1 border-t border-slate-100 flex items-center justify-between">
                    <span>Ambang Batas</span>
                    <span class="text-slate-600 font-bold">&lt; 0.5% / bulan</span>
                </div>
            </div>
        </div>

        <!-- Rekap Mingguan Mortalitas -->
        @foreach($weeklyRekapsByFlock as $flockRekapData)
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs space-y-3.5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">
                        REKAP MINGGUAN MORTALITAS — {{ strtoupper($flockRekapData['flock_name']) }}
                    </h3>
                </div>
                <span class="text-xs text-slate-400 font-medium">Tren Kematian per Umur</span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($flockRekapData['data'] as $w)
                    <div class="p-3.5 sm:p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:bg-white hover:border-slate-300 hover:shadow-xs transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-xs font-black text-rose-800">Umur M{{ $w['age_week'] }}</span>
                                <span class="text-[9.5px] font-bold text-slate-400">{{ $w['date_range'] }}</span>
                            </div>
                            <div class="text-lg sm:text-xl font-black text-rose-700 mt-2">
                                {{ number_format($w['mortality_count'], 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">ekor</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200/70 flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-400">Rate:</span>
                            <span class="text-xs font-black text-rose-700">
                                {{ number_format($w['mortality_rate'], 2, ',', '.') }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Detail Mortalitas per Blok (Grid 6 Kolom Responsif) -->
        <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs space-y-3.5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">MORTALITAS PER BLOK KANDANG</h3>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach($blokRekap as $br)
                    <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80 text-center flex flex-col justify-between">
                        <div class="text-xs font-extrabold text-slate-800">{{ $br['name'] }}</div>
                        <span class="text-[9.5px] text-slate-400 font-medium">{{ $br['flock_name'] }}</span>
                        <div class="text-base font-black text-rose-700 mt-2">
                            {{ number_format($br['mortality_count'], 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">ekor</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: REKAP KESEHATAN -->
    <!-- ========================================================================= -->
    <div id="tab-kesehatan" style="{{ $activeTab === 'kesehatan' ? '' : 'display:none;' }}" class="space-y-6">

        <!-- Ringkasan Kesehatan Ayam (3 Kolom) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <!-- Vaksin -->
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs text-center flex flex-col justify-between">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="shield" class="w-5 h-5"></i>
                </div>
                <div class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Vaksinasi</div>
                <div class="text-2xl font-black text-purple-700 my-1">{{ $totalVaksin }}</div>
                <div class="text-xs text-slate-500 font-medium">Kegiatan Dilaksanakan</div>
            </div>

            <!-- Obat -->
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs text-center flex flex-col justify-between">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="flask-conical" class="w-5 h-5"></i>
                </div>
                <div class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Pengobatan</div>
                <div class="text-2xl font-black text-indigo-700 my-1">{{ $totalObat }}</div>
                <div class="text-xs text-slate-500 font-medium">Perlakuan Obat Kandang</div>
            </div>

            <!-- Vitamin -->
            <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs text-center flex flex-col justify-between">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <div class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider">Suplemen & Vitamin</div>
                <div class="text-2xl font-black text-amber-700 my-1">{{ $totalVitamin }}</div>
                <div class="text-xs text-slate-500 font-medium">Pemberian Nutrisi Tambahan</div>
            </div>
        </div>

        <!-- Riwayat Tindakan Medis Terakhir -->
        @if($healthTreatments->count() > 0)
            <div class="farm-card p-4 sm:p-6 border border-slate-200/90 rounded-2xl bg-white shadow-xs space-y-3.5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
                            <i data-lucide="history" class="w-4 h-4"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">RIWAYAT TINDAKAN MEDIS TERAKHIR</h3>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($healthTreatments as $ht)
                        <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-slate-50/60 px-2 rounded-xl transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0">
                                    <i data-lucide="pill" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <b class="text-xs sm:text-sm text-slate-800">{{ $ht->medicine_name }}</b>
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold uppercase bg-purple-100 text-purple-800">
                                            {{ ucfirst($ht->type) }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex flex-wrap items-center gap-1.5">
                                        <span>{{ $ht->date ? $ht->date->translatedFormat('d M Y') : '-' }}</span>
                                        <span>• {{ $ht->coop ? $ht->coop->name : 'Semua Blok' }}</span>
                                        <span>• Aplikasi: {{ $ht->application_method ?: 'Campur Pakan/Air' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <span class="text-xs font-black text-slate-700 bg-slate-100 px-2 py-1 rounded-lg">
                                    Dosis: {{ $ht->dosage }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- ========================================================================= -->
    <!-- 3. ACTION LINK: TABEL REKAPITULASI DETAIL & EKSPOR EXCEL -->
    <!-- ========================================================================= -->
    <div class="pt-2 pb-6 text-center">
        <a href="{{ route('rekap.detail', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
           class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-maroon-800 via-rose-800 to-maroon-900 hover:from-maroon-900 hover:to-rose-900 text-white font-extrabold text-xs sm:text-sm shadow-md shadow-maroon-900/20 hover:shadow-lg transition-all group active:scale-95">
            <i data-lucide="table" class="w-4 h-4"></i>
            <span>Buka Tabel Rekapitulasi Detail & Ekspor Excel</span>
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
        </a>
    </div>

</div>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

@push('scripts')
<script>
let currentTab = '{{ $activeTab }}';

function switchRekapTab(tabName) {
    currentTab = tabName;
    const allTabs = ['produksi', 'pakan', 'mortalitas', 'kesehatan'];
    
    allTabs.forEach(t => {
        const el = document.getElementById('tab-' + t);
        const btn = document.querySelector(`.rekap-tab-btn[data-tab="${t}"]`);
        
        if (el) {
            el.style.display = (t === tabName) ? 'block' : 'none';
        }
        
        if (btn) {
            if (t === tabName) {
                btn.className = "rekap-tab-btn p-3.5 sm:p-4 rounded-2xl border transition-all text-left group flex items-center gap-3.5 cursor-pointer select-none border-maroon-600 bg-gradient-to-r from-rose-50 to-white text-maroon-800 shadow-xs ring-2 ring-maroon-600/15";
            } else {
                btn.className = "rekap-tab-btn p-3.5 sm:p-4 rounded-2xl border transition-all text-left group flex items-center gap-3.5 cursor-pointer select-none border-slate-200/90 bg-white hover:border-slate-300 hover:bg-slate-50/70 text-slate-700";
            }
        }
    });

    // Update URL param without page refresh
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function filterKloter(flockId) {
    const url = new URL(window.location);
    if (flockId) {
        url.searchParams.set('flock_id', flockId);
    } else {
        url.searchParams.delete('flock_id');
    }
    url.searchParams.set('tab', currentTab);
    window.location.href = url.toString();
}

// Flatpickr initialization
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#travelokaDatePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: ["{{ $startDate }}", "{{ $endDate }}"],
        locale: "id",
        showMonths: window.innerWidth > 768 ? 2 : 1,
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                const start = instance.formatDate(selectedDates[0], "Y-m-d");
                const end = instance.formatDate(selectedDates[1], "Y-m-d");
                
                const url = new URL(window.location);
                url.searchParams.set('start_date', start);
                url.searchParams.set('end_date', end);
                url.searchParams.set('preset', 'custom');
                url.searchParams.set('tab', currentTab);
                window.location.href = url.toString();
            }
        }
    });
});

// Chart.js initialization
let trendChartInstance = null;
const chartLabels = {!! json_encode($chartLabels) !!};
const chartDataSets = {
    egg_peti: {
        title: 'GRAFIK TREN TELUR (PETI)',
        unit: 'Peti',
        masuk: {!! json_encode($chartEggPetiMasuk) !!},
        keluar: {!! json_encode($chartEggPetiKeluar) !!},
        colorMasuk: '#800020',
        colorKeluar: '#059669'
    },
    egg_kg: {
        title: 'GRAFIK TREN TELUR (KG)',
        unit: 'Kg',
        masuk: {!! json_encode($chartEggKgMasuk) !!},
        keluar: {!! json_encode($chartEggKgKeluar) !!},
        colorMasuk: '#800020',
        colorKeluar: '#059669'
    },
    egg_butir: {
        title: 'GRAFIK TREN TELUR (BUTIR)',
        unit: 'Butir',
        masuk: {!! json_encode($chartEggButirMasuk) !!},
        keluar: {!! json_encode($chartEggButirKeluar) !!},
        colorMasuk: '#800020',
        colorKeluar: '#059669'
    }
};

function renderTrendChart(key) {
    const ctx = document.getElementById('rekapTrendChart');
    if (!ctx) return;
    const metric = chartDataSets[key] || chartDataSets.egg_peti;

    if (trendChartInstance) {
        trendChartInstance.destroy();
    }

    trendChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Produksi Masuk (' + metric.unit + ')',
                    data: metric.masuk,
                    borderColor: metric.colorMasuk,
                    backgroundColor: metric.colorMasuk + '15',
                    borderWidth: 2.2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: metric.colorMasuk,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                },
                {
                    label: 'Penjualan Keluar (' + metric.unit + ')',
                    data: metric.keluar,
                    borderColor: metric.colorKeluar,
                    backgroundColor: metric.colorKeluar + '15',
                    borderWidth: 2.2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: metric.colorKeluar,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true,
                        font: { size: 11, weight: 'bold', family: "'Inter', sans-serif" },
                        color: '#334155',
                        padding: 12
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.94)',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.dataset.label + ': ' + Number(context.parsed.y).toLocaleString('id-ID') + ' ' + metric.unit;
                        }
                    }
                }
            },
            scales: {
                x: { 
                    grid: { display: false }, 
                    ticks: { font: { size: 10, weight: '600' }, color: '#64748b' } 
                },
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(226, 232, 240, 0.6)' }, 
                    ticks: { font: { size: 10, weight: '600' }, color: '#64748b' } 
                }
            }
        }
    });
}

function updateChartMetric(val) {
    const metric = chartDataSets[val];
    if (metric) {
        document.getElementById('chartTitle').textContent = metric.title;
    }
    renderTrendChart(val);
}

document.addEventListener('DOMContentLoaded', () => {
    renderTrendChart('egg_peti');
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush
@endsection
