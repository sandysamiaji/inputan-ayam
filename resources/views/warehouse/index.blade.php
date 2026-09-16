@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header Section (Sesuai Desain Mockup) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-maroon-800 text-white flex items-center justify-center shadow-md">
                    <i data-lucide="warehouse" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">GUDANG</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Ringkasan stok barang</p>
                </div>
            </div>
        </div>

        <!-- Traveloka Style Date Range Selector -->
        <div class="relative" id="travelokaDatePicker">
            <!-- Traveloka Search Capsule Trigger -->
            <div onclick="toggleTravelokaPopover()" 
                 id="travelokaTriggerBtn"
                 class="cursor-pointer bg-white hover:bg-slate-50/90 border border-slate-200/90 hover:border-maroon-300 shadow-xs hover:shadow-md transition-all rounded-2xl p-1.5 sm:p-2 flex items-center gap-1.5 sm:gap-2.5 group select-none">
                
                <!-- Start Date Segment -->
                <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-slate-50 group-hover:bg-white transition-colors">
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Dari Tanggal</div>
                        <div class="text-xs sm:text-sm font-black text-slate-800 whitespace-nowrap">
                            {{ $formattedStartDate }}
                        </div>
                    </div>
                </div>

                <!-- Center Duration Badge (Traveloka Style) -->
                <div class="flex items-center gap-1 px-2.5 py-1 rounded-full bg-gradient-to-r from-rose-50 to-amber-50 text-maroon-900 text-[10px] font-extrabold shrink-0 border border-rose-200/80 shadow-2xs">
                    <span>{{ $diffDays }} Hari</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-maroon-700"></i>
                </div>

                <!-- End Date Segment -->
                <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-slate-50 group-hover:bg-white transition-colors">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-check-2" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Sampai Tanggal</div>
                        <div class="text-xs sm:text-sm font-black text-slate-800 whitespace-nowrap">
                            {{ $formattedEndDate }}
                        </div>
                    </div>
                </div>

                <!-- Toggle Dropdown Icon -->
                <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-maroon-800 group-hover:text-white text-slate-500 flex items-center justify-center transition-all shrink-0">
                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <!-- Traveloka Popover Dropdown Panel -->
            <div id="travelokaPopoverPanel" 
                 class="hidden absolute right-0 top-full mt-2 w-[340px] sm:w-[440px] bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-4 sm:p-5 z-50 animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Popover Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center">
                            <i data-lucide="calendar-range" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-extrabold text-slate-800">Pilih Rentang Tanggal</h4>
                            <p class="text-[10px] text-slate-400">Sesuaikan periode data analitik gudang</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeTravelokaPopover()" class="w-6 h-6 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Traveloka Quick Presets -->
                <div class="mb-4">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-2">Pilihan Cepat (Presets)</span>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="setTravelokaPreset(7, 'days')" class="px-2.5 py-1 rounded-lg text-xs font-bold border border-slate-200 hover:border-maroon-400 hover:bg-rose-50 hover:text-maroon-800 text-slate-600 transition-all active:scale-95 {{ $diffDays == 7 ? 'bg-rose-50 border-maroon-400 text-maroon-800 font-extrabold' : '' }}">
                            7 Hari
                        </button>
                        <button type="button" onclick="setTravelokaPreset(14, 'days')" class="px-2.5 py-1 rounded-lg text-xs font-bold border border-slate-200 hover:border-maroon-400 hover:bg-rose-50 hover:text-maroon-800 text-slate-600 transition-all active:scale-95 {{ $diffDays == 14 ? 'bg-rose-50 border-maroon-400 text-maroon-800 font-extrabold' : '' }}">
                            14 Hari (Default)
                        </button>
                        <button type="button" onclick="setTravelokaPreset(30, 'days')" class="px-2.5 py-1 rounded-lg text-xs font-bold border border-slate-200 hover:border-maroon-400 hover:bg-rose-50 hover:text-maroon-800 text-slate-600 transition-all active:scale-95 {{ $diffDays == 30 ? 'bg-rose-50 border-maroon-400 text-maroon-800 font-extrabold' : '' }}">
                            30 Hari
                        </button>
                        <button type="button" onclick="setTravelokaPreset(null, 'this_month')" class="px-2.5 py-1 rounded-lg text-xs font-bold border border-slate-200 hover:border-maroon-400 hover:bg-rose-50 hover:text-maroon-800 text-slate-600 transition-all active:scale-95">
                            Bulan Ini
                        </button>
                        <button type="button" onclick="setTravelokaPreset(null, 'last_month')" class="px-2.5 py-1 rounded-lg text-xs font-bold border border-slate-200 hover:border-maroon-400 hover:bg-rose-50 hover:text-maroon-800 text-slate-600 transition-all active:scale-95">
                            Bulan Lalu
                        </button>
                    </div>
                </div>

                <!-- Custom Date Inputs Form -->
                <form id="travelokaDateForm" action="{{ route('warehouse.index') }}" method="GET" class="space-y-3.5">
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                                Tanggal Mulai
                            </label>
                            <input type="date" 
                                   id="travelokaStartDate" 
                                   name="start_date" 
                                   value="{{ $startDate }}"
                                   class="w-full px-3 py-2 rounded-xl text-xs font-bold border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-maroon-800 focus:ring-2 focus:ring-rose-200 transition-all outline-hidden">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                                Tanggal Selesai
                            </label>
                            <input type="date" 
                                   id="travelokaEndDate" 
                                   name="end_date" 
                                   value="{{ $endDate }}"
                                   class="w-full px-3 py-2 rounded-xl text-xs font-bold border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-maroon-800 focus:ring-2 focus:ring-rose-200 transition-all outline-hidden">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        @if($startDate !== $defaultStartDate || $endDate !== $defaultEndDate)
                            <a href="{{ route('warehouse.index') }}" class="text-xs font-bold text-slate-500 hover:text-rose-700 transition-colors">
                                Reset Default
                            </a>
                        @else
                            <span class="text-[10px] text-slate-400 font-medium">Periode aktif: {{ $diffDays }} hari</span>
                        @endif

                        <button type="submit" 
                                class="px-4 py-2 rounded-xl bg-gradient-to-r from-maroon-800 to-rose-700 hover:from-maroon-900 hover:to-rose-800 text-white font-bold text-xs shadow-md shadow-maroon-900/20 hover:shadow-lg transition-all flex items-center gap-1.5 active:scale-95">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            <span>Terapkan Rentang</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- 3 KARTU UTAMA GUDANG (Sesuai Gambar Mockup 1: Telur, Pakan, Obat) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">

        <!-- 1. GUDANG TELUR -->
        <a href="{{ route('warehouse.telur') }}" class="farm-card farm-card-interactive p-3.5 sm:p-5 block group relative overflow-hidden">
            <!-- Accent stripe on top -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-rose-400 to-maroon-700"></div>

            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <!-- Icon Telur -->
                    <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 border border-amber-200/80 flex items-center justify-center text-amber-600 shadow-inner group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 fill-amber-500 drop-shadow-sm" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C8.13 2 5 6.48 5 12c0 4.42 3.13 8 7 8s7-3.58 7-8c0-5.52-3.13-10-7-10z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 group-hover:text-maroon-800 transition-colors">Gudang Telur</h2>
                        <span class="text-xs text-slate-400 font-medium">Stok Telur Utuh & Peti</span>
                    </div>
                </div>

                <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-maroon-50 text-slate-400 group-hover:text-maroon-700 flex items-center justify-center transition-colors">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Angka Masuk, Keluar, dan Stok Saat Ini (Grid 3 Kolom Responsif Sempurna) -->
            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-1 sm:gap-2 items-start">
                <!-- 1. Masuk (Peti & Kg) -->
                <div class="space-y-0.5 min-w-0">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masuk</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">
                        <div>{{ number_format($telurMasuk, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span></div>
                        <div class="text-[11px] sm:text-xs text-slate-600 font-semibold">& {{ number_format($telurMasukKg, 0, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                        Produksi Kandang
                    </div>
                </div>

                <!-- 2. Keluar (Border Kiri & Kanan Pemisah) -->
                <div class="space-y-0.5 min-w-0 border-x border-slate-200 px-1.5 sm:px-2">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keluar</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">
                        <div>{{ number_format($telurKeluar, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span></div>
                        <div class="text-[11px] sm:text-xs text-slate-600 font-semibold">& {{ number_format($telurKgSold, 0, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate" title="{{ number_format($telurPetiSold, 0, ',', '.') }} Peti • {{ number_format($telurKgSold, 0, ',', '.') }} Kg Terjual">
                        {{ number_format($telurPetiSold, 0, ',', '.') }} Peti Terjual
                    </div>
                </div>

                <!-- 3. Stok Saat Ini (Peti & Kg - Mendukung Nilai Mines / Defisit) -->
                <div class="text-right space-y-0.5 min-w-0">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Stok Saat Ini</div>
                    <div class="text-xs sm:text-sm font-black leading-tight {{ $telurStok < 0 || $telurStokKgTotal < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        <div>{{ number_format($telurStok, 0, ',', '.') }} <span class="text-[10px] font-bold {{ $telurStok < 0 ? 'text-rose-600' : 'text-emerald-700' }}">Peti</span></div>
                        <div class="text-[11px] sm:text-xs font-bold {{ $telurStokKgTotal < 0 ? 'text-rose-600' : 'text-emerald-600' }}">& {{ number_format($telurStokKgTotal, 0, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-semibold truncate {{ $telurStok < 0 || $telurStokKgTotal < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ $telurStok < 0 || $telurStokKgTotal < 0 ? 'Defisit Stok' : 'Tersedia' }}
                    </div>
                </div>
            </div>
        </a>

        <!-- 2. GUDANG PAKAN -->
        <a href="{{ route('warehouse.pakan') }}" class="farm-card farm-card-interactive p-3.5 sm:p-5 block group relative overflow-hidden">
            <!-- Accent stripe on top -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-400 via-teal-400 to-maroon-700"></div>

            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <!-- Icon Karung Pakan -->
                    <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-gradient-to-br from-emerald-100 to-green-50 border border-emerald-200/80 flex items-center justify-center text-emerald-600 shadow-inner group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 fill-emerald-600 drop-shadow-sm" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 6h-2.28a4.99 4.99 0 0 0-9.44 0H5a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3zm-7-2c1.3 0 2.4.84 2.82 2h-5.64A3.003 3.003 0 0 1 12 4zm0 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 group-hover:text-maroon-800 transition-colors">Gudang Pakan</h2>
                        <span class="text-xs text-slate-400 font-medium">Pakan Starter, Grower, Layer</span>
                    </div>
                </div>

                <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-maroon-50 text-slate-400 group-hover:text-maroon-700 flex items-center justify-center transition-colors">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Angka Masuk, Keluar, dan Stok Saat Ini (Grid 3 Kolom Responsif Sempurna) -->
            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-1 sm:gap-2 items-start">
                <!-- 1. Masuk -->
                <div class="space-y-0.5 min-w-0">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masuk</div>
                    <div class="text-xs sm:text-base font-bold text-slate-800 leading-tight">
                        {{ number_format($pakanMasuk, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-normal text-slate-500">Kg</span>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                        ({{ number_format($pakanMasukKarung, 0, ',', '.') }} Krg)
                    </div>
                </div>

                <!-- 2. Keluar (Border Kiri & Kanan Pemisah) -->
                <div class="space-y-0.5 min-w-0 border-x border-slate-200 px-1.5 sm:px-2">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keluar</div>
                    <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">
                        <div>{{ number_format($pakanKarungSold > 0 ? $pakanKarungSold : $pakanTotalKarungKeluar, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Krg</span></div>
                        <div class="text-[11px] sm:text-xs text-slate-600 font-semibold">& {{ number_format($pakanKeluar, 0, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate" title="@if($pakanKarungSold > 0){{ number_format($pakanKarungSold, 0, ',', '.') }} Krg Terjual • {{ number_format($pakanConsumptionKg, 0, ',', '.') }} Kg Kandang @else {{ number_format($pakanTotalKarungKeluar, 0, ',', '.') }} Karung Keluar @endif">
                        @if($pakanKarungSold > 0)
                            {{ number_format($pakanKarungSold, 0, ',', '.') }} Krg Terjual
                        @else
                            {{ number_format($pakanTotalKarungKeluar, 0, ',', '.') }} Karung Keluar
                        @endif
                    </div>
                </div>

                <!-- 3. Stok Saat Ini (Mendukung Nilai Mines / Defisit) -->
                <div class="text-right space-y-0.5 min-w-0">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Stok Saat Ini</div>
                    <div class="text-xs sm:text-lg font-black leading-tight {{ $pakanStok < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ number_format($pakanStok, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-bold {{ $pakanStok < 0 ? 'text-rose-600' : 'text-emerald-700' }}">Kg</span>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-semibold truncate {{ $pakanStokKarung < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        ({{ number_format($pakanStokKarung, 0, ',', '.') }} Krg)
                    </div>
                </div>
            </div>
        </a>

        <!-- 3. GUDANG OBAT, VAKSIN & VITAMIN -->
        <a href="{{ route('warehouse.obat') }}" class="farm-card farm-card-interactive p-4 sm:p-6 block group relative overflow-hidden">
            <!-- Accent stripe on top -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-purple-400 via-rose-400 to-maroon-700"></div>

            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3.5">
                    <!-- Icon Botol Obat/Vaksin -->
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-purple-100 to-rose-50 border border-purple-200/80 flex items-center justify-center text-purple-600 shadow-inner group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-purple-600 drop-shadow-sm" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 3h12v2H6V3zm2 4h8v3h-8V7zm0 5h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8zm4 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 group-hover:text-maroon-800 transition-colors">Gudang Obat</h2>
                        <span class="text-xs text-slate-400 font-medium">Vaksin, Vitamin & Suplemen</span>
                    </div>
                </div>

                <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-maroon-50 text-slate-400 group-hover:text-maroon-700 flex items-center justify-center transition-colors">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </div>
            </div>

            <!-- Angka Masuk, Keluar, dan Stok Saat Ini (Grid 3 Kolom Responsif Sempurna) -->
            <div class="mt-5 pt-3.5 border-t border-slate-100 grid grid-cols-3 gap-1 sm:gap-2 items-center">
                <!-- 1. Masuk -->
                <div class="space-y-0.5 min-w-0 pr-1">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masuk</div>
                    <div class="text-xs sm:text-base font-bold text-slate-800 leading-tight truncate">
                        {{ number_format($obatMasuk, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-normal text-slate-500">Item</span>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                        Vaksin & Obat
                    </div>
                </div>

                <!-- 2. Keluar (Border Kiri & Kanan Pemisah) -->
                <div class="space-y-0.5 min-w-0 border-x border-slate-200/80 px-1.5 sm:px-2">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keluar</div>
                    <div class="text-xs sm:text-base font-bold text-slate-800 leading-tight truncate">
                        {{ number_format($obatKeluar, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-normal text-slate-500">Item</span>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                        Kandang
                    </div>
                </div>

                <!-- 3. Stok Saat Ini (Mendukung Nilai Mines / Defisit) -->
                <div class="text-right space-y-0.5 min-w-0 pl-1">
                    <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Stok Saat Ini</div>
                    <div class="text-xs sm:text-xl font-extrabold leading-tight truncate {{ $obatStok < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ number_format($obatStok, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-bold {{ $obatStok < 0 ? 'text-rose-600' : 'text-emerald-700' }}">Item</span>
                    </div>
                    <div class="text-[9px] sm:text-[10px] font-semibold truncate {{ $obatStok < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ $obatStok < 0 ? 'Defisit' : 'Tersedia' }}
                    </div>
                </div>
            </div>
        </a>

    </div>

    <!-- ========================================================================= -->
    <!-- GRAFIK ALIRAN BARANG GUDANG: MASUK, DIGUNAKAN, KELUAR & TERJUAL -->
    <!-- ========================================================================= -->
    <div class="farm-card p-5 sm:p-6 border border-slate-200/80 shadow-sm relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-maroon-800 text-white flex items-center justify-center shadow-md shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base tracking-tight" id="chartMainTitle">
                        GRAFIK TREN ALIRAN BARANG GUDANG
                    </h3>
                    <p class="text-[11px] text-slate-400" id="chartSubtitle">
                        Visualisasi pergerakan {{ $diffDays }} hari ({{ $formattedStartDate }} – {{ $formattedEndDate }}): Masuk, Digunakan, Keluar & Terjual
                    </p>
                </div>
            </div>

            <!-- Metric Filter Pills -->
            <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100/80 rounded-xl border border-slate-200/60 self-start sm:self-auto">
                <button type="button" onclick="switchChartCommodity('overview')" id="btn-chart-overview" class="chart-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-maroon-800 shadow-xs">
                    Semua Barang
                </button>
                <button type="button" onclick="switchChartCommodity('telur')" id="btn-chart-telur" class="chart-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800">
                    Telur (Peti)
                </button>
                <button type="button" onclick="switchChartCommodity('pakan')" id="btn-chart-pakan" class="chart-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800">
                    Pakan (Kg)
                </button>
                <button type="button" onclick="switchChartCommodity('obat')" id="btn-chart-obat" class="chart-tab-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800">
                    Obat (Item)
                </button>
            </div>
        </div>

        <!-- 4 Quick Stat Summary Cards (Masuk, Digunakan, Keluar, Terjual) -->
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- 1. Masuk -->
            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 flex flex-col justify-between min-h-[110px]">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-700 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span> Masuk
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <i data-lucide="arrow-down-left" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statMasukContent" class="mt-1.5"></div>
                <span id="statMasukSub" class="text-[10px] text-emerald-600/80 font-medium mt-1">Panen & Beli</span>
            </div>

            <!-- 2. Digunakan (Kandang) -->
            <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-200/70 flex flex-col justify-between min-h-[110px]">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase text-indigo-700 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 inline-block"></span> Digunakan
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                        <i data-lucide="utensils" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statDigunakanContent" class="mt-1.5"></div>
                <span id="statDigunakanSub" class="text-[10px] text-indigo-600/80 font-medium mt-1">Konsumsi / Pakai</span>
            </div>

            <!-- 3. Keluar (Gudang) -->
            <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/70 flex flex-col justify-between min-h-[110px]">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase text-rose-700 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-600 inline-block"></span> Keluar
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statKeluarContent" class="mt-1.5"></div>
                <span id="statKeluarSub" class="text-[10px] text-rose-600/80 font-medium mt-1">Total Keluar Gudang</span>
            </div>

            <!-- 4. Terjual -->
            <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/70 flex flex-col justify-between min-h-[110px]">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase text-amber-700 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-600 inline-block"></span> Terjual
                    </span>
                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <i data-lucide="shopping-cart" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statTerjualContent" class="mt-1.5"></div>
                <span id="statTerjualSub" class="text-[10px] text-amber-600/80 font-medium mt-1">Penjualan nochifram</span>
            </div>
        </div>

        <!-- 8 Direct Links to Specific Data Streams -->
        <div class="mt-4 pt-3.5 border-t border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-1 mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-400"></i>
                    Akses Langsung 8 Aliran Data Gudang (Periode {{ $diffDays }} Hari):
                </span>
                <span class="text-[10px] text-slate-400">Klik untuk langsung membuka tab data</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2">
                <!-- 1. Telur Masuk -->
                <a href="{{ route('warehouse.telur', ['tab' => 'masuk']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-emerald-50/60 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-emerald-800">Telur Masuk</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['telur_masuk'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span>
                    </div>
                    <div class="text-[9px] text-emerald-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        Produksi <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 2. Telur Rusak -->
                <a href="{{ route('warehouse.telur', ['tab' => 'keluar']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-rose-50/60 border border-slate-200/80 hover:border-rose-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-rose-800">Telur Rusak</span>
                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['telur_rusak_peti'], 2, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span>
                    </div>
                    <div class="text-[9px] text-rose-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        {{ number_format($streamTotals['telur_rusak_butir'], 0, ',', '.') }} Btr <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 3. Telur Penjualan -->
                <a href="{{ route('warehouse.telur', ['tab' => 'penjualan']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-amber-50/60 border border-slate-200/80 hover:border-amber-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-amber-800">Telur Terjual</span>
                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['telur_terjual'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span>
                    </div>
                    <div class="text-[9px] text-amber-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        Penjualan <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 4. Pakan Masuk -->
                <a href="{{ route('warehouse.pakan', ['tab' => 'masuk']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-sky-50/60 border border-slate-200/80 hover:border-sky-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-sky-800">Pakan Masuk</span>
                        <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['pakan_masuk'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Kg</span>
                    </div>
                    <div class="text-[9px] text-sky-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        Beli/Masuk <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 5. Pemberian Pakan -->
                <a href="{{ route('warehouse.pakan', ['tab' => 'keluar']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-purple-50/60 border border-slate-200/80 hover:border-purple-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-purple-800">Pemberian Pakan</span>
                        <span class="w-2 h-2 rounded-full bg-purple-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['pakan_konsumsi'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Kg</span>
                    </div>
                    <div class="text-[9px] text-purple-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        Kandang <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 6. Pakan Terjual -->
                <a href="{{ route('warehouse.pakan', ['tab' => 'penjualan']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-orange-50/60 border border-slate-200/80 hover:border-orange-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-orange-800">Pakan Terjual</span>
                        <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['pakan_terjual'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Kg</span>
                    </div>
                    <div class="text-[9px] text-orange-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        Penjualan <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 7. Obat Masuk -->
                <a href="{{ route('warehouse.obat', ['tab' => 'masuk']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-teal-50/60 border border-slate-200/80 hover:border-teal-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-teal-800">Obat Masuk</span>
                        <span class="w-2 h-2 rounded-full bg-teal-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['obat_masuk'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Item</span>
                    </div>
                    <div class="text-[9px] text-teal-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        Gudang <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 8. Pemakaian Obat -->
                <a href="{{ route('warehouse.obat', ['tab' => 'keluar']) }}" class="p-2 rounded-xl bg-slate-50/80 hover:bg-pink-50/60 border border-slate-200/80 hover:border-pink-300 hover:shadow-xs transition-all group block">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-pink-800">Pemakaian Obat</span>
                        <span class="w-2 h-2 rounded-full bg-pink-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 mt-1">
                        {{ number_format($streamTotals['obat_konsumsi'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Dosis</span>
                    </div>
                    <div class="text-[9px] text-pink-600 font-semibold group-hover:underline flex items-center gap-0.5 mt-0.5">
                        Kandang <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Line Chart Container -->
        <div class="mt-5 relative w-full" style="height: 340px;">
            <canvas id="warehouseFlowChart"></canvas>
        </div>
    </div>

    <!-- Riwayat Aktivitas & Mutasi Terkini Gudang -->
    <div class="farm-card p-5 sm:p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center">
                    <i data-lucide="history" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm sm:text-base">Mutasi Terkini Gudang</h3>
                    <p class="text-[11px] text-slate-400">Catatan pergerakan barang masuk & keluar</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('warehouse.telur') }}" class="text-xs font-bold text-maroon-700 hover:text-maroon-900 transition-colors">
                    Lihat Semua →
                </a>
            </div>
        </div>

        <!-- List Transaksi Responsif (Tabel di Desktop, Kartu Elegan di Mobile) -->
        <div class="mt-4 divide-y divide-slate-100">
            @forelse($recentTransactions as $item)
                @php
                    $isMasuk = $item->type === 'masuk';
                    $cat = strtolower($item->category);
                    $iconColor = $cat === 'telur' ? 'text-amber-500 bg-amber-50' : ($cat === 'pakan' ? 'text-emerald-600 bg-emerald-50' : 'text-purple-600 bg-purple-50');
                @endphp
                <div class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/60 px-2 rounded-xl transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center shrink-0">
                            @if($cat === 'telur')
                                <i data-lucide="egg" class="w-5 h-5"></i>
                            @elseif($cat === 'pakan')
                                <i data-lucide="wheat" class="w-5 h-5"></i>
                            @else
                                <i data-lucide="flask-conical" class="w-5 h-5"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $item->item_name }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $isMasuk ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                                    {{ $item->type }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5 flex flex-wrap items-center gap-x-2">
                                <span>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}</span>
                                @if($item->created_at)
                                    <span>• {{ $item->created_at->format('H:i') }}</span>
                                @endif
                                @if($item->source)
                                    <span>• {{ $item->source }}</span>
                                @endif
                                <span>• {{ $item->user ? $item->user->name : 'Petugas' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <div class="text-sm sm:text-base font-extrabold {{ $isMasuk ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $isMasuk ? '+' : '-' }}{{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit }}
                        </div>
                        @if($cat === 'telur' && str_contains(strtolower($item->notes ?? ''), 'butir'))
                            <div class="text-[10px] text-slate-400">
                                {{ Str::after($item->notes, '(') ? '(' . Str::after($item->notes, '(') : '' }}
                            </div>
                        @endif
                        
                        <!-- Action Buttons -->
                        <div class="mt-2 flex items-center justify-end gap-2">
                            <a href="{{ route('warehouse.' . $cat) }}" class="px-2 py-1 bg-white border border-slate-200 text-slate-500 rounded-md hover:text-blue-600 hover:border-blue-300 transition-colors text-[10px] font-bold flex items-center gap-1" title="Lihat/Edit di Detail">
                                <i data-lucide="edit" class="w-3 h-3"></i> Edit
                            </a>
                            <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data transaksi ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-white border border-slate-200 text-rose-500 rounded-md hover:bg-rose-50 hover:border-rose-200 transition-colors text-[10px] font-bold flex items-center gap-1" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-sm">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                    Belum ada riwayat mutasi barang di gudang.
                </div>
            @endforelse
        </div>
    </div>

    <!-- TRANSAKSI PENJUALAN BARANG KELUAR (TERHUBUNG 1 DB NOCHIFRAM) -->
    <div class="farm-card p-5 sm:p-6 border-t-4 border-t-maroon-800">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-maroon-800 flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base">Barang Keluar: Penjualan Real-Time</h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">1 DB nochifram</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Data otomatis ditarik langsung dari sistem kasir & penjualan peternakan</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('warehouse.telur', ['tab' => 'penjualan']) }}" class="text-xs font-bold text-maroon-700 hover:text-maroon-900 transition-colors">
                    Semua Penjualan →
                </a>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($recentSales as $sale)
                @php
                    $isTelur = strtolower($sale->category) === 'telur';
                @endphp
                <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/70 hover:border-maroon-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-1 mb-1.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isTelur ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $sale->category }} • {{ $sale->unit }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 font-mono">#{{ $sale->invoice_no }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $sale->item_name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Pembeli: <b class="text-slate-700">{{ $sale->customer_name }}</b>
                        </p>
                    </div>

                    <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-extrabold text-maroon-800 block">-{{ number_format($sale->quantity, 0, ',', '.') }} {{ $sale->unit }}</span>
                            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($sale->date)->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-emerald-600 uppercase">{{ $sale->payment_status }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-6 text-center text-slate-400 text-xs">
                    Belum ada riwayat penjualan tercatat di aplikasi nochifram.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let flowChartInstance = null;
    const warehouseChartLabels = {!! json_encode($chartLabels) !!};
    const warehouseChartDataSets = {!! json_encode($chartDataSets) !!};
    const warehouseChartTotals = {!! json_encode($chartTotals) !!};
    const warehouseStreamTotals = {!! json_encode($streamTotals) !!};

    function updateStatSummaryCards(commodityKey) {
        const cMasuk = document.getElementById('statMasukContent');
        const cDigunakan = document.getElementById('statDigunakanContent');
        const cKeluar = document.getElementById('statKeluarContent');
        const cTerjual = document.getElementById('statTerjualContent');
        const sDigunakan = document.getElementById('statDigunakanSub');
        const sMasuk = document.getElementById('statMasukSub');
        const sKeluar = document.getElementById('statKeluarSub');
        const sTerjual = document.getElementById('statTerjualSub');

        if (!cMasuk) return;

        if (commodityKey === 'overview') {
            if (sMasuk) sMasuk.textContent = 'Panen & Pembelian';
            if (sDigunakan) sDigunakan.textContent = 'Konsumsi & Kerusakan';
            if (sKeluar) sKeluar.textContent = 'Total Pengeluaran';
            if (sTerjual) sTerjual.textContent = 'Penjualan Platform';

            cMasuk.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.telur', ['tab' => 'masuk']) }}" class="flex justify-between items-center text-slate-700 hover:text-emerald-700 transition-colors">
                        <span class="text-slate-500">Telur:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.telur_masuk).toLocaleString('id-ID', {maximumFractionDigits: 1})} Peti</span>
                    </a>
                    <a href="{{ route('warehouse.pakan', ['tab' => 'masuk']) }}" class="flex justify-between items-center text-slate-700 hover:text-emerald-700 transition-colors">
                        <span class="text-slate-500">Pakan:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.pakan_masuk).toLocaleString('id-ID', {maximumFractionDigits: 1})} Kg</span>
                    </a>
                    <a href="{{ route('warehouse.obat', ['tab' => 'masuk']) }}" class="flex justify-between items-center text-slate-700 hover:text-emerald-700 transition-colors">
                        <span class="text-slate-500">Obat:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.obat_masuk).toLocaleString('id-ID', {maximumFractionDigits: 1})} Item</span>
                    </a>
                </div>
            `;

            cDigunakan.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.pakan', ['tab' => 'keluar']) }}" class="flex justify-between items-center text-slate-700 hover:text-indigo-700 transition-colors">
                        <span class="text-slate-500">Pakan:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.pakan_konsumsi).toLocaleString('id-ID', {maximumFractionDigits: 1})} Kg</span>
                    </a>
                    <a href="{{ route('warehouse.telur', ['tab' => 'keluar']) }}" class="flex justify-between items-center text-slate-700 hover:text-indigo-700 transition-colors">
                        <span class="text-slate-500">Telur Rusak:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.telur_rusak_butir).toLocaleString('id-ID')} Btr</span>
                    </a>
                    <a href="{{ route('warehouse.obat', ['tab' => 'keluar']) }}" class="flex justify-between items-center text-slate-700 hover:text-indigo-700 transition-colors">
                        <span class="text-slate-500">Obat Dipakai:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.obat_konsumsi).toLocaleString('id-ID', {maximumFractionDigits: 1})} Dosis</span>
                    </a>
                </div>
            `;

            const totalTelurKeluar = (Number(warehouseStreamTotals.telur_rusak_peti) + Number(warehouseStreamTotals.telur_terjual)).toFixed(1);
            const totalPakanKeluar = (Number(warehouseStreamTotals.pakan_konsumsi) + Number(warehouseStreamTotals.pakan_terjual)).toFixed(1);

            cKeluar.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.telur', ['tab' => 'semua']) }}" class="flex justify-between items-center text-slate-700 hover:text-rose-700 transition-colors">
                        <span class="text-slate-500">Telur Total:</span>
                        <span class="font-extrabold text-slate-800">${Number(totalTelurKeluar).toLocaleString('id-ID')} Peti</span>
                    </a>
                    <a href="{{ route('warehouse.pakan', ['tab' => 'semua']) }}" class="flex justify-between items-center text-slate-700 hover:text-rose-700 transition-colors">
                        <span class="text-slate-500">Pakan Total:</span>
                        <span class="font-extrabold text-slate-800">${Number(totalPakanKeluar).toLocaleString('id-ID')} Kg</span>
                    </a>
                    <a href="{{ route('warehouse.obat', ['tab' => 'keluar']) }}" class="flex justify-between items-center text-slate-700 hover:text-rose-700 transition-colors">
                        <span class="text-slate-500">Obat Pakai:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.obat_konsumsi).toLocaleString('id-ID', {maximumFractionDigits: 1})} Dosis</span>
                    </a>
                </div>
            `;

            cTerjual.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.telur', ['tab' => 'penjualan']) }}" class="flex justify-between items-center text-slate-700 hover:text-amber-700 transition-colors">
                        <span class="text-slate-500">Telur:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.telur_terjual).toLocaleString('id-ID', {maximumFractionDigits: 1})} Peti</span>
                    </a>
                    <a href="{{ route('warehouse.pakan', ['tab' => 'penjualan']) }}" class="flex justify-between items-center text-slate-700 hover:text-amber-700 transition-colors">
                        <span class="text-slate-500">Pakan:</span>
                        <span class="font-extrabold text-slate-800">${Number(warehouseStreamTotals.pakan_terjual).toLocaleString('id-ID', {maximumFractionDigits: 1})} Kg</span>
                    </a>
                    <div class="flex justify-between items-center text-[10px] text-amber-700/70 pt-0.5 border-t border-amber-200/50">
                        <span>Platform:</span>
                        <span class="font-semibold">nochifram</span>
                    </div>
                </div>
            `;
        } else {
            const dataTotals = warehouseChartTotals[commodityKey] || {};

            if (sMasuk) sMasuk.textContent = commodityKey === 'telur' ? 'Panen Kandang' : (commodityKey === 'pakan' ? 'Pembelian / Masuk' : 'Obat Masuk');
            if (sDigunakan) sDigunakan.textContent = commodityKey === 'telur' ? 'Telur Rusak / Pecah' : (commodityKey === 'pakan' ? 'Pemberian Pakan Kandang' : 'Pemakaian Obat Kandang');
            if (sKeluar) sKeluar.textContent = 'Total Keluar Gudang';
            if (sTerjual) sTerjual.textContent = 'Penjualan nochifram';

            cMasuk.innerHTML = `
                <div class="text-base sm:text-xl font-black text-emerald-900 mt-1">
                    ${dataTotals.masuk || '0'}
                </div>
            `;

            cDigunakan.innerHTML = `
                <div class="text-base sm:text-xl font-black text-indigo-900 mt-1">
                    ${dataTotals.digunakan || '0'}
                </div>
            `;

            cKeluar.innerHTML = `
                <div class="text-base sm:text-xl font-black text-rose-900 mt-1">
                    ${dataTotals.keluar || '0'}
                </div>
            `;

            cTerjual.innerHTML = `
                <div class="text-base sm:text-xl font-black text-amber-900 mt-1">
                    ${dataTotals.terjual || '0'}
                </div>
            `;
        }
    }

    function renderWarehouseFlowChart(commodityKey) {
        const ctx = document.getElementById('warehouseFlowChart');
        if (!ctx) return;

        const ds = warehouseChartDataSets[commodityKey] || warehouseChartDataSets.overview;
        const isOverview = ds.is_overview === true;
        
        // Update Title
        const titleElem = document.getElementById('chartMainTitle');
        if (titleElem) titleElem.textContent = ds.title;

        // Update Stat Cards
        updateStatSummaryCards(commodityKey);

        if (flowChartInstance) {
            flowChartInstance.destroy();
        }

        const chartDatasets = (ds.datasets || []).map(item => ({
            label: item.label,
            data: item.data,
            borderColor: item.borderColor,
            backgroundColor: item.backgroundColor,
            borderDash: item.borderDash || [],
            borderWidth: 2.2,
            fill: false,
            tension: 0.32,
            pointBackgroundColor: item.borderColor,
            pointBorderColor: '#ffffff',
            pointBorderWidth: 1.5,
            pointRadius: 3.5,
            pointHoverRadius: 6,
            yAxisID: item.yAxisID || 'y',
            unit: item.unit || ds.unit || '',
            tab_url: item.tab_url || null,
        }));

        flowChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: warehouseChartLabels,
                datasets: chartDatasets
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
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            usePointStyle: true,
                            font: {
                                size: 11,
                                weight: 'bold',
                                family: "'Inter', sans-serif"
                            },
                            color: '#334155',
                            padding: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                const itemUnit = context.dataset.unit || '';
                                return ' ' + context.dataset.label + ': ' + Number(context.parsed.y).toLocaleString('id-ID') + ' ' + itemUnit;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#64748b'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: 'rgba(226, 232, 240, 0.6)' },
                        title: {
                            display: true,
                            text: isOverview ? 'Peti / Butir / Item / Dosis' : (ds.unit || ''),
                            font: { size: 10, weight: 'bold' },
                            color: '#64748b'
                        },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#64748b'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: isOverview,
                        position: 'right',
                        beginAtZero: true,
                        grid: { drawOnChartArea: false },
                        title: {
                            display: isOverview,
                            text: 'Pakan (Kg)',
                            font: { size: 10, weight: 'bold' },
                            color: '#0284c7'
                        },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#0284c7'
                        }
                    }
                }
            }
        });
    }

    function switchChartCommodity(key) {
        document.querySelectorAll('.chart-tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-maroon-800', 'shadow-xs');
            btn.classList.add('text-slate-500');
        });
        const activeBtn = document.getElementById('btn-chart-' + key);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-500');
            activeBtn.classList.add('bg-white', 'text-maroon-800', 'shadow-xs');
        }
        renderWarehouseFlowChart(key);
    }

    // Traveloka Popover Interaction
    function toggleTravelokaPopover() {
        const panel = document.getElementById('travelokaPopoverPanel');
        if (!panel) return;
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            lucide.createIcons();
        }
    }

    function closeTravelokaPopover() {
        const panel = document.getElementById('travelokaPopoverPanel');
        if (panel) panel.classList.add('hidden');
    }

    function setTravelokaPreset(days, type) {
        const now = new Date();
        let start, end;
        if (type === 'days') {
            end = new Date();
            start = new Date();
            start.setDate(end.getDate() - (days - 1));
        } else if (type === 'this_month') {
            start = new Date(now.getFullYear(), now.getMonth(), 1);
            end = new Date();
        } else if (type === 'last_month') {
            start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            end = new Date(now.getFullYear(), now.getMonth(), 0);
        }
        const fmt = d => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        const sInput = document.getElementById('travelokaStartDate');
        const eInput = document.getElementById('travelokaEndDate');
        if (sInput && eInput) {
            sInput.value = fmt(start);
            eInput.value = fmt(end);
            document.getElementById('travelokaDateForm').submit();
        }
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('travelokaDatePicker');
        if (container && !container.contains(e.target)) {
            closeTravelokaPopover();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTravelokaPopover();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        renderWarehouseFlowChart('overview');
    });
</script>
@endpush
