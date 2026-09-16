@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Top Navigation Back & Title Bar (Sesuai Gambar Mockup 2 Layar 3) -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('warehouse.index', array_filter(['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Gudang Pakan</h1>
                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                    <p class="text-xs text-slate-400">Manajemen stok & konsumsi pakan ayam</p>
                    @if(!empty($startDate) && !empty($endDate))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-[10px] font-extrabold text-emerald-800">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                            <a href="{{ route('warehouse.pakan', ['tab' => $tab, 'q' => $search]) }}" class="hover:text-emerald-600 ml-0.5" title="Hapus Filter Tanggal">×</a>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stok Quick Stat Banner -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 bg-white px-4 py-2.5 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Stok</span>
                <span class="text-sm sm:text-base font-extrabold {{ $stokSaatIni < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($stokSaatIni, 0, ',', '.') }} Kg <span class="text-xs font-semibold {{ $stokSaatIni < 0 ? 'text-rose-600' : 'text-emerald-700' }}">({{ number_format($currentStockKarung, 1, ',', '.') }} Krg)</span></span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Masuk</span>
                <span class="text-xs sm:text-sm font-bold text-slate-700">{{ number_format($totalMasuk, 0, ',', '.') }} Kg ({{ number_format($purchasedKarung, 1, ',', '.') }} Krg)</span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-500 block">Konsumsi Kandang</span>
                <span class="text-xs sm:text-sm font-bold text-slate-700">{{ number_format($consumptionKg, 0, ',', '.') }} Kg ({{ number_format($consumptionKarung, 1, ',', '.') }} Krg)</span>
            </div>
            <div class="hidden md:block h-7 w-px bg-slate-200"></div>
            <div class="hidden md:block text-right">
                <span class="text-[10px] uppercase font-bold text-maroon-800 block flex items-center gap-1">
                    <i data-lucide="shopping-cart" class="w-3 h-3"></i> Terjual (nochifram)
                </span>
                <span class="text-xs sm:text-sm font-extrabold text-maroon-800">{{ number_format($karungSold, 0, ',', '.') }} Karung @if($kgSold > 0)& {{ number_format($kgSold, 0, ',', '.') }} Kg @endif</span>
            </div>
        </div>
    </div>

    <!-- Banner Ringkasan Integrasi Penjualan (1 DB nochifram) -->
    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-emerald-50 via-white to-rose-50 border border-emerald-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="database" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Sinkronisasi Pakan Keluar Terhubung</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">1 DB nochifram</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Mencakup 2 jenis pengeluaran: <b>Pemberian pakan kandang ({{ number_format($consumptionKg, 0, ',', '.') }} Kg)</b> dan <b>Pakan terjual ke pelanggan ({{ number_format($karungSold, 0, ',', '.') }} Karung)</b> dari aplikasi penjualan nochifram.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-center">
            <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'penjualan', 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="px-3 py-1.5 rounded-xl bg-white border border-rose-200 text-maroon-800 hover:bg-rose-50 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                <span>Lihat Penjualan Pakan</span>
            </a>
        </div>
    </div>

    <!-- Search Bar & Add Button (Sesuai Mockup) -->
    <div class="flex items-center gap-2 sm:gap-3">
        <form method="GET" action="{{ route('warehouse.pakan') }}" class="flex-1 relative">
            <input type="hidden" name="tab" value="{{ $tab }}">
            @if(!empty($startDate)) <input type="hidden" name="start_date" value="{{ $startDate }}"> @endif
            @if(!empty($endDate)) <input type="hidden" name="end_date" value="{{ $endDate }}"> @endif
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}" 
                placeholder="Cari data pakan..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs sm:text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 shadow-sm transition-all"
            >
            @if($search)
                <a href="{{ route('warehouse.pakan', array_filter(['tab' => $tab, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>

        <button onclick="openModalInputPakan()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-2xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs sm:text-sm font-bold shadow-md shadow-maroon-900/20 transition-all active:scale-95 shrink-0">
            <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
            <span>Input Pakan</span>
        </button>
    </div>

    <!-- Filter Tabs: Semua | Masuk | Keluar | Penjualan -->
    <div class="flex items-center border-b border-slate-200 gap-4 sm:gap-8 px-1 overflow-x-auto">
        <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'semua', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'semua' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Semua Data
            @if($tab === 'semua')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'masuk', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'masuk' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Masuk (Beli)
            @if($tab === 'masuk')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'keluar', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'keluar' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Pemberian Pakan
            @if($tab === 'keluar')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.pakan', array_filter(['tab' => 'penjualan', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'penjualan' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>Penjualan Pakan (nochifram)</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ $tab === 'penjualan' ? 'bg-maroon-800 text-white' : 'bg-slate-200 text-slate-600' }}">{{ count($salesList) }}</span>
            @if($tab === 'penjualan')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
    </div>

    <!-- Jika Tab Penjualan dipilih, tampilkan daftar transaksi penjualan pakan nochifram -->
    @if($tab === 'penjualan')
        <div class="space-y-2.5">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Transaksi Pakan Terjual di Aplikasi nochifram</span>
                <span class="text-xs font-bold text-maroon-800">Total: {{ count($salesList) }} Transaksi</span>
            </div>
            @forelse($salesList as $sale)
                @php
                    $petugasName = $sale->user_name ?? ($sale->user_username ?? null);
                    $tripInfo = $sale->driver_name ? $sale->driver_name . ($sale->trip_code ? " ({$sale->trip_code})" : '') : ($sale->trip_code ?? null);
                    $tooltipText = "Petugas Input: " . ($petugasName ?: 'Kasir System') . ($tripInfo ? " | Perjalanan/Armada: {$tripInfo}" : '');
                @endphp
                <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex items-center justify-between gap-3" title="{{ $tooltipText }}">
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 flex items-center justify-center shrink-0 shadow-inner">
                            <i data-lucide="package-check" class="w-6 h-6 text-emerald-700"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    PAKAN TERJUAL • {{ $sale->unit }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-400 font-mono">#{{ $sale->invoice_no }}</span>
                            </div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-900 truncate">
                                {{ $sale->item_name }} — <span class="text-emerald-700 font-black">{{ number_format($sale->quantity, 0, ',', '.') }} {{ $sale->unit }}</span>
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
                        <span class="text-xs sm:text-sm font-black text-rose-600 block">-{{ number_format($sale->quantity, 0, ',', '.') }} {{ $sale->unit }}</span>
                        <span class="text-[10px] text-slate-400">Barang Keluar</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-2xl border border-slate-100">
                    <p class="text-xs text-slate-400">Belum ada transaksi penjualan pakan tercatat.</p>
                </div>
            @endforelse
        </div>
    @else

    <!-- List Data Pakan (Sesuai Gambar Mockup 2 Layar 3) -->
    <div class="space-y-2.5">
        @forelse($items as $item)
            @php
                $isMasuk = $item->type === 'masuk';
                $isNonaktif = str_starts_with(trim($item->notes ?? ''), '[NONAKTIF]');
                $displayNotes = $isNonaktif ? trim(substr(trim($item->notes), strlen('[NONAKTIF]'))) : $item->notes;
            @endphp
            <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex items-center justify-between gap-3 {{ $isNonaktif ? 'opacity-60 bg-slate-50' : '' }}">
                
                <!-- Left Details & Icon (Click to open Detail) -->
                <div class="flex items-center gap-3.5 min-w-0 cursor-pointer flex-1" onclick="openDetailPakanModal({{ json_encode([
                    'id' => $item->id,
                    'title' => $item->item_name,
                    'type' => $item->type,
                    'quantity' => number_format($item->quantity, 0, ',', '.') . ' ' . $item->unit,
                    'raw_quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'notes' => $displayNotes,
                    'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                    'raw_date' => $item->date->format('Y-m-d'),
                    'time' => $item->created_at ? $item->created_at->format('H:i') : '07:10',
                    'petugas' => $item->user ? $item->user->name : 'Petugas',
                    'kandang' => $item->source ?? 'Semua Blok',
                    'jenis_pakan' => str_contains(strtolower($item->notes ?? ''), 'layer') ? 'Pakan Layer' : (str_contains(strtolower($item->notes ?? ''), 'starter') ? 'Pakan Starter' : 'Pakan Komplit'),
                    'is_nonaktif' => $isNonaktif
                ]) }})">
                    
                    <!-- Feed Sack Icon -->
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center shrink-0 shadow-inner">
                        <svg class="w-7 h-7 fill-emerald-600 drop-shadow-sm" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 6h-2.28a4.99 4.99 0 0 0-9.44 0H5a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3zm-7-2c1.3 0 2.4.84 2.82 2h-5.64A3.003 3.003 0 0 1 12 4zm0 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <!-- Badge Masuk / Keluar / Pemberian Pakan -->
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isMasuk ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $item->type === 'keluar' ? 'PEMBERIAN PAKAN' : 'MASUK (BELI)' }}
                            </span>
                            @if($isNonaktif)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">NONAKTIF</span>
                            @endif
                        </div>

                        <!-- Judul Transaksi (Pembelian Pakan / Pemberian Pakan) -->
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $item->item_name }}</h2>

                        <!-- Jumlah Kg -->
                        <p class="text-xs font-extrabold text-slate-800">
                            {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit }}
                        </p>

                        <!-- Tanggal & Petugas -->
                        <div class="text-[10px] sm:text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                            <span>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }} {{ $item->created_at ? $item->created_at->format('H:i') : '' }}</span>
                            <span>•</span>
                            <span class="text-slate-500 font-medium">{{ $item->user ? $item->user->name : 'Petugas' }}</span>
                            @if($item->source)
                                <span>•</span>
                                <span class="text-slate-500">{{ $item->source }}</span>
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
                        <button onclick="openDetailPakanModal({{ json_encode([
                            'id' => $item->id,
                            'title' => $item->item_name,
                            'type' => $item->type,
                            'quantity' => number_format($item->quantity, 0, ',', '.') . ' ' . $item->unit,
                            'raw_quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'notes' => $displayNotes,
                            'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                            'raw_date' => $item->date->format('Y-m-d'),
                            'time' => $item->created_at ? $item->created_at->format('H:i') : '07:10',
                            'petugas' => $item->user ? $item->user->name : 'Petugas01',
                            'kandang' => $item->source ?? 'A1, A2, A3',
                            'jenis_pakan' => str_contains(strtolower($item->notes ?? ''), 'layer') ? 'Pakan Layer' : (str_contains(strtolower($item->notes ?? ''), 'starter') ? 'Pakan Starter' : 'Pakan Komplit'),
                            'is_nonaktif' => $isNonaktif
                        ]) }})" class="w-full px-3.5 py-2 text-left hover:bg-slate-50 flex items-center gap-2">
                            <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Lihat Detail</span>
                        </button>
                        <button onclick="openEditPakanModal({{ json_encode([
                            'id' => $item->id,
                            'title' => $item->item_name,
                            'type' => $item->type,
                            'quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'source' => $item->source,
                            'notes' => $displayNotes,
                            'date' => $item->date->format('Y-m-d'),
                            'time' => $item->created_at ? $item->created_at->format('H:i') : '07:10',
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
                        <form method="POST" action="{{ route('warehouse.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data transaksi pakan ini?');" class="w-full">
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
                    <i data-lucide="wheat" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada data pakan</h3>
                <p class="text-xs text-slate-400 mt-1">Belum ada catatan transaksi pakan untuk filter ini.</p>
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
<!-- MODAL DETAIL DATA PAKAN (Sesuai Gambar Mockup 2 Layar 4) -->
<!-- ========================================================================= -->
<div id="modalDetailPakan" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <!-- Header Detail with Back/Close -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <button onclick="closeDetailPakanModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </button>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Gudang Pakan - Detail</h3>
            </div>
            <span id="detailPakanBadge" class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase">
                KELUAR
            </span>
        </div>

        <!-- Big Card Hero Figure & Sack Image -->
        <div class="mt-5 p-4 rounded-2xl bg-gradient-to-br from-emerald-50/80 to-teal-50/40 border border-emerald-200/60 flex items-center justify-between">
            <div>
                <h4 id="detailPakanTitle" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pemakaian Pakan</h4>
                <div id="detailPakanQuantity" class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                    1.820 Kg
                </div>
            </div>

            <!-- Big Sack 3D SVG Image -->
            <div class="w-16 h-16 rounded-full bg-white shadow-md flex items-center justify-center shrink-0 border border-emerald-200">
                <svg class="w-10 h-10 fill-emerald-600 drop-shadow-md" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 6h-2.28a4.99 4.99 0 0 0-9.44 0H5a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3zm-7-2c1.3 0 2.4.84 2.82 2h-5.64A3.003 3.003 0 0 1 12 4zm0 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                </svg>
            </div>
        </div>

        <!-- Meta Information List (Tanggal, Waktu, Petugas, Kandang, Jenis Pakan, Keterangan) -->
        <div class="mt-4 bg-slate-50 rounded-2xl p-4 divide-y divide-slate-100 text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Tanggal
                </span>
                <span id="detailPakanDate" class="font-bold text-slate-700">29 Mei 2025</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Waktu
                </span>
                <span id="detailPakanTime" class="font-bold text-slate-700">07:10</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i> Petugas
                </span>
                <span id="detailPakanPetugas" class="font-bold text-slate-700">Petugas01</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Kandang / Asal
                </span>
                <span id="detailPakanKandang" class="font-bold text-slate-700">A1, A2, A3</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="tag" class="w-3.5 h-3.5"></i> Jenis Pakan
                </span>
                <span id="detailPakanJenis" class="font-bold text-slate-700">Pakan Layer</span>
            </div>
            <div class="py-2.5 flex items-start justify-between gap-4">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Keterangan
                </span>
                <span id="detailPakanNotes" class="font-medium text-slate-700 text-right">Pemakaian pagi hari</span>
            </div>
        </div>

        <!-- Aksi Section (Sesuai Mockup Layar 4: Edit Data, Nonaktifkan Data, Hapus Data) -->
        <div class="mt-5 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Aksi</span>
            
            <!-- Tombol Edit Data -->
            <button id="btnEditPakanFromDetail" class="w-full py-2.5 px-4 rounded-xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                <div class="flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit Data</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>

            <!-- Tombol Nonaktifkan Data -->
            <form id="formToggleStatusPakanDetail" method="POST" action="">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white border border-amber-300 text-amber-700 hover:bg-amber-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                    <div class="flex items-center gap-2">
                        <i data-lucide="eye-off" class="w-4 h-4"></i>
                        <span id="btnTogglePakanText">Nonaktifkan Data</span>
                    </div>
                    <i data-lucide="toggle-left" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Tombol Hapus Data -->
            <form id="formDeletePakanDetail" method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?');">
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
<!-- MODAL INPUT TRANSAKSI PAKAN (Tambah Pakan Masuk / Keluar) -->
<!-- ========================================================================= -->
<div id="modalInputPakan" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center font-bold">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Input Transaksi Pakan</h3>
            </div>
            <button onclick="closeModalInputPakan()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('warehouse.store') }}" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="category" value="pakan">

            <!-- Pilihan Jenis: Masuk / Keluar -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="masuk" checked class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            <span>Pakan Masuk</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="keluar" class="peer sr-only">
                        <div class="p-3 text-center rounded-xl border-2 border-slate-200 peer-checked:border-rose-600 peer-checked:bg-rose-50 text-slate-600 peer-checked:text-rose-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                            <span>Pakan Keluar</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Nama Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Transaksi *</label>
                <input type="text" name="item_name" required placeholder="Contoh: Pembelian Pakan, Pemakaian Pakan" list="pakanItemNames" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                <datalist id="pakanItemNames">
                    <option value="Pembelian Pakan">
                    <option value="Pemakaian Pakan">
                    <option value="Pakan Layer Konsentrat">
                    <option value="Pakan Starter (Anak Ayam)">
                    <option value="Pakan Grower (Remaja)">
                    <option value="Koreksi Stok Pakan">
                </datalist>
            </div>

            <!-- Jumlah & Satuan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah (Kg) *</label>
                    <input type="number" step="0.01" name="quantity" required placeholder="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan *</label>
                    <select name="unit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white">
                        <option value="Kg" selected>Kg</option>
                        <option value="Karung">Karung (Sak)</option>
                        <option value="Ton">Ton</option>
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

            <!-- Kandang / Supplier -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kandang / Supplier</label>
                <input type="text" name="source" placeholder="Contoh: A1, A2, A3 atau PT Pakan Ternak" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Jenis Pakan</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Pakan Layer Dewasa (Pemakaian pagi hari)" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Transaksi Pakan
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT DATA PAKAN -->
<!-- ========================================================================= -->
<div id="modalEditPakan" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Edit Transaksi Pakan</h3>
            </div>
            <button onclick="closeModalEditPakan()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formEditPakan" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <!-- Jenis Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" id="editPakanTypeMasuk" name="type" value="masuk" class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            <span>Masuk</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" id="editPakanTypeKeluar" name="type" value="keluar" class="peer sr-only">
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
                <input type="text" id="editPakanItemName" name="item_name" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Jumlah & Satuan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah *</label>
                    <input type="number" step="0.01" id="editPakanQuantity" name="quantity" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan *</label>
                    <select id="editPakanUnit" name="unit" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white">
                        <option value="Kg">Kg</option>
                        <option value="Karung">Karung</option>
                        <option value="Ton">Ton</option>
                    </select>
                </div>
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" id="editPakanDate" name="date" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu</label>
                    <input type="time" id="editPakanTime" name="time" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
            </div>

            <!-- Asal / Supplier / Kandang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kandang / Supplier</label>
                <input type="text" id="editPakanSource" name="source" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan</label>
                <textarea id="editPakanNotes" name="notes" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
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

    // 1. DETAIL MODAL LOGIC (PAKAN)
    function openDetailPakanModal(data) {
        const modal = document.getElementById('modalDetailPakan');
        const content = modal.querySelector('div');

        // Isi Data
        document.getElementById('detailPakanTitle').textContent = data.title;
        document.getElementById('detailPakanQuantity').textContent = data.quantity;
        document.getElementById('detailPakanDate').textContent = data.date;
        document.getElementById('detailPakanTime').textContent = data.time;
        document.getElementById('detailPakanPetugas').textContent = data.petugas;
        document.getElementById('detailPakanKandang').textContent = data.kandang || '-';
        document.getElementById('detailPakanJenis').textContent = data.jenis_pakan || 'Pakan Layer';
        document.getElementById('detailPakanNotes').textContent = data.notes || '-';

        // Badge
        const badge = document.getElementById('detailPakanBadge');
        badge.textContent = data.type === 'keluar' ? 'PEMBERIAN PAKAN' : (data.type === 'masuk' ? 'MASUK (BELI)' : data.type.toUpperCase());
        if (data.type === 'masuk') {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 border border-blue-200';
        }

        // Form actions
        document.getElementById('formDeletePakanDetail').action = `/gudang/${data.id}/destroy`;
        document.getElementById('formToggleStatusPakanDetail').action = `/gudang/${data.id}/toggle-status`;
        document.getElementById('btnTogglePakanText').textContent = data.is_nonaktif ? 'Aktifkan Data' : 'Nonaktifkan Data';

        // Bind Edit button
        document.getElementById('btnEditPakanFromDetail').onclick = function() {
            closeDetailPakanModal();
            openEditPakanModal(data);
        };

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeDetailPakanModal() {
        const modal = document.getElementById('modalDetailPakan');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 2. INPUT MODAL (PAKAN)
    function openModalInputPakan() {
        const modal = document.getElementById('modalInputPakan');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalInputPakan() {
        const modal = document.getElementById('modalInputPakan');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 3. EDIT MODAL (PAKAN)
    function openEditPakanModal(data) {
        const modal = document.getElementById('modalEditPakan');
        const content = modal.querySelector('div');
        
        document.getElementById('formEditPakan').action = `/gudang/${data.id}/update`;
        document.getElementById('editPakanItemName').value = data.title;
        document.getElementById('editPakanQuantity').value = data.raw_quantity || data.quantity;
        document.getElementById('editPakanUnit').value = data.unit || 'Kg';
        document.getElementById('editPakanDate').value = data.raw_date || data.date;
        document.getElementById('editPakanTime').value = data.time || '07:10';
        document.getElementById('editPakanSource').value = data.kandang || data.source || '';
        document.getElementById('editPakanNotes').value = data.notes || '';

        if (data.type === 'masuk') {
            document.getElementById('editPakanTypeMasuk').checked = true;
        } else {
            document.getElementById('editPakanTypeKeluar').checked = true;
        }

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalEditPakan() {
        const modal = document.getElementById('modalEditPakan');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }
</script>
@endpush
@endsection
