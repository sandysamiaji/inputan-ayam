@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Top Navigation Back & Title Bar -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('warehouse.index', array_filter(['start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Gudang Ayam Karantina</h1>
                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                    <p class="text-xs text-slate-400">Manajemen & pemantauan ayam sakit, sembuh, dan isolasi</p>
                    @if(!empty($startDate) && !empty($endDate))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200/80 text-[10px] font-extrabold text-maroon-800">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                            <a href="{{ route('warehouse.karantina', ['tab' => $tab, 'q' => $search]) }}" class="hover:text-rose-600 ml-0.5" title="Hapus Filter Tanggal">×</a>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stok Quick Stat Banner -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 bg-white px-4 py-2.5 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Sisa Diisolasi</span>
                <span class="text-sm sm:text-base font-extrabold {{ $stokSaatIni > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                    {{ number_format($stokSaatIni, 0, ',', '.') }} Ekor
                </span>
                <span class="text-[10px] block font-medium {{ $stokSaatIni > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                    {{ $stokSaatIni > 0 ? 'Ayam Diisolasi' : 'Karantina Kosong' }}
                </span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Sakit (Masuk)</span>
                <span class="text-xs sm:text-sm font-bold text-amber-700">
                    {{ number_format($totalSakit, 0, ',', '.') }} Ekor
                </span>
                <span class="text-[10px] block text-slate-400 font-medium">Masuk Karantina</span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-emerald-800 block flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i> Sembuh (Keluar)
                </span>
                <span class="text-xs sm:text-sm font-extrabold text-emerald-700">
                    {{ number_format($totalSembuh, 0, ',', '.') }} Ekor
                </span>
                <span class="text-[10px] block text-slate-400 font-medium">Kembali ke Kandang</span>
            </div>
        </div>
    </div>

    <!-- Banner Ringkasan Pusat Isolasi Farm -->
    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-amber-50 via-white to-orange-50 border border-amber-200/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Pusat Isolasi & Pemulihan Farm</h3>
                    <span class="px-2 py-0.5 rounded-full {{ $stokSaatIni > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }} text-[10px] font-bold">
                        {{ $stokSaatIni > 0 ? $stokSaatIni . ' Ekor Aktif' : 'Nihil Sakit' }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Memantau ayam sakit yang dipisahkan dari blok kandang, riwayat pengobatan, nomor baterai asal, dan pemulihan kembali ke populasi aktif.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-center">
            <button onclick="openModalInputKarantina()" class="px-3.5 py-2 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 active:scale-95">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                <span>Catat Karantina</span>
            </button>
        </div>
    </div>

    <!-- Search Bar & Add Button -->
    <div class="flex items-center gap-2 sm:gap-3">
        <form method="GET" action="{{ route('warehouse.karantina') }}" class="flex-1 relative">
            <input type="hidden" name="tab" value="{{ $tab }}">
            @if(!empty($startDate)) <input type="hidden" name="start_date" value="{{ $startDate }}"> @endif
            @if(!empty($endDate)) <input type="hidden" name="end_date" value="{{ $endDate }}"> @endif
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input 
                type="text" 
                name="q" 
                value="{{ $search }}" 
                placeholder="Cari kandang, no. baterai, gejala, tindakan, catatan..." 
                class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs sm:text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 shadow-sm transition-all"
            >
            @if($search)
                <a href="{{ route('warehouse.karantina', array_filter(['tab' => $tab, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Filter Tabs: Semua | Sakit (Masuk) | Sembuh (Keluar) | Mati di Isolasi -->
    <div class="flex items-center border-b border-slate-200 gap-4 sm:gap-8 px-1 overflow-x-auto">
        <a href="{{ route('warehouse.karantina', array_filter(['tab' => 'semua', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'semua' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            Semua Data
            @if($tab === 'semua')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.karantina', array_filter(['tab' => 'sakit', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'sakit' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>Sakit (Masuk)</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ $tab === 'sakit' ? 'bg-maroon-800 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $totalSakit }}</span>
            @if($tab === 'sakit')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.karantina', array_filter(['tab' => 'sembuh', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'sembuh' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>Sembuh (Keluar)</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ $tab === 'sembuh' ? 'bg-maroon-800 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $totalSembuh }}</span>
            @if($tab === 'sembuh')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
        <a href="{{ route('warehouse.karantina', array_filter(['tab' => 'mati', 'q' => $search, 'start_date' => $startDate ?? null, 'end_date' => $endDate ?? null])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'mati' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>Mati di Isolasi</span>
            @if($totalMati > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ $tab === 'mati' ? 'bg-maroon-800 text-white' : 'bg-slate-200 text-slate-600' }}">{{ $totalMati }}</span>
            @endif
            @if($tab === 'mati')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>
    </div>

    <!-- List Data Karantina (Format Card Sesuai Gudang Telur) -->
    <div class="space-y-2.5">
        @forelse($items as $item)
            @php
                $isSakit = $item->status === 'sakit';
                $isSembuh = $item->status === 'sembuh';
                $isMati = $item->status === 'mati';
                $isNonaktif = $item->is_nonaktif;
                $displayNotes = $item->notes;
            @endphp
            <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex items-center justify-between gap-3 {{ $isNonaktif ? 'opacity-60 bg-slate-50' : '' }}">
                
                <!-- Left Details & Icon (Click to open Detail) -->
                <div class="flex items-center gap-3.5 min-w-0 cursor-pointer flex-1" onclick="openDetailModal({{ json_encode([
                    'id' => $item->id,
                    'title' => $item->item_name,
                    'status' => $item->status,
                    'type' => $item->type,
                    'quantity' => number_format($item->quantity, 0, ',', '.') . ' Ekor',
                    'raw_quantity' => $item->quantity,
                    'battery_number' => $item->battery_number,
                    'cause' => $item->cause,
                    'action_taken' => $item->action_taken,
                    'notes' => $displayNotes,
                    'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                    'raw_date' => $item->date->format('Y-m-d'),
                    'time' => $item->time,
                    'petugas' => $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas',
                    'source' => $item->source,
                    'is_nonaktif' => $isNonaktif,
                    'coop_id' => $item->coop_id,
                    'flock_id' => $item->flock_id,
                ]) }})">
                    
                    <!-- Status Icon -->
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-inner {{ $isSakit ? 'bg-amber-50 border border-amber-200/80 text-amber-600' : ($isSembuh ? 'bg-emerald-50 border border-emerald-200/80 text-emerald-600' : 'bg-rose-50 border border-rose-200/80 text-rose-600') }}">
                        @if($isSakit)
                            <i data-lucide="shield-alert" class="w-6 h-6 stroke-[2]"></i>
                        @elseif($isSembuh)
                            <i data-lucide="check-circle" class="w-6 h-6 stroke-[2]"></i>
                        @else
                            <i data-lucide="alert-triangle" class="w-6 h-6 stroke-[2]"></i>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <!-- Badge Status -->
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isSakit ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($isSembuh ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                {{ $isSakit ? 'SAKIT (MASUK KARANTINA)' : ($isSembuh ? 'SEMBUH (KEMBALI KE KANDANG)' : 'MATI DI ISOLASI') }}
                            </span>
                            @if($isNonaktif)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600">NONAKTIF</span>
                            @endif
                        </div>

                        <!-- Judul Transaksi -->
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $item->item_name }}</h2>

                        <!-- Jumlah & Baterai Asal -->
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5 text-xs font-extrabold text-slate-800">
                            <span class="{{ $isSakit ? 'text-amber-700' : ($isSembuh ? 'text-emerald-700' : 'text-rose-700') }}">
                                {{ number_format($item->quantity, 0, ',', '.') }} Ekor
                            </span>
                            @if(!empty($item->battery_number))
                                <span class="text-slate-500 font-semibold text-[11px] flex items-center gap-1 bg-slate-100 px-1.5 py-0.2 rounded border border-slate-200">
                                    <i data-lucide="hash" class="w-3 h-3 text-slate-400"></i>
                                    <span>Baterai: {{ $item->battery_number }}</span>
                                </span>
                            @endif
                        </div>

                        <!-- Penyebab / Tindakan / Catatan Singkat -->
                        @if(!empty($item->cause) || !empty($item->action_taken) || !empty($displayNotes))
                            <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">
                                @if(!empty($item->cause))
                                    <span>Gejala: <b>{{ $item->cause }}</b></span>
                                @endif
                                @if(!empty($item->action_taken))
                                    <span class="mx-1">•</span>
                                    <span>Tindakan: {{ $item->action_taken }}</span>
                                @endif
                                @if(!empty($displayNotes))
                                    <span class="mx-1">•</span>
                                    <span class="text-slate-400">{{ $displayNotes }}</span>
                                @endif
                            </div>
                        @endif

                        <!-- Tanggal & Petugas -->
                        <div class="text-[10px] sm:text-[11px] text-slate-400 flex items-center gap-1.5 mt-1">
                            <span>{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }} {{ $item->time ? $item->time : '' }}</span>
                            <span>•</span>
                            <span class="text-slate-600 font-medium">Petugas: {{ $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas' }}</span>
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
                            'status' => $item->status,
                            'type' => $item->type,
                            'quantity' => number_format($item->quantity, 0, ',', '.') . ' Ekor',
                            'raw_quantity' => $item->quantity,
                            'battery_number' => $item->battery_number,
                            'cause' => $item->cause,
                            'action_taken' => $item->action_taken,
                            'notes' => $displayNotes,
                            'date' => \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y'),
                            'raw_date' => $item->date->format('Y-m-d'),
                            'time' => $item->time,
                            'petugas' => $item->user ? ($item->user->username ? '@' . ltrim($item->user->username, '@') : $item->user->name) : 'Petugas',
                            'source' => $item->source,
                            'is_nonaktif' => $isNonaktif,
                            'coop_id' => $item->coop_id,
                            'flock_id' => $item->flock_id,
                        ]) }})" class="w-full px-3.5 py-2 text-left hover:bg-slate-50 flex items-center gap-2">
                            <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Lihat Detail</span>
                        </button>
                        <button onclick="openEditModal({{ json_encode([
                            'id' => $item->id,
                            'title' => $item->item_name,
                            'status' => $item->status,
                            'quantity' => $item->quantity,
                            'battery_number' => $item->battery_number,
                            'cause' => $item->cause,
                            'action_taken' => $item->action_taken,
                            'source' => $item->source,
                            'notes' => $displayNotes,
                            'date' => $item->date->format('Y-m-d'),
                            'time' => $item->time,
                            'coop_id' => $item->coop_id,
                            'flock_id' => $item->flock_id,
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
                        <form method="POST" action="{{ route('warehouse.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karantina ini? Populasi kandang akan disinkronkan kembali.');" class="w-full">
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
                    <i data-lucide="shield-alert" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada data ayam karantina</h3>
                <p class="text-xs text-slate-400 mt-1">Belum ada catatan riwayat karantina untuk filter ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL DATA AYAM KARANTINA -->
