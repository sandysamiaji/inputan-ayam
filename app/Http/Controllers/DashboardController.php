<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flock;
use App\Models\Coop;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\Mortality;
use App\Models\WeightSample;
use App\Models\HealthTreatment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Dashboard utama Nochi Farm Input
     */
    public function index(Request $request)
    {
        $selectedDate = $request->query('date', Carbon::today()->toDateString());
        $carbonDate = Carbon::parse($selectedDate);

        // Ucapan waktu Indonesia
        $hour = Carbon::now()->hour;
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat sore';
        } else {
            $greeting = 'Selamat malam';
        }

        // Petugas aktif atau Petugas default
        $user = Auth::user();
        if (!$user) {
            $user = User::where('role', 'user')->orWhere('username', 'petugas')->first() ?? User::first();
        }

        // Format tanggal Indonesia
        $hariIndonesia = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $bulanIndonesia = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        $bulanFullIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $namaHari = $hariIndonesia[$carbonDate->format('l')] ?? $carbonDate->format('l');
        $namaBulan = $bulanIndonesia[$carbonDate->month] ?? $carbonDate->format('M');
        $namaBulanFull = $bulanFullIndonesia[$carbonDate->month] ?? $carbonDate->format('F');
        $formattedDate = "{$carbonDate->day} {$namaBulanFull} {$carbonDate->year}";

        // 1. Ringkasan Produksi Telur Hari Ini
        $eggProdRecords = EggProduction::whereDate('date', $selectedDate)->get();
        $totalEggCrates = (float) $eggProdRecords->sum('crates_count');
        $totalEggCount = (int) $eggProdRecords->sum('total_eggs');
        $brokenEggCount = (int) $eggProdRecords->sum('broken_eggs');
        $goodEggCount = (int) $eggProdRecords->sum('good_eggs');

        // 2. Ringkasan Pemakaian Pakan Hari Ini
        $feedConsRecords = FeedConsumption::whereDate('date', $selectedDate)->get();
        $totalFeedKg = (float) $feedConsRecords->sum('quantity_kg');

        // 3. Ringkasan Mortalitas Hari Ini
        $mortalityRecords = Mortality::whereDate('date', $selectedDate)->get();
        $totalMortalityCount = (int) $mortalityRecords->sum('count');

        // 4. Ringkasan Berat Badan Terkini
        $latestWeight = WeightSample::whereDate('date', '<=', $selectedDate)
            ->latest('date')
            ->first();
        $averageWeightKg = $latestWeight ? (float) $latestWeight->average_weight_kg : 1.620;

        // 5. Ringkasan Vaksin & Obat Hari Ini
        $healthTreatments = HealthTreatment::whereDate('date', $selectedDate)->get();
        $totalHealthActivities = $healthTreatments->count();

        // 6. Aktivitas Terakhir (Timeline Gabungan)
        $activities = collect();

        foreach ($eggProdRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->time ? substr($item->time, 0, 5) : $item->created_at->format('H:i');
            $activities->push([
                'id' => 'egg_' . $item->id,
                'category' => 'egg',
                'title' => 'Produksi Telur',
                'subtitle' => $coopName . ($item->notes ? ' • ' . $item->notes : ''),
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => '+' . number_format($item->crates_count, 0, ',', '.') . ' Peti',
                'subvalue' => number_format($item->total_eggs, 0, ',', '.') . ' Butir',
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($feedConsRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->time ? substr($item->time, 0, 5) : $item->created_at->format('H:i');
            $activities->push([
                'id' => 'feed_' . $item->id,
                'category' => 'feed',
                'title' => 'Pemakaian Pakan',
                'subtitle' => $coopName . ' • ' . $item->feed_name,
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => '-' . number_format($item->quantity_kg, 0, ',', '.') . ' Kg',
                'subvalue' => $item->feeding_time,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($mortalityRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->time ? substr($item->time, 0, 5) : $item->created_at->format('H:i');
            $activities->push([
                'id' => 'mort_' . $item->id,
                'category' => 'mortality',
                'title' => 'Mortalitas',
                'subtitle' => $coopName . ($item->cause ? ' • ' . $item->cause : ''),
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => $item->count . ' Ekor',
                'subvalue' => ucfirst($item->type),
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($healthTreatments as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Semua Blok';
            $timeStr = $item->time ? substr($item->time, 0, 5) : $item->created_at->format('H:i');
            $activities->push([
                'id' => 'health_' . $item->id,
                'category' => 'health',
                'title' => ucfirst($item->type) . ' / Obat',
                'subtitle' => $coopName . ' • ' . $item->medicine_name,
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => $item->dosage ?: '1 Kegiatan',
                'subvalue' => $item->application_method,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        $activities = $activities->sortByDesc('raw_timestamp')->values();

        // 7. Data Master untuk modal quick action
        $flocks = Flock::with(['coops' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();

        $coops = Coop::with('flock')->where('is_active', true)->get();

        // 8. Integrasi Data Gudang & Penjualan dari nochifram
        $eggSummary = \App\Services\OutboundIntegrationService::getEggOutboundSummary();
        $feedSummary = \App\Services\OutboundIntegrationService::getFeedOutboundSummary();

        $totalEggProducedAllTime = $eggSummary['total_produced_crates'];
        $totalEggSoldAllTime = $eggSummary['peti_sold'];
        $eggKgSold = $eggSummary['kg_sold'];
        $currentEggStockCrates = $eggSummary['current_stock_peti'];

        $totalFeedPurchased = $feedSummary['purchased_kg'];
        $totalFeedUsedAllTime = $feedSummary['consumption_kg'];
        $feedKarungSold = $feedSummary['karung_sold'];
        $feedKgSoldTotal = $feedSummary['sold_in_kg'];
        $currentFeedStockKg = $feedSummary['current_stock_kg'];

        $totalActiveChickens = (int) $coops->sum('active_chickens');
        $totalCoopsCount = $coops->count();

        // 9. Pesan Motivasi & Informasi Kondisi Ayam Otomatis dari Master Standar Produksi
        //    Umur minggu dihitung DINAMIS berdasarkan flock.start_date + tanggal yang dipilih user
        $farmCondition = \App\Services\ProductionStandardService::getActiveFarmCondition($selectedDate);
        $dominantWeek = $farmCondition['dominant_week'];
        $farmStandard = $farmCondition['standard'];
        $coopStandards = $farmCondition['coop_standards'];
        $dynamicAges = $farmCondition['dynamic_ages'] ?? [];

        // Override chicken_age_weeks pada setiap coop agar view menampilkan umur dinamis
        foreach ($coops as $c) {
            if (isset($dynamicAges[$c->id])) {
                $c->chicken_age_weeks = $dynamicAges[$c->id];
            }
        }
        // Override juga pada flocks->coops (untuk konsistensi tampilan flock card)
        foreach ($flocks as $f) {
            foreach ($f->coops as $fc) {
                if (isset($dynamicAges[$fc->id])) {
                    $fc->chicken_age_weeks = $dynamicAges[$fc->id];
                }
            }
        }

        $settingRows = \Illuminate\Support\Facades\DB::table('settings')->whereIn('key', [
            'dashboard_motivation_message',
            'dashboard_chicken_status_message',
            'dashboard_info_active',
            'dashboard_chicken_status_auto',
        ])->pluck('value', 'key');

        $motivationMsg = $settingRows['dashboard_motivation_message'] ?? 'Semangat bekerja dan tetap jaga kebersihan serta performa kandang hari ini!';
        $isAutoStatus = ($settingRows['dashboard_chicken_status_auto'] ?? '1') === '1';
        $customStatusMsg = $settingRows['dashboard_chicken_status_message'] ?? null;

        // Otomatis sinkron dari Master Standar Produksi jika mode auto atau tanpa custom message
        if ($isAutoStatus || empty($customStatusMsg)) {
            $chickenStatusMsg = $farmCondition['status_message'];
        } else {
            $chickenStatusMsg = $customStatusMsg;
        }

        $isInfoActive = ($settingRows['dashboard_info_active'] ?? '1') === '1';

        // Hitung total kebutuhan pakan seluruh blok aktif di farm (dinamis dari setting database)
        $kgPerKarung = \App\Models\Setting::getKgPerKarung();
        $totalFarmPakanKg = 0;
        foreach ($coops as $c) {
            $cStdRow = $coopStandards[$c->id] ?? \App\Services\ProductionStandardService::getStandardForWeek((int)$c->chicken_age_weeks);
            $totalFarmPakanKg += (($c->active_chickens * ($cStdRow['gram_pakan'] ?? 105)) / 1000);
        }
        $totalFarmPakanKg = round($totalFarmPakanKg, 1);
        $farmKarung = floor($totalFarmPakanKg / $kgPerKarung);
        $farmSisaKg = round(fmod($totalFarmPakanKg, $kgPerKarung), 1);
        $totalFarmKarungStr = ($farmKarung > 0 ? $farmKarung . ' karung ' : '') . ($farmSisaKg > 0 ? '+ ' . $farmSisaKg . ' kg' : ($farmKarung == 0 ? '0 kg' : ''));

        // Hitung HD aktual hari ini per coop dan per flock (HANYA jika telur sudah diinput)
        $coopHdData = [];
        $coopEggTodayData = [];

        foreach ($coops as $c) {
            $todayEgg = (int) $eggProdRecords->where('coop_id', $c->id)->sum('total_eggs');
            $coopEggTodayData[$c->id] = $todayEgg;
            if ($todayEgg > 0 && $c->active_chickens > 0) {
                $coopHdData[$c->id] = round(($todayEgg / $c->active_chickens) * 100, 1);
            } else {
                $coopHdData[$c->id] = null; // Belum diinput oleh user
            }
        }

        $flockHdData = [];
        foreach ($flocks as $f) {
            $fCoopIds = $f->coops->pluck('id');
            $fActiveChx = (int) $f->coops->sum('active_chickens');
            $fTodayEggs = (int) $eggProdRecords->whereIn('coop_id', $fCoopIds)->sum('total_eggs');
            if ($fTodayEggs > 0 && $fActiveChx > 0) {
                $flockHdData[$f->id] = round(($fTodayEggs / $fActiveChx) * 100, 1);
            } else {
                $flockHdData[$f->id] = null; // Belum diinput oleh user
            }
        }

        return view('dashboard', compact(
            'selectedDate',
            'greeting',
            'user',
            'namaHari',
            'namaBulan',
            'formattedDate',
            'carbonDate',
            'totalEggCrates',
            'totalEggCount',
            'brokenEggCount',
            'goodEggCount',
            'totalFeedKg',
            'totalMortalityCount',
            'averageWeightKg',
            'totalHealthActivities',
            'activities',
            'flocks',
            'coops',
            'totalEggProducedAllTime',
            'totalEggSoldAllTime',
            'eggKgSold',
            'currentEggStockCrates',
            'currentFeedStockKg',
            'totalFeedPurchased',
            'totalFeedUsedAllTime',
            'feedKarungSold',
            'feedKgSoldTotal',
            'totalActiveChickens',
            'totalCoopsCount',
            'motivationMsg',
            'chickenStatusMsg',
            'isInfoActive',
            'farmCondition',
            'dominantWeek',
            'farmStandard',
            'coopStandards',
            'totalFarmPakanKg',
            'totalFarmKarungStr',
            'kgPerKarung',
            'coopHdData',
            'flockHdData',
            'coopEggTodayData'
        ));
    }

    /**
     * Simpan Produksi Telur Cepat
     */
    public function storeEggProduction(Request $request)
    {
        $validated = $request->validate([
            'coop_id' => 'required|exists:coops,id',
            'good_eggs' => 'required|numeric|min:0',
            'broken_eggs' => 'nullable|numeric|min:0',
            'abnormal_eggs' => 'nullable|numeric|min:0',
            'crates_count' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $coop = Coop::findOrFail($validated['coop_id']);
        $goodEggs = (int) $validated['good_eggs'];
        $brokenEggs = (int) ($validated['broken_eggs'] ?? 0);
        $abnormalEggs = (int) ($validated['abnormal_eggs'] ?? 0);
        $totalEggs = $goodEggs + $brokenEggs + $abnormalEggs;

        // Jika peti tidak diisi, estimasikan
        $cratesCount = isset($validated['crates_count']) && $validated['crates_count'] > 0
            ? (float) $validated['crates_count']
            : round($totalEggs / 25, 2);

        $eggProduction = EggProduction::create([
            'flock_id' => $coop->flock_id,
            'coop_id' => $coop->id,
            'user_id' => Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id'),
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
            'time' => $validated['time'] ?? Carbon::now()->format('H:i:s'),
            'total_eggs' => $totalEggs,
            'broken_eggs' => $brokenEggs,
            'abnormal_eggs' => $abnormalEggs,
            'good_eggs' => $goodEggs,
            'crates_count' => $cratesCount,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data produksi telur berhasil disimpan!',
                'data' => $eggProduction,
            ]);
        }

        return redirect()->back()->with('success', 'Data produksi telur berhasil disimpan!');
    }

    /**
     * Simpan Pemakaian Pakan Cepat
     */
    public function storeFeedConsumption(Request $request)
    {
        $validated = $request->validate([
            'coop_id' => 'nullable|exists:coops,id',
            'feed_name' => 'required|string',
            'quantity_kg' => 'required|numeric|min:0.1',
            'feeding_time' => 'nullable|string',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $flockId = null;
        if (!empty($validated['coop_id'])) {
            $coop = Coop::find($validated['coop_id']);
            $flockId = $coop ? $coop->flock_id : null;
        }

        $feed = FeedConsumption::create([
            'flock_id' => $flockId,
            'coop_id' => $validated['coop_id'] ?? null,
            'user_id' => Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id'),
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
            'time' => Carbon::now()->format('H:i:s'),
            'feeding_time' => $validated['feeding_time'] ?? 'Pagi',
            'feed_name' => $validated['feed_name'],
            'quantity_kg' => $validated['quantity_kg'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Otomatis sinkronisasi ke tabel gudang (FarmStock) sebagai barang keluar
        \App\Models\FarmStock::create([
            'user_id' => $feed->user_id,
            'date' => $feed->date,
            'category' => 'pakan',
            'item_name' => 'Konsumsi Pakan: ' . $feed->feed_name,
            'type' => 'keluar',
            'quantity' => $feed->quantity_kg,
            'unit' => 'Kg',
            'source' => isset($coop) ? $coop->name : 'Semua Blok',
            'notes' => '[AUTO-KONSUMSI] ' . ($validated['notes'] ?? ''),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pemakaian pakan berhasil disimpan!',
                'data' => $feed,
            ]);
        }

        return redirect()->back()->with('success', 'Data pemakaian pakan berhasil disimpan!');
    }

    /**
     * Simpan Mortalitas Cepat
     */
    public function storeMortality(Request $request)
    {
        $validated = $request->validate([
            'coop_id' => 'required|exists:coops,id',
            'count' => 'required|integer|min:1',
            'type' => 'nullable|string',
            'cause' => 'nullable|string',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $coop = Coop::findOrFail($validated['coop_id']);

        $mortality = Mortality::create([
            'flock_id' => $coop->flock_id,
            'coop_id' => $coop->id,
            'user_id' => Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id'),
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
            'time' => Carbon::now()->format('H:i:s'),
            'count' => $validated['count'],
            'type' => $validated['type'] ?? 'mati',
            'cause' => $validated['cause'] ?? 'Wajar',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Perbarui jumlah ayam aktif di blok jika bertipe mati/afkir
        if ($coop->active_chickens >= $validated['count']) {
            $coop->decrement('active_chickens', $validated['count']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data mortalitas berhasil dicatat!',
                'data' => $mortality,
            ]);
        }

        return redirect()->back()->with('success', 'Data mortalitas berhasil dicatat!');
    }

    /**
     * Simpan Berat Badan Cepat
     */
    public function storeWeightSample(Request $request)
    {
        $validated = $request->validate([
            'coop_id' => 'required|exists:coops,id',
            'average_weight_kg' => 'required|numeric|min:0.1',
            'sample_count' => 'nullable|integer|min:1',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $coop = Coop::findOrFail($validated['coop_id']);

        $weight = WeightSample::create([
            'flock_id' => $coop->flock_id,
            'coop_id' => $coop->id,
            'user_id' => Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id'),
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
            'sample_count' => $validated['sample_count'] ?? 50,
            'average_weight_kg' => $validated['average_weight_kg'],
            'age_weeks' => $coop->chicken_age_weeks,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data berat badan berhasil disimpan!',
                'data' => $weight,
            ]);
        }

        return redirect()->back()->with('success', 'Data berat badan berhasil disimpan!');
    }

    /**
     * Simpan Vaksin & Obat Cepat
     */
    public function storeHealthTreatment(Request $request)
    {
        $validated = $request->validate([
            'coop_id' => 'nullable|exists:coops,id',
            'medicine_name' => 'required|string',
            'type' => 'nullable|string',
            'dosage' => 'nullable|string',
            'application_method' => 'nullable|string',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $flockId = null;
        if (!empty($validated['coop_id'])) {
            $coop = Coop::find($validated['coop_id']);
            $flockId = $coop ? $coop->flock_id : null;
        }

        $health = HealthTreatment::create([
            'flock_id' => $flockId,
            'coop_id' => $validated['coop_id'] ?? null,
            'user_id' => Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id'),
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
            'time' => Carbon::now()->format('H:i:s'),
            'type' => $validated['type'] ?? 'vaksin',
            'medicine_name' => $validated['medicine_name'],
            'dosage' => $validated['dosage'] ?? '1 Botol',
            'application_method' => $validated['application_method'] ?? 'Air Minum',
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data vaksin/obat berhasil disimpan!',
                'data' => $health,
            ]);
        }

        return redirect()->back()->with('success', 'Data vaksin/obat berhasil disimpan!');
    }
}
