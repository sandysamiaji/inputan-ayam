@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Top Navigation Back & Title Bar (Sesuai Gambar Mockup 2 Layar 1) -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('warehouse.index', array_filter(['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Gudang Telur</h1>
                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                    <p class="text-xs text-slate-400">Manajemen stok & mutasi telur</p>
                    @if(!empty($startDate) && !empty($endDate))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200/80 text-[10px] font-extrabold text-maroon-800">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                            <a href="{{ route('warehouse.telur', ['tab' => $tab, 'q' => $search]) }}" class="hover:text-rose-600 ml-0.5" title="Hapus Filter Tanggal">×</a>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stok Quick Stat Banner -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 bg-white px-4 py-2.5 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Stok</span>
                <span class="text-sm sm:text-base font-extrabold {{ $stokSaatIni < 0 || $stokSaatIniKg < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($stokSaatIni, 0, ',', '.') }} Peti & {{ number_format($stokSaatIniKg, 0, ',', '.') }} Kg</span>
                <span class="text-[10px] block font-medium {{ $stokSaatIni < 0 || $stokSaatIniKg < 0 ? 'text-rose-500' : 'text-slate-400' }}">{{ $stokSaatIni < 0 || $stokSaatIniKg < 0 ? 'Defisit Stok' : 'Stok Tersedia' }}</span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Masuk</span>
                <span class="text-xs sm:text-sm font-bold text-slate-700">{{ number_format($totalMasuk, 0, ',', '.') }} Peti & {{ number_format($totalMasukKg, 0, ',', '.') }} Kg</span>
                <span class="text-[10px] block text-slate-400 font-medium">Produksi Kandang</span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-maroon-800 block flex items-center gap-1">
                    <i data-lucide="shopping-cart" class="w-3 h-3"></i> Terjual (nochifram)
                </span>
                <span class="text-xs sm:text-sm font-extrabold text-maroon-800">{{ number_format($petiSold, 0, ',', '.') }} Peti & {{ number_format($kgSold, 0, ',', '.') }} Kg</span>
            </div>
        </div>
    </div>

    <!-- Banner Ringkasan Integrasi Penjualan (1 DB nochifram) -->
    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-rose-50 via-white to-amber-50 border border-rose-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-maroon-800 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="database" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Sinkronisasi Barang Keluar Terhubung</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">1 DB nochifram</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Data barang keluar otomatis memotong stok telur berdasarkan penjualan <b>{{ number_format($petiSold, 0, ',', '.') }} Peti</b> dan <b>{{ number_format($kgSold, 1, ',', '.') }} Kg</b> di kasir/toko.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-center">
            <a href="{{ route('warehouse.telur', array_filter(['tab' => 'penjualan', 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="px-3 py-1.5 rounded-xl bg-white border border-rose-200 text-maroon-800 hover:bg-rose-50 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                <span>Lihat {{ $transactionCount }} Penjualan</span>
            </a>
        </div>
    </div>

    <!-- Search Bar & Add Button (Sesuai Mockup) -->
    <div class="flex items-center gap-2 sm:gap-3">
        <form method="GET" action="{{ route('warehouse.telur') }}" class="flex-1 relative">
            <input type="hidden" name="tab" value="{{ $tab }}">
            @if(!empty($startDate)) <input type="hidden" name="start_date" value="{{ $startDate }}"> @endif
            @if(!empty($endDate)) <input type="hidden" name="end_date" value="{{ $endDate }}"> @endif
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}" 
                placeholder="Cari data telur atau penjualan..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs sm:text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 shadow-sm transition-all"
            >
            @if($search)
                <a href="{{ route('warehouse.telur', array_filter(['tab' => $tab, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Filter Tabs: Semua | Masuk | Keluar | Penjualan nochifram -->
    <div class="flex items-center border-b border-slate-200 gap-4 sm:gap-8 px-1 overflow-x-auto">
        <a href="{{ route('warehouse.telur', array_filter(['tab' => 'semua', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'semua' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Semua Data
            @if($tab === 'semua')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.telur', array_filter(['tab' => 'masuk', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'masuk' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Masuk (Produksi)
            @if($tab === 'masuk')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.telur', array_filter(['tab' => 'keluar', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'keluar' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Telur Rusak
            @if($tab === 'keluar')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.telur', array_filter(['tab' => 'penjualan', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'penjualan' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>Penjualan (nochifram)</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ $tab === 'penjualan' ? 'bg-maroon-800 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $transactionCount }}</span>
            @if($tab === 'penjualan')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
    </div>

    <!-- Jika Tab Penjualan dipilih, tampilkan daftar transaksi penjualan nochifram -->
    @if($tab === 'penjualan')
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Transaksi Keluar / Terjual di Aplikasi Penjualan nochifram</span>
                <span class="text-xs font-bold text-maroon-800">Total: {{ $transactionCount }} Transaksi</span>
            </div>
            @forelse($salesList as $sale)
                @php
                    $petugasName = $sale->user_name ?? ($sale->user_username ?? null);
                    $tripInfo = $sale->driver_name ? $sale->driver_name . ($sale->trip_code ? " ({$sale->trip_code})" : '') : ($sale->trip_code ?? null);
                    $tooltipText = "Petugas Input: " . ($petugasName ?: 'Kasir System') . ($tripInfo ? " | Perjalanan/Armada: {$tripInfo}" : '');
                @endphp
                <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex items-center justify-between gap-3" title="{{ $tooltipText }}">
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200/80 text-maroon-800 flex items-center justify-center shrink-0 shadow-inner">
                            <i data-lucide="shopping-bag" class="w-6 h-6 text-maroon-800"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-maroon-100 text-maroon-800 border border-maroon-200">
                                    TERJUAL • {{ $sale->unit }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-400 font-mono">#{{ $sale->invoice_no }}</span>
                            </div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-900 truncate">
                                {{ $sale->item_name }} — <span class="text-maroon-800 font-black">{{ number_format($sale->quantity, 0, ',', '.') }} {{ $sale->unit }}</span>
                            </h2>
                            <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                                <span>Pembeli: <b>{{ $sale->customer_name }}</b></span>
                                <span>•</span>
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $sale->payment_status }} ({{ $sale->payment_method }})</span>
                                <span>•</span>
                                <span class="text-slate-400">{{ \Carbon\Carbon::parse($sale->date)->translatedFormat('d M Y') }}</span>
                                @if($petugasName)
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200" title="Petugas Input: {{ $petugasName }}">
                                        <i data-lucide="user-check" class="w-3 h-3"></i>
                                        <span>Input: <b>{{ $petugasName }}</b></span>
                                    </span>
                                @endif
                                @if($tripInfo)
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200" title="Perjalanan / Armada: {{ $tripInfo }}">
                                        <i data-lucide="truck" class="w-3 h-3"></i>
                                        <span>Armada: <b>{{ $tripInfo }}</b></span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs sm:text-sm font-black text-maroon-800 block">-{{ number_format($sale->quantity, 0, ',', '.') }} {{ $sale->unit }}</span>
                        <span class="text-[10px] text-slate-400">Barang Keluar</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-2xl border border-slate-100">
                    <p class="text-xs text-slate-400">Belum ada transaksi penjualan telur tercatat.</p>
                </div>
            @endforelse
        </div>
    @else

    <!-- List Data Telur (Sesuai Gambar Mockup 2 Layar 1) -->
    <div class="space-y-2.5">
        @forelse($items as $item)
            @php
                $isMasuk = $item->type === 'masuk';
                $isNonaktif = str_starts_with(trim($item->notes ?? ''), '[NONAKTIF]');
                $displayNotes = $isNonaktif ? trim(substr(trim($item->notes), strlen('[NONAKTIF]'))) : $item->notes;
            @endphp
            <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex items-center justify-between gap-3 {{ $isNonaktif ? 'opacity-60 bg-slate-50' : '' }}">
                
                <!-- Left Details & Icon (Click to open Detail) -->
                <div class="flex items-center gap-3.5 min-w-0 cursor-pointer flex-1" onclick="openDetailModal({{ json_encode([
                    'id' => $item->id,
                    'title' => $item->item_name,
                    'type' => $item->type,
                    'quantity' => number_format($item->quantity, 0, ',', '.') . ' ' . $item->unit,
                    'raw_quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'notes' => $displayNotes,
                    'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                    'raw_date' => $item->date->format('Y-m-d'),
                    'time' => $item->created_at ? $item->created_at->format('H:i') : '06:30',
                    'petugas' => $item->user ? $item->user->name : 'Petugas01',
                    'source' => $item->source ?? 'A1, A2, A3',
                    'is_nonaktif' => $isNonaktif
                ]) }})">
                    
                    <!-- Egg Icon -->
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200/60 flex items-center justify-center shrink-0 shadow-inner">
                        <svg class="w-7 h-7 fill-amber-500 drop-shadow-sm" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C8.13 2 5 6.48 5 12c0 4.42 3.13 8 7 8s7-3.58 7-8c0-5.52-3.13-10-7-10z"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <!-- Badge Masuk / Keluar / Telur Rusak -->
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isMasuk ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $item->type === 'keluar' ? 'TELUR RUSAK' : 'MASUK (PRODUKSI)' }}
                            </span>
                            @if($isNonaktif)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">NONAKTIF</span>
                            @endif
                        </div>

                        <!-- Judul Transaksi -->
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $item->item_name }}</h2>

                        <!-- Jumlah Peti / Butir -->
                        <p class="text-xs font-extrabold text-slate-800">
                            {{ number_format($item->quantity, $item->unit === 'Peti' ? 1 : 0, ',', '.') }} {{ $item->unit }}
                            @if(str_contains(strtolower($item->notes ?? ''), 'butir'))
                                <span class="text-slate-400 font-normal text-[11px]">
                                    ({{ Str::after($item->notes, '(') ? Str::before(Str::after($item->notes, '('), ')') : '' }})
                                </span>
                            @endif
                        </p>

                        <!-- Tanggal & Petugas -->
                        <div class="text-[10px] sm:text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                            <span>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }} {{ $item->created_at ? $item->created_at->format('H:i') : '' }}</span>
                            <span>•</span>
                            <span class="text-slate-500 font-medium">{{ $item->user ? $item->user->name : 'Petugas' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Action Menu (3 Dots) -->
                <div class="relative shrink-0" x-data="{ open: false }">
                    <button onclick="toggleActionDropdown(this)" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">
                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                    </button>
                    <!-- Dropdown Content -->
                    <div class="action-dropdown hidden absolute right-0 top-9 z-20 w-44 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 text-xs font-semibold text-slate-700">
                        <button onclick="openDetailModal({{ json_encode([
                            'id' => $item->id,
                            'title' => $item->item_name,
                            'type' => $item->type,
                            'quantity' => number_format($item->quantity, 0, ',', '.') . ' ' . $item->unit,
                            'raw_quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'notes' => $displayNotes,
                            'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                            'raw_date' => $item->date->format('Y-m-d'),
                            'time' => $item->created_at ? $item->created_at->format('H:i') : '06:30',
                            'petugas' => $item->user ? $item->user->name : 'Petugas01',
                            'source' => $item->source ?? 'A1, A2, A3',
                            'is_nonaktif' => $isNonaktif
                        ]) }})" class="w-full px-3.5 py-2 text-left hover:bg-slate-50 flex items-center gap-2">
                            <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Lihat Detail</span>
                        </button>
                        <button onclick="openEditModal({{ json_encode([
                            'id' => $item->id,
                            'title' => $item->item_name,
                            'type' => $item->type,
                            'quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'source' => $item->source,
                            'notes' => $displayNotes,
                            'date' => $item->date->format('Y-m-d'),
                            'time' => $item->created_at ? $item->created_at->format('H:i') : '06:30',
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
                        <form method="POST" action="{{ route('warehouse.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="w-full">
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
                    <i data-lucide="egg" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada data telur</h3>
                <p class="text-xs text-slate-400 mt-1">Belum ada catatan mutasi telur untuk filter ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>
    @endif