<!-- ========================================================================= -->
<div id="modalDetailKarantina" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <!-- Header Detail -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <button onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </button>
                <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Detail Ayam Karantina</h3>
            </div>
            <span id="detailBadge" class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase">
                SAKIT (MASUK)
            </span>
        </div>

        <!-- Big Card Hero Figure -->
        <div id="detailHeroBox" class="mt-5 p-4 rounded-2xl bg-gradient-to-br from-amber-50/80 to-orange-50/40 border border-amber-200/60 flex items-center justify-between">
            <div>
                <h4 id="detailTitle" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ayam Sakit Masuk</h4>
                <div id="detailQuantity" class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
                    1 Ekor
                </div>
                <div id="detailSubStatus" class="text-xs font-semibold text-amber-700 mt-0.5">
                    Dipindahkan ke Isolasi
                </div>
            </div>

            <!-- Big Icon -->
            <div id="detailIconBox" class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center shrink-0 border border-amber-200 text-amber-600">
                <i id="detailIcon" data-lucide="shield-alert" class="w-8 h-8 stroke-[2]"></i>
            </div>
        </div>

        <!-- Meta Information List -->
        <div class="mt-4 bg-slate-50 rounded-2xl p-4 divide-y divide-slate-100 text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Tanggal
                </span>
                <span id="detailDate" class="font-bold text-slate-700">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Waktu
                </span>
                <span id="detailTime" class="font-bold text-slate-700">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Blok Kandang
                </span>
                <span id="detailSource" class="font-bold text-slate-700">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="hash" class="w-3.5 h-3.5"></i> Nomor Baterai
                </span>
                <span id="detailBattery" class="font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200 font-mono">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i> Petugas Input
                </span>
                <span id="detailPetugas" class="font-bold text-slate-700">-</span>
            </div>
            <div class="py-2.5 flex items-start justify-between gap-4">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i data-lucide="activity" class="w-3.5 h-3.5"></i> Gejala / Penyebab
                </span>
                <span id="detailCause" class="font-semibold text-slate-700 text-right">-</span>
            </div>
            <div class="py-2.5 flex items-start justify-between gap-4">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i data-lucide="heart-pulse" class="w-3.5 h-3.5"></i> Tindakan
                </span>
                <span id="detailAction" class="font-semibold text-slate-700 text-right">-</span>
            </div>
            <div class="py-2.5 flex items-start justify-between gap-4">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Catatan
                </span>
                <span id="detailNotes" class="font-normal text-slate-600 text-right italic">-</span>
            </div>
        </div>

        <div class="mt-5">
            <button onclick="closeDetailModal()" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                Tutup Detail
            </button>
        </div>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL INPUT / CATAT KARANTINA BARU -->
