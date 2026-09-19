@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Top Navigation Back & Title Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95 shrink-0" title="Kembali ke Master">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Audit Riwayat & Log Aktivitas</h1>
                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-maroon-800 text-[10px] font-extrabold">Audit Trail</span>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                    <p class="text-xs text-slate-400">Pencatatan real-time seluruh aktivitas pengguna & pemulihan data terhapus</p>
                    @if(!empty($startDate) && !empty($endDate))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200/80 text-[10px] font-extrabold text-maroon-800">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                            <a href="{{ route('master.audit', ['tab' => $tab, 'module' => $module, 'user_id' => $selectedUserId, 'q' => $search]) }}" class="hover:text-rose-600 ml-0.5" title="Hapus Filter Tanggal">×</a>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stat Metric Banner -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 bg-white px-4 py-2.5 rounded-2xl border border-slate-100 shadow-sm">
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Aktivitas</span>
                <span class="text-sm sm:text-base font-extrabold text-slate-800">
                    {{ number_format($totalAktivitas, 0, ',', '.') }}
                </span>
                <span class="text-[10px] block font-medium text-emerald-600">
                    Hari Ini: {{ number_format($aktivitasHariIni, 0, ',', '.') }}
                </span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Data Terhapus</span>
                <span class="text-sm sm:text-base font-extrabold text-rose-700">
                    {{ number_format($totalTerhapus, 0, ',', '.') }}
                </span>
                <span class="text-[10px] block font-medium {{ $totalTerhapusBelumRestore > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                    {{ $totalTerhapusBelumRestore > 0 ? $totalTerhapusBelumRestore . ' Belum Dipulihkan' : 'Nihil Sampah' }}
                </span>
            </div>
            <div class="hidden sm:block h-7 w-px bg-slate-200"></div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] uppercase font-bold text-purple-800 block flex items-center gap-1">
                    <i data-lucide="rotate-ccw" class="w-3 h-3 text-purple-600"></i> Dipulihkan
                </span>
                <span class="text-xs sm:text-sm font-extrabold text-purple-700">
                    {{ number_format($totalRestored, 0, ',', '.') }} Data
                </span>
                <span class="text-[10px] block text-slate-400 font-medium">Restored</span>
            </div>
        </div>
    </div>

    <!-- Banner Edukasi & Fitur Pemulihan (Khusus Admin / Pemegang Izin) -->
    <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-rose-50/70 via-white to-amber-50/70 border border-rose-200/60 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-maroon-800 text-white flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Pusat Keamanan & Pemulihan Data Farm</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                        Auto-Archived
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Setiap penambahan, pengubahan, dan penghapusan data tersimpan aman dengan snapshot lengkap. Data yang tidak sengaja terhapus dapat dipulihkan kapan saja ke database asli seperti semula oleh Administrator.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-end sm:self-center">
            @if($canRestore)
                <a href="{{ route('master.audit', ['tab' => 'terhapus']) }}" class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-maroon-800 text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-700"></i>
                    <span>Lihat Data Terhapus ({{ $totalTerhapusBelumRestore }})</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Bar: Search, Modul, User, & Tanggal -->
    <div class="farm-card p-3.5 sm:p-4">
        <form method="GET" action="{{ route('master.audit') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5 sm:gap-3 items-center">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- Search Input -->
            <div class="relative lg:col-span-2">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ $search }}" 
                    placeholder="Cari aktivitas, nama petugas, tabel..." 
                    class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800 transition-all"
                >
                @if($search)
                    <a href="{{ route('master.audit', array_filter(['tab' => $tab, 'module' => $module, 'user_id' => $selectedUserId, 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>

            <!-- Modul Dropdown -->
            <div>
                <select name="module" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800/20">
                    <option value="semua">Semua Modul</option>
                    <option value="telur" {{ $module === 'telur' ? 'selected' : '' }}>🥚 Produksi Telur</option>
                    <option value="pakan" {{ $module === 'pakan' ? 'selected' : '' }}>🌾 Konsumsi Pakan</option>
                    <option value="mortalitas" {{ $module === 'mortalitas' ? 'selected' : '' }}>💀 Mortalitas & Afkir</option>
                    <option value="karantina" {{ $module === 'karantina' ? 'selected' : '' }}>🛡️ Ayam Karantina</option>
                    <option value="bobot" {{ $module === 'bobot' ? 'selected' : '' }}>⚖️ Sampling Bobot</option>
                    <option value="obat" {{ $module === 'obat' ? 'selected' : '' }}>💊 Vaksin & Obat</option>
                    <option value="gudang" {{ $module === 'gudang' ? 'selected' : '' }}>📦 Gudang / Stok</option>
                    <option value="master" {{ $module === 'master' ? 'selected' : '' }}>⚙️ Master Kandang/Flock</option>
                    <option value="pengguna" {{ $module === 'pengguna' ? 'selected' : '' }}>👤 Pengguna & Akun</option>
                    <option value="auth" {{ $module === 'auth' ? 'selected' : '' }}>🔑 Login & Logout</option>
                </select>
            </div>

            <!-- Petugas / User Dropdown -->
            <div>
                <select name="user_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-800/20">
                    <option value="">Semua Petugas</option>
                    @foreach($usersList as $u)
                        <option value="{{ $u->id }}" {{ (string)$selectedUserId === (string)$u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->username ? '@' . ltrim($u->username, '@') : $u->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Filter Button & Reset -->
            <div class="flex items-center gap-1.5">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-1">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan</span>
                </button>
                @if($search || $module || $selectedUserId || $startDate || $endDate)
                    <a href="{{ route('master.audit', ['tab' => $tab]) }}" class="py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Filter Tabs: Semua | Data Terhapus (Restore) | Update | Create | Dipulihkan -->
    <div class="flex items-center border-b border-slate-200 gap-4 sm:gap-7 px-1 overflow-x-auto">
        <a href="{{ route('master.audit', array_filter(['tab' => 'semua', 'module' => $module, 'user_id' => $selectedUserId, 'q' => $search, 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 {{ $tab === 'semua' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>Semua Aktivitas</span>
            @if($tab === 'semua')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>

        <a href="{{ route('master.audit', array_filter(['tab' => 'terhapus', 'module' => $module, 'user_id' => $selectedUserId, 'q' => $search, 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'terhapus' ? 'text-rose-700 font-extrabold' : 'text-slate-400 hover:text-slate-600' }}">
            <span>🗑️ Data Terhapus</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ $tab === 'terhapus' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-700' }}">
                {{ $totalTerhapus }}
            </span>
            @if($tab === 'terhapus')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-rose-700 rounded-full"></span>
            @endif
        </a>

        <a href="{{ route('master.audit', array_filter(['tab' => 'update', 'module' => $module, 'user_id' => $selectedUserId, 'q' => $search, 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'update' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>✏️ Perubahan (Update)</span>
            @if($tab === 'update')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>

        <a href="{{ route('master.audit', array_filter(['tab' => 'create', 'module' => $module, 'user_id' => $selectedUserId, 'q' => $search, 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'create' ? 'text-maroon-800' : 'text-slate-400 hover:text-slate-600' }}">
            <span>＋ Data Baru (Create)</span>
            @if($tab === 'create')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-maroon-800 rounded-full"></span>
            @endif
        </a>

        <a href="{{ route('master.audit', array_filter(['tab' => 'restored', 'module' => $module, 'user_id' => $selectedUserId, 'q' => $search, 'start_date' => $startDate, 'end_date' => $endDate])) }}" class="pb-3 text-xs sm:text-sm font-bold transition-all relative shrink-0 flex items-center gap-1.5 {{ $tab === 'restored' ? 'text-purple-800 font-extrabold' : 'text-slate-400 hover:text-slate-600' }}">
            <span>🔄 Sudah Dipulihkan</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ $tab === 'restored' ? 'bg-purple-800 text-white' : 'bg-purple-100 text-purple-700' }}">
                {{ $totalRestored }}
            </span>
            @if($tab === 'restored')
                <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-purple-800 rounded-full"></span>
            @endif
        </a>
    </div>

    <!-- List Data Audit & Rekaman Aktivitas -->
    <div class="space-y-2.5">
        @forelse($items as $item)
            @php
                $isDelete = $item->action === 'DELETE';
                $isCreate = $item->action === 'CREATE';
                $isUpdate = $item->action === 'UPDATE';
                $isRestore = $item->action === 'RESTORE';
                $isLogin = $item->action === 'LOGIN';
                $isLogout = $item->action === 'LOGOUT';

                // Modul Icon & Badge Color
                $modulColor = match($item->module) {
                    'telur' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                    'pakan' => 'bg-amber-50 text-amber-800 border-amber-200',
                    'mortalitas' => 'bg-rose-50 text-rose-800 border-rose-200',
                    'karantina' => 'bg-orange-50 text-orange-800 border-orange-200',
                    'bobot' => 'bg-blue-50 text-blue-800 border-blue-200',
                    'obat' => 'bg-teal-50 text-teal-800 border-teal-200',
                    'gudang' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                    'master' => 'bg-slate-100 text-slate-800 border-slate-300',
                    'pengguna' => 'bg-purple-50 text-purple-800 border-purple-200',
                    'auth' => 'bg-zinc-100 text-zinc-800 border-zinc-300',
                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                };

                // Action Badge Styling
                $actionBadge = match($item->action) {
                    'DELETE' => ['label' => 'HAPUS', 'class' => 'bg-rose-600 text-white'],
                    'CREATE' => ['label' => 'TAMBAH', 'class' => 'bg-emerald-600 text-white'],
                    'UPDATE' => ['label' => 'UBAH', 'class' => 'bg-blue-600 text-white'],
                    'RESTORE' => ['label' => 'PULIHKAN', 'class' => 'bg-purple-600 text-white'],
                    'LOGIN' => ['label' => 'MASUK', 'class' => 'bg-amber-600 text-white'],
                    'LOGOUT' => ['label' => 'KELUAR', 'class' => 'bg-slate-600 text-white'],
                    default => ['label' => $item->action, 'class' => 'bg-slate-600 text-white'],
                };
            @endphp
            <div class="farm-card p-3.5 sm:p-4 hover:border-maroon-200 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 {{ $isDelete && !$item->is_restored ? 'bg-rose-50/30 border-rose-200/80' : '' }}">
                
                <!-- Left Details & Description -->
                <div class="flex items-start gap-3.5 min-w-0 flex-1">
                    
                    <!-- Action Icon Circle -->
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 shadow-inner mt-0.5 {{ $isDelete ? 'bg-rose-100 text-rose-700' : ($isCreate ? 'bg-emerald-100 text-emerald-700' : ($isUpdate ? 'bg-blue-100 text-blue-700' : ($isRestore ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700'))) }}">
                        @if($isDelete)
                            <i data-lucide="trash-2" class="w-5 h-5 stroke-[2]"></i>
                        @elseif($isCreate)
                            <i data-lucide="plus-circle" class="w-5 h-5 stroke-[2]"></i>
                        @elseif($isUpdate)
                            <i data-lucide="edit-3" class="w-5 h-5 stroke-[2]"></i>
                        @elseif($isRestore)
                            <i data-lucide="rotate-ccw" class="w-5 h-5 stroke-[2]"></i>
                        @elseif($isLogin)
                            <i data-lucide="log-in" class="w-5 h-5 stroke-[2]"></i>
                        @elseif($isLogout)
                            <i data-lucide="log-out" class="w-5 h-5 stroke-[2]"></i>
                        @else
                            <i data-lucide="activity" class="w-5 h-5 stroke-[2]"></i>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <!-- Badges: Action, Module, Table, & Restore Status -->
                        <div class="flex flex-wrap items-center gap-1.5 mb-1">
                            <span class="px-2 py-0.5 rounded text-[9.5px] font-black uppercase tracking-wider {{ $actionBadge['class'] }}">
                                {{ $actionBadge['label'] }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[9.5px] font-bold uppercase border {{ $modulColor }}">
                                {{ strtoupper($item->module) }}
                            </span>
                            @if($item->is_restored)
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                                    <span>SUDAH DIPULIHKAN</span>
                                </span>
                            @elseif($isDelete)
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                    TERHAPUS
                                </span>
                            @endif
                        </div>

                        <!-- Deskripsi Aktivitas -->
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800 leading-snug">
                            {{ $item->description }}
                        </h2>

                        <!-- Meta Info: Petugas, Tanggal, IP -->
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1.5 text-[11px] text-slate-400">
                            <span class="font-semibold text-slate-700 flex items-center gap-1">
                                <i data-lucide="user" class="w-3 h-3 text-slate-400"></i>
                                <span>{{ $item->user_name }}</span>
                                <span class="text-[9.5px] px-1 py-0.1 rounded bg-slate-100 text-slate-600 font-bold uppercase">{{ $item->user_role ?? 'user' }}</span>
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1 text-slate-500">
                                <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                                <span>{{ $item->created_at->translatedFormat('d M Y, H:i') }}</span>
                                <span class="text-slate-400 font-normal">({{ $item->created_at->diffForHumans() }})</span>
                            </span>
                            @if(!empty($item->ip_address))
                                <span>•</span>
                                <span class="text-slate-400 font-mono text-[10px]">IP: {{ $item->ip_address }}</span>
                            @endif
                        </div>

                        <!-- Catatan Restore Jika Ada -->
                        @if($item->is_restored && $item->restored_at)
                            <div class="mt-1.5 text-[10px] text-purple-700 font-semibold flex items-center gap-1">
                                <i data-lucide="info" class="w-3 h-3"></i>
                                <span>Dipulihkan oleh <b>{{ $item->restored_by_name ?? 'Admin' }}</b> pada {{ \Carbon\Carbon::parse($item->restored_at)->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2 self-end sm:self-center shrink-0 w-full sm:w-auto justify-end pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    
                    <!-- Tombol Detail Snapshot -->
                    <button onclick="openAuditModal({{ $item->id }})" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Detail</span>
                    </button>

                    <!-- Tombol Restore (Hanya untuk aksi DELETE yang belum direstore dan user berhak akses) -->
                    @if($isDelete && !$item->is_restored)
                        @if($canRestore)
                            <form method="POST" action="{{ route('master.audit.restore', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin memulihkan (restore) data ini kembali ke sistem database? Data akan dikembalikan seperti semula.');">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 active:scale-95">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Restore Data</span>
                                </button>
                            </form>
                        @else
                            <span class="px-2 py-1 rounded text-[10px] font-semibold text-slate-400 bg-slate-100" title="Hanya Admin yang berwenang memulihkan data">
                                Terkunci
                            </span>
                        @endif
                    @endif

                </div>

            </div>
        @empty
            <div class="farm-card p-10 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <i data-lucide="history" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada riwayat aktivitas ditemukan</h3>
                <p class="text-xs text-slate-400 mt-1">Belum ada catatan log aktivitas untuk kriteria pencarian dan tab filter ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL SNAPSHOT DATA AUDIT -->
<!-- ========================================================================= -->
<div id="modalAuditDetail" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-300 flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="bg-white w-full sm:max-w-2xl rounded-t-3xl sm:rounded-3xl p-6 shadow-2xl transform translate-y-full sm:translate-y-0 transition-transform duration-300 max-h-[92vh] overflow-y-auto">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div id="modalIconBox" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold shadow-xs">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800 text-sm sm:text-base">Detail Snapshot Audit</h3>
                    <p id="modalSubTitle" class="text-[10px] text-slate-400 font-medium">Informasi mendalam aktivitas sistem</p>
                </div>
            </div>
            <button onclick="closeAuditModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Content Area -->
        <div class="mt-4 space-y-4">
            
            <!-- Deskripsi Box -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ringkasan Aktivitas</span>
                <p id="modalDescription" class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">-</p>
            </div>

            <!-- Meta Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 block font-medium">Petugas</span>
                    <b id="modalUser" class="text-slate-800 font-bold block truncate">-</b>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 block font-medium">Aksi / Modul</span>
                    <b id="modalActionModule" class="text-slate-800 font-bold block truncate">-</b>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 block font-medium">Waktu Tercatat</span>
                    <b id="modalDate" class="text-slate-800 font-bold block truncate">-</b>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] text-slate-400 block font-medium">IP Klien</span>
                    <b id="modalIp" class="text-slate-800 font-mono text-[11px] block truncate">-</b>
                </div>
            </div>

            <!-- JSON Snapshot / Changes Box -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label id="modalDataTitle" class="block text-xs font-bold text-slate-700">Snapshot Data Atribut (JSON)</label>
                    <span class="text-[10px] text-slate-400 font-medium">Tersimpan di tabel audit_logs</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-900 text-slate-100 font-mono text-[11px] max-h-60 overflow-y-auto shadow-inner">
                    <pre id="modalRawJson" class="whitespace-pre-wrap break-all leading-relaxed">{}</pre>
                </div>
            </div>

            <!-- User Agent Info -->
            <div class="text-[10px] text-slate-400 truncate">
                <span>Perangkat / Browser: </span>
                <span id="modalUserAgent" class="font-mono text-slate-500">-</span>
            </div>

            <!-- Action Modal Footer -->
            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button onclick="closeAuditModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Tutup
                </button>
                <div id="modalRestoreBtnContainer"></div>
            </div>

        </div>

    </div>
</div>

@push('scripts')
<script>
    const canUserRestore = @json($canRestore);

    function openAuditModal(id) {
        const modal = document.getElementById('modalAuditDetail');
        const content = modal.querySelector('div');

        // Loading state
        document.getElementById('modalDescription').textContent = 'Memuat data audit...';
        document.getElementById('modalRawJson').textContent = 'Memuat data...';

        fetch(`/master/audit/${id}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    alert('Gagal mengambil data audit.');
                    return;
                }
                const d = res.data;

                document.getElementById('modalDescription').textContent = d.description || '-';
                document.getElementById('modalSubTitle').textContent = `ID #${d.id} • Tabel: ${d.table_name || 'Umum'} • Record #${d.model_id || '-'}`;
                document.getElementById('modalUser').textContent = `${d.user_name} (${d.user_role})`;
                document.getElementById('modalActionModule').textContent = `${d.action} / ${d.module}`;
                document.getElementById('modalDate').textContent = d.date_formatted;
                document.getElementById('modalIp').textContent = d.ip_address || '-';
                document.getElementById('modalUserAgent').textContent = d.user_agent || '-';

                // Display JSON
                let dataToDisplay = d.original_data;
                if (d.action === 'UPDATE' && d.changes) {
                    document.getElementById('modalDataTitle').textContent = 'Perubahan Kolom (Sebelum vs Sesudah)';
                    dataToDisplay = d.changes;
                } else {
                    document.getElementById('modalDataTitle').textContent = 'Snapshot Data Atribut Lengkap (JSON)';
                    dataToDisplay = d.original_data || { message: 'Tidak ada snapshot data mentah' };
                }

                document.getElementById('modalRawJson').textContent = JSON.stringify(dataToDisplay, null, 2);

                // Restore Button in Modal Footer
                const btnContainer = document.getElementById('modalRestoreBtnContainer');
                btnContainer.innerHTML = '';

                if (d.action === 'DELETE' && !d.is_restored && canUserRestore) {
                    btnContainer.innerHTML = `
                        <form method="POST" action="/master/audit/${d.id}/restore" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan (restore) data ini kembali ke database seperti semula?');">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')}">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5 active:scale-95">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Pulihkan Data Ini</span>
                            </button>
                        </form>
                    `;
                    lucide.createIcons();
                } else if (d.is_restored) {
                    btnContainer.innerHTML = `
                        <span class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold">
                            Sudah Dipulihkan (${d.restored_by_name || 'Admin'})
                        </span>
                    `;
                }

                modal.classList.add('modal-active');
                content.classList.add('modal-content-active');
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat memuat detail audit.');
            });
    }

    function closeAuditModal() {
        const modal = document.getElementById('modalAuditDetail');
        const content = modal.querySelector('div');
        modal.classList.remove('modal-active');
        content.classList.remove('modal-content-active');
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAuditModal();
        }
    });
</script>
@endpush
@endsection
