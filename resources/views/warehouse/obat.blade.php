@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Top Navigation Back & Title Bar (Sesuai Gambar Mockup 2 Layar 5) -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('warehouse.index', array_filter(['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-base sm:text-xl font-extrabold text-slate-800 tracking-tight">Gudang Obat, Vaksin & Vitamin</h1>
                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                    <p class="text-xs text-slate-400">Manajemen stok obat, vaksin, vitamin & desinfektan</p>
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
        <div class="hidden sm:flex items-center gap-4 bg-white px-4 py-2 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Stok</span>
                <span class="text-base font-extrabold {{ $stokSaatIni < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($stokSaatIni, 0, ',', '.') }} Item</span>
            </div>
            <div class="h-7 w-px bg-slate-200"></div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Masuk</span>
                <span class="text-xs font-bold text-slate-700">{{ number_format($totalMasuk, 0, ',', '.') }} Item</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Keluar</span>
                <span class="text-xs font-bold text-slate-700">{{ number_format($totalKeluar, 0, ',', '.') }} Item</span>
            </div>
        </div>
    </div>

    <!-- Search Bar & Add Button (Sesuai Mockup) -->
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
                placeholder="Cari data obat, vaksin..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs sm:text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 shadow-sm transition-all"
            >
            @if($search)
                <a href="{{ route('warehouse.obat', array_filter(['tab' => $tab, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>

        <button onclick="openModalInputObat()" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-2xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs sm:text-sm font-bold shadow-md shadow-maroon-900/20 transition-all active:scale-95 shrink-0">
            <i data-lucide="plus" class="w-4 h-4 stroke-[2.5]"></i>
            <span>Input Obat</span>
        </button>
    </div>

    <!-- Filter Tabs: Semua | Masuk | Pemakaian (Kandang) -->
    <div class="flex items-center border-b border-slate-200 gap-6 sm:gap-8 px-1">
        <a href="{{ route('warehouse.obat', array_filter(['tab' => 'semua', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative {{ $tab === 'semua' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Semua Data
            @if($tab === 'semua')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.obat', array_filter(['tab' => 'masuk', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative {{ $tab === 'masuk' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Masuk (Beli)
            @if($tab === 'masuk')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.obat', array_filter(['tab' => 'keluar', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative {{ $tab === 'keluar' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Pemakaian / Keluar (Kandang)
            @if($tab === 'keluar')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
    </div>

    <!-- List Data Obat, Vaksin & Vitamin (Sesuai Gambar Mockup 2 Layar 5) -->
    <div class="space-y-2.5">
        @forelse($items as $item)
            @php
                $isMasuk = $item->type === 'masuk';
                $isNonaktif = str_starts_with(trim($item->notes ?? ''), '[NONAKTIF]');
                $displayNotes = $isNonaktif ? trim(substr(trim($item->notes), strlen('[NONAKTIF]'))) : $item->notes;
            @endphp
            <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex items-center justify-between gap-3 {{ $isNonaktif ? 'opacity-60 bg-slate-50' : '' }}">
                
                <!-- Left Details & Icon (Click to open Detail) -->
                <div class="flex items-center gap-3.5 min-w-0 cursor-pointer flex-1" onclick="openDetailObatModal({{ json_encode([
                    'id' => $item->id,
                    'title' => $item->item_name,
                    'type' => $item->type,
                    'category' => ucfirst($item->category),
                    'quantity' => number_format($item->quantity, 0, ',', '.') . ' ' . $item->unit,
                    'raw_quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'notes' => $displayNotes,
                    'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                    'raw_date' => $item->date->format('Y-m-d'),
                    'time' => $item->created_at ? $item->created_at->format('H:i') : '09:30',
                    'petugas' => $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas',
                    'source' => $item->source ?? 'CV Medika Farma',
                    'is_nonaktif' => $isNonaktif
                ]) }})">
                    
                    <!-- Flask / Medicine Icon -->
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-200/60 flex items-center justify-center shrink-0 shadow-inner">
                        <svg class="w-7 h-7 fill-purple-600 drop-shadow-sm" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 3h12v2H6V3zm2 4h8v3h-8V7zm0 5h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8zm4 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <!-- Badge Masuk / Keluar / Pemakaian -->
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isMasuk ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $item->type === 'keluar' ? 'PEMAKAIAN KANDANG' : 'MASUK (BELI)' }}
                            </span>
                            @if(isset($item->category))
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ strtoupper($item->category) }}
                                </span>
                            @endif
                            @if($isNonaktif)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">NONAKTIF</span>
                            @endif
                        </div>

                        <!-- Judul Transaksi (misal: Pembelian Obat Vitamin B Complex / Pemakaian Vaksin) -->
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $item->item_name }}</h2>

                        <!-- Jumlah Botol / Item -->
                        <p class="text-xs font-extrabold text-slate-800">
                            {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit }}
                        </p>

                        <!-- Tanggal & Petugas -->
                        <div class="text-[10px] sm:text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                            <span>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }} {{ $item->created_at ? $item->created_at->format('H:i') : '' }}</span>
                            <span>•</span>
                            <span class="text-slate-500 font-medium">{{ $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas' }}</span>
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
                            'category' => ucfirst($item->category),
                            'quantity' => number_format($item->quantity, 0, ',', '.') . ' ' . $item->unit,
                            'raw_quantity' => $item->quantity,
                            'unit' => $item->unit,
                            'notes' => $displayNotes,
                            'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                            'raw_date' => $item->date->format('Y-m-d'),
                            'time' => $item->created_at ? $item->created_at->format('H:i') : '09:30',
                            'petugas' => $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas',
                            'source' => $item->source ?? 'CV Medika Farma',
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
                <h3 class="text-sm font-bold text-slate-700">Tidak ada data obat / vaksin</h3>
                <p class="text-xs text-slate-400 mt-1">Belum ada catatan mutasi obat untuk filter ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL DATA OBAT / VAKSIN -->
<!-- ========================================================================= -->
<div id="modalDetailObat" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <!-- Header Detail with Back/Close -->
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

        <!-- Big Card Hero Figure & Flask Image -->
        <div class="mt-5 p-4 rounded-2xl bg-gradient-to-br from-purple-50/80 to-rose-50/40 border border-purple-200/60 flex items-center justify-between">
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

            <!-- Big Flask 3D SVG Image -->
            <div class="w-16 h-16 rounded-full bg-white shadow-md flex items-center justify-center shrink-0 border border-purple-200">
                <svg class="w-10 h-10 fill-purple-600 drop-shadow-md" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 3h12v2H6V3zm2 4h8v3h-8V7zm0 5h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8zm4 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
                </svg>
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
                <span id="detailObatSource" class="font-bold text-slate-700">Apotek Hewan Sejahtera</span>
            </div>
            <div class="py-2.5 flex items-start justify-between gap-4">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Keterangan
                </span>
                <span id="detailObatNotes" class="font-medium text-slate-700 text-right">Vitamin suplemen harian</span>
            </div>
        </div>

        <!-- Aksi Section -->
        <div class="mt-5 space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Aksi</span>
            
            <!-- Tombol Edit Data -->
            <button id="btnEditObatFromDetail" class="w-full py-2.5 px-4 rounded-xl bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 flex items-center justify-between font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-98">
                <div class="flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    <span>Edit Data</span>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>

            <!-- Tombol Nonaktifkan Data -->
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

            <!-- Tombol Hapus Data -->
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
<!-- MODAL INPUT OBAT / VAKSIN (Tambah Transaksi Masuk / Keluar) -->
<!-- ========================================================================= -->
<div id="modalInputObat" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center font-bold">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Input Transaksi Obat & Vaksin</h3>
            </div>
            <button onclick="closeModalInputObat()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('warehouse.store') }}" class="mt-4 space-y-4">
            @csrf

            <!-- Kategori: Obat / Vaksin / Vitamin -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Barang *</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="category" value="obat" checked class="peer sr-only">
                        <div class="p-2 text-center rounded-xl border-2 border-slate-200 peer-checked:border-purple-600 peer-checked:bg-purple-50 text-slate-600 peer-checked:text-purple-800 font-bold text-xs transition-all">
                            Obat
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="category" value="vaksin" class="peer sr-only">
                        <div class="p-2 text-center rounded-xl border-2 border-slate-200 peer-checked:border-purple-600 peer-checked:bg-purple-50 text-slate-600 peer-checked:text-purple-800 font-bold text-xs transition-all">
                            Vaksin
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="category" value="vitamin" class="peer sr-only">
                        <div class="p-2 text-center rounded-xl border-2 border-slate-200 peer-checked:border-purple-600 peer-checked:bg-purple-50 text-slate-600 peer-checked:text-purple-800 font-bold text-xs transition-all">
                            Vitamin
                        </div>
                    </label>
                </div>
            </div>

            <!-- Pilihan Jenis: Masuk / Keluar -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Transaksi *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="masuk" checked class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            <span>Barang Masuk</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="keluar" class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-rose-600 peer-checked:bg-rose-50 text-slate-600 peer-checked:text-rose-800 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                            <span>Barang Keluar</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Nama Item / Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Item / Transaksi *</label>
                <input type="text" name="item_name" required placeholder="Contoh: Pembelian Obat Vitamin B Complex" list="obatItemNames" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                <datalist id="obatItemNames">
                    <option value="Pembelian Obat Vitamin B Complex">
                    <option value="Pemakaian Obat Vitamin B Complex">
                    <option value="Pembelian Vaksin ND IB Vaccine">
                    <option value="Pemakaian Vaksin ND IB Vaccine">
                    <option value="ND Lasota">
                    <option value="Gumboro Vaccine">
                    <option value="Disinfektan Kandang">
                    <option value="Elektrolit & Anti Stres">
                </datalist>
            </div>

            <!-- Jumlah & Satuan -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah *</label>
                    <input type="number" step="0.01" name="quantity" required placeholder="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Satuan *</label>
                    <select name="unit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white">
                        <option value="Botol" selected>Botol</option>
                        <option value="Item">Item</option>
                        <option value="Ampul">Ampul</option>
                        <option value="Pack">Pack</option>
                        <option value="Liter">Liter</option>
                        <option value="Sachet">Sachet</option>
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

            <!-- Sumber / Kandang / Supplier -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Sumber / Kandang / Apotek</label>
                <input type="text" name="source" placeholder="Contoh: Apotek Hewan / Kandang A1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Dosis</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Campuran air minum pagi hari" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Transaksi Obat & Vaksin
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT OBAT / VAKSIN (Matching /input 💊 Vaksin & Obat) -->
<!-- ========================================================================= -->
<div id="modalEditObat" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-lg">
                    💊
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">💊 Vaksin & Obat</h3>
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
            <input type="hidden" name="type" value="keluar">

            <!-- Kloter & Blok -->
            <div class="grid grid-cols-2 gap-3">
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
                    <select name="coop_id" id="editObatCoop" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium">
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
                    <option value="vitamin">Vitamin</option>
                    <option value="vaksin">Vaksin</option>
                    <option value="obat">Obat</option>
                    <option value="disinfektan">Disinfektan</option>
                    <option value="mineral">Mineral / Premix</option>
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
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Pemakaian *</label>
                    <input type="number" step="0.01" name="dosage" id="editObatJumlah" value="1" placeholder="Jumlah" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold">
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
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Cara / Aplikasi</label>
                <select name="application_method" id="editObatAplikasi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-medium">
                    <option value="Air minum">Air minum</option>
                    <option value="Campur pakan">Campur pakan</option>
                    <option value="Tetes mata">Tetes mata</option>
                    <option value="Semprot">Semprot</option>
                    <option value="Suntik">Suntik</option>
                </select>
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
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Cara Aplikasi</label>
                <textarea id="editObatNotes" name="notes" rows="2" placeholder="Opsional..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <!-- Sync Alert -->
            <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl text-xs">
                <b class="text-purple-800 block text-[11px]">↘ Otomatis potong Gudang Obat</b>
                <p class="text-[10px] text-purple-700 mt-0.5 leading-relaxed">Saat disimpan, pemakaian obat/vaksin ini mengurangi stok Gudang Obat dan masuk ke laporan Rekap.</p>
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

    // 1. DETAIL MODAL LOGIC (OBAT)
    function openDetailObatModal(data) {
        const modal = document.getElementById('modalDetailObat');
        const content = modal.querySelector('div');

        // Isi Data
        document.getElementById('detailObatTitle').textContent = data.title;
        document.getElementById('detailObatCategory').textContent = data.category || 'OBAT';
        document.getElementById('detailObatQuantity').textContent = data.quantity;
        document.getElementById('detailObatDate').textContent = data.date;
        document.getElementById('detailObatTime').textContent = data.time;
        document.getElementById('detailObatPetugas').textContent = data.petugas;
        document.getElementById('detailObatSource').textContent = data.source || '-';
        document.getElementById('detailObatNotes').textContent = data.notes || '-';

        // Badge
        const badge = document.getElementById('detailObatBadge');
        badge.textContent = data.type === 'keluar' ? 'PEMAKAIAN KANDANG' : (data.type === 'masuk' ? 'MASUK (BELI)' : data.type.toUpperCase());
        if (data.type === 'masuk') {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-200';
        }

        // Form actions
        document.getElementById('formDeleteObatDetail').action = `/gudang/${data.id}/destroy`;
        document.getElementById('formToggleStatusObatDetail').action = `/gudang/${data.id}/toggle-status`;
        document.getElementById('btnToggleObatText').textContent = data.is_nonaktif ? 'Aktifkan Data' : 'Nonaktifkan Data';

        // Bind Edit button
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

    // 2. INPUT MODAL (OBAT)
    function openModalInputObat() {
        const modal = document.getElementById('modalInputObat');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalInputObat() {
        const modal = document.getElementById('modalInputObat');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 3. EDIT MODAL (OBAT) - Matches /input form card
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
</script>
@endpush
@endsection
