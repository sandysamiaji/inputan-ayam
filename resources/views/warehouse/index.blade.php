@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- ========================================================================= -->
    <!-- HEADER SECTION & TRAVELOKA STYLE DATE RANGE FILTER -->
    <!-- ========================================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-maroon-800 via-rose-900 to-maroon-900 text-white flex items-center justify-center shadow-md shadow-maroon-900/20 shrink-0">
                    <i data-lucide="warehouse" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                        GUDANG
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-maroon-50 text-maroon-800 border border-maroon-200/80">
                            Logistik & Stok
                        </span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Ringkasan stok barang & analitik aliran logistik peternakan</p>
                </div>
            </div>
        </div>

        <!-- Traveloka Style Date Range Selector -->
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_filter_tanggal'))
        <div class="relative w-full sm:w-auto" id="travelokaDatePicker">
            <!-- Traveloka Search Capsule Trigger -->
            <div onclick="toggleTravelokaPopover()" 
                 id="travelokaTriggerBtn"
                 class="cursor-pointer bg-white hover:bg-slate-50/90 border border-slate-200/90 hover:border-maroon-300 shadow-xs hover:shadow-md transition-all rounded-2xl p-1.5 sm:p-2 flex flex-wrap sm:flex-nowrap items-center justify-between sm:justify-start gap-1.5 sm:gap-2.5 group select-none max-w-full overflow-hidden">
                
                @if($hasDateFilter)
                <!-- Start Date Segment -->
                <div class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-50 group-hover:bg-white transition-colors shrink-0">
                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    </div>
                    <div>
                        <div class="text-[8.5px] sm:text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Dari Tanggal</div>
                        <div class="text-[11px] sm:text-sm font-black text-slate-800 truncate">
                            {{ $formattedStartDate }}
                        </div>
                    </div>
                </div>

                <!-- Center Duration Badge (Traveloka Style) -->
                <div class="flex items-center gap-1 px-2.5 sm:px-3 py-1 rounded-full bg-gradient-to-r from-rose-50 to-amber-50 text-maroon-900 text-[9px] sm:text-[10px] font-extrabold shrink-0 border border-rose-200/80 shadow-2xs">
                    <span>{{ $diffDays }} Hari</span>
                    <i data-lucide="arrow-right" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-maroon-700"></i>
                </div>

                <!-- End Date Segment -->
                <div class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-50 group-hover:bg-white transition-colors shrink-0">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-check-2" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    </div>
                    <div>
                        <div class="text-[8.5px] sm:text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Sampai Tanggal</div>
                        <div class="text-[11px] sm:text-sm font-black text-slate-800 truncate">
                            {{ $formattedEndDate }}
                        </div>
                    </div>
                </div>
                @else
                <!-- Default Capsule: Semua Data (Seluruh Waktu) -->
                <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-50 group-hover:bg-rose-50/40 transition-colors">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-100/70 text-maroon-800 flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="calendar-range" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[8.5px] sm:text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Periode Data</div>
                        <div class="text-xs sm:text-sm font-black text-slate-800 flex items-center gap-1.5">
                            <span>Semua Data</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Seluruh Waktu
                            </span>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">
                    <span>Filter Tanggal</span>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400"></i>
                </div>
                @endif

                <!-- Toggle Dropdown Icon Button -->
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-100 group-hover:bg-maroon-800 group-hover:text-white text-slate-500 flex items-center justify-center transition-all shrink-0">
                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                </div>
            </div>

            <!-- Traveloka Popover Dropdown Panel -->
            <div id="travelokaPopoverPanel" 
                 class="hidden absolute right-0 top-full mt-2 w-[calc(100vw-2rem)] max-w-[440px] sm:w-[440px] bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-4 sm:p-5 z-50 animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Popover Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-maroon-800 flex items-center justify-center shadow-xs">
                            <i data-lucide="calendar-range" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-extrabold text-slate-800">Pilih Rentang Tanggal</h4>
                            <p class="text-[10px] text-slate-400">Sesuaikan periode data analitik & mutasi gudang</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeTravelokaPopover()" class="w-7 h-7 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Preset Quick Buttons (7 Hari, 14 Hari, 30 Hari, Semua Data) -->
                <div class="mb-3.5">
                    <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">Pilihan Cepat:</div>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" onclick="applyPresetDates(7)" class="px-2 py-1.5 rounded-xl text-[11px] font-bold bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-maroon-800 border border-slate-200/70 hover:border-maroon-300 transition-all text-center">
                            7 Hari
                        </button>
                        <button type="button" onclick="applyPresetDates(14)" class="px-2 py-1.5 rounded-xl text-[11px] font-bold bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-maroon-800 border border-slate-200/70 hover:border-maroon-300 transition-all text-center">
                            14 Hari
                        </button>
                        <button type="button" onclick="applyPresetDates(30)" class="px-2 py-1.5 rounded-xl text-[11px] font-bold bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-maroon-800 border border-slate-200/70 hover:border-maroon-300 transition-all text-center">
                            30 Hari
                        </button>
                        <a href="{{ route('warehouse.index') }}" class="px-2 py-1.5 rounded-xl text-[11px] font-bold {{ !$hasDateFilter ? 'bg-maroon-800 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600' }} border border-slate-200/70 transition-all text-center flex items-center justify-center">
                            Semua
                        </a>
                    </div>
                </div>

                <!-- Custom Date Inputs Form (Auto-submit saat tanggal dipilih) -->
                <form id="travelokaDateForm" action="{{ route('warehouse.index') }}" method="GET" class="space-y-3.5">
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                                Tanggal Mulai
                            </label>
                            <input type="date" 
                                   id="travelokaStartDate" 
                                   name="start_date" 
                                   value="{{ $startDate ?? '' }}"
                                   onchange="checkAndAutoSubmitDateRange()"
                                   class="w-full px-3 py-2 rounded-xl text-xs font-bold border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-maroon-800 focus:ring-2 focus:ring-rose-200 transition-all outline-hidden cursor-pointer">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                                Tanggal Selesai
                            </label>
                            <input type="date" 
                                   id="travelokaEndDate" 
                                   name="end_date" 
                                   value="{{ $endDate ?? '' }}"
                                   onchange="checkAndAutoSubmitDateRange()"
                                   class="w-full px-3 py-2 rounded-xl text-xs font-bold border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-maroon-800 focus:ring-2 focus:ring-rose-200 transition-all outline-hidden cursor-pointer">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2.5 border-t border-slate-100">
                        @if($hasDateFilter)
                            <a href="{{ route('warehouse.index') }}" class="text-xs font-bold text-slate-500 hover:text-rose-700 flex items-center gap-1 transition-colors">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Reset (Semua Data)</span>
                            </a>
                        @else
                            <span class="text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Menampilkan Semua Data
                            </span>
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
        @else
        <div class="relative">
            <div class="cursor-not-allowed bg-slate-50 border border-slate-200 rounded-2xl p-1.5 sm:p-2 flex items-center gap-1.5 sm:gap-2.5 select-none opacity-80">
                @if($hasDateFilter)
                <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Dari Tanggal</div>
                        <div class="text-xs sm:text-sm font-black text-slate-600 whitespace-nowrap">
                            {{ $formattedStartDate }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-extrabold shrink-0 border border-slate-200">
                    <span>{{ $diffDays }} Hari</span>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-slate-400"></i>
                </div>

                <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-white">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-check-2" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Sampai Tanggal</div>
                        <div class="text-xs sm:text-sm font-black text-slate-600 whitespace-nowrap">
                            {{ $formattedEndDate }}
                        </div>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-range" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Periode Data</div>
                        <div class="text-xs sm:text-sm font-black text-slate-700">Semua Data (Seluruh Waktu)</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- 4 KARTU UTAMA GUDANG (Telur, Pakan, Obat, Karantina) -->
    <!-- ========================================================================= -->
    @if(!auth()->check() || auth()->user()->canAccess('warehouse_card_summary'))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 items-stretch">

        <!-- 1. GUDANG TELUR -->
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_telur'))
        <a href="{{ route('warehouse.telur', array_filter(['start_date' => $startDate, 'end_date' => $endDate])) }}" class="farm-card farm-card-interactive p-4 sm:p-5 flex flex-col justify-between group relative overflow-hidden bg-white border border-slate-200/80 hover:border-amber-400 hover:shadow-lg transition-all duration-300 rounded-2xl h-full">
        @else
        <div class="farm-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden opacity-90 cursor-not-allowed bg-slate-50 border border-slate-200/80 rounded-2xl h-full">
        @endif
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-orange-400 to-rose-500"></div>

            <div>
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 border border-amber-200/90 flex items-center justify-center text-amber-600 shadow-inner group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-6 h-6 fill-amber-500 drop-shadow-2xs" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13 2 5 6.48 5 12c0 4.42 3.13 8 7 8s7-3.58 7-8c0-5.52-3.13-10-7-10z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-800 transition-colors @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_telur')) group-hover:text-amber-800 @endif">Gudang Telur</h2>
                            <span class="text-xs text-slate-400 font-medium">Stok Telur Utuh & Peti</span>
                        </div>
                    </div>

                    @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_telur'))
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 group-hover:bg-amber-100/70 text-slate-400 group-hover:text-amber-800 flex items-center justify-center transition-all group-hover:translate-x-0.5 shrink-0">
                        <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    @else
                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center shrink-0">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    </div>
                    @endif
                </div>

                <!-- 3 Kolom Metrik: Masuk, Keluar, Sisa Stok -->
                <div class="mt-4 pt-3.5 border-t border-slate-100 grid grid-cols-3 gap-1.5 sm:gap-2 items-start text-left">
                    <!-- 1. Masuk -->
                    <div class="space-y-0.5 min-w-0 pr-1">
                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masuk</div>
                        <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">
                            <div>{{ number_format((int) $telurMasuk, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span></div>
                            @if($telurMasukKg > 0)
                                <div class="text-[11px] sm:text-xs text-slate-600 font-semibold">& {{ $telurMasukKg == floor($telurMasukKg) ? number_format($telurMasukKg, 0, ',', '.') : number_format($telurMasukKg, 1, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                            @endif
                        </div>
                        <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                            Produksi Kandang
                        </div>
                    </div>

                    <!-- 2. Keluar -->
                    <div class="space-y-0.5 min-w-0 border-x border-slate-200/80 px-1.5 sm:px-2">
                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keluar</div>
                        <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight">
                            <div>{{ number_format((int) $telurKeluar, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span></div>
                            @if($telurKeluarKg > 0)
                                <div class="text-[11px] sm:text-xs text-slate-600 font-semibold">& {{ $telurKeluarKg == floor($telurKeluarKg) ? number_format($telurKeluarKg, 0, ',', '.') : number_format($telurKeluarKg, 1, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                            @endif
                        </div>
                        <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                            Jual & Rusak
                        </div>
                    </div>

                    <!-- 3. Sisa Stok -->
                    <div class="text-right space-y-0.5 min-w-0 pl-1">
                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sisa Stok</div>
                        <div class="text-xs sm:text-base font-black leading-tight {{ $telurStok < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                            <div>{{ number_format((int) $telurStok, 0, ',', '.') }} <span class="text-[10px] font-bold text-slate-600">Peti</span></div>
                            @if($telurStokKgTotal != 0)
                                <div class="text-[11px] sm:text-xs font-bold {{ $telurStokKgTotal < 0 ? 'text-rose-600' : 'text-emerald-700' }}">& {{ $telurStokKgTotal == floor($telurStokKgTotal) ? number_format($telurStokKgTotal, 0, ',', '.') : number_format($telurStokKgTotal, 1, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                            @endif
                        </div>
                        <div class="text-[9px] sm:text-[10px] font-bold {{ $telurStok < 0 ? 'text-rose-600' : 'text-emerald-600' }} truncate">
                            {{ $telurStok < 0 ? 'Defisit' : 'Tersedia' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rincian Keluar: Terjual vs Rusak -->
            <div class="mt-3 pt-2.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                <div class="p-2 sm:p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/80 flex flex-col justify-between">
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-amber-800 uppercase tracking-wider flex items-center gap-1">
                        <i data-lucide="shopping-cart" class="w-3 h-3 text-amber-600 shrink-0"></i> Terjual
                    </span>
                    <div class="text-xs sm:text-sm font-black text-amber-900 mt-1 leading-tight">
                        {{ number_format((int) $telurPetiSold, 0, ',', '.') }} <span class="text-[10px] font-semibold text-amber-700">Peti</span>
                        @if($telurKgSold > 0)
                            <div class="text-[10px] text-amber-800 font-semibold mt-0.5">& {{ $telurKgSold == floor($telurKgSold) ? number_format($telurKgSold, 0, ',', '.') : number_format($telurKgSold, 1, ',', '.') }} kg</div>
                        @endif
                    </div>
                </div>

                <div class="p-2 sm:p-2.5 rounded-xl bg-rose-50/70 border border-rose-200/80 flex flex-col justify-between">
                    <span class="text-[9px] sm:text-[10px] font-extrabold text-rose-800 uppercase tracking-wider flex items-center gap-1">
                        <i data-lucide="alert-triangle" class="w-3 h-3 text-rose-600 shrink-0"></i> Rusak / Pecah
                    </span>
                    <div class="text-xs sm:text-sm font-black text-rose-900 mt-1 leading-tight">
                        {{ number_format((int) $telurRusakPeti, 0, ',', '.') }} <span class="text-[10px] font-semibold text-rose-700">Peti</span>
                        @if($telurRusakKg > 0)
                            <span class="text-[11px] text-rose-800 font-bold">& {{ $telurRusakKg == floor($telurRusakKg) ? number_format($telurRusakKg, 0, ',', '.') : number_format($telurRusakKg, 1, ',', '.') }} Kg</span>
                        @endif
                        <div class="text-[10px] text-rose-700 font-semibold mt-0.5">({{ number_format($telurRusakButir, 0, ',', '.') }} Btr)</div>
                    </div>
                </div>
            </div>
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_telur'))
        </a>
        @else
        </div>
        @endif

        <!-- 2. GUDANG PAKAN -->
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_pakan'))
        <a href="{{ route('warehouse.pakan', array_filter(['start_date' => $startDate, 'end_date' => $endDate])) }}" class="farm-card farm-card-interactive p-4 sm:p-5 flex flex-col justify-between group relative overflow-hidden bg-white border border-slate-200/80 hover:border-emerald-400 hover:shadow-lg transition-all duration-300 rounded-2xl h-full">
        @else
        <div class="farm-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden opacity-90 cursor-not-allowed bg-slate-50 border border-slate-200/80 rounded-2xl h-full">
        @endif
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-400 via-teal-400 to-green-600"></div>

            <div>
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-green-50 border border-emerald-200/90 flex items-center justify-center text-emerald-600 shadow-inner group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-6 h-6 fill-emerald-600 drop-shadow-2xs" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 6h-2.28a4.99 4.99 0 0 0-9.44 0H5a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3zm-7-2c1.3 0 2.4.84 2.82 2h-5.64A3.003 3.003 0 0 1 12 4zm0 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-800 transition-colors @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_pakan')) group-hover:text-emerald-800 @endif">Gudang Pakan</h2>
                            <span class="text-xs text-slate-400 font-medium">Pakan Starter, Grower, Layer</span>
                        </div>
                    </div>

                    @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_pakan'))
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 group-hover:bg-emerald-100/70 text-slate-400 group-hover:text-emerald-800 flex items-center justify-center transition-all group-hover:translate-x-0.5 shrink-0">
                        <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    @else
                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center shrink-0">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    </div>
                    @endif
                </div>

                @php
                    $stokLayer = $feedSummary['current_stock_kg_layer'] ?? 0;
                    $stokLayerKrg = $feedSummary['current_stock_karung_layer'] ?? 0;
                    $stokGrower = $feedSummary['current_stock_kg_grower'] ?? 0;
                    $stokGrowerKrg = $feedSummary['current_stock_karung_grower'] ?? 0;
                @endphp

                <!-- Grid 2 Jenis Pakan -->
                <div class="mt-4 pt-3.5 border-t border-slate-100 grid grid-cols-2 gap-2 text-xs">
                    <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80 flex flex-col justify-between">
                        <span class="text-[9.5px] font-extrabold text-emerald-800 uppercase tracking-wider block">🌾 Stok Layer</span>
                        <div class="text-xs sm:text-sm font-black leading-tight mt-1 {{ $stokLayer < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                            {{ number_format($stokLayer, 0, ',', '.') }} kg
                        </div>
                        <span class="text-[10px] font-bold text-emerald-800/80 mt-0.5 truncate">({{ \App\Models\Setting::formatKarungKg($stokLayer) }})</span>
                    </div>

                    <div class="p-2.5 rounded-xl bg-sky-50/70 border border-sky-200/80 flex flex-col justify-between">
                        <span class="text-[9.5px] font-extrabold text-sky-800 uppercase tracking-wider block">🌾 Stok Grower</span>
                        <div class="text-xs sm:text-sm font-black leading-tight mt-1 {{ $stokGrower < 0 ? 'text-rose-600' : 'text-sky-700' }}">
                            {{ number_format($stokGrower, 0, ',', '.') }} kg
                        </div>
                        <span class="text-[10px] font-bold text-sky-800/80 mt-0.5 truncate">({{ \App\Models\Setting::formatKarungKg($stokGrower) }})</span>
                    </div>
                </div>
            </div>

            <!-- Total Sisa Stok Gabungan -->
            <div class="mt-3 p-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                <span class="text-[10px] sm:text-[10.5px] font-extrabold text-slate-500 uppercase tracking-wider">Sisa Stok Total</span>
                <span class="text-xs sm:text-sm font-black {{ $pakanStok < 0 ? 'text-rose-600' : 'text-slate-800' }}">
                    {{ number_format($pakanStok, 0, ',', '.') }} Kg <span class="text-[10px] font-bold text-emerald-700">({{ \App\Models\Setting::formatKarungKg($pakanStok) }})</span>
                </span>
            </div>
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_pakan'))
        </a>
        @else
        </div>
        @endif

        <!-- 3. GUDANG OBAT, VAKSIN & VITAMIN -->
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_obat'))
        <a href="{{ route('warehouse.obat', array_filter(['start_date' => $startDate, 'end_date' => $endDate])) }}" class="farm-card farm-card-interactive p-4 sm:p-5 flex flex-col justify-between group relative overflow-hidden bg-white border border-slate-200/80 hover:border-purple-400 hover:shadow-lg transition-all duration-300 rounded-2xl h-full">
        @else
        <div class="farm-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden opacity-90 cursor-not-allowed bg-slate-50 border border-slate-200/80 rounded-2xl h-full">
        @endif
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-purple-400 via-indigo-400 to-rose-500"></div>

            <div>
                <!-- Card Header -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-purple-100 to-rose-50 border border-purple-200/90 flex items-center justify-center text-purple-600 shadow-inner group-hover:scale-105 transition-transform shrink-0">
                            <svg class="w-6 h-6 fill-purple-600 drop-shadow-2xs" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 3h12v2H6V3zm2 4h8v3h-8V7zm0 5h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8zm4 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-800 transition-colors @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_obat')) group-hover:text-purple-800 @endif">Gudang Obat</h2>
                            <span class="text-xs text-slate-400 font-medium">Vaksin, Vitamin & Suplemen</span>
                        </div>
                    </div>

                    @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_obat'))
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 group-hover:bg-purple-100/70 text-slate-400 group-hover:text-purple-800 flex items-center justify-center transition-all group-hover:translate-x-0.5 shrink-0">
                        <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </div>
                    @else
                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center shrink-0">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    </div>
                    @endif
                </div>

                <!-- 3 Kolom Metrik: Masuk, Keluar, Sisa Stok -->
                <div class="mt-4 pt-3.5 border-t border-slate-100 grid grid-cols-3 gap-1.5 sm:gap-2 items-start text-left">
                    <!-- 1. Masuk -->
                    <div class="space-y-0.5 min-w-0 pr-1">
                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masuk</div>
                        <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight truncate">
                            {{ number_format($obatMasuk, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Item</span>
                        </div>
                        <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                            Vaksin & Obat
                        </div>
                    </div>

                    <!-- 2. Keluar -->
                    <div class="space-y-0.5 min-w-0 border-x border-slate-200/80 px-1.5 sm:px-2">
                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keluar</div>
                        <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight truncate">
                            {{ number_format($obatKeluar, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Item</span>
                        </div>
                        <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                            Kandang
                        </div>
                    </div>

                    <!-- 3. Sisa Stok -->
                    <div class="text-right space-y-0.5 min-w-0 pl-1">
                        <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sisa Stok</div>
                        <div class="text-xs sm:text-base font-black leading-tight truncate {{ $obatStok < 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                            {{ number_format($obatStok, 0, ',', '.') }} <span class="text-[10px] font-bold text-slate-600">Item</span>
                        </div>
                        <div class="text-[9px] sm:text-[10px] font-bold truncate {{ $obatStok < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $obatStok < 0 ? 'Defisit' : 'Tersedia' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Sinkron Katalog -->
            <div class="mt-3 p-2.5 px-3 rounded-xl bg-purple-50/70 border border-purple-200/80 flex items-center justify-between text-xs">
                <span class="text-[10px] font-bold text-purple-900 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                    Sinkron Katalog Medis
                </span>
                <span class="text-[10px] font-extrabold text-purple-800 uppercase">Aktif</span>
            </div>
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_obat'))
        </a>
        @else
        </div>
        @endif

        <!-- 4. GUDANG AYAM KARANTINA -->
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_card_quarantine'))
            @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_quarantine'))
            <a href="{{ route('warehouse.karantina', array_filter(['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="farm-card farm-card-interactive p-4 sm:p-5 flex flex-col justify-between group relative overflow-hidden bg-white border border-slate-200/80 hover:border-amber-400 hover:shadow-lg transition-all duration-300 rounded-2xl h-full">
            @else
            <div class="farm-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden opacity-90 cursor-not-allowed bg-slate-50 border border-slate-200/80 rounded-2xl h-full">
            @endif
                <!-- Top Gradient Accent -->
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 via-orange-500 to-rose-600"></div>

                <div>
                    <!-- Card Header -->
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-50 border border-amber-200/90 flex items-center justify-center text-amber-600 shadow-inner group-hover:scale-105 transition-transform shrink-0">
                                <i data-lucide="shield-alert" class="w-6 h-6 stroke-[2]"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-bold text-slate-800 transition-colors @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_quarantine')) group-hover:text-amber-800 @endif">Ayam Karantina</h2>
                                <span class="text-xs text-slate-400 font-medium">Isolasi & Pemulihan Ayam Sakit</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full {{ $karantinaStok > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                {{ $karantinaStok > 0 ? $karantinaStok . ' Ekor' : 'Nihil' }}
                            </span>
                            @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_quarantine'))
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 group-hover:bg-amber-100/70 text-slate-400 group-hover:text-amber-800 flex items-center justify-center transition-all group-hover:translate-x-0.5 shrink-0">
                                <i data-lucide="chevron-right" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            </div>
                            @else
                            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-300 flex items-center justify-center shrink-0">
                                <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- 3 Kolom Metrik: Masuk, Keluar, Sisa Karantina -->
                    <div class="mt-4 pt-3.5 border-t border-slate-100 grid grid-cols-3 gap-1.5 sm:gap-2 items-start text-left">
                        <!-- 1. Masuk -->
                        <div class="space-y-0.5 min-w-0 pr-1">
                            <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Masuk</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight truncate">
                                {{ number_format($karantinaMasuk, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Ekor</span>
                            </div>
                            <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                                Ayam Sakit
                            </div>
                        </div>

                        <!-- 2. Keluar -->
                        <div class="space-y-0.5 min-w-0 border-x border-slate-200/80 px-1.5 sm:px-2">
                            <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keluar</div>
                            <div class="text-xs sm:text-sm font-bold text-slate-800 leading-tight truncate">
                                {{ number_format($karantinaKeluar, 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Ekor</span>
                            </div>
                            <div class="text-[9px] sm:text-[10px] font-medium text-slate-400 truncate">
                                Sembuh Kembali
                            </div>
                        </div>

                        <!-- 3. Sisa Karantina -->
                        <div class="text-right space-y-0.5 min-w-0 pl-1">
                            <div class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sisa Karantina</div>
                            <div class="text-xs sm:text-base font-black leading-tight truncate {{ $karantinaStok > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                {{ number_format($karantinaStok, 0, ',', '.') }} <span class="text-[10px] font-bold text-slate-600">Ekor</span>
                            </div>
                            <div class="text-[9px] sm:text-[10px] font-bold truncate {{ $karantinaStok > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                {{ $karantinaStok > 0 ? 'Diisolasi' : 'Nihil / Sehat' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Isolasi Footer -->
                <div class="mt-3 p-2.5 px-3 rounded-xl {{ $karantinaStok > 0 ? 'bg-amber-50/70 border border-amber-200/80' : 'bg-emerald-50/70 border border-emerald-200/80' }} flex items-center justify-between text-xs">
                    <span class="text-[10px] font-bold {{ $karantinaStok > 0 ? 'text-amber-900' : 'text-emerald-900' }} flex items-center gap-1.5 truncate">
                        <span class="w-1.5 h-1.5 rounded-full {{ $karantinaStok > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                        {{ $karantinaStok > 0 ? 'Dalam Pemulihan Tim Medis' : 'Kondisi Farm Normal / Sehat' }}
                    </span>
                    <span class="text-[9.5px] font-extrabold uppercase {{ $karantinaStok > 0 ? 'text-amber-800' : 'text-emerald-800' }}">
                        {{ $karantinaStok > 0 ? 'Aktif' : 'Aman' }}
                    </span>
                </div>
            @if(!auth()->check() || auth()->user()->canAccess('warehouse_click_quarantine'))
            </a>
            @else
            </div>
            @endif
        @endif

    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- GRAFIK TREN SEMUA ALIRAN BARANG GUDANG (8 DATA STREAM) -->
    <!-- ========================================================================= -->
    @if(!auth()->check() || auth()->user()->canAccess('warehouse_chart_trends'))
    <div class="farm-card p-4 sm:p-6 border border-slate-200/80 shadow-xs relative overflow-hidden bg-white rounded-2xl">
        <!-- Header Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-maroon-800 text-white flex items-center justify-center shadow-md shadow-indigo-900/10 shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-800 text-sm sm:text-base tracking-tight" id="chartMainTitle">
                        TREN SEMUA ALIRAN BARANG GUDANG (8 DATA STREAM)
                    </h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium" id="chartSubtitle">
                        @if($hasDateFilter)
                            Visualisasi pergerakan {{ $diffDays }} hari ({{ $formattedStartDate }} – {{ $formattedEndDate }}): Masuk, Digunakan, Keluar & Terjual
                        @else
                            Visualisasi tren 14 hari terakhir & akumulasi data seluruh waktu: Masuk, Digunakan, Keluar & Terjual
                        @endif
                    </p>
                </div>
            </div>

            <!-- Segmented Commodity Tabs -->
            <div class="flex flex-wrap items-center gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200/70 self-start lg:self-auto">
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

        <!-- 4 Stat Summary Cards (Masuk, Digunakan, Keluar, Terjual) -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- 1. Masuk -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/80 hover:bg-emerald-50/80 transition-all flex flex-col justify-between min-h-[120px]">
                <div class="flex items-center justify-between pb-1.5 border-b border-emerald-100/70">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-800 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> Masuk
                    </span>
                    <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="arrow-down-left" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statMasukContent" class="py-1"></div>
                <div class="flex items-center justify-between text-[10px] text-emerald-700/80 font-bold pt-1 border-t border-emerald-100/60">
                    <span id="statMasukSub">Panen & Beli</span>
                    <span class="text-[9px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded-md">Inbound</span>
                </div>
            </div>

            <!-- 2. Digunakan (Kandang) -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-indigo-50/60 border border-indigo-200/80 hover:bg-indigo-50/80 transition-all flex flex-col justify-between min-h-[120px]">
                <div class="flex items-center justify-between pb-1.5 border-b border-indigo-100/70">
                    <span class="text-[10px] font-extrabold uppercase text-indigo-800 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 inline-block"></span> Digunakan
                    </span>
                    <div class="w-7 h-7 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="utensils" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statDigunakanContent" class="py-1"></div>
                <div class="flex items-center justify-between text-[10px] text-indigo-700/80 font-bold pt-1 border-t border-indigo-100/60">
                    <span id="statDigunakanSub">Konsumsi / Pakai</span>
                    <span class="text-[9px] bg-indigo-100 text-indigo-800 px-1.5 py-0.5 rounded-md">Internal</span>
                </div>
            </div>

            <!-- 3. Keluar (Gudang) -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50/60 border border-rose-200/80 hover:bg-rose-50/80 transition-all flex flex-col justify-between min-h-[120px]">
                <div class="flex items-center justify-between pb-1.5 border-b border-rose-100/70">
                    <span class="text-[10px] font-extrabold uppercase text-rose-800 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 inline-block"></span> Keluar
                    </span>
                    <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="arrow-up-right" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statKeluarContent" class="py-1"></div>
                <div class="flex items-center justify-between text-[10px] text-rose-700/80 font-bold pt-1 border-t border-rose-100/60">
                    <span id="statKeluarSub">Rusak & Penjualan</span>
                    <span class="text-[9px] bg-rose-100 text-rose-800 px-1.5 py-0.5 rounded-md">Outbound</span>
                </div>
            </div>

            <!-- 4. Terjual -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 hover:bg-amber-50/80 transition-all flex flex-col justify-between min-h-[120px]">
                <div class="flex items-center justify-between pb-1.5 border-b border-amber-100/70">
                    <span class="text-[10px] font-extrabold uppercase text-amber-800 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span> Terjual
                    </span>
                    <div class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <i data-lucide="shopping-cart" class="w-4 h-4 stroke-[2.5]"></i>
                    </div>
                </div>
                <div id="statTerjualContent" class="py-1"></div>
                <div class="flex items-center justify-between text-[10px] text-amber-700/80 font-bold pt-1 border-t border-amber-100/60">
                    <span id="statTerjualSub">Penjualan Platform</span>
                    <span class="text-[9px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-md">nochifram</span>
                </div>
            </div>
        </div>

        <!-- 8 Direct Links to Specific Data Streams -->
        @if(!auth()->check() || auth()->user()->canAccess('warehouse_stream_pills'))
        <div class="mt-5 pt-4 border-t border-slate-100">
            <div class="flex flex-wrap items-center justify-between gap-1 mb-2.5">
                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-maroon-700"></i>
                    Akses Langsung 8 Aliran Data Gudang @if($hasDateFilter)<span class="text-maroon-800 font-extrabold">(Periode {{ $diffDays }} Hari)</span>@else<span class="text-emerald-700 font-extrabold">(Seluruh Waktu)</span>@endif:
                </span>
                <span class="text-[10px] text-slate-400">Klik kartu untuk membuka tab data detail</span>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 xl:grid-cols-8 gap-2.5">
                <!-- 1. Telur Masuk -->
                <a href="{{ route('warehouse.telur', array_filter(['tab' => 'masuk', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-emerald-50/50 border border-slate-200/90 hover:border-emerald-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-emerald-800">Telur Masuk</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        <div>{{ number_format((int) ($streamTotals['telur_masuk'] ?? 0), 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span></div>
                        @if(($streamTotals['telur_masuk_kg'] ?? 0) > 0)
                            <div class="text-[10px] font-semibold text-slate-600">& {{ number_format($streamTotals['telur_masuk_kg'], 1, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                        @endif
                    </div>
                    <div class="text-[9px] text-emerald-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Produksi <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 2. Telur Rusak -->
                <a href="{{ route('warehouse.telur', array_filter(['tab' => 'keluar', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-rose-50/50 border border-slate-200/90 hover:border-rose-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-rose-800">Telur Rusak</span>
                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        <div>{{ number_format((int) ($streamTotals['telur_rusak_peti'] ?? 0), 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span></div>
                        @if(($streamTotals['telur_rusak_kg'] ?? 0) > 0)
                            <div class="text-[10px] font-semibold text-slate-600">& {{ number_format($streamTotals['telur_rusak_kg'], 1, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                        @endif
                    </div>
                    <div class="text-[9px] text-rose-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        {{ number_format($streamTotals['telur_rusak_butir'], 0, ',', '.') }} Btr <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 3. Telur Penjualan -->
                <a href="{{ route('warehouse.telur', array_filter(['tab' => 'penjualan', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-amber-50/50 border border-slate-200/90 hover:border-amber-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-amber-800">Telur Terjual</span>
                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        <div>{{ number_format((int) ($streamTotals['telur_terjual'] ?? 0), 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Peti</span></div>
                        @if(($streamTotals['telur_terjual_kg'] ?? 0) > 0)
                            <div class="text-[10px] font-semibold text-slate-600">& {{ number_format($streamTotals['telur_terjual_kg'], 1, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                        @endif
                    </div>
                    <div class="text-[9px] text-amber-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Penjualan <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 4. Pakan Masuk -->
                <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'masuk', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-sky-50/50 border border-slate-200/90 hover:border-sky-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-sky-800">Pakan Masuk</span>
                        <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        {{ number_format($streamTotals['pakan_masuk'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Kg</span>
                    </div>
                    <div class="text-[9px] text-sky-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Beli/Masuk <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 5. Pemberian Pakan -->
                <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'keluar', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-purple-50/50 border border-slate-200/90 hover:border-purple-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-purple-800">Pemberian Pakan</span>
                        <span class="w-2 h-2 rounded-full bg-purple-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        {{ number_format($streamTotals['pakan_konsumsi'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Kg</span>
                    </div>
                    <div class="text-[9px] text-purple-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Kandang <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 6. Pakan Terjual -->
                <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'penjualan', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-orange-50/50 border border-slate-200/90 hover:border-orange-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-orange-800">Pakan Terjual</span>
                        <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        <div>{{ number_format((int) ($streamTotals['pakan_terjual'] ?? 0), 0, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Karung</span></div>
                        @if(($streamTotals['pakan_terjual_kg'] ?? 0) > 0)
                            <div class="text-[10px] font-semibold text-slate-600">& {{ number_format($streamTotals['pakan_terjual_kg'], 1, ',', '.') }} <span class="text-[9px] font-normal text-slate-500">Kg</span></div>
                        @endif
                    </div>
                    <div class="text-[9px] text-orange-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Penjualan <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 7. Obat Masuk -->
                <a href="{{ route('warehouse.obat', array_filter(['tab' => 'masuk', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-teal-50/50 border border-slate-200/90 hover:border-teal-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-teal-800">Obat Masuk</span>
                        <span class="w-2 h-2 rounded-full bg-teal-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        {{ number_format($streamTotals['obat_masuk'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Item</span>
                    </div>
                    <div class="text-[9px] text-teal-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Gudang <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>

                <!-- 8. Pemakaian Obat -->
                <a href="{{ route('warehouse.obat', array_filter(['tab' => 'keluar', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="p-3 rounded-2xl bg-white hover:bg-pink-50/50 border border-slate-200/90 hover:border-pink-300 hover:shadow-sm transition-all group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-slate-700 group-hover:text-pink-800">Pemakaian Obat</span>
                        <span class="w-2 h-2 rounded-full bg-pink-500 shrink-0"></span>
                    </div>
                    <div class="text-xs sm:text-sm font-black text-slate-900 my-1">
                        {{ number_format($streamTotals['obat_konsumsi'], 1, ',', '.') }} <span class="text-[10px] font-normal text-slate-500">Dosis</span>
                    </div>
                    <div class="text-[9px] text-pink-700 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Kandang <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i>
                    </div>
                </a>
            </div>
        </div>
        @endif

        <!-- Line Chart Container -->
        <div class="mt-5 relative w-full rounded-2xl bg-slate-50/40 p-2 sm:p-3 border border-slate-100" style="height: 350px;">
            <canvas id="warehouseFlowChart"></canvas>
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- RIWAYAT AKTIVITAS & MUTASI TERKINI GUDANG -->
    <!-- ========================================================================= -->
    @if(!auth()->check() || auth()->user()->canAccess('warehouse_recent_mutations'))
    <div class="farm-card p-4 sm:p-6 border border-slate-200/80 shadow-xs rounded-2xl bg-white">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0 shadow-2xs">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Mutasi Terkini Gudang</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Catatan pergerakan barang masuk & keluar kandang</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('warehouse.telur', array_filter(['start_date' => $startDate, 'end_date' => $endDate])) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-rose-50 text-xs font-bold text-maroon-800 hover:text-maroon-900 border border-slate-200/80 hover:border-maroon-300 transition-all flex items-center gap-1 group">
                    <span>Lihat Semua</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- List Transaksi Responsif & Rapi -->
        <div class="mt-3.5 divide-y divide-slate-100">
            @forelse($recentTransactions as $item)
                @php
                    $isMasuk = $item->type === 'masuk';
                    $cat = strtolower($item->category);
                    $iconColor = $cat === 'telur' ? 'text-amber-500 bg-amber-50 border-amber-200' : ($cat === 'pakan' ? 'text-emerald-600 bg-emerald-50 border-emerald-200' : 'text-purple-600 bg-purple-50 border-purple-200');
                    $userDisplay = $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas';
                @endphp
                <div class="py-3.5 sm:py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/70 px-2 sm:px-3 rounded-2xl transition-all">
                    <!-- Left Column: Icon & Item Info -->
                    <div class="flex items-start sm:items-center gap-3 min-w-0">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl {{ $iconColor }} border flex items-center justify-center shrink-0 shadow-2xs">
                            @if($cat === 'telur')
                                <i data-lucide="egg" class="w-5 h-5"></i>
                            @elseif($cat === 'pakan')
                                <i data-lucide="wheat" class="w-5 h-5"></i>
                            @else
                                <i data-lucide="flask-conical" class="w-5 h-5"></i>
                            @endif
                        </div>
                        <div class="min-w-0 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs sm:text-sm font-extrabold text-slate-800">{{ $item->item_name }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold uppercase {{ $isMasuk ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }} flex items-center gap-0.5">
                                    <i data-lucide="{{ $isMasuk ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-3 h-3"></i>
                                    {{ $item->type }}
                                </span>
                            </div>
                            
                            <!-- Metadata Chips (Date, Time, Location/Source, User) -->
                            <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-1.5 sm:gap-2">
                                <span class="inline-flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}
                                </span>
                                @if($item->created_at)
                                    <span class="text-slate-300">•</span>
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                                        {{ $item->created_at->format('H:i') }} WIB
                                    </span>
                                @endif
                                @if($item->source)
                                    <span class="text-slate-300">•</span>
                                    <span class="px-1.5 py-0.2 rounded-md bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                        {{ $item->source }}
                                    </span>
                                @endif
                                <span class="text-slate-300">•</span>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-md bg-indigo-50 text-indigo-700 font-bold text-[10px] border border-indigo-100">
                                    <i data-lucide="user" class="w-2.5 h-2.5"></i>
                                    {{ $userDisplay }}
                                </span>
                                @if($cat === 'telur' && str_contains(strtolower($item->notes ?? ''), 'butir'))
                                    <span class="text-slate-300">•</span>
                                    <span class="text-[10px] text-rose-600 font-bold">
                                        {{ Str::after($item->notes, '(') ? '(' . Str::after($item->notes, '(') : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Quantity & Actions -->
                    <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        <div class="text-left sm:text-right">
                            <div class="text-sm sm:text-base font-black {{ $isMasuk ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $isMasuk ? '+' : '-' }}{{ number_format($item->quantity, 0, ',', '.') }} <span class="text-xs font-bold text-slate-600">{{ $item->unit }}</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        @if(!auth()->check() || auth()->user()->canAccess('warehouse_btn_manage'))
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('warehouse.' . $cat, array_filter(['start_date' => $startDate, 'end_date' => $endDate])) }}" class="px-2.5 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50/50 transition-all text-xs font-bold flex items-center gap-1 shadow-2xs" title="Lihat/Edit di Detail">
                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data transaksi ini?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 bg-white border border-slate-200 text-rose-600 rounded-xl hover:bg-rose-50 hover:border-rose-300 transition-all text-xs font-bold flex items-center gap-1 shadow-2xs" title="Hapus Mutasi">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 text-sm">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                        <i data-lucide="inbox" class="w-6 h-6"></i>
                    </div>
                    <div class="font-bold text-slate-600">Belum ada riwayat mutasi barang di gudang</div>
                    <p class="text-xs text-slate-400 mt-0.5">Semua pergerakan barang masuk & keluar akan tercatat otomatis di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TRANSAKSI PENJUALAN BARANG KELUAR (TERHUBUNG 1 DB NOCHIFRAM) -->
    <!-- ========================================================================= -->
    @if(!auth()->check() || auth()->user()->canAccess('warehouse_sales_stream'))
    <div class="farm-card p-4 sm:p-6 border border-slate-200/80 shadow-xs rounded-2xl bg-white border-t-4 border-t-maroon-800">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0 shadow-2xs">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Barang Keluar: Penjualan Real-Time</h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200">1 DB nochifram</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Data otomatis terintegrasi langsung dari modul penjualan & kasir farm</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="{{ route('warehouse.telur', array_filter(['tab' => 'penjualan', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-rose-50 text-xs font-bold text-maroon-800 hover:text-maroon-900 border border-slate-200/80 hover:border-maroon-300 transition-all flex items-center gap-1 group">
                    <span>Semua Penjualan</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- Sales Card Grid -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
            @forelse($recentSales as $sale)
                @php
                    $isTelur = strtolower($sale->category) === 'telur';
                    $petugasUsername = !empty($sale->user_username) ? '@' . ltrim($sale->user_username, '@') : ($sale->user_name ?? null);
                    
                    $rawTripUser = !empty($sale->trip_user_username) ? '@' . ltrim($sale->trip_user_username, '@') : ($sale->trip_user_name ?? null);
                    $tripCodeStr = $sale->trip_code ?? ($sale->driver_name ?? null);
                    $tripUsername = $rawTripUser ? $rawTripUser . ($tripCodeStr ? " ({$tripCodeStr})" : '') : $tripCodeStr;

                    $tooltipText = "Petugas Input: " . ($petugasUsername ?: 'Kasir System') . ($tripUsername ? " | Perjalanan: {$tripUsername}" : '');
                @endphp
                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-maroon-300 hover:shadow-md transition-all flex flex-col justify-between group relative" title="{{ $tooltipText }}">
                    <div>
                        <!-- Header Row: Category Badge + Invoice No + Payment Status -->
                        <div class="flex items-center justify-between gap-1.5 mb-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $isTelur ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                {{ $sale->category }} • {{ $sale->unit }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-bold text-slate-400 font-mono bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200/70">#{{ $sale->invoice_no }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9.5px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                    {{ $sale->payment_status ?: 'LUNAS' }}
                                </span>
                            </div>
                        </div>

                        <!-- Item & Customer -->
                        <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">{{ $sale->item_name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                            <i data-lucide="user" class="w-3 h-3 text-slate-400"></i>
                            Pembeli: <b class="text-slate-800 font-bold">{{ $sale->customer_name }}</b>
                        </p>

                        <!-- Info Username Petugas Input & Perjalanan Armada -->
                        <div class="flex flex-wrap items-center gap-1.5 mt-2.5">
                            @if($petugasUsername)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200/80 text-[10px] font-semibold" title="Petugas Input: {{ $petugasUsername }}">
                                    <i data-lucide="user-check" class="w-3 h-3 text-blue-600"></i>
                                    <span>Input: <b>{{ $petugasUsername }}</b></span>
                                </span>
                            @endif

                            @if($tripUsername)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200/80 text-[10px] font-semibold" title="Armada: {{ $tripUsername }}">
                                    <i data-lucide="truck" class="w-3 h-3 text-purple-600"></i>
                                    <span>Armada: <b>{{ $tripUsername }}</b></span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Footer: Quantity & Date -->
                    <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1 text-[11px] text-slate-400">
                            <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                            <span>{{ \Carbon\Carbon::parse($sale->date)->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-sm text-maroon-800 block">-{{ number_format($sale->quantity, 0, ',', '.') }} {{ $sale->unit }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 py-10 text-center text-slate-400 text-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                    </div>
                    <div class="font-bold text-slate-600 text-sm">Belum ada transaksi penjualan tercatat</div>
                    <p class="text-xs text-slate-400 mt-0.5">Data penjualan dari sistem kasir nochifram akan otomatis disinkronkan ke sini.</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif

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
            if (sMasuk) sMasuk.textContent = 'Panen & Beli';
            if (sDigunakan) sDigunakan.textContent = 'Konsumsi & Kerusakan';
            if (sKeluar) sKeluar.textContent = 'Total Pengeluaran';
            if (sTerjual) sTerjual.textContent = 'Penjualan Platform';

            cMasuk.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.telur', array_filter(['tab' => 'masuk', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-emerald-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Telur:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.telur_masuk).toLocaleString('id-ID', {maximumFractionDigits: 1})} Peti</span>
                    </a>
                    <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'masuk', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-emerald-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Pakan:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.pakan_masuk).toLocaleString('id-ID', {maximumFractionDigits: 1})} Kg</span>
                    </a>
                    <a href="{{ route('warehouse.obat', array_filter(['tab' => 'masuk', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-emerald-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Obat:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.obat_masuk).toLocaleString('id-ID', {maximumFractionDigits: 1})} Item</span>
                    </a>
                </div>
            `;

            cDigunakan.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'keluar', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-indigo-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Pakan:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.pakan_konsumsi).toLocaleString('id-ID', {maximumFractionDigits: 1})} Kg</span>
                    </a>
                    <a href="{{ route('warehouse.telur', array_filter(['tab' => 'keluar', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-indigo-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Telur Rusak:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.telur_rusak_butir).toLocaleString('id-ID')} Btr</span>
                    </a>
                    <a href="{{ route('warehouse.obat', array_filter(['tab' => 'keluar', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-indigo-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Obat Pakai:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.obat_konsumsi).toLocaleString('id-ID', {maximumFractionDigits: 1})} Dosis</span>
                    </a>
                </div>
            `;

            const totalTelurKeluar = (Number(warehouseStreamTotals.telur_rusak_peti) + Number(warehouseStreamTotals.telur_terjual)).toFixed(1);
            const totalPakanKeluarKg = (Number(warehouseStreamTotals.pakan_konsumsi) + Number(warehouseStreamTotals.pakan_terjual_total_kg || (warehouseStreamTotals.pakan_terjual * 50))).toFixed(1);

            cKeluar.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.telur', array_filter(['tab' => 'semua', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-rose-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Telur Total:
                        </span>
                        <span class="font-black text-slate-800">${Number(totalTelurKeluar).toLocaleString('id-ID')} Peti</span>
                    </a>
                    <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'semua', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-rose-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Pakan Total:
                        </span>
                        <span class="font-black text-slate-800">${Number(totalPakanKeluarKg).toLocaleString('id-ID')} Kg</span>
                    </a>
                    <a href="{{ route('warehouse.obat', array_filter(['tab' => 'keluar', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-rose-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span> Obat Pakai:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.obat_konsumsi).toLocaleString('id-ID', {maximumFractionDigits: 1})} Dosis</span>
                    </a>
                </div>
            `;

            cTerjual.innerHTML = `
                <div class="space-y-1 text-xs">
                    <a href="{{ route('warehouse.telur', array_filter(['tab' => 'penjualan', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-amber-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Telur:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.telur_terjual).toLocaleString('id-ID', {maximumFractionDigits: 1})} Peti</span>
                    </a>
                    <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'penjualan', 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="flex justify-between items-center px-2 py-1 rounded-lg hover:bg-amber-100/70 transition-colors">
                        <span class="text-slate-600 font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Pakan:
                        </span>
                        <span class="font-black text-slate-800">${Number(warehouseStreamTotals.pakan_terjual).toLocaleString('id-ID')} Karung${Number(warehouseStreamTotals.pakan_terjual_kg || 0) > 0 ? ' & ' + Number(warehouseStreamTotals.pakan_terjual_kg).toLocaleString('id-ID', {maximumFractionDigits: 1}) + ' Kg' : ''}</span>
                    </a>
                    <div class="flex justify-between items-center text-[10px] text-amber-800/80 pt-1 px-1 border-t border-amber-200/60 font-semibold">
                        <span>Platform:</span>
                        <span class="font-bold">nochifram</span>
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
                <div class="text-lg sm:text-xl font-black text-emerald-900 tracking-tight mt-1">
                    ${dataTotals.masuk || '0'}
                </div>
            `;

            cDigunakan.innerHTML = `
                <div class="text-lg sm:text-xl font-black text-indigo-900 tracking-tight mt-1">
                    ${dataTotals.digunakan || '0'}
                </div>
            `;

            cKeluar.innerHTML = `
                <div class="text-lg sm:text-xl font-black text-rose-900 tracking-tight mt-1">
                    ${dataTotals.keluar || '0'}
                </div>
            `;

            cTerjual.innerHTML = `
                <div class="text-lg sm:text-xl font-black text-amber-900 tracking-tight mt-1">
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
                        backgroundColor: 'rgba(15, 23, 42, 0.94)',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 12,
                        cornerRadius: 12,
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
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }

    function closeTravelokaPopover() {
        const panel = document.getElementById('travelokaPopoverPanel');
        if (panel) panel.classList.add('hidden');
    }

    function checkAndAutoSubmitDateRange() {
        const sInput = document.getElementById('travelokaStartDate');
        const eInput = document.getElementById('travelokaEndDate');
        if (sInput && eInput && sInput.value && eInput.value) {
            if (sInput.value > eInput.value) {
                eInput.value = sInput.value;
            }
            document.getElementById('travelokaDateForm').submit();
        }
    }

    function applyPresetDates(days) {
        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - (days - 1));

        const formatYMD = (d) => {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const sInput = document.getElementById('travelokaStartDate');
        const eInput = document.getElementById('travelokaEndDate');
        if (sInput && eInput) {
            sInput.value = formatYMD(start);
            eInput.value = formatYMD(end);
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
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
