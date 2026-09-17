@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('master.index') }}" class="hover:text-maroon-800 transition-colors">Master Data</a>
                <span>/</span>
                <span class="text-maroon-800">Hak Akses & Pengguna</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-maroon-800 shrink-0">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </span>
                <span>Manajemen Hak Akses & Pengguna</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola hak akses menu dan fitur aplikasi input dengan toggle switch untuk setiap akun pengguna.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="openAddUserModal()" class="px-4 py-2.5 bg-maroon-800 hover:bg-maroon-900 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm active:scale-95 transition-all">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah Pengguna</span>
            </button>
            <a href="{{ route('master.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- User Selector Tabs (Horizontal Scrollable on Mobile) -->
    <div class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2.5 px-1">
            Pilih Pengguna yang Diatur:
        </label>
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
            @foreach($users as $u)
                @php
                    $isSelected = $selectedUser && $selectedUser->id === $u->id;
                @endphp
                <a href="{{ route('master.permissions', ['user_id' => $u->id]) }}" 
                   class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl border text-xs font-bold transition-all shrink-0 {{ $isSelected ? 'bg-maroon-800 text-white border-maroon-900 shadow-md scale-102' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black {{ $isSelected ? 'bg-white/20 text-white' : 'bg-rose-100 text-maroon-800' }}">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                    </div>
                    <div class="text-left">
                        <div class="flex items-center gap-1.5">
                            <span>{{ $u->name }}</span>
                            @if($u->role === 'admin')
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase {{ $isSelected ? 'bg-amber-400 text-maroon-950' : 'bg-amber-100 text-amber-800' }}">Admin</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase {{ $isSelected ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">User</span>
                            @endif
                        </div>
                        <div class="text-[10px] {{ $isSelected ? 'text-rose-100' : 'text-slate-400' }} font-normal">
                            {{ '@' . $u->username }} • {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    @if($selectedUser)
        <!-- Selected User Profile Card & Quick Actions -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-maroon-700 to-rose-900 text-white flex items-center justify-center text-xl font-black shadow-md border-2 border-white">
                        {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg sm:text-xl font-black text-slate-800">{{ $selectedUser->name }}</h2>
                            @if($selectedUser->role === 'admin')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-900 border border-amber-200 flex items-center gap-1">
                                    <i data-lucide="crown" class="w-3 h-3 text-amber-700"></i>
                                    Full Access Admin
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-blue-100 text-blue-900 border border-blue-200 flex items-center gap-1">
                                    <i data-lucide="user" class="w-3 h-3 text-blue-700"></i>
                                    Custom Role User
                                </span>
                            @endif

                            @if($selectedUser->is_active)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Akun Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    Akun Nonaktif
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Username: <span class="font-bold text-slate-700">{{ '@' . $selectedUser->username }}</span> • 
                            Email: <span class="font-medium text-slate-600">{{ $selectedUser->email }}</span>
                        </p>
                    </div>
                </div>

                <!-- User Management Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="openEditUserModal({{ json_encode($selectedUser) }})" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-colors">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        <span>Edit / Reset Password</span>
                    </button>

                    @if($selectedUser->id !== auth()->id())
                        <form action="{{ route('master.users.toggle-active', $selectedUser->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif akun ini?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-2 {{ $selectedUser->is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }} rounded-xl text-xs font-bold flex items-center gap-1.5 transition-colors">
                                <i data-lucide="{{ $selectedUser->is_active ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                                <span>{{ $selectedUser->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($selectedUser->role === 'admin')
                <!-- Notice for Admin Role -->
                <div class="mt-5 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="info" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-900">Perhatian: Hak Akses Administrator</h4>
                        <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                            Pengguna dengan role <strong>Admin</strong> secara otomatis memiliki <strong>Full Access</strong> ke semua menu dan semua fitur aplikasi input. Toggle di bawah ini berstatus aktif permanen dan tidak dapat dimatikan. Jika ingin membatasi hak akses pengguna ini, ubah role-nya menjadi <strong>User</strong> pada tombol "Edit / Reset Password".
                        </p>
                    </div>
                </div>
            @else
                <!-- Presets & Bulk Buttons for Standard User -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <span class="text-xs font-bold text-slate-600">Aksi Cepat Izin:</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="template_field">
                            <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-maroon-800 border border-rose-200 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                                <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i>
                                <span>Preset Petugas Lapangan</span>
                            </button>
                        </form>

                        <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="enable_all">
                            <button type="submit" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                                <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                <span>Aktifkan Semua</span>
                            </button>
                        </form>

                        <form action="{{ route('master.permissions.bulk', $selectedUser->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="disable_all">
                            <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition-all flex items-center gap-1">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                <span>Nonaktifkan Semua</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Permission Categories & Feature Toggle Switches -->
        <div class="space-y-5">
            @foreach($allPermissions as $categoryKey => $category)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="bg-slate-50/80 px-5 py-3.5 border-b border-slate-200/70 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-rose-50 text-maroon-800 border border-rose-100 flex items-center justify-center">
                                <i data-lucide="{{ $category['icon'] ?? 'check-circle' }}" class="w-4 h-4"></i>
                            </div>
                            <h3 class="text-xs sm:text-sm font-black text-slate-800 uppercase tracking-wider">
                                {{ $category['label'] }}
                            </h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400">
                            {{ count($category['items']) }} Fitur
                        </span>
                    </div>

                    <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @foreach($category['items'] as $itemKey => $item)
                            @php
                                $isEnabled = $selectedUser->role === 'admin' ? true : ($userPermissionsMap[$itemKey] ?? false);
                                $isDisabledByAdmin = $selectedUser->role === 'admin';
                            @endphp
                            <div class="p-3.5 rounded-xl border {{ $isEnabled ? 'bg-white border-slate-200 hover:border-maroon-300' : 'bg-slate-50/70 border-slate-200/60 opacity-80' }} transition-all flex items-center justify-between gap-3">
                                <div class="pr-2">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs font-bold text-slate-800">{{ $item['label'] }}</h4>
                                        <span id="badge-{{ $itemKey }}" class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $isEnabled ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $isEnabled ? 'AKTIF' : 'NONAKTIF' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $item['desc'] }}</p>
                                    <span class="text-[9px] font-mono text-slate-400 mt-1 block">{{ $itemKey }}</span>
                                </div>

                                <!-- Toggle Switch Button (Modern iOS / Tailwind style) -->
                                <div class="shrink-0">
                                    <label class="relative inline-flex items-center {{ $isDisabledByAdmin ? 'cursor-not-allowed opacity-75' : 'cursor-pointer' }}">
                                        <input type="checkbox" 
                                               id="toggle-{{ $itemKey }}"
                                               value="{{ $itemKey }}"
                                               class="sr-only peer"
                                               {{ $isEnabled ? 'checked' : '' }}
                                               {{ $isDisabledByAdmin ? 'disabled' : '' }}
                                               onchange="togglePermissionAjax('{{ $selectedUser->id }}', '{{ $itemKey }}', this.checked)">
                                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-maroon-800 shadow-inner"></div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<!-- MODAL: Tambah Pengguna Baru -->
<div id="addUserModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs opacity-0 invisible pointer-events-none transition-all duration-200 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl transform scale-95 transition-all duration-200">
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-sm sm:text-base">Tambah Pengguna Baru</h3>
            </div>
            <button onclick="closeAddUserModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('master.users.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Username (Untuk Login)</label>
                <input type="text" name="username" required placeholder="Contoh: budi12" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email (Opsional)</label>
                <input type="email" name="email" placeholder="Contoh: budi@nochifarm.com" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required placeholder="Minimal 4 karakter" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Role / Peran</label>
                <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
                    <option value="user" selected>User (Akses dapat diatur toggle)</option>
                    <option value="admin">Administrator (Full Access tanpa batas)</option>
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-maroon-800 hover:bg-maroon-900 text-white rounded-xl text-xs font-bold shadow-md">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Edit Pengguna / Reset Password -->
<div id="editUserModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs opacity-0 invisible pointer-events-none transition-all duration-200 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl transform scale-95 transition-all duration-200">
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-maroon-800 flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <h3 class="font-bold text-slate-800 text-sm sm:text-base">Edit Data / Reset Password</h3>
            </div>
            <button onclick="closeEditUserModal()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="editUserForm" action="" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
                <input type="text" id="edit_username" name="username" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                <input type="email" id="edit_email" name="email" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Password Baru <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" id="edit_password" name="password" placeholder="Ketik password baru untuk mereset" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Role</label>
                    <select id="edit_role" name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
                        <option value="user">User</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Akun</label>
                    <select id="edit_is_active" name="is_active" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:border-maroon-800">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-maroon-800 hover:bg-maroon-900 text-white rounded-xl text-xs font-bold shadow-md">
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Toast Notification for AJAX toggle -->
<div id="ajaxToast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 pointer-events-none transition-all duration-300 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-bold">
    <i id="ajaxToastIcon" data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
    <span id="ajaxToastMessage">Izin berhasil diperbarui</span>
</div>

<script>
    // AJAX Toggle Switch
    function togglePermissionAjax(userId, permissionKey, isEnabled) {
        const badge = document.getElementById('badge-' + permissionKey);
        const checkbox = document.getElementById('toggle-' + permissionKey);

        fetch(`/master/hak-akses/${userId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                permission_key: permissionKey,
                is_enabled: isEnabled ? 1 : 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (badge) {
                    badge.textContent = isEnabled ? 'AKTIF' : 'NONAKTIF';
                    badge.className = isEnabled 
                        ? 'px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800'
                        : 'px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-600';
                }
                showToast(data.message || 'Hak akses berhasil diperbarui!', true);
            } else {
                // Kembalikan checkbox jika gagal
                if (checkbox) checkbox.checked = !isEnabled;
                showToast(data.message || 'Gagal mengubah hak akses.', false);
            }
        })
        .catch(err => {
            if (checkbox) checkbox.checked = !isEnabled;
            showToast('Terjadi kesalahan jaringan.', false);
        });
    }

    function showToast(msg, isSuccess) {
        const toast = document.getElementById('ajaxToast');
        const message = document.getElementById('ajaxToastMessage');
        const icon = document.getElementById('ajaxToastIcon');

        message.textContent = msg;
        if (isSuccess) {
            icon.setAttribute('data-lucide', 'check-circle');
            icon.className = 'w-4 h-4 text-emerald-400';
        } else {
            icon.setAttribute('data-lucide', 'alert-circle');
            icon.className = 'w-4 h-4 text-rose-400';
        }
        lucide.createIcons();

        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
        }, 2500);
    }

    // Modal Add User
    function openAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.querySelector('.transform').classList.remove('scale-95');
    }
    function closeAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
        modal.querySelector('.transform').classList.add('scale-95');
    }

    // Modal Edit User
    function openEditUserModal(user) {
        const modal = document.getElementById('editUserModal');
        document.getElementById('editUserForm').action = `/master/users/${user.id}/update`;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_is_active').value = user.is_active ? '1' : '0';

        modal.classList.remove('opacity-0', 'invisible', 'pointer-events-none');
        modal.querySelector('.transform').classList.remove('scale-95');
    }
    function closeEditUserModal() {
        const modal = document.getElementById('editUserModal');
        modal.classList.add('opacity-0', 'invisible', 'pointer-events-none');
        modal.querySelector('.transform').classList.add('scale-95');
    }
</script>
@endsection