<!-- ========================================================================= -->
<div id="modalInputKarantina" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center font-bold shadow-xs">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Catat Ayam Karantina</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Input pemindahan ayam sakit atau sembuh</p>
                </div>
            </div>
            <button onclick="closeModalInputKarantina()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('warehouse.store') }}" class="mt-4 space-y-3.5">
            @csrf
            <input type="hidden" name="category" value="karantina">

            <!-- Pilihan Status: Sakit / Sembuh / Mati -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Karantina *</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="sakit" checked class="peer sr-only" onchange="toggleAddStatusUI('sakit')">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 text-slate-600 peer-checked:text-amber-800 font-bold text-xs flex flex-col items-center justify-center gap-1 transition-all">
                            <i data-lucide="shield-alert" class="w-4 h-4"></i>
                            <span>Ayam Sakit</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="sembuh" class="peer sr-only" onchange="toggleAddStatusUI('sembuh')">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-800 font-bold text-xs flex flex-col items-center justify-center gap-1 transition-all">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Ayam Sembuh</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="mati" class="peer sr-only" onchange="toggleAddStatusUI('mati')">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-rose-600 peer-checked:bg-rose-50 text-slate-600 peer-checked:text-rose-800 font-bold text-xs flex flex-col items-center justify-center gap-1 transition-all">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            <span>Mati di Isolasi</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Blok Kandang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Blok Kandang *</label>
                <select name="coop_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-bold text-slate-800">
                    @foreach($coops as $coop)
                        <option value="{{ $coop->id }}">{{ $coop->name }} ({{ $coop->flock ? $coop->flock->name : 'Kloter' }} - Populasi: {{ number_format($coop->active_chickens, 0, ',', '.') }} ekor)</option>
                    @endforeach
                </select>
            </div>

            <!-- Jumlah Ekor & Nomor Baterai -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah (Ekor) *</label>
                    <input type="number" name="count" min="1" value="1" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-extrabold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Baterai Asal</label>
                    <input type="text" name="battery_number" placeholder="Contoh: Baris 3 / A-12" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold">
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

            <!-- Gejala / Indikasi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Gejala / Indikasi Sakit</label>
                <input type="text" name="cause" placeholder="Contoh: Nafsu makan drop, lemas, bersin" list="karantinaGejalaList" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                <datalist id="karantinaGejalaList">
                    <option value="Nafsu makan menurun & lemas">
                    <option value="Gangguan pernapasan / bersin">
                    <option value="Kaki pincang / cedera">
                    <option value="Kotoran encer / diare">
                    <option value="Kondisi sembuh normal">
                </datalist>
            </div>

            <!-- Tindakan Medis / Pengobatan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tindakan / Pengobatan Diberikan</label>
                <input type="text" name="action_taken" placeholder="Contoh: Diberikan vitamin & antibiotik, dipindah ke kandang isolasi" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Catatan kondisi ayam lainnya..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Catatan Karantina
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT DATA AYAM KARANTINA -->
<!-- ========================================================================= -->
<div id="modalEditKarantina" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[92vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 border border-blue-200/80 flex items-center justify-center font-bold shadow-xs">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Edit Data Karantina</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Perbarui informasi ayam karantina</p>
                </div>
            </div>
            <button onclick="closeModalEditKarantina()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formEditKarantina" method="POST" action="" class="mt-4 space-y-3.5">
            @csrf
            @method('PUT')

            <!-- Status Karantina -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Karantina *</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="status" id="editStatusSakit" value="sakit" class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 text-slate-600 peer-checked:text-amber-800 font-bold text-xs flex flex-col items-center justify-center gap-1 transition-all">
                            <i data-lucide="shield-alert" class="w-4 h-4"></i>
                            <span>Ayam Sakit</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" id="editStatusSembuh" value="sembuh" class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 text-slate-600 peer-checked:text-emerald-800 font-bold text-xs flex flex-col items-center justify-center gap-1 transition-all">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Ayam Sembuh</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" id="editStatusMati" value="mati" class="peer sr-only">
                        <div class="p-2.5 text-center rounded-xl border-2 border-slate-200 peer-checked:border-rose-600 peer-checked:bg-rose-50 text-slate-600 peer-checked:text-rose-800 font-bold text-xs flex flex-col items-center justify-center gap-1 transition-all">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            <span>Mati di Isolasi</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Blok Kandang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Blok Kandang *</label>
                <select name="coop_id" id="editCoopId" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm bg-white font-bold text-slate-800">
                    @foreach($coops as $coop)
                        <option value="{{ $coop->id }}">{{ $coop->name }} ({{ $coop->flock ? $coop->flock->name : 'Kloter' }} - Populasi: {{ number_format($coop->active_chickens, 0, ',', '.') }} ekor)</option>
                    @endforeach
                </select>
            </div>

            <!-- Jumlah Ekor & Nomor Baterai -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah (Ekor) *</label>
                    <input type="number" name="count" id="editCount" min="1" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-extrabold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Baterai Asal</label>
                    <input type="text" name="battery_number" id="editBatteryNumber" placeholder="Contoh: Baris 3 / A-12" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold">
                </div>
            </div>

            <!-- Tanggal & Waktu -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal *</label>
                    <input type="date" name="date" id="editDate" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Waktu</label>
                    <input type="time" name="time" id="editTime" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
            </div>

            <!-- Gejala / Indikasi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Gejala / Indikasi Sakit</label>
                <input type="text" name="cause" id="editCause" placeholder="Contoh: Nafsu makan drop, lemas, bersin" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Tindakan Medis / Pengobatan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tindakan / Pengobatan Diberikan</label>
                <input type="text" name="action_taken" id="editActionTaken" placeholder="Contoh: Diberikan vitamin & antibiotik" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="notes" id="editNotes" rows="2" placeholder="Catatan kondisi ayam lainnya..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Perbarui Data Karantina
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    // 1. DROPDOWN ACTIONS
    function toggleActionDropdown(button) {
        const dropdown = button.nextElementSibling;
        const allDropdowns = document.querySelectorAll('.action-dropdown');
        allDropdowns.forEach(d => {
            if (d !== dropdown) d.classList.add('hidden');
        });
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-dropdown') && !e.target.closest('button[onclick*="toggleActionDropdown"]')) {
            document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));
        }
    });

    // 2. DETAIL MODAL
    function openDetailModal(data) {
        const modal = document.getElementById('modalDetailKarantina');
        const content = modal.querySelector('div');

        document.getElementById('detailTitle').textContent = data.title || 'Ayam Karantina';
        document.getElementById('detailQuantity').textContent = data.quantity || '0 Ekor';
        document.getElementById('detailDate').textContent = data.date || '-';
        document.getElementById('detailTime').textContent = data.time || '-';
        document.getElementById('detailSource').textContent = data.source || '-';
        document.getElementById('detailBattery').textContent = data.battery_number || 'Tidak Dicatat';
        document.getElementById('detailPetugas').textContent = data.petugas || 'Petugas Farm';
        document.getElementById('detailCause').textContent = data.cause || 'Tidak ada catatan gejala';
        document.getElementById('detailAction').textContent = data.action_taken || 'Tidak ada tindakan khusus';
        document.getElementById('detailNotes').textContent = data.notes || 'Tidak ada catatan tambahan';

        const badge = document.getElementById('detailBadge');
        const heroBox = document.getElementById('detailHeroBox');
        const iconBox = document.getElementById('detailIconBox');
        const icon = document.getElementById('detailIcon');
        const subStatus = document.getElementById('detailSubStatus');

        if (data.status === 'sakit') {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-50 text-amber-700 border border-amber-200';
            badge.textContent = 'SAKIT (MASUK)';
            heroBox.className = 'mt-5 p-4 rounded-2xl bg-gradient-to-br from-amber-50/80 to-orange-50/40 border border-amber-200/60 flex items-center justify-between';
            iconBox.className = 'w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center shrink-0 border border-amber-200 text-amber-600';
            subStatus.className = 'text-xs font-semibold text-amber-700 mt-0.5';
            subStatus.textContent = 'Dipindahkan ke Isolasi';
        } else if (data.status === 'sembuh') {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200';
            badge.textContent = 'SEMBUH (KELUAR)';
            heroBox.className = 'mt-5 p-4 rounded-2xl bg-gradient-to-br from-emerald-50/80 to-teal-50/40 border border-emerald-200/60 flex items-center justify-between';
            iconBox.className = 'w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center shrink-0 border border-emerald-200 text-emerald-600';
            subStatus.className = 'text-xs font-semibold text-emerald-700 mt-0.5';
            subStatus.textContent = 'Kembali ke Blok Kandang';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-rose-50 text-rose-700 border border-rose-200';
            badge.textContent = 'MATI DI ISOLASI';
            heroBox.className = 'mt-5 p-4 rounded-2xl bg-gradient-to-br from-rose-50/80 to-slate-50 border border-rose-200/60 flex items-center justify-between';
            iconBox.className = 'w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center shrink-0 border border-rose-200 text-rose-600';
            subStatus.className = 'text-xs font-semibold text-rose-700 mt-0.5';
            subStatus.textContent = 'Meninggal di Karantina';
        }

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeDetailModal() {
        const modal = document.getElementById('modalDetailKarantina');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 3. EDIT MODAL
    function openEditModal(data) {
        const modal = document.getElementById('modalEditKarantina');
        const content = modal.querySelector('div');

        document.getElementById('formEditKarantina').action = `/gudang/${data.id}/update`;

        if (data.status === 'sembuh') {
            document.getElementById('editStatusSembuh').checked = true;
        } else if (data.status === 'mati') {
            document.getElementById('editStatusMati').checked = true;
        } else {
            document.getElementById('editStatusSakit').checked = true;
        }

        if (data.coop_id) {
            document.getElementById('editCoopId').value = data.coop_id;
        }

        document.getElementById('editCount').value = data.quantity || 1;
        document.getElementById('editBatteryNumber').value = data.battery_number || '';
        document.getElementById('editDate').value = data.date || '';
        document.getElementById('editTime').value = data.time || '08:00';
        document.getElementById('editCause').value = data.cause || '';
        document.getElementById('editActionTaken').value = data.action_taken || '';
        document.getElementById('editNotes').value = data.notes || '';

        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalEditKarantina() {
        const modal = document.getElementById('modalEditKarantina');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 4. INPUT MODAL
    function openModalInputKarantina() {
        const modal = document.getElementById('modalInputKarantina');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalInputKarantina() {
        const modal = document.getElementById('modalInputKarantina');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    function toggleAddStatusUI(status) {
        // dynamic helper if needed
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
            closeModalEditKarantina();
            closeModalInputKarantina();
        }
    });
</script>
@endpush
@endsection
