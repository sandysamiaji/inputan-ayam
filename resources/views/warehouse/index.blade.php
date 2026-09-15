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

        <!-- Desktop Quick Action Buttons -->
        <div class="hidden sm:flex items-center gap-2">
            <a href="{{ route('warehouse.telur') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-maroon-800 border border-maroon-200 hover:bg-rose-50 shadow-sm transition-all active:scale-95">
                <i data-lucide="egg" class="w-4 h-4 text-amber-600"></i>
                <span>Gudang Telur</span>
            </a>
            <a href="{{ route('warehouse.pakan') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-maroon-800 border border-maroon-200 hover:bg-rose-50 shadow-sm transition-all active:scale-95">
                <i data-lucide="wheat" class="w-4 h-4 text-emerald-600"></i>
                <span>Gudang Pakan</span>
            </a>
            <a href="{{ route('warehouse.obat') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-maroon-800 text-white hover:bg-maroon-900 shadow-md shadow-maroon-900/20 transition-all active:scale-95">
                <i data-lucide="flask-conical" class="w-4 h-4 text-rose-200"></i>
                <span>Obat & Vaksin</span>
            </a>
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
