@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Info Farm & Dashboard</h1>
                <p class="text-xs text-slate-400">Atur pesan penyemangat & status fase ayam di dashboard</p>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-maroon-300 text-xs font-bold text-slate-700 hover:text-maroon-800 shadow-sm transition-all active:scale-95">
            <i data-lucide="eye" class="w-4 h-4 text-maroon-800"></i>
            <span>Lihat Dashboard</span>
        </a>
    </div>

    <!-- Live Preview Banner Dashboard -->
    <div class="farm-card p-5 sm:p-6 bg-gradient-to-r from-rose-50/70 via-white to-amber-50/40 border border-rose-200/80 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-rose-100 mb-3">
            <span class="text-xs font-extrabold uppercase tracking-wider text-maroon-800 flex items-center gap-1.5">
                <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                Pratinjau Tampilan Dashboard (Live Preview)
            </span>
            <span class="text-[11px] text-slate-400 font-medium">Tampilan yang dilihat petugas</span>
        </div>

        <div class="space-y-2">
            <h3 class="text-lg sm:text-xl font-black text-slate-900 flex items-center gap-2">
                Selamat pagi, Petugas 👋
            </h3>
            <p id="previewMotivation" class="text-xs sm:text-sm text-slate-600 font-medium">
                {{ $settings['dashboard_motivation_message'] }}
            </p>
            <div id="previewStatusContainer" class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs font-semibold shadow-xs">
                <span class="flex h-2 w-2 relative shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                <span id="previewStatus">{{ $settings['dashboard_chicken_status_message'] }}</span>
            </div>
        </div>
    </div>

    <!-- Form Pengaturan Info Farm -->
    <div class="farm-card p-5 sm:p-6">
        <form method="POST" action="{{ route('master.info-farm.update') }}" class="space-y-5">
            @csrf

            <!-- Identitas Peternakan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Peternakan *</label>
                    <input type="text" name="farm_name" value="{{ $settings['farm_name'] }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tagline Peternakan</label>
                    <input type="text" name="farm_tagline" value="{{ $settings['farm_tagline'] }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tgl Pullet Masuk (Global) *</label>
                    <input type="date" name="pullet_in_date" value="{{ $settings['pullet_in_date'] }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Usia Saat Masuk (Minggu)</label>
                    <input type="number" name="pullet_initial_age_weeks" value="{{ $settings['pullet_initial_age_weeks'] ?? 0 }}" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
            </div>

            <!-- Pesan Penyemangat Dashboard -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700">Pesan Penyemangat Dashboard *</label>
                    <span class="text-[11px] text-slate-400">Tampil di salam pembuka</span>
                </div>
                <textarea 
                    name="dashboard_motivation_message" 
                    id="inputMotivation"
                    rows="2" 
                    required 
                    oninput="document.getElementById('previewMotivation').textContent = this.value"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800"
                >{{ $settings['dashboard_motivation_message'] }}</textarea>

                <!-- Template Cepat Penyemangat -->
                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Pilih Cepat:</span>
                    <button type="button" onclick="setMotivationTemplate('Semangat bekerja dan tetap jaga kebersihan serta performa kandang hari ini!')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-[10px] font-semibold text-slate-600 hover:text-maroon-800 border border-slate-200 transition-all">
                        Semangat & Kebersihan
                    </button>
                    <button type="button" onclick="setMotivationTemplate('Kandang bersih, ayam sehat, produksi telur meningkat optimal!')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-[10px] font-semibold text-slate-600 hover:text-maroon-800 border border-slate-200 transition-all">
                        Ayam Sehat & Telur Optimal
                    </button>
                    <button type="button" onclick="setMotivationTemplate('Awali pagi dengan teliti, hitung telur dan cek kesehatan ayam secara seksama!')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-[10px] font-semibold text-slate-600 hover:text-maroon-800 border border-slate-200 transition-all">
                        Cek Teliti Pagi Hari
                    </button>
                </div>
            </div>

            <!-- Pesan Informasi Kondisi & Fase Ayam (Masa Subur / Umur Minggu) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700">Informasi Fase & Kondisi Ayam *</label>
                    <span class="text-[11px] text-amber-700 font-bold flex items-center gap-1">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i> Informasi ayam saat ini
                    </span>
                </div>
                <textarea 
                    name="dashboard_chicken_status_message" 
                    id="inputStatus"
                    rows="2" 
                    required 
                    oninput="document.getElementById('previewStatus').textContent = this.value"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800"
                >{{ $settings['dashboard_chicken_status_message'] }}</textarea>

                <!-- Template Cepat Kondisi Ayam -->
                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Pilih Cepat:</span>
                    <button type="button" onclick="setStatusTemplate('{{ addslashes($farmCondition['status_message']) }}')" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[10px] font-bold text-emerald-800 border border-emerald-300 transition-all flex items-center gap-1 shadow-xs">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>⚡ Otomatis Master (Minggu ke-{{ $farmCondition['dominant_week'] }}: {{ $farmCondition['standard']['fase'] }})</span>
                    </button>
                    <button type="button" onclick="setStatusTemplate('Kondisi ayam saat ini memasuki umur minggu ke-21 (Masa Awal Bertelur Produktif / Subur). Pastikan pencahayaan dan asupan kalsium optimal.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-[10px] font-semibold text-slate-600 hover:text-amber-800 border border-slate-200 transition-all">
                        Minggu ke-21 (Masa Subur / Awal Bertelur)
                    </button>
                    <button type="button" onclick="setStatusTemplate('Ayam berada pada masa puncak produksi telur (Peak Production). Jaga stabilitas suhu kandang dan kecukupan air minum.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-[10px] font-semibold text-slate-600 hover:text-amber-800 border border-slate-200 transition-all">
                        Puncak Produksi (Peak)
                    </button>
                    <button type="button" onclick="setStatusTemplate('Ayam minggu ke-18 (Masa Pullet Menjelang Bertelur). Pantau pertambahan bobot badan dan stimulasi cahaya.')" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-amber-50 text-[10px] font-semibold text-slate-600 hover:text-amber-800 border border-slate-200 transition-all">
                        Minggu ke-18 (Pullet)
                    </button>
                </div>
            </div>

            <!-- Jam Tayang & Pengaktifan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Tayang Mulai</label>
                    <input type="time" name="dashboard_info_schedule_start" value="{{ $settings['dashboard_info_schedule_start'] }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Tayang Selesai</label>
                    <input type="time" name="dashboard_info_schedule_end" value="{{ $settings['dashboard_info_schedule_end'] }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs sm:text-sm">
                </div>
                <div class="flex items-center gap-2 sm:self-end sm:pb-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="dashboard_info_active" value="1" {{ $settings['dashboard_info_active'] === '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-maroon-800"></div>
                        <span class="ml-2.5 text-xs font-bold text-slate-700">Tampilkan Info di Dashboard</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-maroon-800 hover:bg-maroon-900 text-white font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Pengaturan Info Farm</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    function setMotivationTemplate(text) {
        document.getElementById('inputMotivation').value = text;
        document.getElementById('previewMotivation').textContent = text;
    }

    function setStatusTemplate(text) {
        document.getElementById('inputStatus').value = text;
        document.getElementById('previewStatus').textContent = text;
    }
</script>
@endpush
@endsection