</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL DATA TELUR (Sesuai Gambar Mockup 2 Layar 2) -->
<!-- ========================================================================= -->
<div id="modalDetailTelur" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <!-- Header Detail with Back/Close -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <button onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </button>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Detail Data Telur</h3>
            </div>
            <span id="detailBadge" class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase">
                MASUK
            </span>
        </div>

        <!-- Big Card Hero Figure & Egg Image -->
        <div class="mt-5 p-4 rounded-2xl bg-gradient-to-br from-amber-50/80 to-orange-50/40 border border-amber-200/60 flex items-center justify-between">
            <div>
                <h4 id="detailTitle" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Produksi Telur</h4>
                <div id="detailQuantity" class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                    2.480 Peti
                </div>
                <div id="detailSubQuantity" class="text-xs font-medium text-slate-500 mt-0.5">
                    (62.000 Butir)
                </div>
            </div>

            <!-- Big Egg 3D SVG Image -->
            <div class="w-16 h-16 rounded-full bg-white shadow-md flex items-center justify-center shrink-0 border border-amber-200">
                <svg class="w-10 h-10 fill-amber-500 drop-shadow-md" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C8.13 2 5 6.48 5 12c0 4.42 3.13 8 7 8s7-3.58 7-8c0-5.52-3.13-10-7-10z"/>
                </svg>
            </div>
        </div>

        <!-- Meta Information List (Tanggal, Waktu, Petugas, Asal, Keterangan) -->
        <div class="mt-4 bg-slate-50 rounded-2xl p-4 divide-y divide-slate-100 text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Tanggal
                </span>
                <span id="detailDate" class="font-bold text-slate-700">29 Mei 2025</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Waktu
                </span>
                <span id="detailTime" class="font-bold text-slate-700">06:30</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i> Petugas
                </span>
                <span id="detailPetugas" class="font-bold text-slate-700">Petugas01</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5"></i> Asal / Tujuan
                </span>
                <span id="detailSource" class="font-bold text-slate-700">A1, A2, A3</span>
            </div>
            <div class="py-2.5 flex items-start justify-between gap-4">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Keterangan
                </span>
                <span id="detailNotes" class="font-medium text-slate-700 text-right">Produksi harian pagi</span>
            </div>
        </div>

        <!-- Aksi Section (Sesuai Mockup Layar 2: Edit Data, Nonaktifkan Data, Hapus Data) -->
        <div class="mt-5 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Aksi</span>
            
            <!-- Tombol Edit Data -->
            <button id="btnEditFromDetail" class="w-full py-2.5 px-4 rounded-xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                <div class="flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit Data</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>

            <!-- Tombol Nonaktifkan Data -->
            <form id="formToggleStatusDetail" method="POST" action="">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white border border-amber-300 text-amber-700 hover:bg-amber-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                    <div class="flex items-center gap-2">
                        <i data-lucide="eye-off" class="w-4 h-4"></i>
                        <span id="btnToggleText">Nonaktifkan Data</span>
                    </div>
                    <i data-lucide="toggle-left" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Tombol Hapus Data -->
            <form id="formDeleteDetail" method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?');">
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
<!-- MODAL INPUT TRANSAKSI TELUR (Tambah Stok Telur Masuk / Keluar) -->
<!-- ========================================================================= -->
<div id="modalInputTelur" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center font-bold">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Input Transaksi Telur</h3>
            </div>
            <button onclick="closeModalInputTelur()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('warehouse.store') }}" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="category" value="telur">

            <!-- Pilihan Jenis: Masuk / Keluar -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="masuk" checked class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            <span>Telur Masuk</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="keluar" class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-rose-600 peer-checked:bg-rose-50 text-slate-600 peer-checked:text-rose-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                            <span>Telur Keluar</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Nama Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Transaksi / Kategori Telur *</label>
                <input type="text" name="item_name" required placeholder="Contoh: Produksi Telur, Penjualan Telur, Telur Retak" list="telurItemNames" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                <datalist id="telurItemNames">
                    <option value="Produksi Telur">
                    <option value="Penjualan Telur">
                    <option value="Penjualan Telur Partai Besar">
                    <option value="Penjualan Telur Eceran">
                    <option value="Koreksi Stok Telur">
                </datalist>
            </div>

            <!-- Jumlah & Satuan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Peti *</label>
                    <input type="number" step="0.01" name="quantity" required placeholder="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan *</label>
                    <select name="unit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white">
                        <option value="Peti" selected>Peti</option>
                        <option value="Kg">Kg</option>
                        <option value="Butir">Butir</option>
                    </select>
                </div>
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu</label>
                    <input type="time" name="time" value="{{ date('H:i') }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
            </div>

            <!-- Asal / Tujuan (Kandang / Pembeli) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Asal / Tujuan</label>
                <input type="text" name="source" placeholder="Contoh: A1, A2, A3 atau Nama Toko/Distributor" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Produksi harian pagi atau catatan khusus" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Transaksi Telur
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT DATA TELUR -->
<!-- ========================================================================= -->
<div id="modalEditTelur" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Edit Transaksi Telur</h3>
            </div>
            <button onclick="closeModalEditTelur()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formEditTelur" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <!-- Jenis Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" id="editTypeMasuk" name="type" value="masuk" class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            <span>Masuk</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" id="editTypeKeluar" name="type" value="keluar" class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-rose-600 peer-checked:bg-rose-50 text-slate-600 peer-checked:text-rose-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                            <span>Keluar</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Nama Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Transaksi *</label>
                <input type="text" id="editItemName" name="item_name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Jumlah & Satuan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah *</label>
                    <input type="number" step="0.01" id="editQuantity" name="quantity" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan *</label>
                    <select id="editUnit" name="unit" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white">
                        <option value="Peti">Peti</option>
                        <option value="Kg">Kg</option>
                        <option value="Butir">Butir</option>
                    </select>
                </div>
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" id="editDate" name="date" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu</label>
                    <input type="time" id="editTime" name="time" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
            </div>

            <!-- Asal / Tujuan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Asal / Tujuan</label>
                <input type="text" id="editSource" name="source" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan</label>
                <textarea id="editNotes" name="notes" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    let currentActiveItem = null;

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

    // 1. DETAIL MODAL LOGIC
    function openDetailModal(data) {
        currentActiveItem = data;
        const modal = document.getElementById('modalDetailTelur');
        const content = modal.querySelector('div');

        // Isi Data
        document.getElementById('detailTitle').textContent = data.title;
        document.getElementById('detailQuantity').textContent = data.quantity;
        
        // Cek sub quantity butir
        const subElem = document.getElementById('detailSubQuantity');
        if (data.notes && data.notes.includes('(')) {
            const matches = data.notes.match(/\((.*?)\)/);
            subElem.textContent = matches ? '(' + matches[1] + ')' : '';
            subElem.classList.remove('hidden');
        } else {
            subElem.classList.add('hidden');
        }

        document.getElementById('detailDate').textContent = data.date;
        document.getElementById('detailTime').textContent = data.time;
        document.getElementById('detailPetugas').textContent = data.petugas;
        document.getElementById('detailSource').textContent = data.source || '-';
        document.getElementById('detailNotes').textContent = data.notes || '-';

        // Badge
        const badge = document.getElementById('detailBadge');
        badge.textContent = data.type === 'keluar' ? 'TELUR RUSAK' : (data.type === 'masuk' ? 'MASUK (PRODUKSI)' : data.type.toUpperCase());
        if (data.type === 'masuk') {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-200';
        }

        // Form actions
        document.getElementById('formDeleteDetail').action = `/gudang/${data.id}/destroy`;
        document.getElementById('formToggleStatusDetail').action = `/gudang/${data.id}/toggle-status`;
        document.getElementById('btnToggleText').textContent = data.is_nonaktif ? 'Aktifkan Data' : 'Nonaktifkan Data';

        // Bind Edit button
        document.getElementById('btnEditFromDetail').onclick = function() {
            closeDetailModal();
            openEditModal(data);
        };

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeDetailModal() {
        const modal = document.getElementById('modalDetailTelur');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 2. INPUT MODAL
    function openModalInputTelur() {
        const modal = document.getElementById('modalInputTelur');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalInputTelur() {
        const modal = document.getElementById('modalInputTelur');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 3. EDIT MODAL
    function openEditModal(data) {
        const modal = document.getElementById('modalEditTelur');
        const content = modal.querySelector('div');
        
        document.getElementById('formEditTelur').action = `/gudang/${data.id}/update`;
        document.getElementById('editItemName').value = data.title;
        document.getElementById('editQuantity').value = data.raw_quantity || data.quantity;
        document.getElementById('editUnit').value = data.unit || 'Peti';
        document.getElementById('editDate').value = data.raw_date || data.date;
        document.getElementById('editTime').value = data.time || '06:30';
        document.getElementById('editSource').value = data.source || '';
        document.getElementById('editNotes').value = data.notes || '';

        if (data.type === 'masuk') {
            document.getElementById('editTypeMasuk').checked = true;
        } else {
            document.getElementById('editTypeKeluar').checked = true;
        }

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalEditTelur() {
        const modal = document.getElementById('modalEditTelur');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }
</script>
@endpush
@endsection
