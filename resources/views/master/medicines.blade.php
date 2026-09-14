@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.index') }}#card-vaksin-obat" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-maroon-800 hover:border-maroon-300 flex items-center justify-center shadow-sm transition-all active:scale-95">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Vaksin & Obat</h1>
                <p class="text-xs text-slate-400">Jadwal vaksinasi, standar dosis & data obat unggas</p>
            </div>
        </div>

        <a href="{{ route('warehouse.obat') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-maroon-800 hover:bg-maroon-900 text-white text-xs font-bold shadow-md transition-all active:scale-95">
            <i data-lucide="warehouse" class="w-4 h-4 text-rose-200"></i>
            <span>Cek Stok Obat di Gudang</span>
        </a>
    </div>

    <!-- Master List Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($medicines as $med)
            <div class="farm-card p-5 space-y-3 hover:border-maroon-200 transition-all">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-purple-50 border border-purple-200 text-purple-700 flex items-center justify-center shrink-0">
                            <i data-lucide="pill" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-purple-100 text-purple-800">
                                {{ $med['category'] }}
                            </span>
                            <h3 class="text-sm font-bold text-slate-900 mt-1 leading-snug">{{ $med['name'] }}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-3 space-y-2 text-xs divide-y divide-slate-100">
                    <div class="flex justify-between py-1">
                        <span class="text-slate-400 font-medium">Rekomendasi Dosis:</span>
                        <span class="font-bold text-slate-700 text-right">{{ $med['dosage'] }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-400 font-medium">Metode Aplikasi:</span>
                        <span class="font-bold text-maroon-800 text-right">{{ $med['application'] }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-400 font-medium">Jadwal Rutin:</span>
                        <span class="font-bold text-slate-700 text-right">{{ $med['schedule'] }}</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 italic">
                    {{ $med['notes'] }}
                </p>
            </div>
        @endforeach
    </div>

</div>
@endsection
