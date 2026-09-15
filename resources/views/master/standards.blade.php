@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('master.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95 shrink-0" title="Kembali ke Master Hub">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Master Standar Ayam Layer</h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-maroon-50 text-maroon-800 border border-maroon-200 shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-maroon-800 animate-pulse"></span>
                        Umur Farm: {{ $avgAgeWeeks }} Minggu
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Acuan data performa harian umur 13 sampai 90 minggu (Grower s/d Afkir)</p>
            </div>
        </div>

        <!-- Quick Jump to Current Age -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="jumpToCurrentWeek({{ $avgAgeWeeks }})" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-maroon-300 hover:text-maroon-800 text-slate-700 font-bold text-xs shadow-xs transition-all flex items-center gap-1.5">
                <i data-lucide="crosshair" class="w-4 h-4 text-amber-500"></i>
                <span>Lompat ke Umur Farm (Mgg {{ $avgAgeWeeks }})</span>
            </button>
        </div>
    </div>

    <!-- Elegant Tab Navigation -->
    <div class="border-b border-slate-200 bg-white rounded-2xl shadow-xs px-2 sm:px-4 pt-2">
        <nav class="flex space-x-2 sm:space-x-4 overflow-x-auto no-scrollbar" aria-label="Tabs">
            <!-- Tab Standar Produksi -->
            <a href="{{ route('master.standar-produksi') }}"
               class="whitespace-nowrap pb-3.5 pt-2 px-3 border-b-2 font-bold text-xs sm:text-sm transition-all flex items-center gap-2 {{ $activeTab === 'produksi' ? 'border-maroon-800 text-maroon-800 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center {{ $activeTab === 'produksi' ? 'bg-maroon-100 text-maroon-800' : 'bg-slate-100 text-slate-500' }}">🥚</span>
                <span>Standar Produksi</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'produksi' ? 'bg-maroon-800 text-white' : 'bg-slate-200 text-slate-600' }}">HDP & Telur</span>
            </a>

            <!-- Tab Standar Pakan -->
            <a href="{{ route('master.standar-pakan') }}"
               class="whitespace-nowrap pb-3.5 pt-2 px-3 border-b-2 font-bold text-xs sm:text-sm transition-all flex items-center gap-2 {{ $activeTab === 'pakan' ? 'border-maroon-800 text-maroon-800 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center {{ $activeTab === 'pakan' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500' }}">🌾</span>
                <span>Standar Pakan</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'pakan' ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-600' }}">Gram/Ekor</span>
            </a>

            <!-- Tab Standar BB -->
            <a href="{{ route('master.standar-bb') }}"
               class="whitespace-nowrap pb-3.5 pt-2 px-3 border-b-2 font-bold text-xs sm:text-sm transition-all flex items-center gap-2 {{ $activeTab === 'bb' ? 'border-maroon-800 text-maroon-800 font-black' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                <span class="w-7 h-7 rounded-lg flex items-center justify-center {{ $activeTab === 'bb' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' }}">⚖</span>
                <span>Standar Bobot (BB)</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'bb' ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600' }}">Kg Sampling</span>
            </a>

        </nav>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-xs sm:text-sm font-bold shadow-xs animate-fade-in">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- TAB 1: STANDAR PRODUKSI -->
    @if($activeTab === 'produksi')
        <!-- Metric Highlight Produksi -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Hen Day Puncak</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-emerald-600">96.0%</span>
                    <span class="text-xs text-slate-500 font-semibold">Umur 23–28 Mgg</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Berat Telur Puncak</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-amber-600">60.0 - 64.0</span>
                    <span class="text-xs text-slate-500 font-semibold">gram / butir</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Acuan Farm (Minggu {{ $avgAgeWeeks }})</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-maroon-800">{{ $currentStd['hd_target'] ?? 90 }}%</span>
                    <span class="text-xs text-slate-500 font-semibold">{{ $currentStd['berat_telur'] ?? '59.5 g' }}</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Master Terdata</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-slate-900">{{ $weeklyStandards->count() }}</span>
                    <span class="text-xs text-slate-500 font-semibold">Minggu (13 s/d 90)</span>
                </div>
            </div>
        </div>

    <!-- TAB 2: STANDAR PAKAN -->
    @elseif($activeTab === 'pakan')
        <!-- Metric Highlight Pakan -->
        @php
            $stdGram = (float) ($currentStd['gram_pakan'] ?? 110);
            $totalPakanTodayKg = round(($totalChickens * $stdGram) / 1000, 1);
            $totalKarungToday = floor($totalPakanTodayKg / 50);
            $sisaKgToday = round(fmod($totalPakanTodayKg, 50), 1);
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pakan Minggu {{ $avgAgeWeeks }}</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-amber-600">{{ $stdGram }}</span>
                    <span class="text-xs text-slate-500 font-semibold">g / ekor / hari</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pembagian Pagi : Sore</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-slate-800">{{ round($stdGram / 2, 1) }} : {{ round($stdGram / 2, 1) }}</span>
                    <span class="text-xs text-slate-500 font-semibold">gram (50:50)</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Estimasi Pakan Farm/Hari</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-maroon-800">{{ number_format($totalPakanTodayKg, 1, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-semibold">kg ({{ $totalChickens }} ekor)</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kebutuhan Karung (50kg)</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-emerald-600">{{ $totalKarungToday }}</span>
                    <span class="text-xs text-slate-500 font-semibold">krg {{ $sisaKgToday > 0 ? '+ ' . $sisaKgToday . ' kg' : '' }}</span>
                </div>
            </div>
        </div>

        <!-- Panduan Manajemen Pakan Sesuai Dokumen Acuan -->
        <div class="farm-card p-5 bg-gradient-to-br from-amber-50/60 via-white to-orange-50/40 border border-amber-200/80 shadow-xs">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 tracking-tight">Panduan Manajemen Pakan Penting (Standar Acuan Nochi Farm)</h3>
                    <p class="text-[11px] text-slate-500">Prinsip pemberian pakan layer dari umur 13 minggu hingga masa afkir</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs leading-relaxed text-slate-700">
                <div class="p-3 bg-white/80 rounded-xl border border-amber-100 flex items-start gap-2.5">
                    <span class="text-base leading-none">🐣</span>
                    <div>
                        <b class="text-amber-900 block font-bold mb-0.5">Fase Umur 13–17 Minggu (Grower ke Pre-Lay):</b>
                        <span>Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam. Jangan menaikkan pakan terlalu ekstrem agar ayam tidak kegemukan sebelum bertelur.</span>
                    </div>
                </div>

                <div class="p-3 bg-white/80 rounded-xl border border-amber-100 flex items-start gap-2.5">
                    <span class="text-base leading-none">🥚</span>
                    <div>
                        <b class="text-emerald-900 block font-bold mb-0.5">Fase Umur 18–40 Minggu (Masa Puncak Bertelur):</b>
                        <span>Ayam membutuhkan energi dan nutrisi tertinggi untuk pembentukan telur pertama dan mencapai puncak produksi harian. Konsumsi pakan stabil di kisaran 110–115 gram.</span>
                    </div>
                </div>

                <div class="p-3 bg-white/80 rounded-xl border border-amber-100 flex items-start gap-2.5">
                    <span class="text-base leading-none">🐔</span>
                    <div>
                        <b class="text-teal-900 block font-bold mb-0.5">Fase Umur 41 Sampai Afkir (Laying Phase 2 & 3):</b>
                        <span>Persentase bertelur mulai menurun perlahan, namun ukuran telur bertambah besar. Di fase ini, ayam butuh asupan Kalsium (Ca) makro lebih tinggi (seperti grit batu kapur/kulit kerang) untuk menjaga kekuatan kerabang telur agar tidak mudah retak.</span>
                    </div>
                </div>

                <div class="p-3 bg-white/80 rounded-xl border border-amber-100 flex items-start gap-2.5">
                    <span class="text-base leading-none">☀️</span>
                    <div>
                        <b class="text-rose-900 block font-bold mb-0.5">Manajemen Cuaca Panas (Suhu >30°C):</b>
                        <span>Jangan menambah porsi gram pakan pada siang hari yang panas untuk menghindari heat stress akibat panas metabolisme pencernaan. Berikan pakan porsi besar pada pagi-pagi sekali atau sore hari saat udara mulai sejuk.</span>
                    </div>
                </div>
            </div>
        </div>

    <!-- TAB 3: STANDAR BOBOT BADAN -->
    @elseif($activeTab === 'bb')
        <!-- Metric Highlight BB -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Bobot (Minggu {{ $avgAgeWeeks }})</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-blue-600">{{ number_format($currentStd['bb_target'] ?? 1.74, 2, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-semibold">kg / ekor</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Rentang Wajar (Min - Max)</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-slate-800">{{ number_format($currentStd['bb_min'] ?? 1.66, 2, ',', '.') }} - {{ number_format($currentStd['bb_max'] ?? 1.82, 2, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-semibold">kg</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Batas Toleransi Deviasi</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-amber-600">&plusmn; 0.08</span>
                    <span class="text-xs text-slate-500 font-semibold">kg (80 gram)</span>
                </div>
            </div>
            <div class="farm-card p-4 bg-white border border-slate-200">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Target Keseragaman</span>
                <div class="flex items-baseline gap-1 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-emerald-600">&ge; 85%</span>
                    <span class="text-xs text-slate-500 font-semibold">flock seragam</span>
                </div>
            </div>
        </div>

    @endif

    <!-- TABEL MASTER MINGGUAN (UNTUK TAB PRODUKSI, PAKAN, & BB) -->
    <div class="farm-card p-4 sm:p-5 bg-white border border-slate-200 shadow-xs space-y-4">
            

            <!-- Responsive Master Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs">
                <table class="w-full text-left border-collapse text-xs" id="standardsTable">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-extrabold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3.5 whitespace-nowrap">Umur (Minggu)</th>
                            <th class="py-3 px-3.5 whitespace-nowrap">Fase Pertumbuhan</th>

                            @if($activeTab === 'produksi')
                                <th class="py-3 px-3.5 whitespace-nowrap">Target Hen Day (HDP)</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">Target Berat Telur</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">Manajemen & Keterangan</th>
                            @elseif($activeTab === 'pakan')
                                <th class="py-3 px-3.5 whitespace-nowrap">Jenis Pakan</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">Pakan Harian (g/ekor)</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">Porsi Pagi (50%)</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">Porsi Sore (50%)</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">Estimasi Farm (Kg)</th>
                            @elseif($activeTab === 'bb')
                                <th class="py-3 px-3.5 whitespace-nowrap">BB Minimum</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">BB Target (Ideal)</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">BB Maksimum</th>
                                <th class="py-3 px-3.5 whitespace-nowrap">Batas Toleransi</th>
                            @endif

                            <th class="py-3 px-3.5 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($weeklyStandards as $std)
                            @php
                                $isCurrentWeek = ($std->week == $avgAgeWeeks);
                                $rowFeedKg = round(($totalChickens * $std->feed_gram) / 1000, 1);
                            @endphp
                            <tr id="row-week-{{ $std->week }}" 
                                class="transition-colors hover:bg-slate-50/80 {{ $isCurrentWeek ? 'bg-amber-50/50 font-bold border-l-4 border-l-maroon-800' : '' }}"
                                data-week="{{ $std->week }}"
                                data-phase="{{ $std->pill }}"
                                data-json="{{ json_encode($std) }}">

                                <!-- Kolom Umur Minggu -->
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs {{ $isCurrentWeek ? 'bg-maroon-800 text-white shadow-2xs' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $std->week }}
                                        </span>
                                        <div>
                                            <span class="font-extrabold text-slate-800">Minggu {{ $std->week }}</span>
                                            @if($isCurrentWeek)
                                                <span class="block text-[9.5px] font-black text-maroon-800 tracking-wider uppercase">★ UMUR FARM SAAT INI</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Kolom Fase -->
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-extrabold border {{ $std->pill_badge_class }}">
                                        {{ $std->pill }}
                                    </span>
                                    <span class="block text-[10px] text-slate-400 font-medium mt-0.5">{{ $std->phase }}</span>
                                </td>

                                <!-- Kolom Khusus Tab Standar Produksi -->
                                @if($activeTab === 'produksi')
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 h-2 rounded-full bg-slate-100 overflow-hidden shrink-0">
                                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, $std->hd_target) }}%;"></div>
                                            </div>
                                            <b class="text-xs font-black {{ $std->hd_target >= 90 ? 'text-emerald-700' : ($std->hd_target > 0 ? 'text-amber-700' : 'text-slate-400') }}">
                                                {{ number_format($std->hd_target, 1, ',', '.') }}%
                                            </b>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap font-bold text-slate-700">
                                        {{ $std->egg_weight && $std->egg_weight !== '-' ? $std->egg_weight . ' g' : '—' }}
                                    </td>
                                    <td class="py-3 px-3.5 text-slate-500 max-w-xs truncate" title="{{ $std->description }}">
                                        {{ $std->description }}
                                    </td>

                                <!-- Kolom Khusus Tab Standar Pakan -->
                                @elseif($activeTab === 'pakan')
                                    <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-700">
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[11px] font-bold">
                                            {{ $std->feed_type ?: 'Layer Phase' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <b class="text-xs font-black text-amber-600">{{ number_format($std->feed_gram, 1, ',', '.') }}</b>
                                        <span class="text-[11px] text-slate-400">g/ekor</span>
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-700">
                                        {{ number_format($std->feed_pagi, 1, ',', '.') }} g
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-700">
                                        {{ number_format($std->feed_sore, 1, ',', '.') }} g
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap font-bold text-maroon-800">
                                        {{ number_format($rowFeedKg, 1, ',', '.') }} kg
                                    </td>

                                <!-- Kolom Khusus Tab Standar BB -->
                                @elseif($activeTab === 'bb')
                                    <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-600">
                                        {{ number_format($std->weight_min, 2, ',', '.') }} kg
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <b class="text-xs font-black text-blue-700">{{ number_format($std->weight_target, 2, ',', '.') }} kg</b>
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap font-semibold text-slate-600">
                                        {{ number_format($std->weight_max, 2, ',', '.') }} kg
                                    </td>
                                    <td class="py-3 px-3.5 whitespace-nowrap text-slate-500 font-medium">
                                        &plusmn; {{ round(($std->weight_max - $std->weight_min) / 2, 3) * 1000 }} g
                                    </td>
                                @endif

                                <!-- Tombol Aksi Edit Standar -->
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <button type="button" 
                                            onclick='openModalEditStandar(@json($std))'
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-maroon-800 hover:text-maroon-800 text-slate-700 font-extrabold text-[11px] shadow-2xs transition-all active:scale-95 cursor-pointer">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-maroon-800"></i>
                                        <span>Edit</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Belum ada data standar di database. Silakan jalankan migrasi database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-2">
                <span>Menampilkan seluruh 78 minggu umur ayam layer petelur.</span>
                <span>Nilai dapat disesuaikan sewaktu-waktu oleh farm manager.</span>
            </div>
        </div>

</div>

<!-- ======================================================== -->
<!-- MODAL INTERAKTIF: EDIT STANDAR MINGGUAN -->
<!-- ======================================================== -->
<div id="modalEditStandar" class="fixed inset-0 z-50 flex items-center justify-center p-3 bg-slate-900/60 backdrop-blur-xs transition-opacity hidden" aria-hidden="true">
    <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto">
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-maroon-50 text-maroon-800 flex items-center justify-center font-black text-lg border border-maroon-100">
                    <span id="modalWeekBadge">21</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Edit Master Standar Minggu <span id="modalWeekTitle">21</span></h3>
                    <p class="text-[11px] text-slate-500 font-medium">Perbarui parameter standar performa, pakan & bobot</p>
                </div>
            </div>
            <button type="button" onclick="closeModalEditStandar()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm transition-all cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Edit Standar -->
        <form id="formEditStandar" method="POST" action="" class="space-y-4 pt-4" onsubmit="handleModalSubmit(event)">
            @csrf
            <input type="hidden" name="from_tab" value="{{ $activeTab }}">

            <!-- 1. Fase & Label -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Fase Pertumbuhan *</label>
                    <input type="text" id="inputPhase" name="phase" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Pill Label *</label>
                    <input type="text" id="inputPill" name="pill" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800">
                </div>
            </div>

            <!-- 2. Standar Produksi (HD % & Berat Telur) -->
            <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-100 space-y-3">
                <div class="flex items-center gap-1.5 text-xs font-black text-emerald-800">
                    <span>🥚</span>
                    <span>Standar Produksi Telur</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Target HD Production (%) *</label>
                        <input type="number" step="0.1" min="0" max="100" id="inputHdTarget" name="hd_target" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-black text-emerald-700 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Target Berat Telur (Gram)</label>
                        <input type="text" id="inputEggWeight" name="egg_weight" placeholder="misal: 60.5 atau -" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                    </div>
                </div>
            </div>

            <!-- 3. Standar Pakan -->
            <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-100 space-y-3">
                <div class="flex items-center justify-between text-xs font-black text-amber-900">
                    <span class="flex items-center gap-1.5">
                        <span>🌾</span>
                        <span>Standar Konsumsi Pakan Harian</span>
                    </span>
                    <span id="modalPagiSoreLabel" class="text-[10px] font-bold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded">
                        Pagi: 55g · Sore: 55g
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Pakan (Gram / Ekor / Hari) *</label>
                        <input type="number" step="0.5" min="0" max="300" id="inputFeedGram" name="feed_gram" oninput="updateModalPagiSore(this.value)" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-black text-amber-800 bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Jenis Pakan (Umum)</label>
                        <input type="text" id="inputFeedType" name="feed_type" placeholder="misal: Layer Phase 1" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                </div>
            </div>

            <!-- 4. Standar Bobot Badan (BB) -->
            <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100 space-y-3">
                <div class="flex items-center gap-1.5 text-xs font-black text-blue-900">
                    <span>⚖</span>
                    <span>Standar Bobot Badan (BB Sampling)</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[10.5px] font-bold text-slate-700 mb-1">Min (Kg) *</label>
                        <input type="number" step="0.01" min="0" max="10" id="inputWeightMin" name="weight_min" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-[10.5px] font-bold text-slate-700 mb-1">Target (Kg) *</label>
                        <input type="number" step="0.01" min="0" max="10" id="inputWeightTarget" name="weight_target" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs font-black text-blue-700 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-[10.5px] font-bold text-slate-700 mb-1">Max (Kg) *</label>
                        <input type="number" step="0.01" min="0" max="10" id="inputWeightMax" name="weight_max" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    </div>
                </div>
            </div>

            <!-- 5. Keterangan / Catatan Manajemen -->
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Panduan / Manajemen</label>
                <textarea id="inputDescription" name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-maroon-800/20 focus:border-maroon-800"></textarea>
            </div>

            <!-- Tombol Simpan Modal -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalEditStandar()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitModal" class="px-5 py-2 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white font-black text-xs shadow-md transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    <span>Simpan Standar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentEditingWeek = null;

    // Buka Modal Edit Standar
    function openModalEditStandar(data) {
        currentEditingWeek = data.week;
        document.getElementById('modalWeekBadge').textContent = data.week;
        document.getElementById('modalWeekTitle').textContent = data.week;
        
        document.getElementById('inputPhase').value = data.phase || '';
        document.getElementById('inputPill').value = data.pill || '';
        document.getElementById('inputHdTarget').value = data.hd_target !== undefined ? data.hd_target : 0;
        document.getElementById('inputEggWeight').value = data.egg_weight || '';
        document.getElementById('inputFeedGram').value = data.feed_gram || 0;
        document.getElementById('inputFeedType').value = data.feed_type || '';
        document.getElementById('inputWeightMin').value = data.weight_min || 0;
        document.getElementById('inputWeightTarget').value = data.weight_target || 0;
        document.getElementById('inputWeightMax').value = data.weight_max || 0;
        document.getElementById('inputDescription').value = data.description || '';

        updateModalPagiSore(data.feed_gram || 0);

        // Set action form
        const form = document.getElementById('formEditStandar');
        form.action = `/master/weekly-standards/${data.week}/update`;

        const modal = document.getElementById('modalEditStandar');
        modal.classList.remove('hidden');
        lucide.createIcons();
    }

    // Tutup Modal
    function closeModalEditStandar() {
        const modal = document.getElementById('modalEditStandar');
        modal.classList.add('hidden');
        currentEditingWeek = null;
    }

    // Live update porsi pagi dan sore di modal
    function updateModalPagiSore(gram) {
        const val = parseFloat(gram) || 0;
        const half = (val / 2).toFixed(1);
        const label = document.getElementById('modalPagiSoreLabel');
        if (label) {
            label.textContent = `Pagi: ${half}g · Sore: ${half}g`;
        }
    }

    // Handle AJAX Form Submit
    function handleModalSubmit(e) {
        e.preventDefault();
        const form = document.getElementById('formEditStandar');
        const btn = document.getElementById('btnSubmitModal');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span>Menyimpan...</span>';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Gagal menyimpan');
            return response.json();
        })
        .then(data => {
            closeModalEditStandar();
            // Reload halaman untuk memastikan semua hitungan dan KPI sinkron
            window.location.reload();
        })
        .catch(err => {
            // Fallback submit biasa jika AJAX gagal
            form.submit();
        });
    }

    // Lompat ke baris minggu saat ini
    function jumpToCurrentWeek(week) {
        filterByPhase('ALL');
        const input = document.getElementById('filterWeekInput');
        if (input) input.value = '';
        filterStandardsTable();

        const row = document.getElementById(`row-week-${week}`);
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.classList.add('ring-4', 'ring-amber-400', 'transition-all');
            setTimeout(() => {
                row.classList.remove('ring-4', 'ring-amber-400');
            }, 2500);
        }
    }


</script>
@endsection
