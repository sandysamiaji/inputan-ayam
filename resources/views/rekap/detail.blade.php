@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Top Navigation Header (Sesuai Mockup Layar 4) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('rekap.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Rekap Detail</h1>
                <p class="text-xs text-slate-400">Tabel data operasional & ekspor laporan</p>
            </div>
        </div>

        <!-- Tombol Aksi Ekspor (Desktop Header & Print Friendly) -->
        <div class="flex items-center gap-2">
            <a href="{{ route('rekap.export-excel', ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition-all active:scale-95">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs font-bold shadow-sm transition-all active:scale-95">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Export PDF / Cetak</span>
            </button>
        </div>
    </div>

    <!-- Date Filter Bar (Sesuai Mockup Layar 4) -->
    <div class="farm-card p-3 sm:p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 w-full sm:w-auto">
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center shrink-0">
                <i data-lucide="calendar-range" class="w-4 h-4"></i>
            </div>
            <span>Periode: <span class="text-maroon-800">{{ $formattedRange }}</span></span>
        </div>

        <form method="GET" action="{{ route('rekap.detail') }}" class="flex items-center gap-2 w-full sm:w-auto">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white">
            <span class="text-xs text-slate-400">-</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white">
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all">
                Filter
            </button>
        </form>
    </div>

    <!-- Sub-tabs: [Produksi] [Pakan] [Mortalitas] [Berat Badan] [Vaksin/Obat] (Sesuai Mockup Layar 4) -->
    <div class="flex items-center overflow-x-auto no-scrollbar border-b border-slate-200 gap-2 sm:gap-4 px-1 pb-1">
        @php
            $tabs = [
                'produksi' => ['label' => 'Produksi Telur', 'icon' => 'egg'],
                'pakan' => ['label' => 'Pemakaian Pakan', 'icon' => 'wheat'],
                'mortalitas' => ['label' => 'Mortalitas', 'icon' => 'alert-triangle'],
                'bobot' => ['label' => 'Berat Badan', 'icon' => 'scale'],
                'vaksin' => ['label' => 'Vaksin / Obat', 'icon' => 'syringe'],
                'penjualan' => ['label' => 'Barang Keluar (nochifram)', 'icon' => 'shopping-bag'],
            ];
        @endphp

        @foreach($tabs as $key => $t)
            <a 
                href="{{ route('rekap.detail', ['tab' => $key, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                class="pb-2.5 px-3 text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition-all relative {{ $tab === $key ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}"
            >
                <i data-lucide="{{ $t['icon'] }}" class="w-4 h-4 {{ $tab === $key ? 'text-maroon-800' : 'text-slate-400' }}"></i>
                <span>{{ $t['label'] }}</span>
                @if($tab === $key)
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- TABEL DATA SESUAI TAB AKTIF -->
    <div class="farm-card overflow-hidden">
        
        <!-- 1. TABEL PRODUKSI TELUR (Sesuai Mockup Layar 4) -->
        @if($tab === 'produksi')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600">
                            <th rowspan="2" class="py-3.5 px-4 font-extrabold">Tanggal</th>
                            <th colspan="2" class="py-2.5 px-4 font-extrabold text-center border-l border-slate-200 bg-rose-50/50 text-maroon-900">
                                Produksi Telur
                            </th>
                            <th colspan="2" class="py-2.5 px-4 font-extrabold text-center border-l border-slate-200 bg-slate-100/60 text-slate-700 hidden sm:table-cell">
                                Kondisi Telur
                            </th>
                            
                            <th rowspan="2" class="py-3.5 px-4 font-extrabold text-center border-l border-slate-200 w-24">Aksi</th>
                        </tr>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500">
                            <th class="py-2 px-4 text-center border-l border-slate-200 text-maroon-800">Peti</th>
                            <th class="py-2 px-4 text-center text-maroon-800">Butir</th>
                            <th class="py-2 px-4 text-center border-l border-slate-200 hidden sm:table-cell text-emerald-700">Utuh</th>
                            <th class="py-2 px-4 text-center hidden sm:table-cell text-rose-700">Retak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse($data as $index => $row)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ $row['formatted_date'] }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-slate-900 border-l border-slate-100">
                                    {{ number_format($row['peti'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center font-semibold text-slate-600">
                                    {{ number_format($row['butir'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center text-emerald-600 border-l border-slate-100 hidden sm:table-cell">
                                    {{ number_format($row['good'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center text-rose-600 hidden sm:table-cell">
                                    {{ number_format($row['broken'], 0, ',', '.') }}
                                </td>
                                
                                <td class="py-3 px-4 text-center border-l border-slate-100">
                                    <button onclick="document.getElementById('modal-egg-{{ $index }}').classList.remove('hidden')" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded hover:text-maroon-800 hover:border-maroon-300 transition-colors" title="Kelola Riwayat">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <!-- Modal Kelola Data Produksi -->
                                    <div id="modal-egg-{{ $index }}" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
                                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl text-left overflow-hidden">
                                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                                <h3 class="font-extrabold text-lg text-slate-800">Riwayat Produksi: {{ $row['formatted_date'] }}</h3>
                                                <button onclick="document.getElementById('modal-egg-{{ $index }}').classList.add('hidden')" class="p-1 text-slate-400 hover:bg-slate-100 rounded-lg">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>
                                            <div class="p-5">
                                                <div class="space-y-4">
                                                    @foreach($row['records'] as $record)
                                                    <div class="p-4 border border-slate-200 rounded-xl bg-slate-50 relative">
                                                        <div class="flex justify-between items-start mb-3">
                                                            <div>
                                                                <span class="text-xs font-black bg-maroon-100 text-maroon-900 px-2 py-0.5 rounded">
                                                                    {{ $record->coop ? $record->coop->name : 'Semua Blok' }}
                                                                </span>
                                                                <div class="text-xs text-slate-500 mt-1">Waktu: {{ substr($record->time, 0, 5) }} | Dicatat oleh: {{ $record->user ? $record->user->name : '-' }}</div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <form action="{{ route('rekap.data.production.destroy', $record->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-100 rounded-lg transition-colors" title="Hapus">
                                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <form action="{{ route('rekap.data.production.update', $record->id) }}" method="POST" class="grid grid-cols-2 gap-3">
                                                            @csrf @method('PUT')
                                                            <div>
                                                                <label class="text-[10px] font-bold text-slate-500 uppercase">Total Butir</label>
                                                                <input type="number" name="total_eggs" value="{{ $record->total_eggs }}" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-sm font-bold text-slate-800">
                                                            </div>
                                                            <div>
                                                                <label class="text-[10px] font-bold text-slate-500 uppercase">Telur Retak</label>
                                                                <input type="number" name="broken_eggs" value="{{ $record->broken_eggs }}" class="w-full px-3 py-1.5 border border-slate-300 rounded-lg text-sm font-bold text-slate-800">
                                                            </div>
                                                            <div class="col-span-2 text-right mt-1">
                                                                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition-colors">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    Tidak ada data produksi telur pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- Footer Baris Total (Sesuai Mockup Layar 4) -->
                    @if(count($data) > 0)
                        <tfoot>
                            <tr class="bg-rose-50/80 border-t-2 border-maroon-800/30 text-xs sm:text-sm font-extrabold text-maroon-950">
                                <td class="py-3.5 px-4 font-black">Total</td>
                                <td class="py-3.5 px-4 text-center font-black text-maroon-900 border-l border-rose-200">
                                    {{ number_format($summary['total_peti'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-black text-maroon-900">
                                    {{ number_format($summary['total_butir'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-emerald-800 border-l border-rose-200 hidden sm:table-cell">
                                    {{ number_format($summary['total_butir'] - $summary['total_broken'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-rose-800 hidden sm:table-cell">
                                    {{ number_format($summary['total_broken'], 0, ',', '.') }}
                                </td>
                                
                                <td class="border-l border-rose-200"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

        <!-- 2. TABEL PEMAKAIAN PAKAN -->
        @elseif($tab === 'pakan')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold">
                            <th class="py-3.5 px-4">Tanggal & Waktu</th>
                            <th class="py-3.5 px-4">Waktu Pakan</th>
                            <th class="py-3.5 px-4">Jenis Pakan</th>
                            <th class="py-3.5 px-4 text-right">Jumlah (Kg)</th>
                            <th class="py-3.5 px-4">Kandang / Blok</th>
                            <th class="py-3.5 px-4">Petugas</th>
                            
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse($data as $r)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($r->date)->translatedFormat('d M Y') }}
                                    <span class="text-[11px] text-slate-400 block font-normal">{{ $r->time ? substr($r->time, 0, 5) : '-' }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $r->feeding_time ?? 'Pagi' }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $r->feed_name }}</td>
                                <td class="py-3 px-4 text-right font-extrabold text-emerald-700">
                                    {{ number_format($r->quantity_kg, 2, ',', '.') }} Kg
                                </td>
                                <td class="py-3 px-4">{{ $r->coop ? $r->coop->name : 'Semua Blok' }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $r->user ? $r->user->name : 'Petugas' }}</td>
                                
                                <td class="py-3 px-4 text-center flex items-center justify-center gap-2">
                                    <button onclick="document.getElementById('modal-feed-{{ $r->id }}').classList.remove('hidden')" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded hover:text-maroon-800 hover:border-maroon-300 transition-colors" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('rekap.data.feed.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus data pakan ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-100 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Edit -->
                                    <div id="modal-feed-{{ $r->id }}" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
                                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm text-left overflow-hidden">
                                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                                <h3 class="font-extrabold text-lg text-slate-800">Edit Pakan</h3>
                                                <button onclick="document.getElementById('modal-feed-{{ $r->id }}').classList.add('hidden')" class="p-1 text-slate-400 hover:bg-slate-100 rounded-lg">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>
                                            <form action="{{ route('rekap.data.feed.update', $r->id) }}" method="POST" class="p-5 space-y-4">
                                                @csrf @method('PUT')
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Pakan (Kg)</label>
                                                    <input type="number" step="0.01" name="quantity_kg" value="{{ $r->quantity_kg }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-maroon-800/20">
                                                </div>
                                                <div class="pt-2 text-right">
                                                    <button type="button" onclick="document.getElementById('modal-feed-{{ $r->id }}').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-semibold text-sm">Batal</button>
                                                    <button type="submit" class="px-4 py-2 bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm rounded-xl transition-all">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">Tidak ada data pakan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($data) > 0)
                        <tfoot>
                            <tr class="bg-emerald-50/80 border-t-2 border-emerald-300 text-xs sm:text-sm font-extrabold text-emerald-950">
                                <td colspan="3" class="py-3.5 px-4 font-black">Total Pemakaian Pakan</td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-900">
                                    {{ number_format($summary['total_kg'], 2, ',', '.') }} Kg
                                </td>
                                <td colspan="3" class="py-3.5 px-4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

        <!-- 3. TABEL MORTALITAS -->
        @elseif($tab === 'mortalitas')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold">
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Waktu</th>
                            <th class="py-3.5 px-4 text-right">Kematian (Ekor)</th>
                            <th class="py-3.5 px-4">Penyebab / Keterangan</th>
                            <th class="py-3.5 px-4">Kandang</th>
                            <th class="py-3.5 px-4">Petugas</th>
                            
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse($data as $r)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">{{ \Carbon\Carbon::parse($r->date)->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ $r->time ? substr($r->time, 0, 5) : '-' }}</td>
                                <td class="py-3 px-4 text-right font-extrabold text-rose-700">{{ $r->count }} Ekor</td>
                                <td class="py-3 px-4 text-slate-600">{{ $r->cause ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $r->coop ? $r->coop->name : '-' }}</td>
                                <td class="py-3 px-4 text-slate-500">{{ $r->user ? $r->user->name : 'Petugas' }}</td>
                                
                                <td class="py-3 px-4 text-center flex items-center justify-center gap-2">
                                    <button onclick="document.getElementById('modal-mort-{{ $r->id }}').classList.remove('hidden')" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded hover:text-maroon-800 hover:border-maroon-300 transition-colors" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('rekap.data.mortality.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus data mortalitas ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-100 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Edit -->
                                    <div id="modal-mort-{{ $r->id }}" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
                                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm text-left overflow-hidden">
                                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                                <h3 class="font-extrabold text-lg text-slate-800">Edit Mortalitas</h3>
                                                <button onclick="document.getElementById('modal-mort-{{ $r->id }}').classList.add('hidden')" class="p-1 text-slate-400 hover:bg-slate-100 rounded-lg">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>
                                            <form action="{{ route('rekap.data.mortality.update', $r->id) }}" method="POST" class="p-5 space-y-4">
                                                @csrf @method('PUT')
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Mati (Ekor)</label>
                                                    <input type="number" name="count" value="{{ $r->count }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-maroon-800/20">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Penyebab / Keterangan</label>
                                                    <input type="text" name="cause" value="{{ $r->cause }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-maroon-800/20">
                                                </div>
                                                <div class="pt-2 text-right">
                                                    <button type="button" onclick="document.getElementById('modal-mort-{{ $r->id }}').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-semibold text-sm">Batal</button>
                                                    <button type="submit" class="px-4 py-2 bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm rounded-xl transition-all">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">Tidak ada data mortalitas pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($data) > 0)
                        <tfoot>
                            <tr class="bg-rose-50/80 border-t-2 border-rose-300 text-xs sm:text-sm font-extrabold text-rose-950">
                                <td colspan="2" class="py-3.5 px-4 font-black">Total Kematian</td>
                                <td class="py-3.5 px-4 text-right font-black text-rose-900">{{ $summary['total_ekor'] }} Ekor</td>
                                <td colspan="4" class="py-3.5 px-4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

        <!-- 4. TABEL BERAT BADAN -->
        @elseif($tab === 'bobot')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold">
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Kandang</th>
                            <th class="py-3.5 px-4 text-right">Rata-rata Bobot</th>
                            <th class="py-3.5 px-4 text-right">Sampel (Ekor)</th>
                            <th class="py-3.5 px-4 text-right">Keseragaman</th>
                            <th class="py-3.5 px-4">Umur Ayam</th>
                            <th class="py-3.5 px-4">Catatan</th>
                            
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse($data as $r)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">{{ \Carbon\Carbon::parse($r->date)->translatedFormat('d M Y') }}</td>
                                <td class="py-3 px-4">{{ $r->coop ? $r->coop->name : '-' }}</td>
                                <td class="py-3 px-4 text-right font-extrabold text-blue-700">{{ number_format($r->average_weight_kg, 3, ',', '.') }} Kg</td>
                                <td class="py-3 px-4 text-right">{{ $r->sample_count }}</td>
                                <td class="py-3 px-4 text-right font-bold text-slate-900">{{ number_format($r->uniformity_percentage, 1, ',', '.') }}%</td>
                                <td class="py-3 px-4 text-slate-600">{{ $r->age_weeks }} Minggu</td>
                                <td class="py-3 px-4 text-slate-400">{{ $r->notes ?? '-' }}</td>
                                
                                <td class="py-3 px-4 text-center flex items-center justify-center gap-2">
                                    <button onclick="document.getElementById('modal-weight-{{ $r->id }}').classList.remove('hidden')" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded hover:text-maroon-800 hover:border-maroon-300 transition-colors" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('rekap.data.weight.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus data bobot ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-100 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Edit -->
                                    <div id="modal-weight-{{ $r->id }}" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
                                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm text-left overflow-hidden">
                                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                                <h3 class="font-extrabold text-lg text-slate-800">Edit Bobot</h3>
                                                <button onclick="document.getElementById('modal-weight-{{ $r->id }}').classList.add('hidden')" class="p-1 text-slate-400 hover:bg-slate-100 rounded-lg">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>
                                            <form action="{{ route('rekap.data.weight.update', $r->id) }}" method="POST" class="p-5 space-y-4">
                                                @csrf @method('PUT')
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Bobot Rata-rata (Kg)</label>
                                                    <input type="number" step="0.001" name="average_weight_kg" value="{{ $r->average_weight_kg }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-maroon-800/20">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Keseragaman (%)</label>
                                                    <input type="number" step="0.1" name="uniformity_percentage" value="{{ $r->uniformity_percentage }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-maroon-800/20">
                                                </div>
                                                <div class="pt-2 text-right">
                                                    <button type="button" onclick="document.getElementById('modal-weight-{{ $r->id }}').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-semibold text-sm">Batal</button>
                                                    <button type="submit" class="px-4 py-2 bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm rounded-xl transition-all">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400">Tidak ada sampling timbang bobot pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- 5. TABEL VAKSIN / OBAT -->
        @elseif($tab === 'vaksin')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold">
                            <th class="py-3.5 px-4">Tanggal & Waktu</th>
                            <th class="py-3.5 px-4">Jenis</th>
                            <th class="py-3.5 px-4">Nama Obat / Vaksin</th>
                            <th class="py-3.5 px-4">Dosis</th>
                            <th class="py-3.5 px-4">Metode Pemberian</th>
                            <th class="py-3.5 px-4">Kandang</th>
                            <th class="py-3.5 px-4">Catatan</th>
                            
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse($data as $r)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($r->date)->translatedFormat('d M Y') }}
                                    <span class="text-[11px] text-slate-400 block font-normal">{{ $r->time ? substr($r->time, 0, 5) : '-' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800">
                                        {{ $r->type }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $r->medicine_name }}</td>
                                <td class="py-3 px-4">{{ $r->dosage ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $r->application_method ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $r->coop ? $r->coop->name : '-' }}</td>
                                <td class="py-3 px-4 text-slate-400">{{ $r->notes ?? '-' }}</td>
                                
                                <td class="py-3 px-4 text-center flex items-center justify-center gap-2">
                                    <button onclick="document.getElementById('modal-health-{{ $r->id }}').classList.remove('hidden')" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded hover:text-maroon-800 hover:border-maroon-300 transition-colors" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('rekap.data.health.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus data vaksin/obat ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-100 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Edit -->
                                    <div id="modal-health-{{ $r->id }}" class="hidden fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
                                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm text-left overflow-hidden">
                                            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                                                <h3 class="font-extrabold text-lg text-slate-800">Edit Vaksin / Obat</h3>
                                                <button onclick="document.getElementById('modal-health-{{ $r->id }}').classList.add('hidden')" class="p-1 text-slate-400 hover:bg-slate-100 rounded-lg">
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                </button>
                                            </div>
                                            <form action="{{ route('rekap.data.health.update', $r->id) }}" method="POST" class="p-5 space-y-4">
                                                @csrf @method('PUT')
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Obat / Vaksin</label>
                                                    <input type="text" name="medicine_name" value="{{ $r->medicine_name }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-maroon-800/20">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Dosis</label>
                                                    <input type="text" name="dosage" value="{{ $r->dosage }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-maroon-800/20">
                                                </div>
                                                <div class="pt-2 text-right">
                                                    <button type="button" onclick="document.getElementById('modal-health-{{ $r->id }}').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-semibold text-sm">Batal</button>
                                                    <button type="submit" class="px-4 py-2 bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm rounded-xl transition-all">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400">Tidak ada kegiatan vaksinasi/obat pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- 6. TABEL BARANG KELUAR & PENJUALAN (DARI NOCHIFRAM) -->
        @elseif($tab === 'penjualan')
            <!-- Metrik Ringkasan Barang Keluar -->
            <div class="p-4 bg-gradient-to-r from-rose-50 via-white to-emerald-50 border-b border-slate-200">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Telur Terjual (Peti)</span>
                        <span class="text-base sm:text-lg font-black text-maroon-800">{{ number_format($summary['total_peti_telur'], 0, ',', '.') }} Peti</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Telur Eceran (Kg)</span>
                        <span class="text-base sm:text-lg font-black text-amber-600">{{ number_format($summary['total_kg_telur'], 2, ',', '.') }} Kg</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Pakan Terjual</span>
                        <span class="text-base sm:text-lg font-black text-emerald-700">{{ number_format($summary['total_karung_pakan'], 0, ',', '.') }} Karung</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Total Omzet Penjualan</span>
                        <span class="text-base sm:text-lg font-black text-slate-900">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-extrabold">
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">No Invoice</th>
                            <th class="py-3.5 px-4">Kategori & Barang</th>
                            <th class="py-3.5 px-4 text-right">Jumlah Keluar</th>
                            <th class="py-3.5 px-4 text-right">Harga Satuan</th>
                            <th class="py-3.5 px-4 text-right">Total Nilai</th>
                            <th class="py-3.5 px-4">Pembeli</th>
                            <th class="py-3.5 px-4">Metode & Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        @forelse($data as $r)
                            @php
                                $isTelur = strtolower($r->category) === 'telur';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($r->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-[11px] text-slate-500">
                                    #{{ $r->invoice_no }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $isTelur ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $r->category }}
                                    </span>
                                    <span class="font-bold text-slate-900 ml-1">{{ $r->item_name }}</span>
                                </td>
                                <td class="py-3 px-4 text-right font-black text-maroon-800">
                                    -{{ number_format($r->quantity, 0, ',', '.') }} {{ $r->unit }}
                                </td>
                                <td class="py-3 px-4 text-right text-slate-500">
                                    Rp {{ number_format($r->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                    Rp {{ number_format($r->total_price, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-slate-700 font-semibold">
                                    {{ $r->customer_name }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $r->payment_status }} ({{ $r->payment_method }})
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400">Tidak ada transaksi penjualan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($data) > 0)
                        <tfoot>
                            <tr class="bg-slate-100/80 font-black text-slate-900 border-t-2 border-slate-300">
                                <td colspan="3" class="py-3 px-4 text-right uppercase tracking-wider text-[11px]">Total Omzet Penjualan (Barang Keluar):</td>
                                <td colspan="3" class="py-3 px-4 text-right text-sm text-maroon-800">
                                    Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}
                                </td>
                                <td colspan="2" class="py-3 px-4 text-[11px] text-slate-500 font-normal">
                                    {{ $summary['total_transaksi'] }} Transaksi Terdaftar
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @endif

    </div>

    <!-- Tombol Download / Ekspor di Bawah (Sesuai Mockup Layar 4) -->
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
        <a href="{{ route('rekap.export-excel', ['tab' => $tab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-white border-2 border-emerald-600 hover:bg-emerald-50 text-emerald-800 font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
            <span>Export Excel</span>
        </a>
        <button onclick="window.print()" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-white border-2 border-maroon-800 hover:bg-rose-50 text-maroon-800 font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
            <i data-lucide="printer" class="w-4 h-4 text-maroon-800"></i>
            <span>Export PDF</span>
        </button>
    </div>

</div>
@endsection
