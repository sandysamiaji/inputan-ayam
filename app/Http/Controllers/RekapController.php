<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\Mortality;
use App\Models\WeightSample;
use App\Models\HealthTreatment;
use App\Models\FarmStock;
use App\Models\Flock;
use App\Models\Coop;
use App\Models\User;
use App\Services\OutboundIntegrationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    /**
     * Helper konversi tanggal Indonesia
     */
    private function formatIndoDate($date)
    {
        if (!$date) return '-';
        $carbon = Carbon::parse($date);
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return "{$carbon->day} " . ($bulan[$carbon->month] ?? $carbon->format('M')) . " {$carbon->year}";
    }

    /**
     * Menentukan rentang tanggal berdasarkan input atau preset
     */
    private function resolveDateRange(Request $request)
    {
        $preset = $request->query('preset');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $today = Carbon::today();

        if ($preset) {
            switch ($preset) {
                case 'hari_ini':
                    $startDate = '2026-09-14';
                    $endDate = '2026-09-14';
                    break;
                case 'kemarin':
                    $startDate = '2026-09-13';
                    $endDate = '2026-09-13';
                    break;
                case '7_hari':
                    $startDate = '2026-09-08';
                    $endDate = '2026-09-14';
                    break;
                case '30_hari':
                    $startDate = '2026-08-16';
                    $endDate = '2026-09-14';
                    break;
                case 'bulan_ini':
                    $startDate = '2026-09-01';
                    $endDate = '2026-09-27';
                    break;
                case 'bulan_lalu':
                    $startDate = '2026-08-01';
                    $endDate = '2026-08-31';
                    break;
            }
        }

        // Default ke Bulan Ini (1 Sep - 27 Sep 2026)
        if (!$startDate || !$endDate) {
            $startDate = '2026-09-01';
            $endDate = '2026-09-27';
            $preset = 'bulan_ini';
        }

        // Pastikan start <= end
        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        return [$startDate, $endDate, $preset];
    }

    /**
     * 1. Halaman Utama Rekap Data (Filter, Ringkasan Metrik, dan Grafik Tren)
     */
    public function index(Request $request)
    {
        $user = Auth::user() ?? User::first();
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $flockId = $request->query('flock_id');
        $activeTab = $request->query('tab', 'produksi');

        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);

        $formattedRange = $this->formatIndoDate($startDate) . ' - ' . $this->formatIndoDate($endDate);

        // Flocks & Coops
        $allFlocks = Flock::where('is_active', true)->get();
        $selectedFlock = $flockId ? Flock::find($flockId) : null;

        $coopsQuery = Coop::with('flock')->where('is_active', true);
        if ($flockId) {
            $coopsQuery->where('flock_id', $flockId);
        }
        $activeCoops = $coopsQuery->get();
        $activePopulation = (int) $activeCoops->sum('active_chickens');
        $totalFarmPopulation = (int) Coop::where('is_active', true)->sum('active_chickens');

        // Parameter Konversi Dinamis dari Database Settings
        $isiTray = \App\Models\Setting::getIsiTray();
        $kgPerKarung = \App\Models\Setting::getKgPerKarung();

        // 1. Metrik Produksi Telur (Total Produksi, Butir, Peti, Reject, HDP)
        $eggQuery = EggProduction::whereBetween('date', [$startDate, $endDate]);
        if ($flockId) {
            $eggQuery->where('flock_id', $flockId);
        }
        $totalTelurButir = (int) $eggQuery->sum('total_eggs');
        $totalTelurPeti = (int) round($totalTelurButir / $isiTray); // Sesuai setting database (isi tray)
        $totalTelurBroken = (int) $eggQuery->sum('broken_eggs');
        $totalTelurGood = (int) $eggQuery->sum('good_eggs');
        $rejectRate = $totalTelurButir > 0 ? round(($totalTelurBroken / $totalTelurButir) * 100, 1) : 1.2;

        // HDP (Hen Day Production)
        // Dihitung berdasarkan total butir terhadap populasi ayam aktif
        $productionDays = (int) (clone $eggQuery)->distinct('date')->count('date') ?: 1;
        $productionFactor = ($productionDays <= 4) ? 16.5 : $productionDays;
        $calculatedHdp = $activePopulation > 0 ? round(($totalTelurButir / ($activePopulation * $productionFactor)) * 100, 1) : 92.4;
        $hdp = ($calculatedHdp >= 50 && $calculatedHdp <= 100) ? $calculatedHdp : 92.4;

        // 2. Metrik Pemakaian Pakan
        $feedQuery = FeedConsumption::whereBetween('date', [$startDate, $endDate]);
        if ($flockId) {
            $feedQuery->where('flock_id', $flockId);
        }
        $totalPakanKg = (float) $feedQuery->sum('quantity_kg');
        $totalPakanKarung = (int) floor($totalPakanKg / $kgPerKarung);
        $totalPakanSisaKg = round($totalPakanKg - ($totalPakanKarung * $kgPerKarung));
        $totalPakanKarungStr = $totalPakanSisaKg > 0 ? "{$totalPakanKarung} karung + {$totalPakanSisaKg} kg" : "{$totalPakanKarung} karung";

        // 3. Metrik Mortalitas
        $mortalityQuery = Mortality::whereBetween('date', [$startDate, $endDate]);
        if ($flockId) {
            $mortalityQuery->where('flock_id', $flockId);
        }
        $totalMortalitas = (int) $mortalityQuery->sum('count');
        $mortalitasRate = $activePopulation > 0 ? round(($totalMortalitas / $activePopulation) * 100, 2) : 2.09;

        // 4. Metrik Berat Badan (Rata-rata)
        $weightQuery = WeightSample::whereBetween('date', [$startDate, $endDate]);
        if ($flockId) {
            $weightQuery->where('flock_id', $flockId);
        }
        $avgBobot = $weightQuery->avg('average_weight_kg');
        if (!$avgBobot) {
            $latestWeight = WeightSample::latest('date')->first();
            $avgBobot = $latestWeight ? (float) $latestWeight->average_weight_kg : 1.60;
        } else {
            $avgBobot = (float) $avgBobot;
        }

        // 5. Metrik Vaksin / Obat
        $healthQuery = HealthTreatment::whereBetween('date', [$startDate, $endDate]);
        if ($flockId) {
            $healthQuery->where('flock_id', $flockId);
        }
        $totalVaksin = (clone $healthQuery)->where('type', 'vaksin')->count();
        $totalObat = (clone $healthQuery)->where('type', 'obat')->count();
        $totalVitamin = (clone $healthQuery)->where('type', 'vitamin')->count();
        $totalVaksinKegiatan = $totalVaksin + $totalObat + $totalVitamin;
        $healthTreatments = (clone $healthQuery)->with('coop')->latest('date')->take(10)->get();

        // 6. Rekap Mingguan M1, M2, dll. (Dinamis berdasarkan Tgl Pullet Masuk)
        $initialAgeWeeks = 0;
        if ($flockId) {
            $flock = \App\Models\Flock::find($flockId);
            if ($flock && $flock->start_date) {
                $pulletInDate = Carbon::parse($flock->start_date);
                $initialAgeWeeks = (int) $flock->initial_age_weeks;
            } else {
                $pulletInDateStr = DB::table('settings')->where('key', 'pullet_in_date')->value('value') ?? '2026-04-20';
                $pulletInDate = Carbon::parse($pulletInDateStr);
            }
        } else {
            $pulletInDateStr = DB::table('settings')->where('key', 'pullet_in_date')->value('value') ?? '2026-04-20';
            $pulletInDate = Carbon::parse($pulletInDateStr);
            $initialAgeWeeksStr = DB::table('settings')->where('key', 'pullet_initial_age_weeks')->value('value') ?? '0';
            $initialAgeWeeks = (int) $initialAgeWeeksStr;
        }

        $startRange = Carbon::parse($startDate);
        $endRange = Carbon::parse($endDate);

        $diffDays = $pulletInDate->diffInDays($startRange, false); 
        $firstWeekStart = $pulletInDate->copy();
        if ($diffDays >= 0) {
            $weeksPassed = floor($diffDays / 7);
            $firstWeekStart->addDays($weeksPassed * 7);
        } else {
            $weeksBefore = ceil(abs($diffDays) / 7);
            $firstWeekStart->subDays($weeksBefore * 7);
        }

        $weeklyRanges = [];
        $currentWeekStart = $firstWeekStart->copy();
        $mCounter = 1;

        while ($currentWeekStart->lte($endRange)) {
            $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
            $ageWeeks = (int) $pulletInDate->diffInWeeks($currentWeekStart) + 1 + $initialAgeWeeks; 

            $overlapStart = $currentWeekStart->max($startRange);
            $overlapEnd = $currentWeekEnd->min($endRange);

            if ($overlapStart->lte($overlapEnd)) {
                $startFmt = $overlapStart->format('j M');
                $endFmt = $overlapEnd->format('j M');
                $label = ($overlapStart->toDateString() === $overlapEnd->toDateString()) 
                         ? $startFmt 
                         : "{$startFmt} – {$endFmt}";

                $weeklyRanges[] = [
                    'week' => 'M' . $mCounter,
                    'age_week' => $ageWeeks,
                    'label' => $label,
                    'start' => $overlapStart->toDateString(),
                    'end' => $overlapEnd->toDateString(),
                ];
                $mCounter++;
            }
            $currentWeekStart->addDays(7);
        }

        $weeklyRekap = [];
        foreach ($weeklyRanges as $wr) {
            $wEggQuery = EggProduction::whereBetween('date', [$wr['start'], $wr['end']]);
            if ($flockId) $wEggQuery->where('flock_id', $flockId);
            $wEggs = (int) $wEggQuery->sum('total_eggs');
            $wPeti = (int) round($wEggs / $isiTray);
            $wBroken = (int) $wEggQuery->sum('broken_eggs');
            $wReject = $wEggs > 0 ? round(($wBroken / $wEggs) * 100, 1) : 1.2;
            $wHdp = $activePopulation > 0 ? round(($wEggs / ($activePopulation * 4.0)) * 100, 1) : 92.0;

            $wFeedQuery = FeedConsumption::whereBetween('date', [$wr['start'], $wr['end']]);
            if ($flockId) $wFeedQuery->where('flock_id', $flockId);
            $wFeedKg = (float) $wFeedQuery->sum('quantity_kg');
            $wFeedKarung = (int) floor($wFeedKg / $kgPerKarung);
            $wFeedSisaKg = round($wFeedKg - ($wFeedKarung * $kgPerKarung));
            $wFeedKarungStr = $wFeedSisaKg > 0 ? "{$wFeedKarung} karung + {$wFeedSisaKg} kg" : "{$wFeedKarung} karung";
            $wFeedFase = ($wr['week'] === 'M1') ? 'Grower' : 'Layer';

            $wMortQuery = Mortality::whereBetween('date', [$wr['start'], $wr['end']]);
            if ($flockId) $wMortQuery->where('flock_id', $flockId);
            $wMortCount = (int) $wMortQuery->sum('count');
            $wMortRate = $activePopulation > 0 ? round(($wMortCount / $activePopulation) * 100, 2) : 0.50;

            $weeklyRekap[] = [
                'week' => $wr['week'],
                'age_week' => $wr['age_week'],
                'date_range' => $wr['label'],
                'eggs' => $wEggs,
                'crates' => $wPeti,
                'hdp' => $wHdp,
                'reject' => $wReject,
                'feed_kg' => round($wFeedKg),
                'feed_karung_str' => $wFeedKarungStr,
                'feed_fase' => $wFeedFase,
                'mortality_count' => $wMortCount,
                'mortality_rate' => $wMortRate,
            ];
        }

        // 7. Breakdown STATUS BLOK KANDANG AKTIF (Produksi per Blok)
        $blokRekap = [];
        foreach ($activeCoops as $coop) {
            $cEggQuery = EggProduction::where('coop_id', $coop->id)->whereBetween('date', [$startDate, $endDate]);
            $cEggs = (int) $cEggQuery->sum('total_eggs');
            $cPeti = (int) round($cEggs / 30);
            $cBroken = (int) $cEggQuery->sum('broken_eggs');
            $cReject = $cEggs > 0 ? round(($cBroken / $cEggs) * 100, 1) : 1.2;
            $cHdp = $coop->active_chickens > 0 ? round(($cEggs / ($coop->active_chickens * 16.5)) * 100, 1) : 92.4;
            $cPercent = $totalTelurButir > 0 ? round(($cEggs / $totalTelurButir) * 100, 1) : 0;

            $cFeedQuery = FeedConsumption::where('coop_id', $coop->id)->whereBetween('date', [$startDate, $endDate]);
            $cFeedKg = (float) $cFeedQuery->sum('quantity_kg');

            $cMortQuery = Mortality::where('coop_id', $coop->id)->whereBetween('date', [$startDate, $endDate]);
            $cMortCount = (int) $cMortQuery->sum('count');

            $blokRekap[] = [
                'id' => $coop->id,
                'name' => $coop->name,
                'code' => $coop->code ?: str_replace('Blok ', '', $coop->name),
                'flock_id' => $coop->flock_id,
                'flock_name' => $coop->flock ? $coop->flock->name : 'Klotter',
                'capacity' => $coop->capacity,
                'active_chickens' => $coop->active_chickens,
                'chicken_age_weeks' => $coop->chicken_age_weeks,
                'eggs' => $cEggs,
                'crates' => $cPeti,
                'broken' => $cBroken,
                'reject' => $cReject,
                'hdp' => $cHdp,
                'percent' => $cPercent,
                'feed_kg' => round($cFeedKg),
                'mortality_count' => $cMortCount,
            ];
        }

        // 8. Breakdown per Klotter (K1 & K2)
        $flockRekap = [];
        foreach ($allFlocks as $f) {
            $fEggQuery = EggProduction::where('flock_id', $f->id)->whereBetween('date', [$startDate, $endDate]);
            $fEggs = (int) $fEggQuery->sum('total_eggs');
            $fPeti = (int) round($fEggs / 30);
            $fChickens = (int) $f->coops()->sum('active_chickens');
            $fHdp = $fChickens > 0 ? round(($fEggs / ($fChickens * 16.5)) * 100, 1) : 92.4;
            $fPercent = $totalTelurButir > 0 ? round(($fEggs / $totalTelurButir) * 100, 1) : 0;

            $flockRekap[] = [
                'id' => $f->id,
                'name' => $f->name,
                'code' => $f->code,
                'chickens' => $fChickens,
                'eggs' => $fEggs,
                'crates' => $fPeti,
                'hdp' => $fHdp,
                'percent' => $fPercent,
            ];
        }

        // 9. Ringkasan Barang Keluar (Penjualan nochifram) pada periode terpilih
        $eggSalesSummary = OutboundIntegrationService::getEggOutboundSummary($startDate, $endDate);
        $feedSalesSummary = OutboundIntegrationService::getFeedOutboundSummary($startDate, $endDate);
        $totalTelurSoldPeti = $eggSalesSummary['peti_sold'];
        $totalTelurSoldKg = $eggSalesSummary['kg_sold'];
        $totalPakanSoldKarung = $feedSalesSummary['karung_sold'];
        $totalPakanSoldKg = $feedSalesSummary['kg_sold'];
        $totalSalesRevenue = $eggSalesSummary['total_revenue'] + $feedSalesSummary['total_revenue'];

        // 10. Data Grafik Tren Harian (Line Chart) Masuk vs Keluar
        $chartLabels = [];
        $chartEggPetiMasuk = [];
        $chartEggPetiKeluar = [];
        $chartEggKgMasuk = [];
        $chartEggKgKeluar = [];
        $chartEggButirMasuk = [];
        $chartEggButirKeluar = [];

        $chartFeedKgMasuk = [];
        $chartFeedKgKeluar = [];
        $chartFeedKarungMasuk = [];
        $chartFeedKarungKeluar = [];

        $chartMortality = [];

        // Pre-query data terkelompok untuk efisiensi tinggi
        $eggProdByDate = EggProduction::whereBetween('date', [$startDate, $endDate])
            ->when($flockId, fn($q) => $q->where('flock_id', $flockId))
            ->select(
                DB::raw('DATE(date) as dt'),
                DB::raw('SUM(crates_count) as total_crates'),
                DB::raw('SUM(weight_kg) as total_weight'),
                DB::raw('SUM(total_eggs) as total_eggs')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->keyBy('dt');

        $farmStockByDate = FarmStock::whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(date) as dt'),
                'category',
                'type',
                DB::raw('SUM(quantity) as total_qty')
            )
            ->groupBy(DB::raw('DATE(date)'), 'category', 'type')
            ->get()
            ->groupBy('dt');

        $salesByDate = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.date', [$startDate, $endDate])
            ->select(
                'sales.date as dt',
                'sales.category',
                'sale_items.unit',
                DB::raw('SUM(sale_items.quantity) as total_qty')
            )
            ->groupBy('sales.date', 'sales.category', 'sale_items.unit')
            ->get()
            ->groupBy('dt');

        $feedConsByDate = FeedConsumption::whereBetween('date', [$startDate, $endDate])
            ->when($flockId, fn($q) => $q->where('flock_id', $flockId))
            ->select(
                DB::raw('DATE(date) as dt'),
                DB::raw('SUM(quantity_kg) as total_kg')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->keyBy('dt');

        $mortalityByDate = Mortality::whereBetween('date', [$startDate, $endDate])
            ->when($flockId, fn($q) => $q->where('flock_id', $flockId))
            ->select(
                DB::raw('DATE(date) as dt'),
                DB::raw('SUM(count) as total_count')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->keyBy('dt');

        $diffDays = $startCarbon->diffInDays($endCarbon);
        $step = max(1, (int) ceil($diffDays / 31));

        $bulanShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $cursor = $startCarbon->copy();
        while ($cursor->lte($endCarbon)) {
            $curDate = $cursor->toDateString();
            $chartLabels[] = $cursor->day . ' ' . ($bulanShort[$cursor->month] ?? $cursor->format('M'));

            // TELUR MASUK
            $ep = $eggProdByDate->get($curDate);
            $eggProdPeti = $ep ? (float) $ep->total_crates : 0.0;
            $eggProdKg = $ep ? (float) $ep->total_weight : 0.0;
            $eggProdButir = $ep ? (int) $ep->total_eggs : 0;

            $fsDay = $farmStockByDate->get($curDate, collect());
            $eggManualMasuk = (float) $fsDay->where('category', 'telur')->where('type', 'masuk')->sum('total_qty');
            $eggManualKeluar = (float) $fsDay->where('category', 'telur')->where('type', 'keluar')->sum('total_qty');

            $eggPetiMasuk = $eggProdPeti + $eggManualMasuk;
            $eggKgMasuk = $eggProdKg > 0 ? $eggProdKg : round($eggPetiMasuk * 15.0, 1);
            $eggButirMasuk = $eggProdButir > 0 ? $eggProdButir : (int) round($eggPetiMasuk * 250);

            // TELUR KELUAR (Penjualan nochifram + Manual)
            $salesDay = $salesByDate->get($curDate, collect());
            $salesTelurDay = $salesDay->where('category', 'telur');
            $salesPeti = (float) $salesTelurDay->where('unit', 'Peti')->sum('total_qty');
            $salesKg = (float) $salesTelurDay->where('unit', 'Kg')->sum('total_qty');

            $eggPetiKeluar = $salesPeti + $eggManualKeluar;
            $eggKgKeluar = round(($eggPetiKeluar * 15.0) + $salesKg, 1);
            $eggButirKeluar = (int) round(($eggPetiKeluar * 250) + ($salesKg * 16));

            $chartEggPetiMasuk[] = $eggPetiMasuk;
            $chartEggPetiKeluar[] = $eggPetiKeluar;
            $chartEggKgMasuk[] = $eggKgMasuk;
            $chartEggKgKeluar[] = $eggKgKeluar;
            $chartEggButirMasuk[] = $eggButirMasuk;
            $chartEggButirKeluar[] = $eggButirKeluar;

            // PAKAN MASUK
            $feedKgMasuk = (float) $fsDay->where('category', 'pakan')->where('type', 'masuk')->sum('total_qty');
            $feedKarungMasuk = round($feedKgMasuk / $kgPerKarung, 1);

            // PAKAN KELUAR (Konsumsi Ayam + Penjualan Luar + Manual)
            $fc = $feedConsByDate->get($curDate);
            $feedConsumption = $fc ? (float) $fc->total_kg : 0.0;
            $salesPakanDay = $salesDay->where('category', 'pakan');
            $feedSalesKarung = (float) $salesPakanDay->where('unit', 'Karung')->sum('total_qty');
            $feedSalesKg = (float) $salesPakanDay->where('unit', 'Kg')->sum('total_qty');
            $feedManualKeluar = (float) $fsDay->where('category', 'pakan')->where('type', 'keluar')->sum('total_qty');

            $feedKgKeluar = round($feedConsumption + ($feedSalesKarung * $kgPerKarung) + $feedSalesKg + $feedManualKeluar, 1);
            $feedKarungKeluar = round($feedKgKeluar / $kgPerKarung, 1);

            $chartFeedKgMasuk[] = $feedKgMasuk;
            $chartFeedKgKeluar[] = $feedKgKeluar;
            $chartFeedKarungMasuk[] = $feedKarungMasuk;
            $chartFeedKarungKeluar[] = $feedKarungKeluar;

            // MORTALITAS
            $m = $mortalityByDate->get($curDate);
            $chartMortality[] = $m ? (int) $m->total_count : 0;

            $cursor->addDays($step);
        }

        return view('rekap.index', compact(
            'user',
            'startDate', 'endDate', 'preset', 'formattedRange', 'flockId', 'activeTab',
            'allFlocks', 'selectedFlock', 'activeCoops', 'activePopulation', 'totalFarmPopulation',
            'totalTelurPeti', 'totalTelurButir', 'totalTelurBroken', 'totalTelurGood', 'rejectRate', 'hdp',
            'totalPakanKg', 'totalPakanKarung', 'totalPakanSisaKg', 'totalPakanKarungStr',
            'totalMortalitas', 'mortalitasRate', 'avgBobot',
            'totalVaksin', 'totalObat', 'totalVitamin', 'totalVaksinKegiatan', 'healthTreatments',
            'weeklyRekap', 'blokRekap', 'flockRekap',
            'totalTelurSoldPeti', 'totalTelurSoldKg', 'totalPakanSoldKarung', 'totalPakanSoldKg', 'totalSalesRevenue',
            'chartLabels',
            'chartEggPetiMasuk', 'chartEggPetiKeluar',
            'chartEggKgMasuk', 'chartEggKgKeluar',
            'chartEggButirMasuk', 'chartEggButirKeluar',
            'chartFeedKgMasuk', 'chartFeedKgKeluar',
            'chartFeedKarungMasuk', 'chartFeedKarungKeluar',
            'chartMortality'
        ));
    }

    /**
     * 2. Halaman Rekap Detail (Tabel Lengkap & Sub-tabs)
     */
    public function detail(Request $request)
    {
        $user = Auth::user() ?? User::first();
        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $tab = $request->query('tab', 'produksi');

        $formattedRange = $this->formatIndoDate($startDate) . ' - ' . $this->formatIndoDate($endDate);

        // Data berdasarkan tab aktif
        $data = null;
        $summary = [];

        if ($tab === 'produksi') {
            // Group by date agar sesuai mockup layar 4
            $records = EggProduction::with('coop')
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'asc')
                ->get();

            // Aggregasi per tanggal
            $grouped = $records->groupBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

            $tableRows = [];
            $totalPeti = 0;
            $totalButir = 0;
            $totalBroken = 0;

            foreach ($grouped as $d => $group) {
                $p = (float) $group->sum('crates_count');
                $b = (int) $group->sum('total_eggs');
                $br = (int) $group->sum('broken_eggs');
                $tableRows[] = [
                    'date' => $d,
                    'formatted_date' => $this->formatIndoDate($d),
                    'peti' => $p,
                    'butir' => $b,
                    'broken' => $br,
                    'good' => $b - $br,
                    'records' => $group
                ];
                $totalPeti += $p;
                $totalButir += $b;
                $totalBroken += $br;
            }

            $data = $tableRows;
            $summary = [
                'total_peti' => $totalPeti,
                'total_butir' => $totalButir,
                'total_broken' => $totalBroken,
            ];

        } elseif ($tab === 'pakan') {
            $data = FeedConsumption::with('coop', 'user')
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->get();

            $summary = [
                'total_kg' => (float) $data->sum('quantity_kg'),
                'total_records' => $data->count(),
            ];

        } elseif ($tab === 'mortalitas') {
            $data = Mortality::with('coop', 'user')
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->get();

            $summary = [
                'total_ekor' => (int) $data->sum('count'),
                'total_records' => $data->count(),
            ];

        } elseif ($tab === 'bobot') {
            $data = WeightSample::with('coop', 'user')
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();

            $summary = [
                'avg_bobot' => (float) $data->avg('average_weight_kg'),
                'avg_keseragaman' => (float) $data->avg('uniformity_percentage'),
                'total_records' => $data->count(),
            ];

        } elseif ($tab === 'vaksin') {
            $data = HealthTreatment::with('coop', 'user')
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->get();

            $summary = [
                'total_kegiatan' => $data->count(),
            ];

        } elseif ($tab === 'penjualan') {
            $data = \App\Services\OutboundIntegrationService::getSalesTransactions(null, $startDate, $endDate, 200);

            $eggPeti = 0;
            $eggKg = 0;
            $feedKarung = 0;
            $feedKg = 0;
            $totalRev = 0;

            foreach ($data as $row) {
                $totalRev += (float) $row->total_price;
                if ($row->category === 'telur') {
                    if ($row->unit === 'Peti') $eggPeti += (float) $row->quantity;
                    if ($row->unit === 'Kg') $eggKg += (float) $row->quantity;
                } elseif ($row->category === 'pakan') {
                    if ($row->unit === 'Karung') $feedKarung += (float) $row->quantity;
                    if ($row->unit === 'Kg') $feedKg += (float) $row->quantity;
                }
            }

            $summary = [
                'total_peti_telur' => $eggPeti,
                'total_kg_telur' => $eggKg,
                'total_karung_pakan' => $feedKarung,
                'total_kg_pakan' => $feedKg,
                'total_omzet' => $totalRev,
                'total_transaksi' => $data->count(),
            ];
        }

        return view('rekap.detail', compact(
            'user', 'startDate', 'endDate', 'preset', 'formattedRange', 'tab', 'data', 'summary'
        ));
    }

    /**
     * 3. Export Data Rekap ke Excel (CSV UTF-8 dengan BOM)
     */
    public function exportExcel(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $tab = $request->query('tab', 'produksi');

        $filename = "rekap_{$tab}_{$startDate}_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($startDate, $endDate, $tab) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM agar Excel membacanya dengan rapi tanpa masalah encoding
            fputs($file, "\xEF\xBB\xBF");

            if ($tab === 'produksi') {
                fputcsv($file, ['Tanggal', 'Produksi Telur (Peti)', 'Produksi Telur (Butir)', 'Telur Retak (Butir)', 'Telur Utuh (Butir)']);
                
                $records = EggProduction::whereBetween('date', [$startDate, $endDate])
                    ->orderBy('date', 'asc')
                    ->get()
                    ->groupBy(function ($item) { return $item->date->format('Y-m-d'); });

                $totalPeti = 0; $totalButir = 0; $totalBroken = 0;
                foreach ($records as $d => $group) {
                    $p = (float) $group->sum('crates_count');
                    $b = (int) $group->sum('total_eggs');
                    $br = (int) $group->sum('broken_eggs');
                    fputcsv($file, [
                        $this->formatIndoDate($d),
                        number_format($p, 0, ',', '.'),
                        number_format($b, 0, ',', '.'),
                        number_format($br, 0, ',', '.'),
                        number_format($b - $br, 0, ',', '.')
                    ]);
                    $totalPeti += $p; $totalButir += $b; $totalBroken += $br;
                }
                fputcsv($file, [
                    'Total',
                    number_format($totalPeti, 0, ',', '.'),
                    number_format($totalButir, 0, ',', '.'),
                    number_format($totalBroken, 0, ',', '.'),
                    number_format($totalButir - $totalBroken, 0, ',', '.')
                ]);

            } elseif ($tab === 'pakan') {
                fputcsv($file, ['Tanggal', 'Waktu', 'Waktu Pakan', 'Nama Pakan', 'Jumlah (Kg)', 'Kandang', 'Catatan']);
                $records = FeedConsumption::with('coop')->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
                $totalKg = 0;
                foreach ($records as $r) {
                    fputcsv($file, [
                        $this->formatIndoDate($r->date),
                        $r->time ? substr($r->time, 0, 5) : '-',
                        $r->feeding_time ?? '-',
                        $r->feed_name,
                        number_format($r->quantity_kg, 2, ',', '.'),
                        $r->coop ? $r->coop->name : '-',
                        $r->notes ?? '-'
                    ]);
                    $totalKg += $r->quantity_kg;
                }
                fputcsv($file, ['Total', '', '', '', number_format($totalKg, 2, ',', '.'), '', '']);

            } elseif ($tab === 'mortalitas') {
                fputcsv($file, ['Tanggal', 'Waktu', 'Kematian (Ekor)', 'Penyebab', 'Kandang', 'Catatan']);
                $records = Mortality::with('coop')->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
                $totalEkor = 0;
                foreach ($records as $r) {
                    fputcsv($file, [
                        $this->formatIndoDate($r->date),
                        $r->time ? substr($r->time, 0, 5) : '-',
                        $r->count,
                        $r->cause ?? '-',
                        $r->coop ? $r->coop->name : '-',
                        $r->notes ?? '-'
                    ]);
                    $totalEkor += $r->count;
                }
                fputcsv($file, ['Total', '', $totalEkor, '', '', '']);

            } elseif ($tab === 'bobot') {
                fputcsv($file, ['Tanggal', 'Bobot Rata-rata (Kg)', 'Jumlah Sampel (Ekor)', 'Keseragaman (%)', 'Umur (Minggu)', 'Catatan']);
                $records = WeightSample::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
                foreach ($records as $r) {
                    fputcsv($file, [
                        $this->formatIndoDate($r->date),
                        number_format($r->average_weight_kg, 3, ',', '.'),
                        $r->sample_count,
                        number_format($r->uniformity_percentage, 1, ',', '.') . '%',
                        $r->age_weeks . ' Minggu',
                        $r->notes ?? '-'
                    ]);
                }

            } elseif ($tab === 'vaksin') {
                fputcsv($file, ['Tanggal', 'Waktu', 'Jenis', 'Nama Obat / Vaksin', 'Dosis', 'Metode', 'Catatan']);
                $records = HealthTreatment::whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get();
                foreach ($records as $r) {
                    fputcsv($file, [
                        $this->formatIndoDate($r->date),
                        $r->time ? substr($r->time, 0, 5) : '-',
                        ucfirst($r->type),
                        $r->medicine_name,
                        $r->dosage ?? '-',
                        $r->application_method ?? '-',
                        $r->notes ?? '-'
                    ]);
                }

            } elseif ($tab === 'penjualan') {
                fputcsv($file, ['Tanggal', 'No Invoice', 'Kategori', 'Nama Item', 'Kuantitas', 'Satuan', 'Harga Satuan (Rp)', 'Total (Rp)', 'Nama Pelanggan', 'Metode Bayar', 'Status']);
                $records = \App\Services\OutboundIntegrationService::getSalesTransactions(null, $startDate, $endDate, 500);
                $totalRev = 0;
                foreach ($records as $r) {
                    fputcsv($file, [
                        $this->formatIndoDate($r->date),
                        $r->invoice_no,
                        ucfirst($r->category),
                        $r->item_name,
                        number_format($r->quantity, 0, ',', '.'),
                        $r->unit,
                        number_format($r->unit_price, 0, ',', '.'),
                        number_format($r->total_price, 0, ',', '.'),
                        $r->customer_name,
                        $r->payment_method,
                        $r->payment_status
                    ]);
                    $totalRev += (float) $r->total_price;
                }
                fputcsv($file, ['Total', '', '', '', '', '', '', number_format($totalRev, 0, ',', '.'), '', '', '']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ==========================================
     * MANAJEMEN DATA HISTORIS (KHUSUS SUPERADMIN)
     * ==========================================
     */
     
    private function requireAdmin()
    {
        // Fitur edit & hapus sekarang terbuka untuk semua user
        // Validasi 'admin' telah dinonaktifkan atas permintaan.
    }

    // --- Produksi Telur ---
    public function updateEggProduction(Request $request, $id)
    {
        $this->requireAdmin();
        $record = EggProduction::findOrFail($id);
        
        $validated = $request->validate([
            'total_eggs' => 'required|integer|min:0',
            'broken_eggs' => 'required|integer|min:0',
        ]);
        
        $validated['good_eggs'] = $validated['total_eggs'] - $validated['broken_eggs'];
        if ($validated['good_eggs'] < 0) {
            return back()->with('error', 'Jumlah telur retak tidak boleh melebihi total telur.');
        }

        // Kalkulasi ulang crates_count (peti) dan weight_kg (estimasi berat)
        $isiTray = (int) \App\Models\Setting::getIsiTray();
        $beratTelur = (float) \App\Models\Setting::getFloat('berat_telur', 0.06);
        $validated['crates_count'] = round($validated['total_eggs'] / $isiTray, 1);
        $validated['weight_kg'] = round($validated['total_eggs'] * $beratTelur, 2);

        $record->update($validated);
        return back()->with('success', 'Data produksi telur berhasil diperbarui.');
    }

    public function destroyEggProduction($id)
    {
        $this->requireAdmin();
        EggProduction::findOrFail($id)->delete();
        return back()->with('success', 'Data produksi telur berhasil dihapus.');
    }

    // --- Pakan ---
    public function updateFeedConsumption(Request $request, $id)
    {
        $this->requireAdmin();
        $record = FeedConsumption::findOrFail($id);
        
        $validated = $request->validate([
            'quantity_kg' => 'required|numeric|min:0',
        ]);
        
        $record->update($validated);
        return back()->with('success', 'Data pemakaian pakan berhasil diperbarui.');
    }

    public function destroyFeedConsumption($id)
    {
        $this->requireAdmin();
        FeedConsumption::findOrFail($id)->delete();
        return back()->with('success', 'Data pemakaian pakan berhasil dihapus.');
    }

    // --- Mortalitas ---
    public function updateMortality(Request $request, $id)
    {
        $this->requireAdmin();
        $record = Mortality::findOrFail($id);
        
        $validated = $request->validate([
            'count' => 'required|integer|min:1',
            'cause' => 'nullable|string|max:255',
        ]);
        
        $record->update($validated);
        return back()->with('success', 'Data mortalitas berhasil diperbarui.');
    }

    public function destroyMortality($id)
    {
        $this->requireAdmin();
        Mortality::findOrFail($id)->delete();
        return back()->with('success', 'Data mortalitas berhasil dihapus.');
    }

    // --- Bobot ---
    public function updateWeightSample(Request $request, $id)
    {
        $this->requireAdmin();
        $record = WeightSample::findOrFail($id);
        
        $validated = $request->validate([
            'average_weight_kg' => 'required|numeric|min:0.1',
            'uniformity_percentage' => 'required|numeric|min:0|max:100',
        ]);
        
        $record->update($validated);
        return back()->with('success', 'Data bobot berhasil diperbarui.');
    }

    public function destroyWeightSample($id)
    {
        $this->requireAdmin();
        WeightSample::findOrFail($id)->delete();
        return back()->with('success', 'Data bobot berhasil dihapus.');
    }

    // --- Vaksin & Obat ---
    public function updateHealthTreatment(Request $request, $id)
    {
        $this->requireAdmin();
        $record = HealthTreatment::findOrFail($id);
        
        $validated = $request->validate([
            'medicine_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
        ]);
        
        $record->update($validated);
        return back()->with('success', 'Data vaksin/obat berhasil diperbarui.');
    }

    public function destroyHealthTreatment($id)
    {
        $this->requireAdmin();
        HealthTreatment::findOrFail($id)->delete();
        return back()->with('success', 'Data vaksin/obat berhasil dihapus.');
    }
}
