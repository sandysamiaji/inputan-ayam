@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.index') }}#card-vaksin-obat" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Katalog Vaksin & Obat Unggas</h1>
                <p class="text-xs text-slate-400">Standar dosis, klasifikasi medis & panduan klinis ayam petelur</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('input.index') }}?type=obat" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-md transition-all active:scale-95">
                <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-200"></i>
                <span>Input Pemakaian</span>
            </a>
            <a href="{{ route('warehouse.obat') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs font-bold shadow-md transition-all active:scale-95">
                <i data-lucide="warehouse" class="w-4 h-4 text-rose-200"></i>
                <span>Gudang Obat</span>
            </a>
        </div>
    </div>

    <!-- Search & Category Filters -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm space-y-3">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="searchMedicine" oninput="filterCatalog()" placeholder="Cari nama obat (Vermixon, Neomeditril...), penyakit, atau dosis..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-maroon-700/20 focus:border-maroon-700 transition-all">
        </div>

        <!-- Filter Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
            <button type="button" onclick="setCategoryFilter('all')" class="cat-pill active px-3 py-1.5 rounded-xl font-bold transition-all bg-maroon-800 text-white shrink-0" data-cat="all">
                Semua ({{ count($medicines) }})
            </button>
            @foreach($categories as $key => $cat)
                @php
                    $catCount = collect($medicines)->where('category_key', $key)->count();
                @endphp
                <button type="button" onclick="setCategoryFilter('{{ $key }}')" class="cat-pill px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0" data-cat="{{ $key }}">
                    {{ $cat['icon'] }} {{ $cat['label'] }} ({{ $catCount }})
                </button>
            @endforeach
        </div>
    </div>

    <!-- Master List Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="medicinesContainer">
        @foreach($medicines as $med)
            @php
                $catMeta = $categories[$med['category_key']] ?? null;
                $badgeClass = $catMeta['badge_color'] ?? 'bg-slate-100 text-slate-800';
                $icon = $catMeta['icon'] ?? '💊';
            @endphp
            <div class="med-card farm-card p-5 space-y-3 hover:border-maroon-200 transition-all" data-category="{{ $med['category_key'] }}" data-name="{{ strtolower($med['name']) }}" data-indication="{{ strtolower($med['indication'] ?? '') }}" data-notes="{{ strtolower($med['notes'] ?? '') }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-xl shrink-0">
                            {{ $icon }}
                        </div>
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $badgeClass }}">
                                {{ $med['category'] }}
                            </span>
                            <h3 class="text-sm font-bold text-slate-900 mt-1 leading-snug">{{ $med['name'] }}</h3>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
                        {{ $med['stock'] }} {{ $med['unit'] }}
                    </span>
                </div>

                @if(!empty($med['indication']))
                    <div class="bg-rose-50/70 border border-rose-100 rounded-xl p-2.5 text-xs text-rose-800 space-y-0.5">
                        <div class="font-bold flex items-center gap-1.5 text-[11px]">
                            <span>🎯</span>
                            <span>Indikasi & Gejala:</span>
                        </div>
                        <p class="text-[11.5px] leading-relaxed pl-5">{{ $med['indication'] }}</p>
                    </div>
                @endif

                <div class="bg-slate-50 rounded-xl p-3 space-y-1.5 text-xs divide-y divide-slate-100">
                    <div class="flex justify-between py-1">
                        <span class="text-slate-400 font-medium">Standar Dosis:</span>
                        <span class="font-bold text-slate-700 text-right">{{ $med['dosage'] }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-400 font-medium">Metode Aplikasi:</span>
                        <span class="font-bold text-maroon-800 text-right">{{ $med['application'] }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-400 font-medium">Jadwal Rekomendasi:</span>
                        <span class="font-bold text-slate-700 text-right">{{ $med['schedule'] }}</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 italic bg-amber-50/50 border border-amber-100/60 rounded-lg p-2 leading-relaxed">
                    💡 {{ $med['notes'] }}
                </p>
            </div>
        @endforeach
    </div>

    <div id="noMedicinesFound" class="hidden text-center py-12 bg-white rounded-2xl border border-slate-200">
        <p class="text-sm font-bold text-slate-600">Tidak ada produk obat yang cocok dengan filter atau pencarian.</p>
        <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain atau pilih Semua Kategori.</p>
    </div>

</div>

<script>
let currentCategory = 'all';

function setCategoryFilter(cat) {
    currentCategory = cat;
    document.querySelectorAll('.cat-pill').forEach(btn => {
        if (btn.getAttribute('data-cat') === cat) {
            btn.className = 'cat-pill active px-3 py-1.5 rounded-xl font-bold transition-all bg-maroon-800 text-white shrink-0';
        } else {
            btn.className = 'cat-pill px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0';
        }
    });
    filterCatalog();
}

function filterCatalog() {
    const query = (document.getElementById('searchMedicine').value || '').toLowerCase().trim();
    const cards = document.querySelectorAll('.med-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const cat = card.getAttribute('data-category');
        const name = card.getAttribute('data-name') || '';
        const ind = card.getAttribute('data-indication') || '';
        const notes = card.getAttribute('data-notes') || '';

        const matchCat = (currentCategory === 'all' || cat === currentCategory);
        const matchQuery = (!query || name.includes(query) || ind.includes(query) || notes.includes(query));

        if (matchCat && matchQuery) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const emptyBox = document.getElementById('noMedicinesFound');
    if (emptyBox) {
        emptyBox.classList.toggle('hidden', visibleCount > 0);
    }
}
</script>
@endsection
