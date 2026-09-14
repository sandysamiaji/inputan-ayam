@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('master.index') }}#card-master-flock" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95 shrink-0">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Blok & Klotter (Flock)</h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Manajemen data kandang, populasi ayam & umur flock</p>
            </div>
        </div>

        <!-- Action Buttons in Header -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <button onclick="openModalTambahFlock()" class="px-3.5 py-2 sm:py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-95 flex items-center gap-1.5">
                <i data-lucide="folder-plus" class="w-4 h-4"></i>
                <span>+ Tambah Klotter</span>
            </button>
            <button onclick="openModalTambahCoop()" class="px-3.5 py-2 sm:py-2.5 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-95 flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Tambah Blok</span>
            </button>
            <button onclick="openModalPanenFromFlock()" class="px-3.5 py-2 sm:py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs sm:text-sm shadow-sm transition-all active:scale-95 flex items-center gap-1.5">
                <i data-lucide="egg" class="w-4 h-4"></i>
                <span>Catat Panen Telur</span>
            </button>
        </div>
    </div>

    <!-- Summary Metrics Card -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="farm-card p-4 bg-white border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Klotter</span>
                    <span class="text-lg sm:text-xl font-black text-slate-900">{{ $flocks->count() }} Klotter</span>
                </div>
            </div>
        </div>

        <div class="farm-card p-4 bg-white border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                    <i data-lucide="grid" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Blok</span>
                    <span class="text-lg sm:text-xl font-black text-slate-900">{{ $coops->count() }} Blok Kandang</span>
                </div>
            </div>
        </div>

        <div class="farm-card p-4 bg-white border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-maroon-800 flex items-center justify-center font-bold">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Ayam Aktif</span>
                    <span class="text-lg sm:text-xl font-black text-maroon-800">{{ number_format($coops->sum('active_chickens'), 0, ',', '.') }} Ekor</span>
                </div>
            </div>
        </div>

        <div class="farm-card p-4 bg-white border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Rata-rata Umur</span>
                    <span class="text-lg sm:text-xl font-black text-slate-900">{{ (int) ($coops->avg('chicken_age_weeks') ?: 21) }} Minggu</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Klotter (Flocks) & Blok Kandang (Coops) -->
    @forelse($flocks as $flock)
        @php
            $flockActiveChickens = (int) $flock->coops->sum('active_chickens');
            $flockCapacity = (int) $flock->coops->sum('capacity');
            // Usia real = usia saat diinput + minggu berlalu sejak tanggal masuk
            $flockAgeWeeks = $flock->current_age_weeks;
        @endphp
        <div class="farm-card p-5 sm:p-6 bg-gradient-to-r from-purple-50/40 via-white to-white border border-purple-100 shadow-sm relative">
            
            <!-- Header Klotter Card -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3.5">
                    <div class="w-13 h-13 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center font-black text-xl shadow-inner border border-purple-200 shrink-0">
                        {{ $flock->code ?: 'K' . $flock->id }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-base sm:text-lg font-black text-slate-900">{{ $flock->name }}</h2>
                            @if($flock->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">AKTIF</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">NONAKTIF</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Ras: <span class="font-bold text-slate-700">{{ $flock->breed ?? 'Lohmann Brown' }}</span> • 
                            Masuk: <span class="font-bold text-slate-700">{{ $flock->start_date ? \Carbon\Carbon::parse($flock->start_date)->translatedFormat('d M Y') : '-' }}</span>
                            @if($flock->start_date)
                                <span class="text-purple-700 font-semibold">(Usia: {{ $flockAgeWeeks }} Minggu)</span>
                            @endif
                        </p>
                        @if($flock->notes)
                            <p class="text-[11px] text-slate-400 mt-0.5 italic">{{ $flock->notes }}</p>
                        @endif
                    </div>
                </div>

                <!-- Summary Angka Klotter & Tombol Aksi Klotter -->
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 self-start md:self-center">
                    <div class="text-right text-xs">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Populasi Awal</span>
                        <span class="font-bold text-slate-700">{{ number_format($flock->initial_population, 0, ',', '.') }} Ekor</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                    <div class="text-right text-xs">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Ayam Saat Ini</span>
                        <span class="font-black text-maroon-800 text-sm sm:text-base">{{ number_format($flockActiveChickens, 0, ',', '.') }} Ekor</span>
                    </div>

                    <div class="flex items-center gap-1.5 pl-2 border-l border-slate-200">
                        <button onclick="openModalEditFlock({{ json_encode($flock) }})" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-purple-600 hover:text-purple-700 text-slate-600 transition-colors shadow-xs" title="Edit Klotter">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('master.flocks.destroy', $flock->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Klotter ini beserta seluruh blok kandang di dalamnya?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-rose-600 hover:text-rose-700 text-rose-500 transition-colors shadow-xs" title="Hapus Klotter">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <button onclick="openModalTambahCoopForFlock({{ $flock->id }}, '{{ addslashes($flock->name) }}')" class="px-2.5 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold transition-colors border border-purple-200 flex items-center gap-1" title="Tambah Blok ke Klotter ini">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Blok</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Daftar Blok Kandang Cards Grid -->
            <div class="mt-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                        <span>Daftar Blok Kandang ({{ $flock->coops->count() }} Blok)</span>
                    </h3>
                </div>

                @if($flock->coops->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        @foreach($flock->coops as $coop)
                            @php
                                $percent = $coop->capacity > 0 ? min(100, round(($coop->active_chickens / $coop->capacity) * 100)) : 0;
                            @endphp
                            <div class="p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-maroon-300 hover:shadow-md transition-all space-y-3 relative group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-maroon-800"></span>
                                        <span class="text-sm sm:text-base font-black text-slate-800">{{ $coop->name }}</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-extrabold bg-maroon-50 text-maroon-800 border border-maroon-200">
                                        Umur {{ $coop->chicken_age_weeks }} Mgg
                                    </span>
                                </div>

                                <div class="space-y-1.5 text-xs">
                                    <div class="flex justify-between text-slate-600 font-medium">
                                        <span>Ayam Aktif:</span>
                                        <span class="font-extrabold text-slate-900">{{ number_format($coop->active_chickens, 0, ',', '.') }} Ekor</span>
                                    </div>
                                    <div class="flex justify-between text-slate-500 text-[11px]">
                                        <span>Kapasitas:</span>
                                        <span class="font-semibold text-slate-700">{{ number_format($coop->capacity, 0, ',', '.') }} Ekor ({{ $percent }}%)</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-1">
                                        <div class="bg-maroon-800 h-full rounded-full transition-all" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>

                                <!-- Action Buttons: Panen Telur & Edit -->
                                <div class="pt-2 flex items-center gap-2">
                                    <!-- Direct Egg Harvest Button -->
                                    <button onclick="openModalPanenForCoop({{ $coop->id }}, '{{ addslashes($coop->name) }}', {{ $coop->active_chickens }}, {{ $coop->chicken_age_weeks }})" 
                                            class="flex-1 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200/80 font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition-all active:scale-95"
                                            title="Catat jumlah panen telur masuk dari kandang ini">
                                        <i data-lucide="egg" class="w-3.5 h-3.5 text-amber-600"></i>
                                        <span>Panen Telur</span>
                                    </button>

                                    <!-- Edit Coop Button -->
                                    <button onclick="openModalEditCoop({{ json_encode($coop) }})" 
                                            class="py-2 px-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition-all active:scale-95"
                                            title="Edit Data Blok">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        <span>Edit</span>
                                    </button>
                                    
                                    <!-- Delete Coop Form -->
                                    <form action="{{ route('master.coops.destroy', $coop->id) }}" method="POST" onsubmit="return confirm('Hapus Blok Kandang ini secara permanen?');" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="py-2 px-3 rounded-xl bg-slate-50 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 text-rose-500 font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition-all active:scale-95" title="Hapus Blok">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center bg-purple-50/20 rounded-2xl border border-dashed border-purple-200">
                        <i data-lucide="inbox" class="w-8 h-8 text-purple-300 mx-auto mb-2"></i>
                        <p class="text-xs text-slate-500 font-medium">Belum ada blok kandang di klotter ini.</p>
                        <button onclick="openModalTambahCoopForFlock({{ $flock->id }}, '{{ addslashes($flock->name) }}')" class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition-colors">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Tambah Blok Pertama</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="farm-card p-12 text-center bg-white border border-slate-200">
            <div class="w-16 h-16 rounded-full bg-purple-50 text-purple-700 flex items-center justify-center mx-auto mb-3">
                <i data-lucide="layers" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">Belum Ada Data Klotter (Flock)</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                Silakan tambahkan data klotter pertama Anda untuk mengelompokkan blok kandang dan populasi ayam layer.
            </p>
            <button onclick="openModalTambahFlock()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-md transition-all active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Klotter Sekarang</span>
            </button>
        </div>
    @endforelse

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: TAMBAH KLOTTER (FLOCK) BARU                                      -->
<!-- ========================================================================= -->
<div id="modalTambahFlock" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                    <i data-lucide="folder-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base">Tambah Klotter (Flock)</h3>
                    <p class="text-[11px] text-slate-400">Data populasi induk klotter ayam</p>
                </div>
            </div>
            <button onclick="closeModalTambahFlock()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('master.flocks.store') }}" class="mt-4 space-y-4">
            @csrf

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Klotter *</label>
                    <input type="text" name="name" required placeholder="Contoh: Klotter 2" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:border-purple-600 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode *</label>
                    <input type="text" name="code" placeholder="K2" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-purple-700 focus:border-purple-600 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tgl Masuk ke Kandang</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700 focus:border-purple-600 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Usia Saat Masuk (Minggu) *</label>
                    <input type="number" name="initial_age_weeks" required min="0" value="0" placeholder="Contoh: 14" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-purple-700 focus:border-purple-600 outline-none">
                    <p class="text-[10px] text-slate-400 mt-0.5">Usia nyata ayam saat pertama diinput</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Populasi Awal (Ekor) *</label>
                    <input type="number" name="initial_population" required min="1" placeholder="Contoh: 2000" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-900 focus:border-purple-600 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Ras / Strain Ayam</label>
                <input type="text" name="breed" value="Lohmann Brown" placeholder="Lohmann Brown / Hy-Line / Novogen" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:border-purple-600 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan asal bibit, supplier pullet, dsb." 
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:border-purple-600 outline-none"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-sm shadow-md transition-all active:scale-98 flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Klotter Baru</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: EDIT DATA KLOTTER (FLOCK)                                        -->
<!-- ========================================================================= -->
<div id="modalEditFlock" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-black text-slate-900 text-base">Edit Data Klotter (Flock)</h3>
            <button onclick="closeModalEditFlock()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formEditFlock" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Klotter *</label>
                    <input type="text" id="editFlockName" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode *</label>
                    <input type="text" id="editFlockCode" name="code" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-purple-700">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tgl Masuk ke Kandang</label>
                    <input type="date" id="editFlockDate" name="start_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Usia Saat Masuk (Minggu)</label>
                    <input type="number" id="editFlockInitialAge" name="initial_age_weeks" min="0" value="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-purple-700">
                    <p class="text-[10px] text-slate-400 mt-0.5">Usia nyata ayam saat pertama diinput</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Populasi Awal *</label>
                    <input type="number" id="editFlockInitial" name="initial_population" required min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Ras Ayam</label>
                <input type="text" id="editFlockBreed" name="breed" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                <textarea id="editFlockNotes" name="notes" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700"></textarea>
            </div>

            <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                <input type="checkbox" id="editFlockActive" name="is_active" value="1" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500">
                <label for="editFlockActive" class="text-xs font-bold text-slate-700 cursor-pointer">Klotter Masih Aktif Berproduksi</label>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Perubahan Klotter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: TAMBAH BLOK KANDANG (COOP) BARU                                  -->
<!-- ========================================================================= -->
<div id="modalTambahCoop" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-maroon-100 text-maroon-800 flex items-center justify-center">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base">Tambah Blok Kandang</h3>
                    <p class="text-[11px] text-slate-400">Pendaftaran blok dan kapasitas ayam</p>
                </div>
            </div>
            <button onclick="closeModalTambahCoop()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('master.coops.store') }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Klotter Induk *</label>
                <select id="tambahCoopFlockId" name="flock_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:border-maroon-800 outline-none">
                    @foreach($flocks as $f)
                        <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Blok Kandang *</label>
                <input type="text" name="name" required placeholder="Contoh: Blok D" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:border-maroon-800 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kapasitas Maksimal *</label>
                    <input type="number" name="capacity" required min="1" placeholder="800" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-700 focus:border-maroon-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ayam Aktif (Ekor) *</label>
                    <input type="number" name="active_chickens" required min="0" placeholder="780" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-maroon-800 focus:border-maroon-800 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Umur Ayam Saat Ini (Minggu) *</label>
                <input type="number" name="chicken_age_weeks" required min="1" value="21" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:border-maroon-800 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan (Opsional)</label>
                <input type="text" name="notes" placeholder="Lokasi lorong kandang, ventilasi, dsb." 
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 focus:border-maroon-800 outline-none">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98 flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Blok Kandang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 4: EDIT DATA BLOK KANDANG (COOP)                                    -->
<!-- ========================================================================= -->
<div id="modalEditCoop" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Edit Data Blok Kandang</h3>
            <button onclick="closeModalEditCoop()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="formEditCoop" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Klotter Induk *</label>
                <select id="editCoopFlockId" name="flock_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800">
                    @foreach($flocks as $f)
                        <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Blok *</label>
                <input type="text" id="editCoopName" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ayam Aktif (Ekor) *</label>
                    <input type="number" id="editCoopActive" name="active_chickens" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-maroon-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kapasitas Maksimal *</label>
                    <input type="number" id="editCoopCapacity" name="capacity" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-700">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Umur Ayam (Minggu) *</label>
                <input type="number" id="editCoopAge" name="chicken_age_weeks" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-700">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                <input type="text" id="editCoopNotes" name="notes" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-700">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-sm shadow-md transition-all active:scale-98">
                    Simpan Perubahan Blok
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 5: INPUT PRODUKSI TELUR (PANEN MASUK DARI KANDANG TERHUBUNG)        -->
<!-- ========================================================================= -->
<div id="modalPanenTelur" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                    <i data-lucide="egg" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-base">Catat Panen Telur Masuk</h3>
                    <p class="text-[11px] text-slate-400">Terhubung langsung dengan populasi & umur kandang</p>
                </div>
            </div>
            <button onclick="closeModalPanen()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('production.store') }}" method="POST" class="py-4 space-y-4">
            @csrf

            <!-- Tanggal Panen -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Panen Telur</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}" required 
                       class="w-full text-xs sm:text-sm font-semibold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
            </div>

            <!-- Pilih Blok Kandang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Blok Kandang *</label>
                <select name="coop_id" id="panenCoopSelect" required onchange="updatePanenCoopInfo(this)"
                        class="w-full text-xs sm:text-sm font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                    <option value="">-- Pilih Blok Kandang --</option>
                    @foreach($coops as $c)
                        <option value="{{ $c->id }}" data-active="{{ $c->active_chickens }}" data-age="{{ $c->chicken_age_weeks }}" data-flock="{{ $c->flock ? $c->flock->name : '' }}">
                            {{ $c->name }} ({{ number_format($c->active_chickens, 0, ',', '.') }} Ekor - Umur {{ $c->chicken_age_weeks }} Mgg)
                        </option>
                    @endforeach
                </select>

                <!-- Info Blok Kandang Badge -->
                <div id="panenCoopBadge" class="mt-2.5 p-3 bg-rose-50/70 border border-rose-100 rounded-xl flex items-center justify-between text-xs text-slate-600">
                    <div>
                        <span class="font-bold text-maroon-900 block" id="panenCoopActiveText">Ayam Aktif: -</span>
                        <span class="text-slate-500 text-[11px]">Umur Flock: <b id="panenCoopAgeText" class="text-slate-800">-</b></span>
                    </div>
                    <span class="px-2.5 py-1 bg-white text-maroon-800 font-bold rounded-lg border border-rose-200 text-xs shadow-xs">Blok Siap Panen</span>
                </div>
            </div>

            <!-- Input Telur Masuk & Retak -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Telur Masuk (Butir) *</label>
                    <div class="relative">
                        <input type="number" name="total_eggs" id="panenTotalEggs" required placeholder="0" min="1"
                               oninput="calcPanenPerformance()"
                               class="w-full text-sm font-bold text-slate-900 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                        <span class="absolute right-3 top-2.5 text-xs font-semibold text-slate-400">Butir</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Telur Retak / Pecah</label>
                    <div class="relative">
                        <input type="number" name="broken_eggs" id="panenBrokenEggs" value="0" min="0"
                               oninput="calcPanenPerformance()"
                               class="w-full text-sm font-bold text-rose-700 px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
                        <span class="absolute right-3 top-2.5 text-xs font-semibold text-slate-400">Butir</span>
                    </div>
                </div>
            </div>

            <!-- Estimasi Hen-Day (HD %) & Peti Otomatis -->
            <div class="p-3.5 bg-amber-50/80 border border-amber-200 rounded-xl space-y-2 text-xs sm:text-sm">
                <div class="flex justify-between items-center text-slate-700 font-medium">
                    <span>Produktivitas Hen-Day (HD):</span>
                    <span id="calcHDPercent" class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-black text-sm">0%</span>
                </div>
                <div class="flex justify-between text-slate-600 font-medium">
                    <span>Telur Baik / Utuh:</span>
                    <b id="calcPanenGoodEggs" class="text-slate-900 font-bold">0 Butir</b>
                </div>
                <div class="flex justify-between text-slate-600 font-medium">
                    <span>Estimasi Peti Masuk:</span>
                    <b id="calcPanenCrates" class="text-maroon-800 font-black text-sm sm:text-base">0 Peti</b>
                </div>
            </div>
            <input type="hidden" name="crates_count" id="panenCratesCount" value="0">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Panen (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Panen pagi kondisi bersih, cangkang tebal"
                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-maroon-800 outline-none bg-slate-50">
            </div>

            <div class="flex gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModalPanen()" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 sm:py-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Panen Telur</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // 1. MODAL TAMBAH FLOCK
    function openModalTambahFlock() {
        const modal = document.getElementById('modalTambahFlock');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }
    function closeModalTambahFlock() {
        const modal = document.getElementById('modalTambahFlock');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 2. MODAL EDIT FLOCK
    function openModalEditFlock(flock) {
        document.getElementById('formEditFlock').action = `/master/flocks/${flock.id}/update`;
        document.getElementById('editFlockName').value = flock.name;
        document.getElementById('editFlockCode').value = flock.code || '';
        document.getElementById('editFlockDate').value = flock.start_date ? flock.start_date.substring(0, 10) : '';
        document.getElementById('editFlockInitialAge').value = flock.initial_age_weeks || 0;
        document.getElementById('editFlockInitial').value = flock.initial_population;
        document.getElementById('editFlockBreed').value = flock.breed || '';
        document.getElementById('editFlockNotes').value = flock.notes || '';
        document.getElementById('editFlockActive').checked = flock.is_active ? true : false;

        const modal = document.getElementById('modalEditFlock');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }
    function closeModalEditFlock() {
        const modal = document.getElementById('modalEditFlock');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 3. MODAL TAMBAH COOP
    function openModalTambahCoop() {
        const modal = document.getElementById('modalTambahCoop');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }
    function openModalTambahCoopForFlock(flockId, flockName) {
        const select = document.getElementById('tambahCoopFlockId');
        if (select) select.value = flockId;
        openModalTambahCoop();
    }
    function closeModalTambahCoop() {
        const modal = document.getElementById('modalTambahCoop');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 4. MODAL EDIT COOP
    function openModalEditCoop(coop) {
        document.getElementById('formEditCoop').action = `/master/coops/${coop.id}/update`;
        document.getElementById('editCoopFlockId').value = coop.flock_id;
        document.getElementById('editCoopName').value = coop.name;
        document.getElementById('editCoopActive').value = coop.active_chickens;
        document.getElementById('editCoopCapacity').value = coop.capacity;
        document.getElementById('editCoopAge').value = coop.chicken_age_weeks;
        document.getElementById('editCoopNotes').value = coop.notes || '';

        const modal = document.getElementById('modalEditCoop');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }
    function closeModalEditCoop() {
        const modal = document.getElementById('modalEditCoop');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // 5. MODAL PANEN TELUR TERHUBUNG
    let currentSelectedCoopActive = 0;

    function openModalPanenFromFlock() {
        const modal = document.getElementById('modalPanenTelur');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
        updatePanenCoopInfo(document.getElementById('panenCoopSelect'));
    }

    function openModalPanenForCoop(coopId, coopName, activeChickens, ageWeeks) {
        const select = document.getElementById('panenCoopSelect');
        if (select) {
            select.value = coopId;
            updatePanenCoopInfo(select);
        }
        const modal = document.getElementById('modalPanenTelur');
        const content = modal.querySelector('div');
        modal.classList.add('modal-active');
        content.classList.add('modal-content-active');
    }

    function closeModalPanen() {
        const modal = document.getElementById('modalPanenTelur');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    function updatePanenCoopInfo(selectEl) {
        if (!selectEl) return;
        const selected = selectEl.options[selectEl.selectedIndex];
        if (selected && selected.value) {
            currentSelectedCoopActive = parseInt(selected.getAttribute('data-active') || '0');
            const age = selected.getAttribute('data-age') || '-';
            const flock = selected.getAttribute('data-flock') || '';

            document.getElementById('panenCoopActiveText').innerText = `Ayam Aktif: ${currentSelectedCoopActive.toLocaleString('id-ID')} Ekor`;
            document.getElementById('panenCoopAgeText').innerText = `${age} Minggu ${flock ? '(' + flock + ')' : ''}`;
        } else {
            currentSelectedCoopActive = 0;
            document.getElementById('panenCoopActiveText').innerText = `Ayam Aktif: -`;
            document.getElementById('panenCoopAgeText').innerText = `-`;
        }
        calcPanenPerformance();
    }

    function calcPanenPerformance() {
        const totalEggs = parseInt(document.getElementById('panenTotalEggs').value || '0');
        const brokenEggs = parseInt(document.getElementById('panenBrokenEggs').value || '0');
        const goodEggs = Math.max(0, totalEggs - brokenEggs);

        document.getElementById('calcPanenGoodEggs').innerText = `${goodEggs.toLocaleString('id-ID')} Butir`;

        // 1 Peti estimasi ~ 250 butir (atau ~15kg)
        const crates = (totalEggs / 250).toFixed(1);
        document.getElementById('calcPanenCrates').innerText = `${crates} Peti`;
        document.getElementById('panenCratesCount').value = crates;

        // Hen-Day (HD %) Calculation
        if (currentSelectedCoopActive > 0 && totalEggs > 0) {
            const hd = ((totalEggs / currentSelectedCoopActive) * 100).toFixed(1);
            document.getElementById('calcHDPercent').innerText = `${hd}%`;
            if (hd >= 85) {
                document.getElementById('calcHDPercent').className = "px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-black text-sm";
            } else if (hd >= 70) {
                document.getElementById('calcHDPercent').className = "px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-black text-sm";
            } else {
                document.getElementById('calcHDPercent').className = "px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 font-black text-sm";
            }
        } else {
            document.getElementById('calcHDPercent').innerText = `0%`;
            document.getElementById('calcHDPercent').className = "px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-black text-sm";
        }
    }
</script>
@endpush
@endsection
