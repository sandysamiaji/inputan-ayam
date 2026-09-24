@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Top Navigation Back & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('warehouse.index', array_filter(['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                    <span>💊 Gudang Obat, Vaksin & Vitamin</span>
                </h1>
                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                    <p class="text-xs text-slate-400">Manajemen inventaris stok obat, vaksin & sinkronisasi pemakaian kandang</p>
                    @if(!empty($startDate) && !empty($endDate))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-purple-50 border border-purple-200/80 text-[10px] font-extrabold text-purple-800">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                            <a href="{{ route('warehouse.obat', ['tab' => $tab, 'q' => $search]) }}" class="hover:text-purple-600 ml-0.5" title="Hapus Filter Tanggal">×</a>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stok Quick Stat Banner -->
        <div class="flex items-center justify-between sm:justify-end gap-3 bg-white px-4 py-2.5 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-left sm:text-right">
                <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Sisa Stok Total</span>
                <span class="text-sm sm:text-base font-extrabold {{ $stokSaatIni <= 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ number_format($stokSaatIni, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-500">Item</span>
                </span>
            </div>
            <div class="h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Total Masuk</span>
                <span class="text-xs sm:text-sm font-bold text-slate-700">
                    {{ number_format($totalMasuk, 0, ',', '.') }} <span class="text-[10px] text-slate-400">Item</span>
                </span>
            </div>
            <div class="h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Total Dipakai</span>
                <span class="text-xs sm:text-sm font-bold text-rose-600">
                    {{ number_format($totalKeluar, 0, ',', '.') }} <span class="text-[10px] text-slate-400">Item</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Alert Rekonsiliasi & Penjelasan Perhitungan Stok Real-Time -->
    @if(!empty($inventorySummary['deficit_products_count']) && $inventorySummary['deficit_products_count'] > 0)
    <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50/90 border border-amber-200/90 text-xs text-amber-900 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-300/60 text-amber-700 flex items-center justify-center shrink-0 text-base shadow-sm">
                💡
            </div>
            <div>
                <div class="font-extrabold text-amber-950 text-xs sm:text-sm flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span>Penjelasan Perhitungan: Sisa Stok Fisik ({{ number_format($stokSaatIni, 0, ',', '.') }} Item)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200/80 text-amber-900 border border-amber-300">
                        {{ $inventorySummary['deficit_products_count'] }} Produk Habis / Perlu Restok
                    </span>
                </div>
                <p class="text-slate-600 text-[11px] sm:text-xs mt-1 leading-relaxed">
                    Stok fisik yang masih tersedia di rak gudang saat ini adalah <b>{{ number_format($stokSaatIni, 0, ',', '.') }} Item</b> (berasal dari <b>{{ $inventorySummary['safe_count'] + $inventorySummary['low_count'] }} produk</b> yang masih ada stok).
                    Total log pemakaian tercatat <b>{{ number_format($totalKeluar, 0, ',', '.') }} Item</b> karena terdapat <b>{{ $inventorySummary['deficit_products_count'] }} produk</b> yang pemakaiannya melampaui stok awal tercatat (stok fisik habis = 0):
                    @foreach($inventorySummary['deficit_products'] as $dp)
                        <span class="font-bold text-rose-700 underline decoration-rose-300 ml-1">{{ $dp['name'] }} (Masuk {{ $dp['masuk'] }}, Pakai {{ $dp['keluar'] }} {{ $dp['unit'] }})</span>{{ !$loop->last ? ',' : '.' }}
                    @endforeach
                </p>
                <div class="flex items-center gap-2 mt-1.5 text-[11px] text-amber-800 font-medium">
                    <span>ℹ️ <i>Stok gudang tidak bernilai minus (-8) karena barang habis berhenti di 0. Silakan catat <b>Restok Masuk</b> untuk produk tersebut agar mutasi masuk dan pemakaian seimbang.</i></span>
                </div>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <button type="button" onclick="openModalInputObat('masuk')" class="w-full sm:w-auto px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 whitespace-nowrap active:scale-95">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Restok Masuk
            </button>
        </div>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. INVENTARIS & KATALOG STOK REAL-TIME (Sinkron dengan /input) -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Header Section Inventaris -->
        <div class="p-4 sm:p-5 border-b border-slate-100 bg-gradient-to-r from-purple-50/50 via-white to-rose-50/30 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold shadow-sm">
                        📦
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-slate-800">
                            Inventaris & Stok Real-Time
                        </h2>
                        <p class="text-[11px] text-slate-400">
                            Data stok tersinkronisasi otomatis dengan pemakaian kandang di menu <b>Input Medis</b>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Status Stok Badge -->
            <div class="flex items-center gap-2 text-[11px] font-bold">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $inventorySummary['safe_count'] }} Aman
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    {{ $inventorySummary['low_count'] }} Menipis
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200/80">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    {{ $inventorySummary['empty_count'] }} Habis
                </span>
            </div>
        </div>

        <!-- Filter Kategori & Pencarian Stok Cepat -->
        <div class="p-4 bg-slate-50/70 border-b border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Search Input Realtime -->
            <div class="relative flex-1 max-w-sm">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input 
                    type="text" 
                    id="stockSearchInput" 
                    oninput="filterLiveInventory()" 
                    placeholder="Cari obat di inventaris (cth: Vermixon, ND Lasota)..." 
                    class="w-full pl-9 pr-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 shadow-sm"
                >
            </div>

            <!-- Category Pills Horizontal Scroll -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-thin">
                <button type="button" onclick="setInventoryCategoryFilter('all', this)" class="inv-cat-pill active px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-purple-700 text-white shadow-sm">
                    Semua ({{ count($medicines) }})
                </button>
                <button type="button" onclick="setInventoryCategoryFilter('obat_cacing', this)" class="inv-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                    🪱 Cacing
                </button>
                <button type="button" onclick="setInventoryCategoryFilter('antibiotik', this)" class="inv-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                    💊 Antibiotik
                </button>
                <button type="button" onclick="setInventoryCategoryFilter('antikoksidia', this)" class="inv-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                    🩸 Antikoksidia
                </button>
                <button type="button" onclick="setInventoryCategoryFilter('vitamin', this)" class="inv-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                    🍊 Vitamin
                </button>
                <button type="button" onclick="setInventoryCategoryFilter('vaksin', this)" class="inv-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                    💉 Vaksin
                </button>
                <button type="button" onclick="setInventoryCategoryFilter('mineral', this)" class="inv-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                    🧱 Mineral
                </button>
                <button type="button" onclick="setInventoryCategoryFilter('disinfektan', this)" class="inv-cat-pill px-3 py-1 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-slate-600 hover:bg-slate-100 border border-slate-200">
                    🧪 Disinfektan
                </button>
            </div>
        </div>

        <!-- Grid Produk Inventaris Real-Time -->
        <div class="p-4 sm:p-5">
            <div id="inventoryGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach($medicines as $med)
                    @php
                        $badgeStyle = 'bg-slate-100 text-slate-700';
                        if ($med['category_key'] === 'obat_cacing') $badgeStyle = 'bg-amber-100 text-amber-800 border-amber-200';
                        elseif ($med['category_key'] === 'antibiotik') $badgeStyle = 'bg-rose-100 text-rose-800 border-rose-200';
                        elseif ($med['category_key'] === 'antikoksidia') $badgeStyle = 'bg-red-100 text-red-800 border-red-200';
                        elseif ($med['category_key'] === 'vitamin') $badgeStyle = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                        elseif ($med['category_key'] === 'vaksin') $badgeStyle = 'bg-purple-100 text-purple-800 border-purple-200';
                        elseif ($med['category_key'] === 'mineral') $badgeStyle = 'bg-sky-100 text-sky-800 border-sky-200';
                        elseif ($med['category_key'] === 'disinfektan') $badgeStyle = 'bg-cyan-100 text-cyan-800 border-cyan-200';
                    @endphp
                    <div class="inventory-card bg-white rounded-2xl border border-slate-200/90 hover:border-purple-300 p-3.5 transition-all hover:shadow-md flex flex-col justify-between"
                         data-category="{{ $med['category_key'] }}"
                         data-name="{{ strtolower($med['name']) }}">
                        
                        <div>
                            <!-- Header Item: Kategori & Status Badge -->
                            <div class="flex items-center justify-between gap-1.5 mb-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase border {{ $badgeStyle }} truncate max-w-[170px]">
                                    {{ $med['category'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase border {{ $med['status_color'] }} shrink-0">
                                    {{ $med['status_label'] }}
                                </span>
                            </div>

                            <!-- Nama Produk -->
                            <h3 class="font-extrabold text-slate-800 text-xs sm:text-sm line-clamp-1 mb-1" title="{{ $med['name'] }}">
                                {{ $med['name'] }}
                            </h3>

                            <!-- Indikasi Singkat -->
                            <p class="text-[10.5px] text-slate-400 line-clamp-1 mb-3" title="{{ $med['indication'] ?? '' }}">
                                {{ $med['indication'] ?? ($med['dosage'] ?? 'SOP Medis Nochi Farm') }}
                            </p>
                        </div>

                        <!-- Kotak Rincian Stok: Masuk, Keluar, Sisa -->
                        <div>
                            <div class="bg-slate-50/80 rounded-xl p-2.5 border border-slate-100/90 mb-2.5">
                                <div class="grid grid-cols-3 gap-1 text-center">
                                    <div class="border-r border-slate-200/70 pr-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Masuk</span>
                                        <b class="text-[11px] font-bold text-slate-700">{{ number_format($med['total_masuk'], 1, ',', '.') }}</b>
                                    </div>
                                    <div class="border-r border-slate-200/70 px-1">
                                        <span class="text-[9px] font-bold text-rose-500 uppercase block">Dipakai</span>
                                        <b class="text-[11px] font-bold text-rose-600">{{ number_format($med['total_keluar'], 1, ',', '.') }}</b>
                                    </div>
                                    <div class="pl-1">
                                        <span class="text-[9px] font-extrabold text-emerald-600 uppercase block">Sisa Stok</span>
                                        <b class="text-xs font-black {{ $med['stock'] <= 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                            {{ number_format($med['stock'], 1, ',', '.') }} <span class="text-[9px] font-semibold text-slate-400">{{ $med['unit'] }}</span>
                                        </b>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi Cepat: Pakai Kandang atau Tambah Stok Masuk -->
                            <div class="grid grid-cols-2 gap-1.5 pt-1 border-t border-slate-100 text-[10.5px]">
                                <button type="button" 
                                    onclick="openInputObatForProduct('{{ addslashes($med['name']) }}', 'keluar')"
                                    class="py-1.5 px-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold border border-rose-200/70 text-center transition-all flex items-center justify-center gap-1">
                                    <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                                    <span>Pakai</span>
                                </button>
                                <button type="button" 
                                    onclick="openInputObatForProduct('{{ addslashes($med['name']) }}', 'masuk')"
                                    class="py-1.5 px-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold border border-emerald-200/70 text-center transition-all flex items-center justify-center gap-1">
                                    <i data-lucide="plus" class="w-3 h-3"></i>
                                    <span>Restok</span>
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Pesan jika hasil pencarian kosong -->
            <div id="noInventoryMatch" class="hidden py-8 text-center">
                <p class="text-xs text-slate-400 font-medium">Tidak ada produk yang cocok dengan pencarian inventaris.</p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. RIWAYAT TRANSAKSI & PEMAKAIAN KANDANG -->
    <!-- ========================================================================= -->
    <div class="space-y-3 pt-2">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-purple-700"></i>
                    <span>Riwayat Transaksi & Pemakaian Obat</span>
                </h2>
                <p class="text-xs text-slate-400">Daftar mutasi masuk (pembelian) & mutasi keluar (pemakaian kandang)</p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="openModalInputObat('keluar')" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-2xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs sm:text-sm font-bold shadow-md shadow-maroon-900/20 transition-all active:scale-95 shrink-0">
                    <i data-lucide="plus-circle" class="w-4 h-4 stroke-[2.5]"></i>
                    <span>Input Transaksi Obat</span>
                </button>
            </div>
        </div>

        <!-- Search Bar Transaksi -->
        <div class="flex items-center gap-2 sm:gap-3">
            <form method="GET" action="{{ route('warehouse.obat') }}" class="flex-1 relative">
                <input type="hidden" name="tab" value="{{ $tab }}">
                @if(!empty($startDate)) <input type="hidden" name="start_date" value="{{ $startDate }}"> @endif
                @if(!empty($endDate)) <input type="hidden" name="end_date" value="{{ $endDate }}"> @endif
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}" 
                    placeholder="Cari riwayat transaksi obat, kandang, tanggal..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs sm:text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 shadow-sm transition-all"
                >
                @if($search)
                    <a href="{{ route('warehouse.obat', array_filter(['tab' => $tab, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Filter Tabs: Semua | Masuk | Pemakaian (Kandang) -->
        <div class="flex items-center border-b border-slate-200 gap-6 sm:gap-8 px-1">
            <a href="{{ route('warehouse.obat', array_filter(['tab' => 'semua', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative {{ $tab === 'semua' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
                Semua Riwayat ({{ $countSemua ?? $items->total() }})
                @if($tab === 'semua')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
                @endif
            </a>
            <a href="{{ route('warehouse.obat', array_filter(['tab' => 'masuk', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative {{ $tab === 'masuk' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
                Masuk (Beli / Restok) ({{ $countMasuk ?? 0 }})
                @if($tab === 'masuk')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
                @endif
            </a>
            <a href="{{ route('warehouse.obat', array_filter(['tab' => 'keluar', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative {{ $tab === 'keluar' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
                Pemakaian Kandang ({{ $countKeluar ?? 0 }})
                @if($tab === 'keluar')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
                @endif
            </a>
        </div>

        <!-- List Data Transaksi -->
        <div class="space-y-2.5">
            @forelse($items as $item)
                @php
                    $isMasuk = $item->type === 'masuk';
                    $isNonaktif = str_starts_with(trim($item->notes ?? ''), '[NONAKTIF]');
                    $displayNotes = $isNonaktif ? trim(substr(trim($item->notes), strlen('[NONAKTIF]'))) : $item->notes;
                @endphp
                <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex items-center justify-between gap-3 {{ $isNonaktif ? 'opacity-60 bg-slate-50' : '' }}">
                    
                    <!-- Left Details & Icon -->
                    <div class="flex items-center gap-3.5 min-w-0 cursor-pointer flex-1" onclick="openDetailObatModal({{ json_encode([
                        'id' => $item->id,
                        'title' => $item->item_name,
                        'type' => $item->type,
                        'category' => ucfirst($item->category ?? 'Obat'),
                        'quantity' => number_format($item->quantity, 1, ',', '.') . ' ' . $item->unit,
                        'raw_quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'notes' => $displayNotes,
                        'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                        'raw_date' => $item->date->format('Y-m-d'),
                        'time' => $item->created_at ? $item->created_at->format('H:i') : '09:30',
                        'petugas' => $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas',
                        'source' => $item->source ?? 'Kandang',
                        'is_nonaktif' => $isNonaktif
                    ]) }})">
                        
                        <!-- Flask / Medicine Icon -->
                        <div class="w-11 h-11 rounded-2xl {{ $isMasuk ? 'bg-emerald-50 border-emerald-200/60 text-emerald-600' : 'bg-rose-50 border-rose-200/60 text-rose-600' }} border flex items-center justify-center shrink-0 shadow-inner">
                            @if($isMasuk)
                                <i data-lucide="package-plus" class="w-5 h-5"></i>
                            @else
                                <i data-lucide="flask-conical" class="w-5 h-5"></i>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <!-- Badge Masuk / Keluar / Pemakaian -->
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isMasuk ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $item->type === 'keluar' ? 'PEMAKAIAN KANDANG' : 'MASUK (BELI / RESTOK)' }}
                                </span>
                                @if(!empty($item->category))
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase">
                                        {{ str_replace('_', ' ', $item->category) }}
                                    </span>
                                @endif
                                @if($isNonaktif)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">NONAKTIF</span>
                                @endif
                            </div>

                            <!-- Judul Transaksi (Nama Obat & Jumlah) -->
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 truncate">
                                {{ $item->item_name }}
                            </h3>

                            <!-- Jumlah & Satuan Riil -->
                            <p class="text-xs font-extrabold {{ $isMasuk ? 'text-emerald-700' : 'text-slate-800' }}">
                                {{ $isMasuk ? '+' : '-' }}{{ number_format($item->quantity, 1, ',', '.') }} {{ $item->unit }}
                            </p>

                            <!-- Tanggal & Petugas & Blok/Kloter/Supplier -->
                            <div class="text-[10px] sm:text-[11px] text-slate-400 flex flex-wrap items-center gap-1.5 mt-0.5">
                                <span>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }} {{ $item->created_at ? $item->created_at->format('H:i') : '' }}</span>
                                <span>•</span>
                                <span class="text-slate-500 font-medium">{{ $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas' }}</span>
                                @if($item->source)
                                    <span>•</span>
                                    <span class="text-slate-600 font-bold bg-slate-100 px-1.5 py-0.2 rounded border border-slate-200">{{ $item->source }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Action Menu (3 Dots) -->
                    <div class="relative shrink-0">
                        <button onclick="toggleActionDropdown(this)" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">
                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                        </button>
                        <!-- Dropdown Content -->
                        <div class="action-dropdown hidden absolute right-0 top-9 z-20 w-44 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-xs font-semibold text-slate-700">
                            <button onclick="openDetailObatModal({{ json_encode([
                                'id' => $item->id,
                                'title' => $item->item_name,
                                'type' => $item->type,
                                'category' => ucfirst($item->category ?? 'Obat'),
                                'quantity' => number_format($item->quantity, 1, ',', '.') . ' ' . $item->unit,
                                'raw_quantity' => $item->quantity,
                                'unit' => $item->unit,
                                'notes' => $displayNotes,
                                'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                                'raw_date' => $item->date->format('Y-m-d'),
                                'time' => $item->created_at ? $item->created_at->format('H:i') : '09:30',
                                'petugas' => $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas',
                                'source' => $item->source ?? 'Kandang',
                                'is_nonaktif' => $isNonaktif
                            ]) }})" class="w-full px-3.5 py-2 text-left hover:bg-slate-50 flex items-center gap-2">
                                <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                                <span>Lihat Detail</span>
                            </button>
                            <button onclick="openEditObatModal({{ json_encode([
                                'id' => $item->id,
                                'title' => $item->item_name,
                                'type' => $item->type,
                                'category' => $item->category ?? 'obat',
                                'quantity' => $item->quantity,
                                'unit' => $item->unit,
                                'source' => $item->source,
                                'notes' => $displayNotes,
                                'date' => $item->date->format('Y-m-d'),
                                'raw_date' => $item->date->format('Y-m-d'),
                                'time' => $item->created_at ? $item->created_at->format('H:i') : '09:30',
                                'coop_id' => $item->coop_id ?? null,
                                'flock_id' => $item->flock_id ?? null,
                                'medicine_name' => $item->medicine_name ?? $item->item_name,
                                'medicine_type' => $item->medicine_type ?? ($item->category ?? 'obat'),
                                'dosage' => $item->dosage ?? ($item->quantity . ' ' . $item->unit),
                                'application_method' => $item->application_method ?? 'Air minum',
                            ]) }})" class="w-full px-3.5 py-2 text-left hover:bg-slate-50 flex items-center gap-2">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-blue-600"></i>
                                <span>Edit Data</span>
                            </button>
                            <form method="POST" action="{{ route('warehouse.toggle-status', $item->id) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full px-3.5 py-2 text-left hover:bg-slate-50 flex items-center gap-2 text-amber-700">
                                    <i data-lucide="{{ $isNonaktif ? 'check-circle' : 'eye-off' }}" class="w-3.5 h-3.5"></i>
                                    <span>{{ $isNonaktif ? 'Aktifkan Data' : 'Nonaktifkan' }}</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('warehouse.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?');" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3.5 py-2 text-left hover:bg-rose-50 flex items-center gap-2 text-rose-600">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Hapus Data</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="farm-card p-10 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <i data-lucide="flask-conical" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">Tidak ada riwayat transaksi obat / vaksin</h3>
                    <p class="text-xs text-slate-400 mt-1">Belum ada catatan mutasi obat untuk filter ini.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL INPUT TRANSAKSI OBAT & VAKSIN (SESUAI FITUR /input 💊 Vaksin & Obat) -->
