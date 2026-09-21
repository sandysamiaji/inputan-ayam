@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- 1. KARTU SAMBUTAN & STATUS KANDANG (RESPONSIVE BANNER) -->
    @if(!auth()->check() || auth()->user()->canAccess('dash_filter'))
    <div class="farm-card p-4 sm:p-6 bg-gradient-to-r from-white via-white to-rose-50/60 border border-rose-100/70 shadow-sm relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            
            <!-- Status Ringkas -->
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-maroon-50 text-maroon-800 text-xs font-bold border border-maroon-100 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Kandang Aktif
                    </span>
                    @forelse($flocks as $flock)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $flock->name }} ({{ $flock->coops->count() }} Blok)
                        </span>
                    @empty
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $totalCoopsCount }} Blok Kandang
                        </span>
                    @endforelse
                    @if($coops->whereNull('flock_id')->count() > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-slate-400"></i>
                            Non-Kloter ({{ $coops->whereNull('flock_id')->count() }} Blok)
                        </span>
                    @endif
                    <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                        {{ number_format($totalActiveChickens, 0, ',', '.') }} Ekor Ayam
                    </span>
                </div>
            </div>

            <!-- Kartu Penanggalan Kalender & Filter Cepat -->
            <div class="flex items-center gap-3 self-start md:self-center">
                <div class="text-center bg-white border border-slate-200 rounded-2xl px-4 py-2.5 shadow-sm min-w-[85px] flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-maroon-800 mr-2"></i>
                    <span class="block text-lg font-black text-maroon-800">{{ $user ? ($user->username ?: $user->name) : 'Petugas' }}</span>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Pilih Tanggal</label>
                    @if(!auth()->check() || auth()->user()->canAccess('dash_filter_tanggal'))
                    <input type="date" value="{{ $selectedDate }}" onchange="window.location.href='?date=' + this.value" 
                           class="text-xs sm:text-sm text-maroon-800 font-bold bg-white border border-rose-200 rounded-xl px-3 py-2 outline-none cursor-pointer hover:border-maroon-700 shadow-sm transition-all">
                    @else
                    <input type="date" value="{{ $selectedDate }}" readonly disabled
                           class="text-xs sm:text-sm text-maroon-800 font-bold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 outline-none cursor-not-allowed text-slate-500 shadow-sm">
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endif

    <!-- 2. RINGKASAN HARI INI (RESPONSIVE GRID: 2 COLS DI HP, 5 COLS DI LAPTOP/DESKTOP) -->
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-maroon-800"></div>
                <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-800">RINGKASAN HARI INI</h3>
                <span class="text-xs text-slate-400 font-medium hidden sm:inline">(Data per {{ $carbonDate->day }} {{ $namaBulan }} {{ $carbonDate->year }})</span>
            </div>
            <span class="text-xs text-maroon-800 font-semibold flex items-center gap-1">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Real-time DB
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">

            <!-- Card 1: Produksi Telur (Amber) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_card_egg'))
            <div class="farm-card p-3.5 sm:p-4 border-l-4 border-l-amber-500 bg-white flex flex-col justify-between">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 shadow-xs">
                        <svg class="w-6 h-6 fill-amber-500 text-amber-500" viewBox="0 0 24 24">
                            <path d="M12 2C7.5 2 4 7.5 4 13.5C4 18.2 7.6 22 12 22C16.4 22 20 18.2 20 13.5C20 7.5 16.5 2 12 2Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="12" cy="14" r="4" fill="currentColor" fill-opacity="0.8"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">Masuk</span>
                </div>
                <div class="mt-3">
                    <p class="text-xs font-semibold text-slate-500">Produksi Telur</p>
                    @php
                        $cNormPeti = (int) round($totalEggCrates);
                        $cNormKg = (float) $totalEggKg;
                        if ($cNormKg >= 10) {
                            $extraP = (int) floor($cNormKg / 10);
                            $cNormPeti += $extraP;
                            $cNormKg = round($cNormKg - ($extraP * 10), 1);
                        } else {
                            $cNormKg = round($cNormKg, 1);
                        }
                        $cratesStr = number_format($cNormPeti, 0, ',', '.') . ' Peti';
                        $kgStr = $cNormKg > 0 ? ($cNormKg == floor($cNormKg) ? number_format($cNormKg, 0, ',', '.') : number_format($cNormKg, 1, ',', '.')) . ' kg' : '';
                        if ($cNormPeti > 0 && $cNormKg > 0) {
                            $prodTelurDisplay = $cratesStr . ' + ' . $kgStr;
                        } elseif ($cNormKg > 0) {
                            $prodTelurDisplay = $kgStr;
                        } else {
                            $prodTelurDisplay = $cratesStr;
                        }
                    @endphp
                    <p class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-tight mt-0.5">
                        {{ $prodTelurDisplay }}
                    </p>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                        ({{ number_format($totalEggCount, 0, ',', '.') }} Butir)
                    </p>
                </div>
            </div>
            @endif

            <!-- Card 2: Pemakaian Pakan (Emerald) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_card_feed'))
            @php
                $dashStokLayer = $feedSummary['current_stock_kg_layer'] ?? 0;
                $dashStokGrower = $feedSummary['current_stock_kg_grower'] ?? 0;
            @endphp
            <div class="farm-card p-3.5 sm:p-4 border-l-4 border-l-emerald-600 bg-white flex flex-col justify-between">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 shadow-xs">
                        <i data-lucide="package" class="w-5 h-5 stroke-[2.2]"></i>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Kandang</span>
                </div>
                <div class="mt-3">
                    <p class="text-xs font-semibold text-slate-500">Pemakaian Pakan Hari Ini</p>
                    <p class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-tight mt-0.5">
                        {{ number_format($totalFeedKg, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">Kg</span>
                    </p>
                    <div class="mt-1 pt-1 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-600">
                        <span>Stok Layer: <b class="{{ $dashStokLayer < 0 ? 'text-rose-600' : 'text-emerald-700' }}">{{ number_format($dashStokLayer, 0, ',', '.') }} kg</b></span>
                        <span>Grower: <b class="{{ $dashStokGrower < 0 ? 'text-rose-600' : 'text-sky-700' }}">{{ number_format($dashStokGrower, 0, ',', '.') }} kg</b></span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Card 3: Mortalitas (Rose/Red) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_card_mortality'))
            @php
                $canClickMortality = auth()->check() && (auth()->user()->canAccess('dash_card_mortality_click') || auth()->user()->canAccess('warehouse_click_quarantine') || auth()->user()->canAccess('feature_warehouse_karantina') || auth()->user()->role === 'admin');
            @endphp
            @if($canClickMortality)
            <a href="{{ route('warehouse.karantina') }}" class="farm-card p-3.5 sm:p-4 border-l-4 border-l-rose-600 bg-white flex flex-col justify-between hover:border-rose-500 hover:shadow-md transition-all cursor-pointer group">
            @else
            <div class="farm-card p-3.5 sm:p-4 border-l-4 border-l-rose-600 bg-white flex flex-col justify-between cursor-default">
            @endif
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 shadow-xs {{ $canClickMortality ? 'group-hover:scale-105 transition-transform' : '' }}">
                        <i data-lucide="skull" class="w-5 h-5 stroke-[2.2]"></i>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200">Mati</span>
                </div>
                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-slate-500">💀 Mati</p>
                        <!-- <p class="text-base sm:text-lg font-black text-rose-700 tracking-tight leading-tight">
                            {{ $totalMortalityCount }} <span class="text-xs font-bold text-slate-500">Ekor</span>
                        </p> -->
                        <p class="text-base sm:text-lg font-black text-rose-700 tracking-tight leading-tight">
                            {{ $matiHariIni ?? 0 }} <span class="text-xs font-bold text-slate-500">Ekor</span>
                        </p>
                    </div>

                    <!-- Split Rincian: Ayam Mati vs Ayam Afkir -->
                    <!-- <div hidden class="grid grid-cols-2 gap-1.5 mt-2 text-[10.5px]">
                        <div class="flex items-center justify-between px-2 py-1 rounded-md bg-rose-50/80 border border-rose-200/70 text-rose-950">
                            <span class="font-bold flex items-center gap-1 text-[10px] text-rose-800">💀 Mati</span>
                            <b class="font-black text-rose-700 text-xs">{{ $matiHariIni ?? 0 }}</b>
                        </div>
                        <div class="flex items-center justify-between px-2 py-1 rounded-md bg-amber-50/80 border border-amber-200/70 text-amber-950">
                            <span class="font-bold flex items-center gap-1 text-[10px] text-amber-800">✂️ Afkir</span>
                            <b class="font-black text-amber-700 text-xs">{{ $afkirHariIni ?? 0 }}</b>
                        </div>
                    </div> -->

                    <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px]">
                        <span class="text-slate-400 font-medium">Riwayat Kematian & Afkir</span>
                        @if($canClickMortality)
                            <span class="text-rose-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                                Kelola &raquo;
                            </span>
                        @else
                            <span class="text-slate-400 font-semibold flex items-center gap-1">
                                <i data-lucide="lock" class="w-2.5 h-2.5"></i> Terkunci
                            </span>
                        @endif
                    </div>
                </div>
            @if($canClickMortality)
            </a>
            @else
            </div>
            @endif
            @endif

            <!-- Card 4: Berat Badan 6 Blok (Sky Blue) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_card_weight'))
            @php
                $canClickWeight = auth()->check() && auth()->user()->canAccess('dash_card_weight_click');
                $totalTargetAchieved = 0;
                $totalTargetMissed = 0;
                foreach($coops as $c) {
                    $wVal = $coopWeightData[$c->id] ?? null;
                    if ($wVal !== null) {
                        $cStdVal = $coopStandards[$c->id] ?? \App\Services\ProductionStandardService::getStandardForWeek((int)$c->chicken_age_weeks);
                        $tgtVal = (float) ($cStdVal['bb_target'] ?? 0);
                        if ($wVal >= $tgtVal) {
                            $totalTargetAchieved++;
                        } else {
                            $totalTargetMissed++;
                        }
                    }
                }
            @endphp
            <div class="farm-card p-3 sm:p-3.5 border-l-4 border-l-sky-600 bg-white flex flex-col justify-between {{ $canClickWeight ? 'hover:border-sky-500 hover:shadow-md transition-all cursor-pointer group' : 'cursor-default' }}" 
                 @if($canClickWeight) onclick="openModal('modalBobot6Blok')" @endif>
                <div>
                    <div class="flex items-start justify-between gap-1.5">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100 shadow-xs {{ $canClickWeight ? 'group-hover:scale-105 transition-transform' : '' }}">
                                <i data-lucide="scale" class="w-4 h-4 stroke-[2.2]"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700 leading-tight">Bobot Ayam</p>
                                <span class="text-[10px] text-sky-700 font-extrabold">Data 6 Blok</span>
                            </div>
                        </div>
                        @if($totalTargetAchieved + $totalTargetMissed > 0)
                            @if($totalTargetMissed == 0)
                                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Capai Target
                                </span>
                            @else
                                <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ $totalTargetMissed }} Blok Kurang
                                </span>
                            @endif
                        @else
                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-sky-50 text-sky-800 border border-sky-200">
                                6 Blok
                            </span>
                        @endif
                    </div>

                    <!-- Mini Grid Data 6 Blok (Hijau jika capai/lebih target, Merah jika di bawah target) -->
                    <div class="grid grid-cols-2 gap-1 mt-2.5">
                        @foreach($coops as $c)
                            @php
                                $w = $coopWeightData[$c->id] ?? null;
                                $cShort = str_replace(['BLOK ', 'Blok '], '', $c->name);
                                $cStd = $coopStandards[$c->id] ?? \App\Services\ProductionStandardService::getStandardForWeek((int)$c->chicken_age_weeks);
                                $bbTarget = (float) ($cStd['bb_target'] ?? 0);
                                $isTargetOrMore = ($w !== null && $w >= $bbTarget);
                                $isMissed = ($w !== null && $w < $bbTarget);
                            @endphp
                            <div class="flex items-center justify-between px-1.5 py-0.5 rounded transition-colors {{ $w === null ? 'bg-slate-50 border border-slate-100 text-slate-400' : ($isTargetOrMore ? 'bg-emerald-50/80 border border-emerald-200 text-emerald-950' : 'bg-rose-50/80 border border-rose-200 text-rose-950') }}" 
                                 title="{{ $w !== null ? ($isTargetOrMore ? 'Capai Target / Lebih (Target: ' . number_format($bbTarget, 1, ',', '.') . ' kg)' : 'Belum Sesuai Master (Target: ' . number_format($bbTarget, 1, ',', '.') . ' kg)') : 'Belum Ada Input' }}">
                                <div class="flex items-center gap-1">
                                    @if($w !== null)
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $isTargetOrMore ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    @endif
                                    <span class="text-[10px] font-extrabold {{ $w === null ? 'text-slate-500' : ($isTargetOrMore ? 'text-emerald-900' : 'text-rose-900') }}">{{ $cShort }}</span>
                                </div>
                                <span class="text-[10.5px] font-black {{ $w === null ? 'text-slate-400' : ($isTargetOrMore ? 'text-emerald-700' : 'text-rose-700') }}">
                                    {{ $w ? number_format($w, 1, ',', '.') . ' kg' : '-' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[10px]">
                    <div class="flex items-center gap-2 text-[9.5px]">
                        <span class="inline-flex items-center gap-1 text-emerald-700 font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Capai</span>
                        <span class="inline-flex items-center gap-1 text-rose-600 font-bold"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Kurang</span>
                    </div>
                    @if($canClickWeight)
                        <span class="text-sky-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                            Detail &raquo;
                        </span>
                    @else
                        <span class="text-slate-400 font-semibold flex items-center gap-1">
                            <i data-lucide="lock" class="w-2.5 h-2.5"></i> Terkunci
                        </span>
                    @endif
                </div>
            </div>
            @endif

            <!-- Card 5: Vaksin / Obat (Maroon) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_card_health'))
            <div class="farm-card p-3.5 sm:p-4 border-l-4 border-l-maroon-800 bg-white flex flex-col justify-between">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center shrink-0 border border-maroon-100 shadow-xs">
                        <i data-lucide="syringe" class="w-5 h-5 stroke-[2.2]"></i>
                    </div>
                    <!-- Left intentionally blank, + Catat button removed as per user request -->
                </div>
                <div class="mt-3">
                    <p class="text-xs font-semibold text-slate-500">Vaksin / Obat</p>
                    <p class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-tight mt-0.5">
                        {{ $totalHealthActivities }} <span class="text-xs font-bold text-slate-500">Kegiatan</span>
                    </p>
                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                        Perlakuan Medis
                    </p>
                </div>
            </div>
            @endif

            <!-- Card 6: Karantina Ayam (Amber/Orange) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_card_quarantine'))
            @php
                $canClickQuarantine = auth()->check() && auth()->user()->canAccess('dash_card_quarantine_click');
            @endphp
            @if($canClickQuarantine)
            <a href="{{ route('warehouse.karantina') }}" class="farm-card p-3.5 sm:p-4 border-l-4 border-l-amber-500 bg-white flex flex-col justify-between hover:border-amber-600 hover:shadow-md transition-all cursor-pointer group">
            @else
            <div class="farm-card p-3.5 sm:p-4 border-l-4 border-l-amber-500 bg-white flex flex-col justify-between cursor-default">
            @endif
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 shadow-xs {{ $canClickQuarantine ? 'group-hover:scale-105 transition-transform' : '' }}">
                        <i data-lucide="shield-alert" class="w-5 h-5 stroke-[2.2]"></i>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md {{ $currentQuarantineCount > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $currentQuarantineCount > 0 ? 'Isolasi' : 'Nihil' }}
                        </span>
                        @if($canClickQuarantine)
                            <div class="w-5 h-5 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </div>
                        @else
                            <div class="w-5 h-5 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center">
                                <i data-lucide="lock" class="w-3 h-3"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-xs font-semibold text-slate-500">Ayam Karantina</p>
                    <p class="text-lg sm:text-xl font-black {{ $currentQuarantineCount > 0 ? 'text-amber-700' : 'text-slate-900' }} tracking-tight leading-tight mt-0.5">
                        {{ $currentQuarantineCount }} <span class="text-xs font-bold text-slate-500">Ekor</span>
                    </p>
                    <p class="text-[11px] font-medium mt-0.5 {{ $currentQuarantineCount > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                        @if($todaySickCount > 0 || $todayRecoveredCount > 0)
                            +{{ $todaySickCount }} Sakit • -{{ $todayRecoveredCount }} Sembuh
                        @elseif($currentQuarantineCount > 0)
                            Sedang Diisolasi
                        @else
                            Kondisi Sehat
                        @endif
                    </p>
                </div>
            @if($canClickQuarantine)
            </a>
            @else
            </div>
            @endif
            @endif

        </div>
    </div>

    <!-- 3. MAIN SECTION: LAYOUT 2 KOLOM DI LAPTOP / DESKTOP -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI (7 Kolom di Desktop): AKSI CEPAT & STATUS BLOK -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-6">

            <!-- STATUS BLOK KANDANG (Info Rinci Per Blok Sesuai Mockup) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_section_coops'))
            <div>
                <div class="flex items-center justify-between mb-2.5 px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-600"></div>
                        <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-800">STATUS BLOK KANDANG AKTIF</h3>
                    </div>
                    @if(!auth()->check() || auth()->user()->canAccess('dash_coop_fase_info'))
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openModal('modalFasePenjelasan')" class="text-xs text-emerald-800 hover:text-emerald-900 font-bold flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200 transition-colors">
                            <i data-lucide="help-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                            <span class="hidden sm:inline">Kenapa "PRODUKSI NAIK"?</span>
                            <span class="sm:hidden">Alasan Fase</span>
                        </button>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($coops as $coop)
                        @php
                            $capacityPercent = $coop->capacity > 0 ? min(100, round(($coop->active_chickens / $coop->capacity) * 100)) : 0;
                            $cStd = $coopStandards[$coop->id] ?? \App\Services\ProductionStandardService::getStandardForWeek((int)$coop->chicken_age_weeks);
                            $totalPakanCoopKg = round(($coop->active_chickens * ($cStd['gram_pakan'] ?? 105)) / 1000, 1);
                            $kPerKrg = $kgPerKarung ?? 50;
                            $cKarung = floor($totalPakanCoopKg / $kPerKrg);
                            $cSisaKg = round(fmod($totalPakanCoopKg, $kPerKrg), 1);
                            $cKarungText = ($cKarung > 0 ? $cKarung . ' karung ' : '') . ($cSisaKg > 0 ? ($cKarung > 0 ? '+ ' : '') . $cSisaKg . ' kg' : ($cKarung == 0 ? '0 kg' : ''));
                            $pagiKg = round($totalPakanCoopKg * 0.4, 1);
                            $soreKg = round($totalPakanCoopKg * 0.6, 1);
                            $coopHd = $coopHdData[$coop->id] ?? null;
                            $flockHd = $flockHdData[$coop->flock_id] ?? null;
                            $todayEgg = $coopEggTodayData[$coop->id] ?? 0;
                        @endphp
                        <div class="farm-card p-4 bg-white border border-slate-200 hover:border-maroon-300 transition-all flex flex-col justify-between shadow-xs">
                            <div>
                                <!-- Header Blok, Klotter & HD DI DEPAN -->
                                <div class="flex items-center justify-between mb-2 gap-1.5 flex-wrap">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <h4 class="font-black text-slate-900 text-sm sm:text-base flex items-center gap-1.5 whitespace-nowrap">
                                            <span class="w-2.5 h-2.5 rounded-full bg-maroon-800 shrink-0"></span>
                                            <span>{{ $coop->name }}</span>
                                        </h4>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $coop->flock ? $coop->flock->name : 'Klotter' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <!-- BADGE HD UTAMA DI DEPAN: HANYA MUNCUL JIKA TELUR SUDAH DIINPUT -->
                                        @if(!auth()->check() || auth()->user()->canAccess('dash_coop_hd'))
                                            @if($coopHd !== null)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-black px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-2xs" title="Hen-Day Production (HD) Blok {{ $coop->name }} Hari Ini">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                    HD {{ number_format($coopHd, 1, ',', '.') }}%
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 border border-slate-200" title="Belum ada data input telur untuk tanggal ini">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                    HD: Belum Input
                                                </span>
                                            @endif
                                        @endif

                                        @if(!auth()->check() || auth()->user()->canAccess('dash_coop_fase_info'))
                                        <button type="button" 
                                                onclick="openModal('modalFasePenjelasan')"
                                                title="Klik untuk melihat panduan lengkap fase {{ $cStd['pill'] }}"
                                                class="inline-flex items-center gap-1 text-[10px] font-extrabold px-1.5 py-0.5 rounded-md border {{ $cStd['pill_class'] ?? 'bg-emerald-50 text-emerald-700 border-emerald-200' }} hover:shadow-xs transition-all cursor-pointer">
                                            <span>{{ $cStd['pill'] }}</span>
                                            <i data-lucide="help-circle" class="w-3 h-3 opacity-75"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Umur, HD & Kapasitas Aktif -->
                                <div class="space-y-1.5 mt-2">
                                    <div class="flex justify-between items-center text-xs text-slate-500">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-rose-50 text-maroon-800 border border-rose-100">
                                                {{ $coop->chicken_age_weeks }} Minggu
                                            </span>
                                            @if(!auth()->check() || auth()->user()->canAccess('dash_coop_hd'))
                                                @if($coopHd !== null)
                                                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                        HD: {{ number_format($coopHd, 1, ',', '.') }}% ({{ number_format($todayEgg, 0, ',', '.') }} butir)
                                                    </span>
                                                @else
                                                    <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                                        Telur: Belum Input
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        <span class="text-xs">Kapasitas: <b class="text-slate-900 font-bold">{{ number_format($coop->active_chickens, 0, ',', '.') }} / {{ number_format($coop->capacity, 0, ',', '.') }}</b></span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-maroon-700 h-full rounded-full transition-all" style="width: {{ $capacityPercent }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] text-slate-400">
                                        <span>Kloter: <b>{{ $coop->flock ? $coop->flock->name : 'Klotter' }} (HD: {{ $flockHd !== null ? number_format($flockHd, 1, ',', '.') . '%' : 'Belum Input' }})</b></span>
                                        <span>Target HD Master: <b>{{ $cStd['hd_target'] }}%</b></span>
                                    </div>
                                </div>

                                <!-- KOTAK PENJELASAN ALASAN STATUS: PENDEK DULU, BISA DI-KLIK DETAIL MEMANJANG OTOMATIS -->
                                @if(!auth()->check() || auth()->user()->canAccess('dash_coop_fase_info'))
                                <div class="mt-2.5 p-2 rounded-lg bg-emerald-50/70 border border-emerald-100 text-[11px] text-emerald-950 transition-all">
                                    <div class="flex items-center justify-between font-bold text-emerald-900 text-[10.5px]">
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="trending-up" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                            <span>Alasan: {{ $cStd['pill'] }}</span>
                                        </span>
                                        <button type="button" 
                                                id="coopDetailBtn_{{ $coop->id }}"
                                                onclick="toggleCoopDetail({{ $coop->id }})"
                                                class="text-[10.5px] text-emerald-700 hover:text-emerald-900 underline font-bold cursor-pointer">
                                            Detail &raquo;
                                        </button>
                                    </div>
                                    <!-- Versi Pendek (Default Singkat Sesuai Request) -->
                                    <p id="coopShortText_{{ $coop->id }}" class="text-[10.5px] text-emerald-800 mt-0.5 leading-snug">
                                        Umur <b>{{ $coop->chicken_age_weeks }} mgg</b>: {{ $cStd['keterangan'] }} (Target HD {{ $cStd['hd_target'] }}%)...
                                    </p>
                                    <!-- Versi Panjang (Expand Otomatis Saat Klik Detail) -->
                                    <div id="coopFullText_{{ $coop->id }}" class="hidden text-[10.5px] text-emerald-800 mt-1.5 leading-relaxed border-t border-emerald-200/60 pt-1.5 space-y-1">
                                        <p>
                                             Umur <b>{{ $coop->chicken_age_weeks }} mgg</b> masuk fase <b>{{ $cStd['fase'] }}</b> (rentang 21–25 mgg). Oviduk matang, masa subur & lonjakan bertelur pesat menuju puncak.
                                        </p>
                                        <div class="text-[10px] text-emerald-950 font-medium bg-white/70 p-1.5 rounded border border-emerald-200/50">
                                            <div>• Target Standar HD: <b>{{ $cStd['hd_target'] }}%</b> (Acuan Master Umur {{ $coop->chicken_age_weeks }} Mgg)</div>
                                            <div>• HD Aktual Hari Ini: <b>{{ $coopHd !== null ? number_format($coopHd, 1, ',', '.') . '% (' . number_format($todayEgg, 0, ',', '.') . ' butir)' : 'Belum Diinput (Klik tombol input di bawah)' }}</b></div>
                                            <div>• Kebutuhan Pakan: <b>{{ $cStd['gram_pakan'] }} g/ekor</b> ({{ number_format($totalPakanCoopKg, 1, ',', '.') }} kg/hari)</div>
                                        </div>
                                        <div class="pt-0.5 flex justify-end">
                                            <button type="button" onclick="openModal('modalFasePenjelasan')" class="text-[10px] text-maroon-800 hover:text-maroon-900 underline font-semibold">
                                                Buka Panduan 6 Fase Lengkap &raquo;
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Data Acuan Master Standar Produksi Otomatis -->
                                @if(!auth()->check() || auth()->user()->canAccess('dash_coop_standards'))
                                <div class="mt-2.5 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                                        <span class="text-slate-400 block text-[10px] font-medium">Acuan Telur</span>
                                        <b class="text-slate-800 font-bold text-xs">{{ $cStd['berat_telur'] !== '-' ? $cStd['berat_telur'] : 'Grower' }}</b>
                                        <span class="text-[9px] text-slate-400 block mt-0.5">Target: {{ $cStd['berat_telur'] }}</span>
                                    </div>
                                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                                        <span class="text-slate-400 block text-[10px] font-medium">Standar Pakan</span>
                                        <b class="text-slate-800 font-bold text-xs">{{ $cStd['gram_pakan'] }} g/ekor</b>
                                        <span class="text-[9px] text-slate-400 block mt-0.5">Pagi {{ $cStd['pagi_gram'] }}g • Sore {{ $cStd['sore_gram'] }}g</span>
                                    </div>
                                </div>
                                @if(isset($coopWeightData[$coop->id]) && $coopWeightData[$coop->id])
                                <div class="mt-2 p-1.5 px-2 rounded-lg bg-sky-50/70 border border-sky-100 flex flex-wrap items-center justify-between gap-1 text-[10.5px]">
                                    <span class="flex items-center gap-1 font-bold text-sky-950">
                                        <i data-lucide="scale" class="w-3.5 h-3.5 text-sky-600"></i>
                                        <span>Sampel Bobot: <b class="text-sky-800">{{ number_format($coopWeightData[$coop->id], 2, ',', '.') }} kg</b></span>
                                    </span>
                                    <span class="text-slate-500 font-medium text-[9.5px]">
                                        {{ !empty($coopWeightDetails[$coop->id]['battery_number']) ? 'Baterai ' . $coopWeightDetails[$coop->id]['battery_number'] : '' }}
                                        {{ !empty($coopWeightDetails[$coop->id]['egg_weight_gram']) ? ' • Telur ' . $coopWeightDetails[$coop->id]['egg_weight_gram'] . 'g' : '' }}
                                    </span>
                                </div>
                                @endif
                                @endif

                                <!-- ESTIMASI TELUR DARI ACUAN & ADU DATA REALISASI INPUT KARYAWAN -->
                                @if(!auth()->check() || auth()->user()->canAccess('dash_coop_egg_comparison'))
                                @php
                                    $eggGram = (!empty($cStd['berat_telur_val']) && $cStd['berat_telur_val'] > 0) ? (float) $cStd['berat_telur_val'] : 60.0;
                                    $estKg = $todayEgg > 0 ? round(($todayEgg * $eggGram) / 1000, 1) : 0;
                                    $estPeti = (int) floor($estKg / 10);
                                    $estSisaKg = round($estKg - ($estPeti * 10), 1);
                                    $estPetiText = ($estPeti > 0 ? $estPeti . ' Peti ' : '') . ($estSisaKg > 0 ? ($estPeti > 0 ? '+ ' : '') . number_format($estSisaKg, 1, ',', '.') . ' kg' : ($estPeti == 0 ? '0 kg' : ''));

                                    $actPeti = (int) ($coopEggCratesData[$coop->id] ?? 0);
                                    $actKg = (float) ($coopEggKgData[$coop->id] ?? 0);
                                    $actTotalKg = round(($actPeti * 10) + $actKg, 1);
                                    $normActPeti = (int) floor($actTotalKg / 10);
                                    $normActKg = round($actTotalKg - ($normActPeti * 10), 1);
                                    $actPetiText = ($normActPeti > 0 ? $normActPeti . ' Peti ' : '') . ($normActKg > 0 ? ($normActPeti > 0 ? '+ ' : '') . number_format($normActKg, 1, ',', '.') . ' kg' : ($normActPeti == 0 ? '0 kg' : ''));

                                    $eggDiffKg = round($actTotalKg - $estKg, 1);
                                    $hasEggInput = ($todayEgg > 0 || $actTotalKg > 0);
                                    $isEggMatch = ($hasEggInput && $actTotalKg >= $estKg && $estKg > 0);
                                @endphp
                                <div class="mt-2.5 p-2.5 rounded-lg text-xs {{ !$hasEggInput ? 'bg-slate-50 border border-slate-200' : ($isEggMatch ? 'bg-emerald-50/80 border border-emerald-200' : 'bg-rose-50/80 border border-rose-200') }}">
                                    <!-- Header Realisasi vs Acuan -->
                                    <div class="flex flex-wrap items-center justify-between gap-1 font-bold text-[11px]">
                                        <span class="flex items-center gap-1.5 {{ !$hasEggInput ? 'text-slate-700' : ($isEggMatch ? 'text-emerald-900' : 'text-rose-900') }}">
                                            <span class="text-sm">🥚</span>
                                            <span>Realisasi Panen Telur:</span>
                                        </span>
                                        @if(!$hasEggInput)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-200/80 text-slate-600 border border-slate-300/60">
                                                Belum Input
                                            </span>
                                        @elseif($isEggMatch)
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1 shadow-2xs">
                                                <i data-lucide="check-circle-2" class="w-3 h-3 text-emerald-600"></i>
                                                {{ $eggDiffKg > 0 ? 'Lebih ' . number_format($eggDiffKg, 1, ',', '.') . ' kg' : 'Sesuai Hitungan' }}
                                            </span>
                                        @else
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1 shadow-2xs">
                                                <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600"></i>
                                                Kurang {{ number_format(abs($eggDiffKg), 1, ',', '.') }} kg
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Angka Realisasi vs Perkiraan -->
                                    <div class="mt-1 flex flex-col sm:flex-row sm:items-baseline justify-between gap-1 text-[11px]">
                                        <div>
                                            <span class="text-sm font-black {{ !$hasEggInput ? 'text-slate-500' : ($isEggMatch ? 'text-emerald-800' : 'text-rose-800') }}">
                                                {{ $hasEggInput ? $actPetiText : '0 Peti' }}
                                            </span>
                                            @if($actTotalKg > 0)
                                                <span class="text-[10px] text-slate-500 font-medium ml-1">({{ number_format($actTotalKg, 1, ',', '.') }} kg)</span>
                                            @endif
                                        </div>
                                        <div class="text-[10.5px] text-slate-500 font-medium text-left sm:text-right">
                                            Perkiraan: <b class="text-slate-800">{{ $estPetiText }}</b>
                                            @if($estKg > 0)
                                                <span class="text-[10px] text-slate-400">({{ number_format($estKg, 1, ',', '.') }} kg)</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Keterangan Status -->
                                    @if($hasEggInput)
                                        <div class="mt-1.5 pt-1.5 border-t {{ $isEggMatch ? 'border-emerald-200/60 text-emerald-800' : 'border-rose-200/60 text-rose-900' }} text-[10.5px] leading-snug font-medium">
                                            @if($isEggMatch)
                                                ✔ <b>Input telur karyawan sesuai / melebihi estimasi aplikasi</b> (acuan {{ number_format($todayEgg, 0, ',', '.') }} butir × {{ $cStd['berat_telur'] }} = {{ number_format($estKg, 1, ',', '.') }} kg).
                                            @else
                                                ⚠️ <b>Input telur karyawan belum sesuai hitungan aplikasi</b> (masih kurang {{ number_format(abs($eggDiffKg), 1, ',', '.') }} kg dari estimasi {{ $estPetiText }} / {{ number_format($estKg, 1, ',', '.') }} kg).
                                            @endif
                                        </div>
                                        @if(!empty($coopEggUserInputData[$coop->id]))
                                        <div class="mt-1 text-[9.5px] text-slate-500 font-medium">
                                            Diinput oleh: <b class="text-slate-700">{{ $coopEggUserInputData[$coop->id] }}</b>
                                        </div>
                                        @endif
                                    @else
                                        <div class="mt-1 text-[10px] text-slate-400">
                                            Belum ada pencatatan panen telur untuk {{ $coop->name }} hari ini.
                                        </div>
                                    @endif
                                </div>
                                @endif

                                <!-- TOTAL KEBUTUHAN PAKAN BLOK INI & ADU DATA REALISASI -->
                                @if(!auth()->check() || auth()->user()->canAccess('dash_coop_feed_comparison'))
                                <div class="mt-2 p-2.5 rounded-lg bg-amber-50/70 border border-amber-200/80 text-slate-800">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wide flex items-center gap-1">
                                            <span>🌾</span> Standar Pakan Blok Ini:
                                        </span>
                                        <span class="text-[10px] text-amber-800 font-semibold">({{ number_format($coop->active_chickens, 0, ',', '.') }} ekor × {{ $cStd['gram_pakan'] }}g)</span>
                                    </div>
                                    <div class="mt-1 flex items-baseline justify-between">
                                        <div>
                                            <span class="text-base font-black text-maroon-800">{{ number_format($totalPakanCoopKg, 1, ',', '.') }}</span>
                                            <span class="text-xs font-bold text-slate-700">kg / hari</span>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 bg-white/90 px-2 py-0.5 rounded border border-amber-200/70 shadow-2xs">
                                            {{ $cKarungText }}
                                        </span>
                                    </div>
                                    <div class="mt-1.5 pt-1.5 border-t border-amber-200/60 flex justify-between text-[10px] text-slate-600 font-medium">
                                        <span>Jadwal Pagi (40%): <b class="text-slate-800">{{ number_format($pagiKg, 1, ',', '.') }} kg</b></span>
                                        <span>Sore (60%): <b class="text-slate-800">{{ number_format($soreKg, 1, ',', '.') }} kg</b></span>
                                    </div>
                                </div>

                                <!-- ADU DATA: REALISASI INPUT PAKAN VS HITUNGAN STANDAR -->
                                @php
                                    $actualFeedKg = $coopFeedTodayData[$coop->id] ?? 0;
                                    $feedDiffKg = round($actualFeedKg - $totalPakanCoopKg, 1);
                                    $hasPagi = $coopFeedHasPagiData[$coop->id] ?? false;
                                    $hasSore = $coopFeedHasSoreData[$coop->id] ?? false;
                                    $actPagiKg = $coopFeedPagiData[$coop->id] ?? 0;
                                    $actSoreKg = $coopFeedSoreData[$coop->id] ?? 0;
                                    $uPagi = $coopFeedPagiUserData[$coop->id] ?? null;
                                    $uSore = $coopFeedSoreUserData[$coop->id] ?? null;
                                @endphp
                                <div class="mt-2 p-2.5 rounded-lg text-xs {{ $actualFeedKg == 0 ? 'bg-slate-50 border border-slate-200' : (abs($feedDiffKg) <= 1.0 ? 'bg-emerald-50/80 border border-emerald-200' : ($feedDiffKg > 1.0 ? 'bg-amber-50/80 border border-amber-200' : 'bg-rose-50/80 border border-rose-200')) }}">
                                    <div class="flex items-center justify-between font-bold text-[11px]">
                                        <span class="flex items-center gap-1.5 {{ $actualFeedKg == 0 ? 'text-slate-700' : (abs($feedDiffKg) <= 1.0 ? 'text-emerald-900' : ($feedDiffKg > 1.0 ? 'text-amber-900' : 'text-rose-900')) }}">
                                            <i data-lucide="scale" class="w-3.5 h-3.5"></i>
                                            <span>Realisasi Input Pakan:</span>
                                        </span>
                                        @if($actualFeedKg == 0)
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-200/80 text-slate-600 border border-slate-300/60">
                                                Belum Input
                                            </span>
                                        @elseif(abs($feedDiffKg) <= 1.0)
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1 shadow-2xs">
                                                <i data-lucide="check-circle-2" class="w-3 h-3 text-emerald-600"></i>
                                                Sesuai Standar
                                            </span>
                                        @elseif($feedDiffKg > 1.0)
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1 shadow-2xs">
                                                <i data-lucide="alert-triangle" class="w-3 h-3 text-amber-600"></i>
                                                Lebih {{ number_format(abs($feedDiffKg), 1, ',', '.') }} kg
                                            </span>
                                        @else
                                            <span class="text-[10px] font-black px-2 py-0.5 rounded bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1 shadow-2xs">
                                                <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600"></i>
                                                Kurang {{ number_format(abs($feedDiffKg), 1, ',', '.') }} kg
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-1 flex items-baseline justify-between text-[11px]">
                                        <div>
                                            <span class="text-sm font-black {{ $actualFeedKg == 0 ? 'text-slate-500' : (abs($feedDiffKg) <= 1.0 ? 'text-emerald-800' : ($feedDiffKg > 1.0 ? 'text-amber-800' : 'text-rose-800')) }}">
                                                {{ number_format($actualFeedKg, 1, ',', '.') }} kg
                                            </span>
                                            <span class="text-[10px] text-slate-500 font-medium ml-1">diinput hari ini</span>
                                        </div>
                                        <div class="text-[10.5px] text-slate-500 font-medium">
                                            Standar Hitungan: <b class="text-slate-800">{{ number_format($totalPakanCoopKg, 1, ',', '.') }} kg</b>
                                        </div>
                                    </div>

                                    <!-- Jadwal Pagi & Sore Realisasi Samping-Sampingan -->
                                    <div class="mt-1.5 pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-[10px] text-slate-600 font-medium">
                                        <span>Jadwal Pagi (40%): <b class="{{ $hasPagi ? 'text-slate-900 font-extrabold' : 'text-slate-400 font-normal' }}">{{ $hasPagi ? number_format($actPagiKg, 1, ',', '.') . ' kg' : 'Belum Input' }}</b></span>
                                        <span>Sore (60%): <b class="{{ $hasSore ? 'text-slate-900 font-extrabold' : 'text-slate-400 font-normal' }}">{{ $hasSore ? number_format($actSoreKg, 1, ',', '.') . ' kg' : 'Belum Input' }}</b></span>
                                    </div>

                                    @if($actualFeedKg > 0)
                                        <div class="mt-1.5 pt-1.5 border-t {{ abs($feedDiffKg) <= 1.0 ? 'border-emerald-200/60 text-emerald-800' : ($feedDiffKg > 1.0 ? 'border-amber-200/60 text-amber-900' : 'border-rose-200/60 text-rose-900') }} text-[10.5px] leading-snug font-medium">
                                            @if(abs($feedDiffKg) <= 1.0)
                                                ✔ <b>Pemberian pakan tepat & sesuai hitungan standar</b> (selisih {{ number_format(abs($feedDiffKg), 1, ',', '.') }} kg dari {{ number_format($totalPakanCoopKg, 1, ',', '.') }} kg).
                                            @elseif($feedDiffKg > 1.0)
                                                ⚠️ <b>Pemberian pakan MELEBIHI standar</b> sebesar {{ number_format(abs($feedDiffKg), 1, ',', '.') }} kg dari acuan {{ number_format($totalPakanCoopKg, 1, ',', '.') }} kg.
                                            @else
                                                ⚠️ <b>Pemberian pakan KURANG dari standar</b> sebesar {{ number_format(abs($feedDiffKg), 1, ',', '.') }} kg dari acuan {{ number_format($totalPakanCoopKg, 1, ',', '.') }} kg.
                                            @endif
                                        </div>
                                        @php
                                            $feedUsersText = '';
                                            if ($hasPagi && $hasSore) {
                                                if ($uPagi === $uSore && !empty($uPagi)) {
                                                    $feedUsersText = $uPagi;
                                                } else {
                                                    $parts = [];
                                                    if ($uPagi) $parts[] = 'Pagi: ' . $uPagi;
                                                    if ($uSore) $parts[] = 'Sore: ' . $uSore;
                                                    $feedUsersText = implode(' • ', $parts);
                                                }
                                            } elseif ($hasPagi && !empty($uPagi)) {
                                                $feedUsersText = $uPagi . ' (Pagi)';
                                            } elseif ($hasSore && !empty($uSore)) {
                                                $feedUsersText = $uSore . ' (Sore)';
                                            } elseif (!empty($coopFeedUserInputData[$coop->id])) {
                                                $feedUsersText = $coopFeedUserInputData[$coop->id];
                                            }
                                        @endphp
                                        @if(!empty($feedUsersText))
                                        <div class="mt-1 text-[9.5px] text-slate-500 font-medium">
                                            Diinput oleh: <b class="text-slate-700">{{ $feedUsersText }}</b>
                                        </div>
                                        @endif
                                    @else
                                        <div class="mt-1 text-[10px] text-slate-400">
                                            Belum ada pencatatan pakan untuk {{ $coop->name }} hari ini.
                                        </div>
                                    @endif
                                </div>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- KOLOM KANAN (5 Kolom di Desktop): GUDANG INTEGRASI & AKTIVITAS TERAKHIR -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-6">

            <!-- KARTU INTEGRASI GUDANG NOCHIFRAM (Stok Masuk Kandang vs Keluar Penjualan) -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_section_warehouse_summary'))
            <div class="farm-card p-4 sm:p-5 bg-gradient-to-br from-white to-slate-50 border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-maroon-100 text-maroon-800 flex items-center justify-center">
                            <i data-lucide="warehouse" class="w-4 h-4"></i>
                        </div>
                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm">Gudang & Integrasi Penjualan</h4>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">1 DB Terhubung</span>
                </div>

                <div class="space-y-3">
                    <!-- Gudang Telur -->
                    @if(!auth()->check() || auth()->user()->canAccess('dash_widget_egg_stock'))
                    <div class="p-3 rounded-xl bg-amber-50/50 border border-amber-100">
                        @php
                            $stokCratesFormatted = number_format((int) $currentEggStockCrates, 0, ',', '.') . ' Peti';
                            $absStokKg = abs($currentEggStockKg);
                            $stokKgFormatted = ($absStokKg > 0) ? '& ' . ($absStokKg == floor($absStokKg) ? number_format($absStokKg, 0, ',', '.') : number_format($absStokKg, 1, ',', '.')) . ' Kg' : '';
                            $stokTelurDisplay = trim($stokCratesFormatted . ' ' . $stokKgFormatted);

                            $masukCratesFormatted = number_format((int) $totalEggProducedAllTime, 0, ',', '.') . ' Peti';
                            $masukKgFormatted = ($totalEggProducedKgAllTime > 0) ? '& ' . ($totalEggProducedKgAllTime == floor($totalEggProducedKgAllTime) ? number_format($totalEggProducedKgAllTime, 0, ',', '.') : number_format($totalEggProducedKgAllTime, 1, ',', '.')) . ' Kg' : '';
                            $masukTelurDisplay = trim($masukCratesFormatted . ' ' . $masukKgFormatted);

                            $keluarCratesFormatted = number_format((int) $totalEggSoldAllTime, 0, ',', '.') . ' Peti';
                            $keluarKgFormatted = ($eggKgSold > 0) ? ' & ' . ($eggKgSold == floor($eggKgSold) ? number_format($eggKgSold, 0, ',', '.') : number_format($eggKgSold, 1, ',', '.')) . ' Kg' : '';
                            $keluarTelurDisplay = $keluarCratesFormatted . $keluarKgFormatted . ' Terjual';
                        @endphp
                        <div class="flex items-center justify-between text-xs font-bold text-slate-800 mb-1">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Stok Telur Saat Ini
                            </span>
                            <span class="text-maroon-800 font-black text-sm">{{ $stokTelurDisplay }}</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between text-[11px] text-slate-500 pt-1.5 border-t border-amber-100/60 gap-1">
                            <span>Masuk: <b class="text-slate-700">{{ $masukTelurDisplay }}</b></span>
                            <span>Keluar: <b class="text-maroon-800 font-bold">{{ $keluarTelurDisplay }}</b></span>
                        </div>
                    </div>
                    @endif

                    <!-- Gudang Pakan -->
                    @if(!auth()->check() || auth()->user()->canAccess('dash_widget_feed_stock'))
                    @php
                        $dashMasukLayer = $feedSummary['purchased_kg_layer'] ?? 0;
                        $dashMasukLayerKrg = $feedSummary['purchased_karung_layer'] ?? 0;
                        $dashKeluarLayer = $feedSummary['total_keluar_kg_layer'] ?? 0;
                        $dashKeluarLayerKrg = $feedSummary['total_keluar_karung_layer'] ?? 0;
                        $dashStokLayer = $feedSummary['current_stock_kg_layer'] ?? 0;
                        $dashStokLayerKrg = $feedSummary['current_stock_karung_layer'] ?? 0;

                        $dashMasukGrower = $feedSummary['purchased_kg_grower'] ?? 0;
                        $dashMasukGrowerKrg = $feedSummary['purchased_karung_grower'] ?? 0;
                        $dashKeluarGrower = $feedSummary['total_keluar_kg_grower'] ?? 0;
                        $dashKeluarGrowerKrg = $feedSummary['total_keluar_karung_grower'] ?? 0;
                        $dashStokGrower = $feedSummary['current_stock_kg_grower'] ?? 0;
                        $dashStokGrowerKrg = $feedSummary['current_stock_karung_grower'] ?? 0;

                        $dashMasukTotal = $feedSummary['purchased_kg'] ?? 0;
                        $dashMasukTotalKrg = $feedSummary['purchased_karung'] ?? 0;
                        $dashKeluarTotal = $feedSummary['total_keluar_kg'] ?? 0;
                        $dashKeluarTotalKrg = $feedSummary['total_keluar_karung'] ?? 0;
                        $dashStokTotalKrg = $feedSummary['current_stock_karung'] ?? 0;
                        $dashKonsumsiKrg = $feedSummary['consumption_karung'] ?? 0;
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200/80 space-y-3">
                        <!-- Header Widget -->
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-xs font-black text-slate-800 uppercase tracking-wide">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Stok Pakan Gudang Saat Ini
                            </span>
                            <a href="{{ route('warehouse.pakan') }}" class="text-[10px] font-extrabold text-emerald-800 hover:text-emerald-900 bg-white px-2 py-0.5 rounded-lg border border-emerald-200 shadow-2xs hover:underline">
                                Gudang Pakan »
                            </a>
                        </div>

                        <!-- Card Grid Per Jenis Pakan (Layer vs Grower) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <!-- 🌾 PAKAN LAYER -->
                            <div class="p-2.5 rounded-xl bg-white border border-emerald-200/90 shadow-2xs space-y-1.5">
                                <div class="flex items-center justify-between pb-1 border-b border-emerald-100">
                                    <span class="text-[11px] font-black text-emerald-800 flex items-center gap-1">
                                        🌾 Pakan Layer
                                    </span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Petelur
                                    </span>
                                </div>
                                <div class="space-y-1 text-[10.5px]">
                                    <div class="flex justify-between text-slate-600">
                                        <span>Masuk:</span>
                                        <b class="text-slate-800">{{ number_format($dashMasukLayer, 0, ',', '.') }} kg <span class="text-[9px] font-normal text-slate-400">({{ \App\Models\Setting::formatKarungKg($dashMasukLayer) }})</span></b>
                                    </div>
                                    <div class="flex justify-between text-slate-600">
                                        <span>Keluar:</span>
                                        <b class="text-rose-700">{{ number_format($dashKeluarLayer, 0, ',', '.') }} kg <span class="text-[9px] font-normal text-slate-400">({{ \App\Models\Setting::formatKarungKg($dashKeluarLayer) }})</span></b>
                                    </div>
                                    <div class="flex justify-between pt-1 border-t border-dashed border-slate-100 font-extrabold text-[11px]">
                                        <span class="text-slate-700">Sisa Stok Layer:</span>
                                        <span class="{{ $dashStokLayer < 0 ? 'text-rose-600 font-black' : 'text-emerald-700 font-black' }}">
                                            {{ number_format($dashStokLayer, 0, ',', '.') }} kg <span class="text-[9px] font-bold text-slate-500">({{ \App\Models\Setting::formatKarungKg($dashStokLayer) }})</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- 🌾 PAKAN GROWER -->
                            <div class="p-2.5 rounded-xl bg-white border border-sky-200/90 shadow-2xs space-y-1.5">
                                <div class="flex items-center justify-between pb-1 border-b border-sky-100">
                                    <span class="text-[11px] font-black text-sky-800 flex items-center gap-1">
                                        🌾 Pakan Grower
                                    </span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-sky-50 text-sky-700 border border-sky-200">
                                        Remaja/Starter
                                    </span>
                                </div>
                                <div class="space-y-1 text-[10.5px]">
                                    <div class="flex justify-between text-slate-600">
                                        <span>Masuk:</span>
                                        <b class="text-slate-800">{{ number_format($dashMasukGrower, 0, ',', '.') }} kg <span class="text-[9px] font-normal text-slate-400">({{ \App\Models\Setting::formatKarungKg($dashMasukGrower) }})</span></b>
                                    </div>
                                    <div class="flex justify-between text-slate-600">
                                        <span>Keluar:</span>
                                        <b class="text-rose-700">{{ number_format($dashKeluarGrower, 0, ',', '.') }} kg <span class="text-[9px] font-normal text-slate-400">({{ \App\Models\Setting::formatKarungKg($dashKeluarGrower) }})</span></b>
                                    </div>
                                    <div class="flex justify-between pt-1 border-t border-dashed border-slate-100 font-extrabold text-[11px]">
                                        <span class="text-slate-700">Sisa Stok Grower:</span>
                                        <span class="{{ $dashStokGrower < 0 ? 'text-rose-600 font-black' : 'text-sky-700 font-black' }}">
                                            {{ number_format($dashStokGrower, 0, ',', '.') }} kg <span class="text-[9px] font-bold text-slate-500">({{ \App\Models\Setting::formatKarungKg($dashStokGrower) }})</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TOTAL SISA STOK PAKAN GUDANG SAAT INI (HASIL AKHIR / TOTALIN) -->
                        <div class="p-2.5 rounded-xl bg-emerald-900 text-white shadow-md flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-200 block">Total Sisa Stok Pakan Gudang Saat Ini</span>
                                <span class="text-[10px] text-emerald-100/90 font-medium">
                                    Total Masuk: {{ number_format($dashMasukTotal, 0, ',', '.') }} kg | Keluar: {{ number_format($dashKeluarTotal, 0, ',', '.') }} kg
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="text-base sm:text-lg font-black text-amber-300 leading-none">
                                    {{ number_format($currentFeedStockKg, 0, ',', '.') }} <span class="text-xs font-bold text-white">Kg</span>
                                </div>
                                <span class="text-[9.5px] text-emerald-200 font-bold block">
                                    ({{ \App\Models\Setting::formatKarungKg($currentFeedStockKg) }})
                                </span>
                            </div>
                        </div>

                        <!-- Rincian Konsumsi Kandang vs Penjualan -->
                        <div class="flex flex-col sm:flex-row sm:justify-between text-[10.5px] text-slate-600 pt-1 border-t border-emerald-200/60 gap-1 bg-white/70 p-2 rounded-lg border border-emerald-100">
                            <span>🐔 Konsumsi Kandang: <b class="text-slate-800 font-bold">{{ number_format($totalFeedUsedAllTime, 0, ',', '.') }} Kg</b> <span class="text-slate-400 text-[9.5px]">({{ \App\Models\Setting::formatKarungKg($totalFeedUsedAllTime) }})</span></span>
                            <span>🛒 Terjual Luar: <b class="text-emerald-800 font-bold">{{ number_format($feedKarungSold, 0, ',', '.') }} Krg ({{ number_format($feedKgSoldTotal, 0, ',', '.') }} Kg)</b></span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- AKTIVITAS TERAKHIR TIMELINE -->
            @if(!auth()->check() || auth()->user()->canAccess('dash_section_recent_activity'))
            <div>
                <div class="flex items-center justify-between mb-3 px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-maroon-800"></div>
                        <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-slate-800">AKTIVITAS TERAKHIR</h3>
                    </div>
                    <a href="{{ route('rekap.index') }}" class="text-xs text-maroon-800 font-bold hover:underline flex items-center gap-1">
                        <span>Lihat semua</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="farm-card divide-y divide-slate-100 overflow-hidden shadow-xs">
                    @forelse($activities as $act)
                        <div class="p-3.5 sm:p-4 flex items-center justify-between hover:bg-slate-50/80 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($act['category'] === 'egg')
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100 shadow-xs">
                                        <svg class="w-4 h-4 fill-amber-500 text-amber-500" viewBox="0 0 24 24">
                                            <path d="M12 2C7.5 2 4 7.5 4 13.5C4 18.2 7.6 22 12 22C16.4 22 20 18.2 20 13.5C20 7.5 16.5 2 12 2Z" fill="currentColor"/>
                                        </svg>
                                    </div>
                                @elseif($act['category'] === 'feed')
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100 shadow-xs">
                                        <i data-lucide="package" class="w-4 h-4"></i>
                                    </div>
                                @elseif($act['category'] === 'mortality')
                                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 shadow-xs">
                                        <i data-lucide="skull" class="w-4 h-4"></i>
                                    </div>
                                @elseif($act['category'] === 'stock_masuk')
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100 shadow-xs">
                                        <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                                    </div>
                                @elseif($act['category'] === 'stock_keluar')
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100 shadow-xs">
                                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                                    </div>
                                @elseif($act['category'] === 'sale')
                                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center shrink-0 border border-purple-100 shadow-xs">
                                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                    </div>
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center shrink-0 border border-maroon-100 shadow-xs">
                                        <i data-lucide="syringe" class="w-4 h-4"></i>
                                    </div>
                                @endif

                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="text-xs sm:text-sm font-bold text-slate-900 truncate">{{ $act['title'] }}</p>
                                        @if(!empty($act['user_username']))
                                            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[10.5px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded-md border border-slate-200/80 shrink-0" title="Input oleh {{ $act['user_username'] }}">
                                                <i data-lucide="user" class="w-2.5 h-2.5 text-slate-400"></i>
                                                <span>Input: <b>{{ $act['user_username'] }}</b></span>
                                            </span>
                                        @endif
                                        @if(!empty($act['trip_username']))
                                            <span class="inline-flex items-center gap-1 text-[10px] sm:text-[10.5px] font-semibold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded-md border border-indigo-100/80 shrink-0" title="Pembuat Perjalanan: {{ $act['trip_username'] }}">
                                                <i data-lucide="truck" class="w-2.5 h-2.5 text-indigo-500"></i>
                                                <span>Perjalanan: <b>{{ $act['trip_username'] }}</b></span>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $act['datetime'] }} • {{ $act['subtitle'] }}</p>
                                </div>
                            </div>

                            <div class="text-right shrink-0 ml-3">
                                <span class="text-xs sm:text-sm font-black {{ $act['category'] === 'egg' ? 'text-amber-600' : ($act['category'] === 'feed' ? 'text-emerald-600' : ($act['category'] === 'mortality' ? 'text-rose-600' : ($act['category'] === 'sale' ? 'text-purple-700' : ($act['category'] === 'stock_masuk' ? 'text-blue-600' : ($act['category'] === 'stock_keluar' ? 'text-indigo-600' : 'text-maroon-800'))))) }}">
                                    {{ $act['value'] }}
                                </span>
                                @if(!empty($act['subvalue']))
                                    <p class="text-[10.5px] text-slate-400 font-medium">{{ $act['subvalue'] }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-1 text-slate-300"></i>
                            <p class="text-xs font-medium">Belum ada aktivitas tercatat.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: INPUT PRODUKSI TELUR (RESPONSIVE DI HP & LAPTOP)                 -->
<!-- ========================================================================= -->
<div id="modalProduksi" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <i data-lucide="egg" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base">Input Produksi Telur</h3>
                    <p class="text-[11px] text-slate-400">Catat jumlah panen telur masuk dari kandang</p>
                </div>
            </div>
            <button onclick="closeModal('modalProduksi')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('production.store') }}" method="POST" class="py-4 space-y-4">
            @csrf
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <!-- 1. Pilih Klotter & Blok -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Klotter & Blok (Wajib)</label>
                <select name="coop_id" id="prodCoopSelect" required onchange="updateCoopInfo(this)"
                        class="w-full text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 focus:ring-1 focus:ring-maroon-800 outline-none bg-slate-50">
                    <option value="">-- Pilih Blok Kandang --</option>
                    @foreach($coops as $coop)
                        <option value="{{ $coop->id }}" data-capacity="{{ $coop->capacity }}" data-active="{{ $coop->active_chickens }}" data-age="{{ $coop->chicken_age_weeks }}">
                            {{ $coop->flock ? $coop->flock->name . ' - ' : '' }}{{ $coop->name }} ({{ number_format($coop->active_chickens, 0, ',', '.') }} Ekor)
                        </option>
                    @endforeach
                </select>

                <!-- Info Blok Badge -->
                <div id="coopInfoBox" class="mt-2.5 p-3 bg-rose-50/70 border border-rose-100 rounded-xl flex items-center justify-between text-xs text-slate-600 hidden">
                    <div>
                        <span class="font-bold text-maroon-900 block" id="coopActiveText">Kapasitas Aktif: -</span>
                        <span class="text-slate-500 text-[11px]">Umur Ayam: <b id="coopAgeText" class="text-slate-800">-</b></span>
                    </div>
                    <span class="px-2.5 py-1 bg-white text-maroon-800 font-bold rounded-lg border border-rose-200 text-xs shadow-xs">Blok Terpilih</span>
                </div>
            </div>

            <!-- 2. Grid Telur Baik & Retak/Pecah -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Telur Baik (Butir)</label>
                    <div class="relative">
                        <input type="number" name="good_eggs" id="prodGoodEggs" required placeholder="0"
                               oninput="calculateEggEstimates()"
                               class="w-full text-sm font-bold text-slate-900 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 focus:ring-1 focus:ring-maroon-800 outline-none bg-slate-50">
                        <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">Butir</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Retak/Pecah (Butir)</label>
                    <div class="relative">
                        <input type="number" name="broken_eggs" id="prodBrokenEggs" value="0" placeholder="0"
                               oninput="calculateEggEstimates()"
                               class="w-full text-sm font-bold text-rose-700 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 focus:ring-1 focus:ring-maroon-800 outline-none bg-slate-50">
                        <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">Butir</span>
                    </div>
                </div>
            </div>

            <!-- 3. Grid Jumlah Peti & Jumlah Kg (Opsional) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Peti (Opsional)</label>
                    <div class="relative">
                        <input type="number" step="1" name="crates_count" id="prodCratesCount" value="0" placeholder="0"
                               class="w-full text-sm font-bold text-maroon-800 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 focus:ring-1 focus:ring-maroon-800 outline-none bg-slate-50">
                        <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">Peti</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah kg (Opsional)</label>
                    <div class="relative">
                        <input type="number" step="0.1" name="weight_kg" id="prodWeightKg" value="0" placeholder="0"
                               onchange="autoConvertEggKg('prodWeightKg', 'prodCratesCount')"
                               onblur="autoConvertEggKg('prodWeightKg', 'prodCratesCount')"
                               class="w-full text-sm font-bold text-amber-800 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 focus:ring-1 focus:ring-maroon-800 outline-none bg-slate-50">
                        <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">Kg</span>
                    </div>
                </div>
            </div>

            <!-- 4. Estimasi Hasil & Info Hen-Day -->
            <div class="p-3.5 bg-amber-50/80 border border-amber-200 rounded-xl space-y-2 mt-3">
                <div class="flex justify-between items-center text-slate-700 font-medium text-xs sm:text-sm">
                    <span>Produktivitas Hen-Day (HD):</span>
                    <span id="dashCalcHD" class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-black text-xs sm:text-sm">0%</span>
                </div>
                <div class="flex justify-between text-slate-600 font-medium text-xs sm:text-sm">
                    <span>Total Telur (Semua):</span>
                    <b id="calcTotalEggs" class="text-slate-900 font-bold">0 Butir</b>
                </div>
            </div>

            <!-- 4. Keterangan Opsional -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Panen pagi kondisi bagus"
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalProduksi')" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs sm:text-sm shadow-md shadow-maroon-900/20 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Produksi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: INPUT PEMAKAIAN PAKAN                                            -->
<!-- ========================================================================= -->
<div id="modalPakan" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="wheat" class="w-5 h-5"></i>
                </div>
                <h3 class="font-black text-slate-900 text-base">Input Pemakaian Pakan</h3>
            </div>
            <button onclick="closeModal('modalPakan')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('feed.store') }}" method="POST" class="py-4 space-y-4">
            @csrf
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Blok Kandang</label>
                <select name="coop_id" class="w-full text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <option value="">Semua Blok (Global)</option>
                    @foreach($coops as $coop)
                        <option value="{{ $coop->id }}">{{ $coop->name }} ({{ $coop->active_chickens }} Ekor)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Waktu Pemberian</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold cursor-pointer hover:bg-rose-50/50 has-[:checked]:bg-maroon-50 has-[:checked]:border-maroon-800 has-[:checked]:text-maroon-800 transition-all">
                        <input type="radio" name="feeding_time" value="Pagi" checked class="accent-maroon-800">
                        <span>Pagi (07:00)</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold cursor-pointer hover:bg-rose-50/50 has-[:checked]:bg-maroon-50 has-[:checked]:border-maroon-800 has-[:checked]:text-maroon-800 transition-all">
                        <input type="radio" name="feeding_time" value="Sore" class="accent-maroon-800">
                        <span>Sore (15:30)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Pakan</label>
                <input type="text" name="feed_name" value="Pakan Layer" required
                       class="w-full text-xs sm:text-sm font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Pakan (Kg)</label>
                <div class="relative">
                    <input type="number" step="0.1" name="quantity_kg" required placeholder="Contoh: 80"
                           class="w-full text-sm font-bold text-slate-900 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">Kg</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan</label>
                <input type="text" name="notes" placeholder="Opsional"
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
            </div>

            <div class="flex gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalPakan')" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-md">
                    Simpan Pakan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: INPUT MORTALITAS                                                 -->
<!-- ========================================================================= -->
<div id="modalMortalitas" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                    <i data-lucide="skull" class="w-5 h-5"></i>
                </div>
                <h3 class="font-black text-slate-900 text-base">Input Mortalitas Ayam</h3>
            </div>
            <button onclick="closeModal('modalMortalitas')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('mortality.store') }}" method="POST" class="py-4 space-y-4">
            @csrf
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Blok (Wajib)</label>
                <select name="coop_id" required class="w-full text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <option value="">-- Pilih Blok --</option>
                    @foreach($coops as $coop)
                        <option value="{{ $coop->id }}">{{ $coop->name }} (Sisa {{ $coop->active_chickens }} Ekor)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Ayam Mati / Afkir</label>
                <div class="relative">
                    <input type="number" name="count" required min="1" placeholder="Contoh: 2"
                           class="w-full text-sm font-bold text-rose-700 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">Ekor</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status / Kategori</label>
                <select name="type" id="modalMortType" onchange="toggleModalMortType()" class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <option value="mati">Kematian (Mati)</option>
                    <option value="afkir">Afkir (Dipisahkan)</option>
                    @if(!auth()->check() || auth()->user()->canAccess('input_form_quarantine'))
                    <option value="sakit">Karantina (Ayam Sakit)</option>
                    <option value="sembuh">Sembuh (Kembali ke Kandang)</option>
                    @endif
                </select>
            </div>

            <!-- Dynamic Battery Number Field for Modal -->
            <div id="modalMortBatteryField" style="display: none;">
                <label id="modalMortBatteryLabel" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Baterai Kandang</label>
                <input type="text" name="battery_number" id="modalMortBatteryInput" placeholder="Contoh: A-12 / B-05"
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                <span class="text-[10px] text-slate-400 mt-1 block" id="modalMortBatteryHint">Catat posisi baterai untuk riwayat isolasi.</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Penyebab / Indikasi</label>
                <input type="text" name="cause" placeholder="Contoh: Stres panas wajar, kanibalisme, dll"
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
            </div>

            <div class="flex gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalMortalitas')" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-rose-700 hover:bg-rose-800 text-white font-bold text-xs sm:text-sm shadow-md">
                    Simpan Mortalitas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: INPUT BERAT BADAN                                                -->
<!-- ========================================================================= -->
<!-- MODAL: DATA SAMPEL BOBOT AYAM 6 BLOK & INPUT CEPAT                         -->
<!-- ========================================================================= -->
<!-- MODAL: DATA SAMPEL BOBOT AYAM & EVALUASI ADU DATA MASTER (6 BLOK)          -->
<!-- ========================================================================= -->
@if(auth()->check() && auth()->user()->canAccess('dash_card_weight_click'))
<div id="modalBobot6Blok" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-5xl rounded-t-3xl sm:rounded-2xl p-4 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[92vh] overflow-y-auto">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 shadow-xs">
                    <i data-lucide="scale" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base sm:text-lg">Evaluasi Sampel Bobot Ayam & Telur vs Data Master</h3>
                    <p class="text-xs text-slate-500">Adu kesesuaian input BB ayam & berat butir telur dengan acuan master (BB Min, Target, Max, & Toleransi Telur sesuai minggu umur)</p>
                </div>
            </div>
            <button onclick="closeModal('modalBobot6Blok')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="py-4 space-y-4">
            <!-- Filter Kloter & Blok -->
            <div class="bg-slate-50 p-3 sm:p-3.5 rounded-xl border border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Pilih Kloter</label>
                        <select id="modalBobotFilterFlock" onchange="onModalBobotFlockChange()" class="text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 outline-none shadow-xs">
                            <option value="all">Semua Kloter</option>
                            @foreach($flocks as $flock)
                                <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Pilih Blok</label>
                        <select id="modalBobotFilterCoop" onchange="filterModalBobot()" class="text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 outline-none shadow-xs">
                            <option value="all">Semua Blok (6 Blok)</option>
                            @foreach($coops as $coop)
                                <option value="{{ $coop->id }}" data-flock="{{ $coop->flock_id }}">{{ $coop->name }} ({{ $coop->flock ? $coop->flock->name : 'K1' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Acuan Master Standar Produksi
                    </span>
                </div>
            </div>

            <!-- Kartu Detail Evaluasi Blok Terpilih (Muncul jika user memilih blok tertentu) -->
            @foreach($coops as $c)
                @php
                    $d = $coopWeightDetails[$c->id] ?? null;
                    $cStd = $coopStandards[$c->id] ?? \App\Services\ProductionStandardService::getStandardForWeek((int)$c->chicken_age_weeks);
                    $bbAct = $d && isset($d['weight_kg']) ? (float)$d['weight_kg'] : null;
                    $eggAct = $d && isset($d['egg_weight_gram']) ? (float)$d['egg_weight_gram'] : null;
                    $bbMin = (float)($cStd['bb_min'] ?? 0);
                    $bbTarget = (float)($cStd['bb_target'] ?? 0);
                    $bbMax = (float)($cStd['bb_max'] ?? 0);
                    $eggTargetVal = (float)($cStd['berat_telur_val'] ?? 0);
                    $eggMinTol = $eggTargetVal > 0 ? round($eggTargetVal - 2.5, 1) : 0;
                    $eggMaxTol = $eggTargetVal > 0 ? round($eggTargetVal + 2.5, 1) : 0;

                    $isTargetOrMore = ($bbAct !== null && $bbAct >= $bbTarget);
                    $isBelowTarget = ($bbAct !== null && $bbAct < $bbTarget);
                    $isIdealEgg = ($eggAct !== null && $eggTargetVal > 0 && $eggAct >= $eggMinTol && $eggAct <= $eggMaxTol);
                @endphp
                <div class="modal-bobot-card-detail p-4 rounded-xl border {{ $bbAct === null ? 'bg-slate-50 border-slate-200' : ($isTargetOrMore ? 'bg-emerald-50/50 border-emerald-200' : 'bg-rose-50/50 border-rose-200') }}" 
                     data-coop="{{ $c->id }}" 
                     style="display: none;">
                    
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-2.5 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-slate-900">{{ $c->name }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $c->flock ? $c->flock->name : 'Kloter' }}
                            </span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-rose-50 text-maroon-800 border border-rose-100">
                                Umur: {{ $c->chicken_age_weeks }} Minggu
                            </span>
                            <span class="text-xs font-extrabold px-2 py-0.5 rounded border {{ $cStd['pill_class'] }}">
                                {{ $cStd['pill'] }} ({{ $cStd['fase'] }})
                            </span>
                        </div>
                        <div>
                            @if($bbAct === null)
                                <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                    Belum Ada Sampel Diinput
                                </span>
                            @elseif($isTargetOrMore)
                                <span class="text-xs font-black px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1 shadow-2xs">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    Kondisi Ayam: SUDAH CAPAI TARGET MASTER ({{ number_format($bbAct, 1, ',', '.') }} kg >= {{ number_format($bbTarget, 1, ',', '.') }} kg)
                                </span>
                            @else
                                <span class="text-xs font-black px-2.5 py-1 rounded-md bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1 shadow-2xs">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 text-rose-600"></i>
                                    Kondisi Ayam: DI BAWAH TARGET MASTER (-{{ number_format(round($bbTarget - $bbAct, 2), 1, ',', '.') }} kg dari target {{ number_format($bbTarget, 1, ',', '.') }} kg)
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Adu Data Metrik Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 mt-3 text-xs">
                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">BB Ayam (Input Aktual)</span>
                            <b class="text-base font-black {{ $bbAct === null ? 'text-slate-400' : ($isTargetOrMore ? 'text-emerald-700' : 'text-rose-700') }}">
                                {{ $bbAct ? number_format($bbAct, 1, ',', '.') . ' Kg' : 'Belum Input' }}
                            </b>
                            <span class="text-[10px] text-slate-500 block mt-0.5">
                                {{ $d && $d['battery_number'] ? 'Baterai: ' . $d['battery_number'] : 'Posisi sampel acak' }}
                            </span>
                        </div>

                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Standar BB Master (Umur {{ $c->chicken_age_weeks }} Mgg)</span>
                            <div class="flex items-center gap-1 font-bold text-slate-800 text-xs mt-0.5">
                                <span class="text-slate-500">Min: {{ number_format($bbMin, 1, ',', '.') }}</span> •
                                <b class="text-emerald-700 font-black">Target: {{ number_format($bbTarget, 1, ',', '.') }}</b> •
                                <span class="text-slate-500">Max: {{ number_format($bbMax, 1, ',', '.') }}</span>
                            </div>
                            <span class="text-[10px] font-semibold text-emerald-800 block mt-0.5">
                                Rentang ideal: {{ number_format($bbMin, 1, ',', '.') }} – {{ number_format($bbMax, 1, ',', '.') }} Kg
                            </span>
                        </div>

                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">BB Telur Butir (Input Aktual)</span>
                            <b class="text-base font-black {{ $eggAct ? ($isIdealEgg ? 'text-emerald-700' : 'text-rose-700') : 'text-slate-400' }}">
                                {{ $eggAct ? number_format($eggAct, 1, ',', '.') . ' Gram' : '-' }}
                            </b>
                            <span class="text-[10px] {{ $eggAct ? ($isIdealEgg ? 'text-emerald-700 font-bold' : 'text-rose-600 font-bold') : 'text-slate-500' }} block mt-0.5">
                                {{ $eggAct ? ($isIdealEgg ? '✓ Sesuai batas toleransi' : '⚠️ Di luar toleransi target') : 'Belum ditimbang' }}
                            </span>
                        </div>

                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 block uppercase">Acuan & Toleransi Telur</span>
                            <b class="text-sm font-black text-amber-900 block mt-0.5">
                                {{ $eggTargetVal > 0 ? number_format($eggMinTol, 1, ',', '.') . ' – ' . number_format($eggMaxTol, 1, ',', '.') . ' g' : '-' }}
                            </b>
                            <span class="text-[10px] text-slate-500 block mt-0.5">
                                Target Ideal Master: <b>{{ $cStd['berat_telur'] }}</b>
                            </span>
                        </div>
                    </div>

                    @if($d && $d['notes'])
                        <div class="mt-2.5 p-2 bg-white rounded-lg border border-slate-200 text-xs text-slate-700">
                            <b class="text-slate-900 font-bold">Catatan Kondisi Ayam:</b> {{ $d['notes'] }}
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- General Banner (Muncul bila 'Semua Blok' dipilih) -->
            <div id="modalBobotGeneralBanner" class="p-3 bg-sky-50/70 rounded-xl border border-sky-200 text-xs text-sky-950 flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-sky-700 shrink-0 mt-0.5"></i>
                <div class="text-[11px] leading-relaxed">
                    <b class="text-sky-950">Adu Data Master Standar Produksi vs Hasil Inputan Aktual:</b>
                    <span class="text-sky-900">
                        Setiap baris blok kandang membandingkan berat badan (BB) ayam dan bobot butir telur sampel terhadap batas <b>BB Minimum</b>, <b>BB Target (Ideal)</b>, <b>BB Maksimum</b>, dan <b>Batas Toleransi Telur</b> berdasarkan umur minggu ayam di blok tersebut.
                    </span>
                </div>
            </div>

            <!-- Tabel Evaluasi Adu Data Master 6 Blok -->
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[9.5px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Kloter & Blok</th>
                            <th class="py-2.5 px-2.5 text-center">Umur</th>
                            <th class="py-2.5 px-3">Fase Pertumbuhan</th>
                            <th class="py-2.5 px-3 text-right bg-sky-50/50 text-sky-950">BB Ayam (Input)</th>
                            <th class="py-2.5 px-2.5 text-right bg-slate-100/60">BB Min</th>
                            <th class="py-2.5 px-2.5 text-right bg-emerald-100/50 text-emerald-950">BB Target</th>
                            <th class="py-2.5 px-2.5 text-right bg-slate-100/60">BB Max</th>
                            <th class="py-2.5 px-3 text-center">Status BB Ayam</th>
                            <th class="py-2.5 px-3 text-right bg-amber-50/50 text-amber-950">BB Telur (Input)</th>
                            <th class="py-2.5 px-3 text-right bg-amber-100/40 text-amber-950">Toleransi Telur</th>
                            <th class="py-2.5 px-3">No. Baterai</th>
                            <th class="py-2.5 px-2.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($coops as $c)
                            @php
                                $d = $coopWeightDetails[$c->id] ?? null;
                                $cStd = $coopStandards[$c->id] ?? \App\Services\ProductionStandardService::getStandardForWeek((int)$c->chicken_age_weeks);
                                $bbAct = $d && isset($d['weight_kg']) ? (float)$d['weight_kg'] : null;
                                $eggAct = $d && isset($d['egg_weight_gram']) ? (float)$d['egg_weight_gram'] : null;
                                $bbMin = (float)($cStd['bb_min'] ?? 0);
                                $bbTarget = (float)($cStd['bb_target'] ?? 0);
                                $bbMax = (float)($cStd['bb_max'] ?? 0);
                                $eggTargetVal = (float)($cStd['berat_telur_val'] ?? 0);
                                $eggMinTol = $eggTargetVal > 0 ? round($eggTargetVal - 2.5, 1) : 0;
                                $eggMaxTol = $eggTargetVal > 0 ? round($eggTargetVal + 2.5, 1) : 0;
                                $isIdealEgg = $eggAct !== null && ($eggAct >= $eggMinTol && $eggAct <= $eggMaxTol);
                            @endphp
                            <tr class="modal-bobot-row hover:bg-sky-50/30 transition-colors" 
                                data-flock="{{ $c->flock_id }}" 
                                data-coop="{{ $c->id }}">
                                
                                <!-- Kloter & Blok -->
                                <td class="py-3 px-3 font-extrabold text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-maroon-800"></span>
                                        <span>{{ $c->name }}</span>
                                        <span class="text-[10px] font-semibold text-slate-400">({{ $c->flock ? $c->flock->code : 'K1' }})</span>
                                    </div>
                                </td>

                                <!-- Umur Minggu (Otomatis) -->
                                <td class="py-3 px-2.5 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded bg-rose-50 text-maroon-800 font-black text-[10px] border border-rose-100">
                                        {{ $c->chicken_age_weeks }} Mgg
                                    </span>
                                </td>

                                <!-- Fase Pertumbuhan (Otomatis) -->
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold border {{ $cStd['pill_class'] }}">
                                        {{ $cStd['pill'] }}
                                    </span>
                                    <span class="block text-[9px] text-slate-400 font-medium truncate max-w-[110px]" title="{{ $cStd['fase'] }}">{{ $cStd['fase'] }}</span>
                                </td>

                                <!-- BB Ayam Aktual -->
                                <td class="py-3 px-3 text-right whitespace-nowrap {{ $bbAct === null ? 'bg-sky-50/30' : ($bbAct >= $bbTarget ? 'bg-emerald-50/40' : 'bg-rose-50/40') }}">
                                    <b class="text-xs font-black {{ $bbAct === null ? 'text-slate-400' : ($bbAct >= $bbTarget ? 'text-emerald-700 text-sm' : 'text-rose-700 text-sm') }}">
                                        {{ $bbAct ? number_format($bbAct, 1, ',', '.') . ' Kg' : '-' }}
                                    </b>
                                </td>

                                <!-- Acuan Master: BB Min -->
                                <td class="py-3 px-2.5 text-right whitespace-nowrap font-semibold text-slate-600 bg-slate-50/50">
                                    {{ number_format($bbMin, 1, ',', '.') }} kg
                                </td>

                                <!-- Acuan Master: BB Target (Ideal) -->
                                <td class="py-3 px-2.5 text-right whitespace-nowrap bg-emerald-50/40">
                                    <b class="font-black text-emerald-800 text-xs px-1.5 py-0.5 rounded bg-emerald-100/70 border border-emerald-300">
                                        {{ number_format($bbTarget, 1, ',', '.') }} kg
                                    </b>
                                </td>

                                <!-- Acuan Master: BB Max -->
                                <td class="py-3 px-2.5 text-right whitespace-nowrap font-semibold text-slate-600 bg-slate-50/50">
                                    {{ number_format($bbMax, 1, ',', '.') }} kg
                                </td>

                                <!-- Status Kesesuaian Ayam (Adu Master: Hijau jika capai/lebih target, Merah jika di bawah target) -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    @if($bbAct === null)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold text-[9.5px] border border-slate-200">
                                            Belum Input
                                        </span>
                                    @elseif($bbAct >= $bbTarget)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-black text-[10px] border border-emerald-300 inline-flex items-center gap-1 shadow-2xs" title="Capai target atau lebih ({{ number_format($bbAct, 1, ',', '.') }} kg >= {{ number_format($bbTarget, 1, ',', '.') }} kg)">
                                            <i data-lucide="check-circle-2" class="w-3 h-3 text-emerald-600"></i> Capai Target / Lebih
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-black text-[10px] border border-rose-300 inline-flex items-center gap-1 shadow-2xs" title="Belum sesuai master (Kurang {{ number_format(round($bbTarget - $bbAct, 3), 1, ',', '.') }} kg)">
                                            <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600"></i> Di Bawah Target
                                        </span>
                                    @endif
                                </td>

                                <!-- BB Telur Aktual -->
                                <td class="py-3 px-3 text-right whitespace-nowrap {{ $eggAct === null ? 'bg-amber-50/20' : ($isIdealEgg ? 'bg-emerald-50/40' : 'bg-rose-50/40') }}">
                                    <b class="text-xs font-black {{ $eggAct === null ? 'text-slate-400' : ($isIdealEgg ? 'text-emerald-700' : 'text-rose-700') }}">
                                        {{ $eggAct ? number_format($eggAct, 1, ',', '.') . ' g' : '-' }}
                                    </b>
                                    @if($eggAct !== null)
                                        <span class="block text-[9.5px] font-black {{ $isIdealEgg ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $isIdealEgg ? '✓ Sesuai' : '⚠️ Di Luar' }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Batas Toleransi Telur Master -->
                                <td class="py-3 px-3 text-right whitespace-nowrap bg-amber-100/20">
                                    @if($eggTargetVal > 0)
                                        <b class="text-[10.5px] font-extrabold text-amber-950 block">
                                            {{ number_format($eggMinTol, 1, ',', '.') }}–{{ number_format($eggMaxTol, 1, ',', '.') }} g
                                        </b>
                                        <span class="text-[9px] text-slate-400 block font-medium">Acuan: {{ $cStd['berat_telur'] }}</span>
                                    @else
                                        <span class="text-slate-400 font-medium">-</span>
                                    @endif
                                </td>

                                <!-- No. Baterai & Tanggal -->
                                <td class="py-3 px-3 whitespace-nowrap font-medium text-slate-700">
                                    <span class="font-bold text-slate-900 block text-[11px]">{{ $d && $d['battery_number'] ? $d['battery_number'] : '-' }}</span>
                                    <span class="text-[9.5px] text-slate-400 block">{{ $d && $d['date'] ? $d['date'] : '-' }}</span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3 px-2.5 text-center whitespace-nowrap">
                                    <a href="{{ route('input.index', ['type' => 'bobot']) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 font-bold text-[10px] transition-all">
                                        <i data-lucide="edit-3" class="w-3 h-3"></i>
                                        <span>Input</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="flex flex-col sm:flex-row gap-2.5 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalBobot6Blok')" class="py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm">
                    Tutup
                </button>
                <a href="{{ route('input.index', ['type' => 'bobot']) }}" class="flex-1 py-2.5 px-4 rounded-xl bg-sky-700 hover:bg-sky-800 active:scale-[0.99] text-white font-bold text-xs sm:text-sm text-center shadow-md flex items-center justify-center gap-1.5 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>+ Input Timbang Ayam + Telur Baru</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- MODAL 5: INPUT VAKSIN & OBAT                                              -->
<!-- ========================================================================= -->
<div id="modalVaksin" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-maroon-100 text-maroon-800 flex items-center justify-center">
                    <i data-lucide="syringe" class="w-5 h-5"></i>
                </div>
                <h3 class="font-black text-slate-900 text-base">Input Vaksin / Obat</h3>
            </div>
            <button onclick="closeModal('modalVaksin')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('health.store') }}" method="POST" class="py-4 space-y-4">
            @csrf
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Blok Sasaran</label>
                <select name="coop_id" class="w-full text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <option value="">Semua Blok (Kandang Keseluruhan)</option>
                    @foreach($coops as $coop)
                        <option value="{{ $coop->id }}">{{ $coop->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori</label>
                <select name="type" class="w-full text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <option value="vitamin">Vitamin</option>
                    <option value="vaksin">Vaksin</option>
                    <option value="obat">Obat / Antibiotik</option>
                    <option value="disinfektan">Disinfektan</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Produk / Vaksin</label>
                <input type="text" name="medicine_name" required placeholder="Contoh: Vitamin B Complex, ND IB Vaccine"
                       class="w-full text-xs sm:text-sm font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Dosis / Jumlah</label>
                    <input type="text" name="dosage" placeholder="Contoh: 10 Botol"
                           class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Cara Aplikasi</label>
                    <select name="application_method" class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                        <option value="Air Minum">Air Minum</option>
                        <option value="Suntik">Suntik</option>
                        <option value="Tetes Mata">Tetes Mata</option>
                        <option value="Semprot (Spray)">Semprot (Spray)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalVaksin')" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs sm:text-sm shadow-md">
                    Simpan Vaksin
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL PENJELASAN FASE PRODUKSI NAIK & STANDAR PAKAN -->
<!-- ========================================================================= -->
<div id="modalFasePenjelasan" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-2xl rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base">Penjelasan Fase: PRODUKSI NAIK</h3>
                    <p class="text-xs text-slate-500">Standar Umur 21 Minggu & Kebutuhan Pakan 105 g/ekor</p>
                </div>
            </div>
            <button onclick="closeModal('modalFasePenjelasan')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="py-4 space-y-4 text-xs sm:text-sm text-slate-700">
            <!-- Poin 1: Kenapa Fase Ini Dinamakan PRODUKSI NAIK? -->
            <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-xl space-y-2">
                <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs sm:text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs">1</span>
                    <span>Kenapa Statusnya "PRODUKSI NAIK"?</span>
                </div>
                <div class="pl-8 text-xs text-emerald-950 space-y-1.5 leading-relaxed">
                    <p>
                        Ayam petelur pada blok kandang saat ini berada pada <b>Umur 21 Minggu</b> (rentang standar umur <b>21–25 minggu</b>).
                    </p>
                    <p>
                        <b>Alasan Biologis:</b> Di usia 21 minggu, ayam telah menyelesaikan masa pembentukan kerangka (grower) dan saluran telur (oviduk) berkembang penuh. Ayam sedang berada pada <b>fase lonjakan bertelur tercepat</b> menuju titik puncak produksi, di mana persentase produksi (Hen Day / HDP) melonjak tajam dari ~50% hingga melampaui <b>92% – 95%</b>.
                    </p>
                    <div class="grid grid-cols-3 gap-2 mt-2 pt-2 border-t border-emerald-200/60 text-center">
                        <div class="bg-white/80 p-2 rounded-lg border border-emerald-200/60">
                            <span class="text-[10px] text-emerald-700 block">Target HDP</span>
                            <b class="text-slate-900 text-xs sm:text-sm font-black">92% – 95%</b>
                        </div>
                        <div class="bg-white/80 p-2 rounded-lg border border-emerald-200/60">
                            <span class="text-[10px] text-emerald-700 block">Acuan Telur</span>
                            <b class="text-slate-900 text-xs sm:text-sm font-black">60 g / butir</b>
                        </div>
                        <div class="bg-white/80 p-2 rounded-lg border border-emerald-200/60">
                            <span class="text-[10px] text-emerald-700 block">Bobot Badan</span>
                            <b class="text-slate-900 text-xs sm:text-sm font-black">1,55 – 1,69 kg</b>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Poin 2: Kenapa Standar Pakan 105 g/ekor dan Berapa Totalnya? -->
            <div class="p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-xl space-y-2">
                <div class="flex items-center gap-2 text-amber-900 font-bold text-xs sm:text-sm">
                    <span class="w-6 h-6 rounded-full bg-amber-600 text-white flex items-center justify-center text-xs">2</span>
                    <span>Standar Pakan 105 g/ekor & Total Kebutuhan Pakan</span>
                </div>
                <div class="pl-8 text-xs text-amber-950 space-y-2 leading-relaxed">
                    <p>
                        <b>Alasan Kebutuhan 105 g/ekor:</b> Karena ayam sedang memproduksi telur setiap hari, kebutuhan energi, protein (17–18%), dan kalsium (3,8–4,2%) meningkat drastis. Dosis <b>105 gram per ekor per hari</b> adalah takaran presisi agar ayam bertelur maksimal tanpa kelebihan lemak tubuh.
                    </p>
                    <div class="p-2.5 bg-white/90 rounded-xl border border-amber-200/80 space-y-1.5">
                        <div class="font-bold text-slate-900 flex justify-between">
                            <span>Total Kebutuhan Seluruh Farm (4.017 ekor):</span>
                            <span class="text-maroon-800 font-black">{{ number_format($totalFarmPakanKg, 1, ',', '.') }} kg / hari ({{ $totalFarmKarungStr }})</span>
                        </div>
                        <div class="text-[11px] text-slate-600 flex justify-between border-t border-amber-100 pt-1">
                            <span>Jadwal Pemberian Pagi (50%): <b>{{ number_format($totalFarmPakanKg / 2, 1, ',', '.') }} kg</b></span>
                            <span>Jadwal Pemberian Sore (50%): <b>{{ number_format($totalFarmPakanKg / 2, 1, ',', '.') }} kg</b></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rincian Pakan Per Blok Kandang -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">Rincian Total Pakan Per Blok (Otomatis):</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                    @foreach($coops as $c)
                        @php
                            $cKg = round(($c->active_chickens * 105) / 1000, 1);
                            $kPerKrg = $kgPerKarung ?? 50;
                            $cKr = floor($cKg / $kPerKrg);
                            $cSk = round(fmod($cKg, $kPerKrg), 1);
                            $cKrStr = ($cKr > 0 ? $cKr . ' karung ' : '') . ($cSk > 0 ? '+ ' . $cSk . ' kg' : '');
                        @endphp
                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="flex justify-between font-bold text-slate-900">
                                <span>{{ $c->name }}</span>
                                <span class="text-maroon-800">{{ number_format($cKg, 1, ',', '.') }} kg</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5 flex justify-between">
                                <span>{{ $c->active_chickens }} ekor</span>
                                <span>{{ $cKrStr }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Poin 3: Panduan 6 Siklus Standar Fase Umur Ayam Petelur -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">Acuan 6 Fase Umur Siklus Ayam Petelur:</h4>
                <div class="space-y-1.5 text-xs">
                    <div class="p-2 rounded-lg bg-blue-50/60 border border-blue-200/70 flex items-center justify-between">
                        <div>
                            <b class="text-blue-900">13–17 Minggu • Pullet / Grower</b>
                            <p class="text-[11px] text-blue-800">Pertumbuhan kerangka & organ tubuh (Pakan 70–85g/ekor)</p>
                        </div>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-blue-100 text-blue-800">GROWER</span>
                    </div>
                    <div class="p-2 rounded-lg bg-amber-50/60 border border-amber-200/70 flex items-center justify-between">
                        <div>
                            <b class="text-amber-900">18–20 Minggu • Pra-Layer</b>
                            <p class="text-[11px] text-amber-800">Awal bertelur (HD 5-50%), adaptasi pakan layer (Pakan 90–100g/ekor)</p>
                        </div>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-amber-100 text-amber-800">AWAL BERTELUR</span>
                    </div>
                    <div class="p-2 rounded-lg bg-emerald-100/70 border border-emerald-300 flex items-center justify-between shadow-2xs">
                        <div>
                            <b class="text-emerald-950">21–25 Minggu • Produksi Naik (KANDANG ANDA SAAT INI)</b>
                            <p class="text-[11px] text-emerald-900">Lonjakan telur pesat menuju puncak (Pakan 105g/ekor, Telur 60g)</p>
                        </div>
                        <span class="text-[10px] font-black px-2 py-0.5 rounded bg-emerald-600 text-white animate-pulse">PRODUKSI NAIK</span>
                    </div>
                    <div class="p-2 rounded-lg bg-emerald-50/60 border border-emerald-200/70 flex items-center justify-between">
                        <div>
                            <b class="text-emerald-900">26–45 Minggu • Puncak Produksi (Peak)</b>
                            <p class="text-[11px] text-emerald-800">Performa puncak stabil (HD > 92%, Pakan 115g/ekor)</p>
                        </div>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">PUNCAK PRODUKSI</span>
                    </div>
                    <div class="p-2 rounded-lg bg-teal-50/60 border border-teal-200/70 flex items-center justify-between">
                        <div>
                            <b class="text-teal-900">46–70 Minggu • Produksi Stabil</b>
                            <p class="text-[11px] text-teal-800">Produksi stabil, perhatikan asupan kalsium & cangkang telur</p>
                        </div>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-teal-100 text-teal-800">PRODUKSI STABIL</span>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <b class="text-slate-800">>70 Minggu • Post-Peak / Afkir</b>
                            <p class="text-[11px] text-slate-600">Fase akhir produksi sebelum peremajaan / afkir</p>
                        </div>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-slate-200 text-slate-700">POST PEAK</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-3 border-t border-slate-100">
            <button type="button" onclick="closeModal('modalFasePenjelasan')" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm">
                Tutup
            </button>
            <a href="{{ route('master.index') }}#card-standar-produksi" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs sm:text-sm shadow-md text-center flex items-center justify-center gap-1.5">
                <span>Buka Master Standar (13–90 Mgg)</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal Open & Close Helpers
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('modal-active');
            modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
            modal.classList.add('visible', 'opacity-100', 'pointer-events-auto');
            const content = modal.querySelector('div');
            if (content) {
                content.classList.add('modal-content-active');
                content.classList.remove('translate-y-full');
                content.classList.add('translate-y-0');
            }
            if (window.lucide) {
                lucide.createIcons();
            }
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('modal-active', 'visible', 'opacity-100', 'pointer-events-auto');
            modal.classList.add('invisible', 'opacity-0', 'pointer-events-none');
            const content = modal.querySelector('div');
            if (content) {
                content.classList.remove('modal-content-active', 'translate-y-0');
                content.classList.add('translate-y-full');
            }
        }
    }

    // Buka modal produksi langsung memilih blok tertentu
    function openModalForCoop(modalId, coopId) {
        openModal(modalId);
        const coopSelect = document.getElementById('prodCoopSelect');
        if (coopSelect) {
            coopSelect.value = coopId;
            updateCoopInfo(coopSelect);
        }
    }

    // Toggle expand/collapse penjelasan alasan fase umur pada kartu blok kandang
    function toggleCoopDetail(coopId) {
        const shortText = document.getElementById('coopShortText_' + coopId);
        const fullText = document.getElementById('coopFullText_' + coopId);
        const btn = document.getElementById('coopDetailBtn_' + coopId);
        
        if (fullText && shortText && btn) {
            if (fullText.classList.contains('hidden')) {
                fullText.classList.remove('hidden');
                shortText.classList.add('hidden');
                btn.innerHTML = 'Tutup &laquo;';
            } else {
                fullText.classList.add('hidden');
                shortText.classList.remove('hidden');
                btn.innerHTML = 'Detail &raquo;';
            }
        }
    }

    let dashSelectedCoopActive = 0;

    // Update info blok saat memilih blok di modal produksi
    function updateCoopInfo(selectElem) {
        const selected = selectElem.options[selectElem.selectedIndex];
        const infoBox = document.getElementById('coopInfoBox');
        if (selected && selected.value) {
            dashSelectedCoopActive = parseInt(selected.getAttribute('data-active') || '0');
            const age = selected.getAttribute('data-age') || '0';
            document.getElementById('coopActiveText').textContent = 'Kapasitas Aktif: ' + Number(dashSelectedCoopActive).toLocaleString('id-ID') + ' Ekor';
            document.getElementById('coopAgeText').textContent = age + ' Minggu';
            infoBox.classList.remove('hidden');
        } else {
            dashSelectedCoopActive = 0;
            infoBox.classList.add('hidden');
        }
        calculateEggEstimates();
    }

    // Kalkulasi estimasi butir baik dan peti secara otomatis
    function calculateEggEstimates() {
        const good = parseInt(document.getElementById('prodGoodEggs').value) || 0;
        const broken = parseInt(document.getElementById('prodBrokenEggs').value) || 0;
        const total = good + broken;

        document.getElementById('calcTotalEggs').textContent = total.toLocaleString('id-ID') + ' Butir';

        // Hitung Hen-Day (HD %)
        const hdElem = document.getElementById('dashCalcHD');
        if (hdElem) {
            if (dashSelectedCoopActive > 0 && total > 0) {
                const hd = ((total / dashSelectedCoopActive) * 100).toFixed(1);
                hdElem.textContent = hd + '%';
                if (hd >= 85) {
                    hdElem.className = "px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-black text-xs sm:text-sm";
                } else if (hd >= 70) {
                    hdElem.className = "px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-black text-xs sm:text-sm";
                } else {
                    hdElem.className = "px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-black text-xs sm:text-sm";
                }
            } else {
                hdElem.textContent = '0%';
                hdElem.className = "px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-black text-xs sm:text-sm";
            }
        }
    }

    // Konversi otomatis: setiap 10 kg menjadi 1 Peti (Peti selalu bulat tanpa koma)
    function autoConvertEggKg(kgInputId, petiInputId) {
        const kgEl = document.getElementById(kgInputId);
        const petiEl = document.getElementById(petiInputId);
        if (!kgEl || !petiEl) return;
        
        let kgVal = parseFloat(kgEl.value) || 0;
        if (kgVal >= 10) {
            const extraPeti = Math.floor(kgVal / 10);
            const currentPeti = parseInt(petiEl.value || 0, 10);
            petiEl.value = currentPeti + extraPeti;
            const remainderKg = Math.round((kgVal - (extraPeti * 10)) * 10) / 10;
            kgEl.value = remainderKg > 0 ? remainderKg : '';
        }
    }

    // Toggle jenis status mortalitas & nomor baterai di modal
    function toggleModalMortType() {
        const sel = document.getElementById('modalMortType');
        const field = document.getElementById('modalMortBatteryField');
        const label = document.getElementById('modalMortBatteryLabel');
        const hint = document.getElementById('modalMortBatteryHint');
        const val = sel ? sel.value : 'mati';
        if (val === 'sakit') {
            if (field) field.style.display = 'block';
            if (label) label.textContent = 'Nomor Baterai Asal (Kandang)';
            if (hint) hint.textContent = 'Catat nomor baterai asal ayam sakit untuk riwayat isolasi karantina.';
        } else if (val === 'sembuh') {
            if (field) field.style.display = 'block';
            if (label) label.textContent = 'Nomor Baterai Tujuan (Kandang)';
            if (hint) hint.textContent = 'Catat nomor baterai tempat ayam yang sembuh dikembalikan ke kandang.';
        } else {
            if (field) field.style.display = 'none';
        }
    }

    // Filter bar & selector untuk Modal Bobot 6 Blok
    function filterModalBobot() {
        const flockVal = document.getElementById('modalBobotFilterFlock').value;
        const coopVal = document.getElementById('modalBobotFilterCoop').value;
        const rows = document.querySelectorAll('.modal-bobot-row');

        rows.forEach(row => {
            const rFlock = row.getAttribute('data-flock');
            const rCoop = row.getAttribute('data-coop');

            const matchFlock = (flockVal === 'all' || rFlock === flockVal);
            const matchCoop = (coopVal === 'all' || rCoop === coopVal);

            if (matchFlock && matchCoop) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const coopCards = document.querySelectorAll('.modal-bobot-card-detail');
        coopCards.forEach(card => {
            const cId = card.getAttribute('data-coop');
            if (coopVal !== 'all' && cId === coopVal) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        const generalBanner = document.getElementById('modalBobotGeneralBanner');
        if (generalBanner) {
            generalBanner.style.display = (coopVal === 'all') ? 'flex' : 'none';
        }
    }

    function onModalBobotFlockChange() {
        const flockVal = document.getElementById('modalBobotFilterFlock').value;
        const coopSelect = document.getElementById('modalBobotFilterCoop');

        Array.from(coopSelect.options).forEach(opt => {
            if (opt.value === 'all') return;
            const optFlock = opt.getAttribute('data-flock');
            if (flockVal === 'all' || optFlock === flockVal) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });

        coopSelect.value = 'all';
        filterModalBobot();
    }
</script>
@endpush