<!-- ========================================================================= -->
<div id="modalInputObat" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-xl rounded-t-3xl sm:rounded-3xl p-5 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[92vh] overflow-y-auto">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-lg shadow-sm border border-purple-200/60">
                    💊
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base flex items-center gap-2">
                        <span>Vaksin & Obat</span>
                        <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">↘ Gudang Obat</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Form transaksi tersinkronisasi dengan Master Katalog dan Stok Gudang</p>
                </div>
            </div>
            <button type="button" onclick="closeModalInputObat()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Mode Switch: Pemakaian Kandang (Keluar) vs Masuk (Beli / Restok) -->
        <div class="mt-4 p-1 rounded-2xl bg-slate-100 grid grid-cols-2 gap-1 text-xs font-extrabold">
            <button type="button" id="tabModeKeluar" onclick="switchModalMode('keluar')" class="py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 bg-white text-rose-700 shadow-sm">
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                <span>Pemakaian Kandang (Keluar)</span>
            </button>
            <button type="button" id="tabModeMasuk" onclick="switchModalMode('masuk')" class="py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-emerald-700">
                <i data-lucide="package-plus" class="w-3.5 h-3.5"></i>
                <span>Masuk (Beli / Restok)</span>
            </button>
        </div>

        <form method="POST" action="{{ route('warehouse.store') }}" id="formWarehouseInputObat" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="type" id="modalInputType" value="keluar">

            <!-- Field Kloter & Blok (Hanya Aktif di Mode Pemakaian Kandang) -->
            <div id="sectionCoopSelect" class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kloter</label>
                    <select id="modalInputKloter" onchange="filterModalCoops()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                        @foreach($flocks as $flock)
                            <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Blok *</label>
                    <select name="coop_id" id="modalInputCoop" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                        @foreach($coops as $coop)
                            <option value="{{ $coop->id }}" data-flock="{{ $coop->flock_id }}">
                                {{ $coop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kategori Obat / Medis -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Obat / Medis</label>
                <select name="category" id="modalInputCategory" onchange="filterModalMedicines()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    <option value="all">🔍 Semua Kategori (Tampilkan Semua 26 Produk)</option>
                    <option value="obat_cacing">🪱 Obat Cacing / Anthelmintik (Vermixon, Wormzol)</option>
                    <option value="antibiotik">💊 Antibiotik / Antibakteri (Neomeditril, Amoxitin, CRD)</option>
                    <option value="antikoksidia">🩸 Antikoksidiosis (Toltrazuril, Berak Darah)</option>
                    <option value="vitamin">🍊 Vitamin & Suplemen Antistres (Vita Stress, B Complex)</option>
                    <option value="vaksin">💉 Vaksin Unggas (ND Lasota, ND IB, AI, Coryza)</option>
                    <option value="mineral">🧱 Mineral, Kalsium & Premix (Kalsium, CaCO3)</option>
                    <option value="disinfektan">🧪 Disinfektan & Sanitasi (Medisep, Antisep, Rodalon)</option>
                    <option value="obat">🌿 Obat Lainnya / Herbal Unggas</option>
                </select>
            </div>

            <!-- Pilih Produk (26 Katalog + Custom Input) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs font-bold text-slate-700">Pilih Produk *</label>
                    <span id="modalMedFilterCount" class="text-[10px] text-slate-400 font-semibold">{{ count($medicines) }} produk tersedia</span>
                </div>
                <select name="medicine_name" id="modalInputProduk" onchange="updateModalMedDetails()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-bold text-slate-800 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    @foreach($medicines as $med)
                        <option value="{{ $med['name'] }}" 
                            data-category="{{ $med['category_key'] }}" 
                            data-category-label="{{ $med['category'] }}"
                            data-dosage="{{ $med['dosage'] }}" 
                            data-app="{{ $med['application'] }}" 
                            data-sch="{{ $med['schedule'] }}" 
                            data-unit="{{ $med['unit'] }}" 
                            data-stock="{{ $med['stock'] }}"
                            data-indication="{{ $med['indication'] ?? '' }}"
                            data-notes="{{ $med['notes'] ?? '' }}">
                            {{ $med['name'] }} — [{{ $med['category'] }}]
                        </option>
                    @endforeach
                    <option value="custom" data-category="custom" data-category-label="Input Manual" data-dosage="Sesuai dosis kemasan" data-app="Air minum" data-sch="Sesuai anjuran" data-unit="Botol" data-stock="0" data-indication="Obat / vitamin khusus luar katalog" data-notes="Input manual oleh petugas">+ Tulis Nama Produk Lainnya (Input Manual)...</option>
                </select>
            </div>

            <!-- Custom Product Name Input (If Custom is selected) -->
            <div id="modalFieldCustomMed" style="display:none;" class="p-3 bg-purple-50/60 border border-purple-200/80 rounded-2xl">
                <label class="block text-xs font-bold text-purple-800 mb-1">Nama Produk Baru (Input Manual)</label>
                <input type="text" id="modalInputCustomName" placeholder="Contoh: Super Tetra, Tetrasiklin, Herbal Kunyit..." class="w-full px-3 py-2 rounded-xl border border-purple-200 text-xs sm:text-sm bg-white">
            </div>

            <!-- Acuan Medis dari Master -->
            <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-3.5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <b class="text-xs text-slate-800">Acuan Medis dari Master</b>
                        <span id="modalMedCategoryBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800">-</span>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-600 flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Terdaftar
                    </span>
                </div>

                <div id="modalMedIndicationBox" class="text-[11px] text-rose-800 bg-rose-50 border border-rose-200/80 rounded-xl p-2.5 mb-2 leading-relaxed">
                    <b>🎯 Indikasi / Gejala:</b> <span id="modalMedIndicationText">-</span>
                </div>

                <div class="text-[11.5px] leading-relaxed text-slate-600 space-y-0.5">
                    <div>• Dosis Standar: <b id="modalMedDosisText" class="text-slate-800">-</b></div>
                    <div>• Rekomendasi Aplikasi: <b id="modalMedAplikasiText" class="text-maroon-800">-</b></div>
                    <div>• Jadwal / Waktu: <b id="modalMedJadwalText" class="text-slate-800">-</b></div>
                    <div id="modalMedNotesText" class="text-[11px] text-slate-400 italic mt-1">-</div>
                </div>
            </div>

            <!-- Highlight Stok Produk Real-Time -->
            <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200/80">
                <div class="text-[10px] uppercase font-bold text-amber-800 mb-0.5">Status Stok Gudang Real-Time</div>
                <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                    <span>Stok Saat Ini: <b id="modalStokSekarang" class="text-amber-900 font-extrabold text-sm">0 Botol</b></span>
                    <span id="modalStokEstimasiWrapper">Sisa Setelah Transaksi: <b id="modalStokSetelahTx" class="text-emerald-700 font-extrabold text-sm">0 Botol</b></span>
                </div>
            </div>

            <!-- Jumlah Aktual & Satuan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label id="lblJumlahAktual" class="block text-xs font-bold text-slate-700 mb-1">Jumlah Pemakaian Aktual *</label>
                    <input type="number" step="0.1" name="quantity" id="modalInputJumlah" value="1" placeholder="Jumlah" oninput="calcModalObatSisa()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan *</label>
                    <select name="unit" id="modalInputSatuan" onchange="calcModalObatSisa()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                        <option value="Botol">Botol</option>
                        <option value="Box">Box</option>
                        <option value="Kg">Kg</option>
                        <option value="Gram">Gram</option>
                        <option value="Liter">Liter</option>
                        <option value="Dosis">Dosis</option>
                        <option value="Ampul">Ampul</option>
                        <option value="Pack">Pack</option>
                        <option value="Sachet">Sachet</option>
                    </select>
                </div>
            </div>

            <!-- Cara / Aplikasi Aktual (Mode Pemakaian) OR Sumber / Supplier (Mode Masuk) -->
            <div id="sectionAplikasiMethod">
                <label class="block text-xs font-bold text-slate-700 mb-1">Cara / Aplikasi Aktual</label>
                <select name="application_method" id="modalInputAplikasi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    <option value="Air minum">Air minum</option>
                    <option value="Campur pakan">Campur pakan</option>
                    <option value="Tetes mata">Tetes mata</option>
                    <option value="Semprot">Semprot</option>
                    <option value="Suntik paha / dada">Suntik paha / dada</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div id="sectionSupplier" style="display:none;">
                <label class="block text-xs font-bold text-slate-700 mb-1">Sumber / Supplier / Apotek</label>
                <input type="text" name="source" id="modalInputSource" placeholder="Contoh: CV Medika Farma / Apotek Hewan Sejahtera" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" name="date" id="modalInputDate" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu</label>
                    <input type="time" name="time" id="modalInputTime" value="{{ date('H:i') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
            </div>

            <!-- Keterangan Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan</label>
                <input type="text" name="notes" id="modalInputNotes" placeholder="Opsional (misal: pemberian jam 08:00 pagi pasca vaksinasi)..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Info Sync Alert Box -->
            <div id="modalNoticeBox" class="p-3 bg-purple-50 border border-purple-200 rounded-2xl text-xs">
                <b id="modalNoticeTitle" class="text-purple-900 block text-[11px] mb-0.5">↘ Otomatis potong Gudang Obat</b>
                <p id="modalNoticeDesc" class="text-[10.5px] text-purple-700 leading-relaxed">Saat disimpan, jumlah pemakaian menjadi transaksi keluar/pemakaian. Stok produk di Gudang Obat otomatis berkurang dan riwayat kegiatan tersimpan.</p>
            </div>

            <div class="pt-2 space-y-2">
                <button type="submit" id="btnSubmitModalObat" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Pemakaian Obat / Vaksin
                </button>
                <button type="button" onclick="closeModalInputObat()" class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all">
                    Batal
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL DATA OBAT / VAKSIN -->
<!-- ========================================================================= -->
<div id="modalDetailObat" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <button onclick="closeDetailObatModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </button>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Detail Obat / Vaksin</h3>
            </div>
            <span id="detailObatBadge" class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase">
                MASUK
            </span>
        </div>

        <!-- Big Card Figure -->
        <div class="mt-4 p-4 rounded-2xl bg-gradient-to-br from-purple-50/80 to-rose-50/40 border border-purple-200/60 flex items-center justify-between">
            <div>
                <span id="detailObatCategory" class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 uppercase inline-block mb-1">
                    OBAT
                </span>
                <h4 id="detailObatTitle" class="text-sm sm:text-base font-bold text-slate-800 leading-tight">
                    Vitamin B Complex
                </h4>
                <div id="detailObatQuantity" class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                    10 Botol
                </div>
            </div>

            <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center shrink-0 border border-purple-200 text-purple-700">
                <i data-lucide="flask-conical" class="w-7 h-7"></i>
            </div>
        </div>

        <!-- Meta Information List -->
        <div class="mt-4 bg-slate-50 rounded-2xl p-4 divide-y divide-slate-100 text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Tanggal
                </span>
                <span id="detailObatDate" class="font-bold text-slate-700">29 Mei 2025</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Waktu
                </span>
                <span id="detailObatTime" class="font-bold text-slate-700">09:30</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i> Petugas
                </span>
                <span id="detailObatPetugas" class="font-bold text-slate-700">Petugas01</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="store" class="w-3.5 h-3.5"></i> Sumber / Kandang
                </span>
                <span id="detailObatSource" class="font-bold text-slate-700">Kandang A1</span>
            </div>
            <div class="py-2.5 flex items-start justify-between gap-4">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Keterangan
                </span>
                <span id="detailObatNotes" class="font-medium text-slate-700 text-right">-</span>
            </div>
        </div>

        <!-- Aksi Section -->
        <div class="mt-5 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Aksi</span>
            
            <button id="btnEditObatFromDetail" class="w-full py-2.5 px-4 rounded-xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                <div class="flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit Data</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>

            <form id="formToggleStatusObatDetail" method="POST" action="">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white border border-amber-300 text-amber-700 hover:bg-amber-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                    <div class="flex items-center gap-2">
                        <i data-lucide="eye-off" class="w-4 h-4"></i>
                        <span id="btnToggleObatText">Nonaktifkan Data</span>
                    </div>
                    <i data-lucide="toggle-left" class="w-4 h-4"></i>
                </button>
            </form>

            <form id="formDeleteObatDetail" method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white border border-rose-300 text-rose-600 hover:bg-rose-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                    <div class="flex items-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Hapus Data</span>
                    </div>
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT OBAT / VAKSIN -->
<!-- ========================================================================= -->
<div id="modalEditObat" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-lg">
                    💊
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Edit Transaksi Obat & Vaksin</h3>
                    <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">↘ Gudang Obat</span>
                </div>
            </div>
            <button onclick="closeModalEditObat()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formEditObat" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" id="editObatType" value="keluar">

            <!-- Kloter & Blok (Muncul jika ada coop_id) -->
            <div id="editCoopRow" class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kloter</label>
                    <select id="editObatKloter" onchange="filterEditObatCoops()" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium">
                        @foreach($flocks as $flock)
                            <option value="{{ $flock->id }}">{{ $flock->name }} ({{ $flock->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Blok *</label>
                    <select name="coop_id" id="editObatCoop" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium">
                        @foreach($coops as $coop)
                            <option value="{{ $coop->id }}" data-flock="{{ $coop->flock_id }}">
                                {{ $coop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                <select name="category" id="editObatKategori" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium">
                    <option value="obat_cacing">Obat Cacing</option>
                    <option value="antibiotik">Antibiotik</option>
                    <option value="antikoksidia">Antikoksidia</option>
                    <option value="vitamin">Vitamin</option>
                    <option value="vaksin">Vaksin</option>
                    <option value="mineral">Mineral / Premix</option>
                    <option value="disinfektan">Disinfektan</option>
                    <option value="obat">Obat Lainnya</option>
                </select>
            </div>

            <!-- Produk / Nama Obat -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Produk / Nama Obat *</label>
                <input type="text" name="medicine_name" id="editObatProduk" required placeholder="Nama obat atau vaksin..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold">
            </div>

            <!-- Jumlah Pemakaian & Satuan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah *</label>
                    <input type="number" step="0.1" name="dosage" id="editObatJumlah" value="1" placeholder="Jumlah" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan *</label>
                    <select name="unit" id="editObatSatuan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium">
                        <option value="Botol">Botol</option>
                        <option value="Box">Box</option>
                        <option value="Kg">Kg</option>
                        <option value="Gram">Gram</option>
                        <option value="Liter">Liter</option>
                        <option value="Dosis">Dosis</option>
                        <option value="Ampul">Ampul</option>
                        <option value="Pack">Pack</option>
                        <option value="Sachet">Sachet</option>
                    </select>
                </div>
            </div>

            <!-- Cara / Aplikasi -->
            <div id="editAplikasiRow">
                <label class="block text-xs font-bold text-slate-700 mb-1">Cara / Aplikasi</label>
                <select name="application_method" id="editObatAplikasi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium">
                    <option value="Air minum">Air minum</option>
                    <option value="Campur pakan">Campur pakan</option>
                    <option value="Tetes mata">Tetes mata</option>
                    <option value="Semprot">Semprot</option>
                    <option value="Suntik paha / dada">Suntik paha / dada</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <!-- Sumber / Supplier (Untuk Masuk) -->
            <div id="editSourceRow" style="display:none;">
                <label class="block text-xs font-bold text-slate-700 mb-1">Sumber / Supplier / Apotek</label>
                <input type="text" name="source" id="editObatSource" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" id="editObatDate" name="date" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu</label>
                    <input type="time" id="editObatTime" name="time" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan</label>
                <textarea id="editObatNotes" name="notes" rows="2" placeholder="Opsional..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <div class="pt-2 space-y-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Perubahan
                </button>
                <button type="button" onclick="closeModalEditObat()" class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all">
                    Batal
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    // Inisialisasi Opsi Master Medis
    let masterMedicineOptions = [];
    document.addEventListener('DOMContentLoaded', () => {
        const medSel = document.getElementById('modalInputProduk');
        if (medSel) {
            masterMedicineOptions = Array.from(medSel.options).map(o => o.cloneNode(true));
        }

        const formWarehouseObat = document.getElementById('formWarehouseInputObat');
        if (formWarehouseObat) {
            formWarehouseObat.addEventListener('submit', function (e) {
                const sel = document.getElementById('modalInputProduk');
                if (sel && sel.value === 'custom') {
                    const customInput = document.getElementById('modalInputCustomName');
                    const customVal = customInput ? customInput.value.trim() : '';
                    if (!customVal) {
                        e.preventDefault();
                        alert('Silakan tuliskan nama produk obat/vaksin manual terlebih dahulu.');
                        if (customInput) customInput.focus();
                        return false;
                    }
                    sel.options[sel.selectedIndex].value = customVal;
                }
            });
        }

        filterModalCoops();
        updateModalMedDetails();
    });

    // 1. FILTER KATALOG INVENTARIS REAL-TIME
    let currentInventoryCategory = 'all';

    function setInventoryCategoryFilter(catKey, btn) {
        currentInventoryCategory = catKey;
        document.querySelectorAll('.inv-cat-pill').forEach(b => {
            b.classList.remove('bg-purple-700', 'text-white', 'shadow-sm');
            b.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
        });
        btn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200');
        btn.classList.add('bg-purple-700', 'text-white', 'shadow-sm');
        filterLiveInventory();
    }

    function filterLiveInventory() {
        const query = (document.getElementById('stockSearchInput')?.value || '').toLowerCase().trim();
        const cards = document.querySelectorAll('.inventory-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category') || '';
            const cardName = card.getAttribute('data-name') || '';

            const catMatch = (currentInventoryCategory === 'all' || cardCat === currentInventoryCategory);
            const nameMatch = (!query || cardName.includes(query) || cardCat.includes(query));

            if (catMatch && nameMatch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noMatch = document.getElementById('noInventoryMatch');
        if (noMatch) {
            noMatch.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // 2. MODAL INPUT OBAT (Sinkronisasi dengan /input)
    function switchModalMode(mode) {
        const inputType = document.getElementById('modalInputType');
        const tabKeluar = document.getElementById('tabModeKeluar');
        const tabMasuk = document.getElementById('tabModeMasuk');
        const sectionCoop = document.getElementById('sectionCoopSelect');
        const sectionApp = document.getElementById('sectionAplikasiMethod');
        const sectionSup = document.getElementById('sectionSupplier');
        const lblJml = document.getElementById('lblJumlahAktual');
        const noticeTitle = document.getElementById('modalNoticeTitle');
        const noticeDesc = document.getElementById('modalNoticeDesc');
        const btnSubmit = document.getElementById('btnSubmitModalObat');

        inputType.value = mode;

        if (mode === 'keluar') {
            tabKeluar.className = 'py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 bg-white text-rose-700 shadow-sm';
            tabMasuk.className = 'py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-emerald-700';

            sectionCoop.style.display = 'grid';
            sectionApp.style.display = 'block';
            sectionSup.style.display = 'none';

            lblJml.textContent = 'Jumlah Pemakaian Aktual *';
            noticeTitle.textContent = '↘ Otomatis potong Gudang Obat';
            noticeDesc.textContent = 'Saat disimpan, pemakaian obat ini menjadi transaksi keluar dan mengurangi stok Gudang Obat serta tercatat di Rekap.';
            btnSubmit.textContent = 'Simpan Pemakaian Obat / Vaksin';
            btnSubmit.className = 'w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98';
        } else {
            tabMasuk.className = 'py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 bg-white text-emerald-700 shadow-sm';
            tabKeluar.className = 'py-2 px-3 rounded-xl transition-all flex items-center justify-center gap-1.5 text-slate-600 hover:text-rose-700';

            sectionCoop.style.display = 'none';
            sectionApp.style.display = 'none';
            sectionSup.style.display = 'block';

            lblJml.textContent = 'Jumlah Masuk (Beli / Restok) *';
            noticeTitle.textContent = '↗ Menambah Stok Gudang Obat';
            noticeDesc.textContent = 'Saat disimpan, stok produk di Gudang Obat otomatis bertambah dan riwayat transaksi masuk tersimpan.';
            btnSubmit.textContent = 'Simpan Stok Masuk';
            btnSubmit.className = 'w-full py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition-all active:scale-98';
        }

        calcModalObatSisa();
    }

    function filterModalCoops() {
        const flockId = document.getElementById('modalInputKloter')?.value;
        const coopSelect = document.getElementById('modalInputCoop');
        if (!coopSelect) return;

        let firstMatch = null;
        Array.from(coopSelect.options).forEach(opt => {
            if (!flockId || opt.getAttribute('data-flock') == flockId) {
                opt.style.display = '';
                if (!firstMatch) firstMatch = opt;
            } else {
                opt.style.display = 'none';
            }
        });

        if (firstMatch && coopSelect.selectedOptions[0]?.style.display === 'none') {
            coopSelect.value = firstMatch.value;
        }
    }

    function filterModalMedicines() {
        const katSel = document.getElementById('modalInputCategory');
        const medSel = document.getElementById('modalInputProduk');
        if (!katSel || !medSel || !masterMedicineOptions.length) return;

        const selectedCategory = katSel.value;
        const currentVal = medSel.value;

        medSel.innerHTML = '';
        let matchingCount = 0;

        masterMedicineOptions.forEach(opt => {
            const optCat = opt.getAttribute('data-category');
            if (selectedCategory === 'all' || optCat === selectedCategory || opt.value === 'custom') {
                medSel.appendChild(opt.cloneNode(true));
                if (opt.value !== 'custom') matchingCount++;
            }
        });

        const countLbl = document.getElementById('modalMedFilterCount');
        if (countLbl) {
            countLbl.textContent = `${matchingCount} produk tersedia`;
        }

        let found = false;
        for (let i = 0; i < medSel.options.length; i++) {
            if (medSel.options[i].value === currentVal) {
                medSel.selectedIndex = i;
                found = true;
                break;
            }
        }
        if (!found && medSel.options.length > 0) {
            medSel.selectedIndex = 0;
        }

        updateModalMedDetails();
    }

    function updateModalMedDetails() {
        const sel = document.getElementById('modalInputProduk');
        if (!sel || sel.selectedIndex < 0) return;
        const opt = sel.options[sel.selectedIndex];
        if (!opt) return;

        const isCustom = (opt.value === 'custom');
        const customDiv = document.getElementById('modalFieldCustomMed');
        if (customDiv) {
            customDiv.style.display = isCustom ? 'block' : 'none';
            if (isCustom) {
                const customInput = document.getElementById('modalInputCustomName');
                if (customInput) customInput.focus();
            }
        }

        const dosage = opt.getAttribute('data-dosage') || '-';
        const app = opt.getAttribute('data-app') || '-';
        const sch = opt.getAttribute('data-sch') || '-';
        const unit = opt.getAttribute('data-unit') || 'Botol';
        const catLabel = opt.getAttribute('data-category-label') || '-';
        const indication = opt.getAttribute('data-indication') || '';
        const notes = opt.getAttribute('data-notes') || '';
        const stock = opt.getAttribute('data-stock') || '0';

        // Master UI
        const elBadge = document.getElementById('modalMedCategoryBadge');
        if (elBadge) elBadge.textContent = catLabel;

        const elIndicationBox = document.getElementById('modalMedIndicationBox');
        const elIndicationText = document.getElementById('modalMedIndicationText');
        if (elIndicationBox && elIndicationText) {
            if (indication) {
                elIndicationBox.style.display = 'block';
                elIndicationText.textContent = indication;
            } else {
                elIndicationBox.style.display = 'none';
            }
        }

        const elDosis = document.getElementById('modalMedDosisText');
        if (elDosis) elDosis.textContent = dosage;

        const elApp = document.getElementById('modalMedAplikasiText');
        if (elApp) elApp.textContent = app;

        const elSch = document.getElementById('modalMedJadwalText');
        if (elSch) elSch.textContent = sch;

        const elNotes = document.getElementById('modalMedNotesText');
        if (elNotes) elNotes.textContent = notes ? `ℹ️ Catatan: ${notes}` : '';

        // Auto sync unit select
        const unitSelect = document.getElementById('modalInputSatuan');
        if (unitSelect) {
            for (let i = 0; i < unitSelect.options.length; i++) {
                if (unitSelect.options[i].value.toLowerCase() === unit.toLowerCase()) {
                    unitSelect.selectedIndex = i;
                    break;
                }
            }
        }

        // Auto sync application method select
        const appSelect = document.getElementById('modalInputAplikasi');
        if (appSelect) {
            const appLower = app.toLowerCase();
            for (let i = 0; i < appSelect.options.length; i++) {
                const optVal = appSelect.options[i].value.toLowerCase();
                if (appLower.includes(optVal) || (optVal === 'air minum' && appLower.includes('air'))) {
                    appSelect.selectedIndex = i;
                    break;
                }
            }
        }

        // Update Stock UI
        const elStokNow = document.getElementById('modalStokSekarang');
        if (elStokNow) {
            elStokNow.textContent = `${stock} ${unit}`;
        }

        calcModalObatSisa();
    }

    function calcModalObatSisa() {
        const sel = document.getElementById('modalInputProduk');
        if (!sel || sel.selectedIndex < 0) return;
        const opt = sel.options[sel.selectedIndex];
        const stok = opt ? parseFloat(opt.getAttribute('data-stock') || 0) : 0;
        const unit = opt ? (opt.getAttribute('data-unit') || 'Botol') : 'Botol';
        const jml = parseFloat(document.getElementById('modalInputJumlah')?.value) || 0;
        const mode = document.getElementById('modalInputType')?.value || 'keluar';

        let sisa = 0;
        const elEstWrap = document.getElementById('modalStokEstimasiWrapper');
        const elSisa = document.getElementById('modalStokSetelahTx');

        if (mode === 'keluar') {
            sisa = Math.max(0, parseFloat((stok - jml).toFixed(2)));
            if (elEstWrap) elEstWrap.innerHTML = `Sisa Setelah Dipakai: <b id="modalStokSetelahTx" class="font-extrabold text-sm ${sisa <= 0 ? 'text-rose-600' : 'text-emerald-700'}">${sisa} ${unit}</b>`;
        } else {
            sisa = parseFloat((stok + jml).toFixed(2));
            if (elEstWrap) elEstWrap.innerHTML = `Total Stok Pasca Masuk: <b id="modalStokSetelahTx" class="font-extrabold text-sm text-emerald-700">${sisa} ${unit}</b>`;
        }
    }

    function openModalInputObat(defaultMode = 'keluar') {
        const modal = document.getElementById('modalInputObat');
        const content = modal.querySelector('div');
        switchModalMode(defaultMode);
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalInputObat() {
        const modal = document.getElementById('modalInputObat');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    function openInputObatForProduct(productName, mode = 'keluar') {
        openModalInputObat(mode);
        const katSel = document.getElementById('modalInputCategory');
        const medSel = document.getElementById('modalInputProduk');
        if (katSel) katSel.value = 'all';
        filterModalMedicines();

        for (let i = 0; i < medSel.options.length; i++) {
            if (medSel.options[i].value === productName) {
                medSel.selectedIndex = i;
                break;
            }
        }
        updateModalMedDetails();
    }

    // 3. DETAIL MODAL LOGIC
    function openDetailObatModal(data) {
        const modal = document.getElementById('modalDetailObat');
        const content = modal.querySelector('div');

        document.getElementById('detailObatTitle').textContent = data.title;
        document.getElementById('detailObatCategory').textContent = data.category || 'OBAT';
        document.getElementById('detailObatQuantity').textContent = data.quantity;
        document.getElementById('detailObatDate').textContent = data.date;
        document.getElementById('detailObatTime').textContent = data.time;
        document.getElementById('detailObatPetugas').textContent = data.petugas;
        document.getElementById('detailObatSource').textContent = data.source || '-';
        document.getElementById('detailObatNotes').textContent = data.notes || '-';

        const badge = document.getElementById('detailObatBadge');
        badge.textContent = data.type === 'keluar' ? 'PEMAKAIAN KANDANG' : 'MASUK (BELI / RESTOK)';
        if (data.type === 'masuk') {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-200';
        }

        document.getElementById('formDeleteObatDetail').action = `/gudang/${data.id}/destroy`;
        document.getElementById('formToggleStatusObatDetail').action = `/gudang/${data.id}/toggle-status`;
        document.getElementById('btnToggleObatText').textContent = data.is_nonaktif ? 'Aktifkan Data' : 'Nonaktifkan Data';

        document.getElementById('btnEditObatFromDetail').onclick = function() {
            closeDetailObatModal();
            openEditObatModal(data);
        };

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeDetailObatModal() {
        const modal = document.getElementById('modalDetailObat');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 4. EDIT MODAL LOGIC
    function filterEditObatCoops() {
        const flockId = document.getElementById('editObatKloter').value;
        const coopSelect = document.getElementById('editObatCoop');
        let firstMatch = null;

        Array.from(coopSelect.options).forEach(opt => {
            if (opt.getAttribute('data-flock') == flockId) {
                opt.style.display = '';
                if (!firstMatch) firstMatch = opt;
            } else {
                opt.style.display = 'none';
            }
        });

        if (firstMatch && coopSelect.selectedOptions[0]?.style.display === 'none') {
            coopSelect.value = firstMatch.value;
        }
    }

    function openEditObatModal(data) {
        const modal = document.getElementById('modalEditObat');
        const content = modal.querySelector('div');
        
        document.getElementById('formEditObat').action = `/gudang/${data.id}/update`;
        document.getElementById('editObatProduk').value = data.medicine_name || data.title || '';
        document.getElementById('editObatJumlah').value = parseFloat(data.dosage) || data.raw_quantity || data.quantity || 1;
        document.getElementById('editObatSatuan').value = data.unit || 'Botol';
        document.getElementById('editObatDate').value = data.raw_date || data.date;
        document.getElementById('editObatTime').value = data.time || '09:30';
        document.getElementById('editObatNotes').value = data.notes || '';
        document.getElementById('editObatType').value = data.type || 'keluar';

        const isKeluar = data.type === 'keluar';
        document.getElementById('editCoopRow').style.display = isKeluar ? 'grid' : 'none';
        document.getElementById('editAplikasiRow').style.display = isKeluar ? 'block' : 'none';
        document.getElementById('editSourceRow').style.display = isKeluar ? 'none' : 'block';
        if (data.source) {
            document.getElementById('editObatSource').value = data.source;
        }

        if (data.category || data.medicine_type) {
            const cat = (data.category || data.medicine_type).toLowerCase();
            const catSelect = document.getElementById('editObatKategori');
            if (Array.from(catSelect.options).some(o => o.value === cat)) {
                catSelect.value = cat;
            }
        }

        if (data.application_method) {
            const appSelect = document.getElementById('editObatAplikasi');
            if (Array.from(appSelect.options).some(o => o.value === data.application_method)) {
                appSelect.value = data.application_method;
            }
        }

        if (data.coop_id) {
            const coopSelect = document.getElementById('editObatCoop');
            coopSelect.value = data.coop_id;
            const opt = coopSelect.selectedOptions[0];
            if (opt && opt.getAttribute('data-flock')) {
                document.getElementById('editObatKloter').value = opt.getAttribute('data-flock');
            }
        }

        filterEditObatCoops();

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalEditObat() {
        const modal = document.getElementById('modalEditObat');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 5. DROPDOWN MENU
    function toggleActionDropdown(btn) {
        event.stopPropagation();
        const dropdown = btn.parentElement.querySelector('.action-dropdown');
        document.querySelectorAll('.action-dropdown').forEach(d => {
            if (d !== dropdown) d.classList.add('hidden');
        });
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', () => {
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));
    });
</script>
@endpush
@endsection
