<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FarmStock;
use App\Models\User;
use App\Models\Coop;
use App\Models\Flock;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\HealthTreatment;
use App\Models\Quarantine;
use App\Models\Mortality;
use App\Services\OutboundIntegrationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class WarehouseController extends Controller
{
    /**
     * Helper untuk format tanggal Indonesia
     */
    private function formatIndoDate($date)
    {
        if (!$date) return '-';
        $carbon = Carbon::parse($date);
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return "{$carbon->day} " . ($bulanIndonesia[$carbon->month] ?? $carbon->format('M')) . " {$carbon->year}";
    }

    /**
     * 1. Halaman Utama Gudang (Overview)
     */
    public function index(Request $request)
    {
        $user = Auth::user() ?? User::where('role', 'user')->orWhere('username', 'petugas')->first() ?? User::first();

        // Parameter Rentang Tanggal: Default NULL (Tarik SEMUA Data Tanpa Filter jika user tidak memilih)
        $hasDateFilter = $request->filled('start_date') && $request->filled('end_date');
        $defaultStartDate = Carbon::today()->subDays(13)->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();

        if ($hasDateFilter) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            try {
                $startCarbon = Carbon::parse($startDate)->startOfDay();
                $endCarbon = Carbon::parse($endDate)->endOfDay();
            } catch (\Exception $e) {
                $startCarbon = Carbon::parse($defaultStartDate)->startOfDay();
                $endCarbon = Carbon::parse($defaultEndDate)->endOfDay();
                $startDate = $defaultStartDate;
                $endDate = $defaultEndDate;
            }

            if ($startCarbon->gt($endCarbon)) {
                $temp = $startCarbon;
                $startCarbon = $endCarbon->copy()->startOfDay();
                $endCarbon = $temp->copy()->endOfDay();
                $startDate = $startCarbon->toDateString();
                $endDate = $endCarbon->toDateString();
            }

            $diffDays = $startCarbon->diffInDays($endCarbon) + 1;
        } else {
            $startDate = null;
            $endDate = null;
            $startCarbon = null;
            $endCarbon = null;
            $diffDays = null;
        }

        $namaHari = [
            'Sunday' => 'Min', 'Monday' => 'Sen', 'Tuesday' => 'Sel',
            'Wednesday' => 'Rab', 'Thursday' => 'Kam', 'Friday' => 'Jum', 'Saturday' => 'Sab'
        ];
        $bulanShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $formattedStartDate = ($hasDateFilter && $startCarbon) ? (($namaHari[$startCarbon->format('l')] ?? $startCarbon->format('D')) . ', ' . $startCarbon->day . ' ' . ($bulanShort[$startCarbon->month] ?? $startCarbon->format('M')) . ' ' . $startCarbon->year) : null;
        $formattedEndDate = ($hasDateFilter && $endCarbon) ? (($namaHari[$endCarbon->format('l')] ?? $endCarbon->format('D')) . ', ' . $endCarbon->day . ' ' . ($bulanShort[$endCarbon->month] ?? $endCarbon->format('M')) . ' ' . $endCarbon->year) : null;

        // 1. Gudang Telur (Terintegrasi Penjualan nochifram)
        $eggSummary = OutboundIntegrationService::getEggOutboundSummary($startDate, $endDate);
        $telurMasuk = $eggSummary['total_produced_crates'];
        $telurMasukButir = $eggSummary['total_produced_eggs'];
        $telurMasukKg = $eggSummary['total_produced_kg'];
        $telurKeluar = $eggSummary['total_keluar_peti'];
        $telurKeluarKg = $eggSummary['total_keluar_kg'];
        $telurKeluarEggs = $eggSummary['total_keluar_eggs'];
        $telurStok = $eggSummary['current_stock_peti'];
        $telurStokKgTotal = $eggSummary['current_stock_kg_total'];
        $telurStokButir = $eggSummary['current_stock_eggs'];
        $telurPetiSold = $eggSummary['peti_sold'];
        $telurKgSold = $eggSummary['kg_sold'];
        $telurRusakPeti = $eggSummary['total_broken_peti'] ?? 0;
        $telurRusakKg = $eggSummary['total_broken_kg'] ?? 0;
        $telurRusakButir = $eggSummary['total_broken_eggs'] ?? 0;
        $telurRevenue = $eggSummary['total_revenue'];

        // 2. Gudang Pakan (Terintegrasi Konsumsi Kandang & Penjualan Luar)
        $feedSummary = OutboundIntegrationService::getFeedOutboundSummary($startDate, $endDate);
        $pakanMasuk = $feedSummary['purchased_kg'];
        $pakanMasukKarung = $feedSummary['purchased_karung'];
        $pakanKeluar = $feedSummary['total_keluar_kg'];
        $pakanTotalKarungKeluar = $feedSummary['total_keluar_karung'];
        $pakanStok = $feedSummary['current_stock_kg'];
        $pakanStokKarung = $feedSummary['current_stock_karung'];
        $pakanKarungSold = $feedSummary['karung_sold'];
        $pakanKgSold = $feedSummary['kg_sold'];
        $pakanConsumptionKg = $feedSummary['consumption_kg'];
        $pakanConsumptionKarung = $feedSummary['consumption_karung'];
        $pakanRevenue = $feedSummary['total_revenue'];

        // 3. Gudang Obat, Vaksin & Vitamin (Satuan: Item / Botol)
        $obatMasukQuery = FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'masuk');
        $obatKeluarManualQuery = FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'keluar');
        $htQuery = \App\Models\HealthTreatment::query();

        if ($startDate && $endDate) {
            $obatMasukQuery->whereBetween('date', [$startDate, $endDate]);
            $obatKeluarManualQuery->whereBetween('date', [$startDate, $endDate]);
            $htQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $obatMasuk = (float) $obatMasukQuery->sum('quantity');
        $obatKeluarManual = (float) $obatKeluarManualQuery->sum('quantity');
        
        $healthTreatments = $htQuery->get();
        $obatKeluarKandang = 0;
        foreach ($healthTreatments as $ht) {
            $val = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
            if ($val == 0) $val = 1;
            $obatKeluarKandang += $val;
        }
        
        $obatKeluar = $obatKeluarManual + $obatKeluarKandang;
        $obatStok = round($obatMasuk - $obatKeluar, 1);

        // 4. Gudang Ayam Karantina (Satuan: Ekor)
        try {
            Quarantine::ensureTableExists();
            $qMasukQuery = Quarantine::where('status', 'sakit');
            $qKeluarQuery = Quarantine::where('status', 'sembuh');
            if ($startDate && $endDate) {
                $qMasukQuery->whereBetween('date', [$startDate, $endDate]);
                $qKeluarQuery->whereBetween('date', [$startDate, $endDate]);
            }
            $karantinaMasuk = (int) $qMasukQuery->sum('count');
            $karantinaKeluar = (int) $qKeluarQuery->sum('count');
            $karantinaStok = Quarantine::getCurrentCount();
        } catch (\Throwable $e) {
            $karantinaMasuk = 0;
            $karantinaKeluar = 0;
            $karantinaStok = 0;
        }

        // Periode tanggal untuk visualisasi grafik tren harian
        $chartStart = $hasDateFilter ? $startCarbon->copy() : Carbon::today()->subDays(13)->startOfDay();
        $chartEnd = $hasDateFilter ? $endCarbon->copy() : Carbon::today()->endOfDay();
        $chartStartDate = $chartStart->toDateString();
        $chartEndDate = $chartEnd->toDateString();

        // Pre-query data aliran barang berdasarkan rentang tanggal grafik
        $eggProdByDate = EggProduction::whereBetween('date', [$chartStartDate, $chartEndDate])
            ->select(
                DB::raw('DATE(date) as dt'),
                DB::raw('SUM(crates_count) as total_crates'),
                DB::raw('SUM(broken_eggs) as total_broken'),
                DB::raw('SUM(good_eggs) as total_good')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->keyBy('dt');

        $feedConsByDate = FeedConsumption::whereBetween('date', [$chartStartDate, $chartEndDate])
            ->select(
                DB::raw('DATE(date) as dt'),
                DB::raw('SUM(quantity_kg) as total_kg')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->keyBy('dt');

        $farmStockByDate = FarmStock::whereBetween('date', [$chartStartDate, $chartEndDate])
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
            ->whereBetween('sales.date', [$chartStartDate, $chartEndDate])
            ->select(
                'sales.date as dt',
                'sales.category',
                'sale_items.unit',
                DB::raw('SUM(sale_items.quantity) as total_qty')
            )
            ->groupBy('sales.date', 'sales.category', 'sale_items.unit')
            ->get()
            ->groupBy('dt');

        $healthByDate = HealthTreatment::whereBetween('date', [$chartStartDate, $chartEndDate])
            ->get()
            ->groupBy(fn($ht) => Carbon::parse($ht->date)->toDateString());

        $chartLabels = [];
        $chartEggMasuk = [];
        $chartEggRusak = [];
        $chartEggTerjual = [];
        $chartEggTotalKeluar = [];

        $chartFeedMasuk = [];
        $chartFeedKonsumsi = [];
        $chartFeedTerjual = [];
        $chartFeedTotalKeluar = [];

        $chartObatMasuk = [];
        $chartObatKonsumsi = [];
        $chartObatTotalKeluar = [];

        $chartAllMasuk = [];
        $chartAllDigunakan = [];
        $chartAllKeluar = [];
        $chartAllTerjual = [];

        $cursor = $chartStart->copy();
        while ($cursor->lte($chartEnd)) {
            $dt = $cursor->toDateString();
            $chartLabels[] = $cursor->day . ' ' . ($bulanShort[$cursor->month] ?? $cursor->format('M'));

            // 1. Telur (Satuan: Peti)
            $ep = $eggProdByDate->get($dt);
            $fsEggList = $farmStockByDate->get($dt, collect())->where('category', 'telur');
            $fsEggMasuk = (float) $fsEggList->where('type', 'masuk')->sum('total_qty');
            $fsEggKeluar = (float) $fsEggList->where('type', 'keluar')->sum('total_qty');

            $eggMasukPeti = ($ep ? (float) $ep->total_crates : 0.0) + $fsEggMasuk;
            $eggRusakButir = $ep ? (int) $ep->total_broken : 0;
            $targetEggWeightKg = \App\Services\ProductionStandardService::getTargetEggWeightKgForCoop(null, $dt);
            $eggRusakKg = round($eggRusakButir * $targetEggWeightKg, 2);
            $eggRusakPeti = round($eggRusakKg / 10, 2);

            $salesListDay = $salesByDate->get($dt, collect());
            $eggSalesDay = $salesListDay->where('category', 'telur');
            $eggPetiSold = (float) $eggSalesDay->where('unit', 'Peti')->sum('total_qty');
            $eggKgSold = (float) $eggSalesDay->where('unit', 'Kg')->sum('total_qty');
            $eggTotalSoldPeti = $eggPetiSold + round($eggKgSold / 10, 2);

            $eggTotalKeluarPeti = $eggTotalSoldPeti + $eggRusakPeti + $fsEggKeluar;

            $chartEggMasuk[] = round($eggMasukPeti, 1);
            $chartEggRusak[] = round($eggRusakPeti, 2);
            $chartEggTerjual[] = round($eggTotalSoldPeti, 1);
            $chartEggTotalKeluar[] = round($eggTotalKeluarPeti, 1);

            // 2. Pakan (Satuan: Kg)
            $fc = $feedConsByDate->get($dt);
            $feedKonsumsiKg = $fc ? (float) $fc->total_kg : 0.0;

            $fsFeedList = $farmStockByDate->get($dt, collect())->where('category', 'pakan');
            $feedMasukKg = (float) $fsFeedList->where('type', 'masuk')->sum('total_qty');
            $feedManualKeluarKg = (float) $fsFeedList->where('type', 'keluar')->sum('total_qty');

            $feedSalesDay = $salesListDay->where('category', 'pakan');
            $feedKarungSold = (float) $feedSalesDay->where('unit', 'Karung')->sum('total_qty');
            $feedKgSold = (float) $feedSalesDay->where('unit', 'Kg')->sum('total_qty');
            $feedSoldTotalKg = ($feedKarungSold * 50) + $feedKgSold;

            $feedTotalKeluarKg = $feedKonsumsiKg + $feedSoldTotalKg + $feedManualKeluarKg;

            $chartFeedMasuk[] = round($feedMasukKg, 1);
            $chartFeedKonsumsi[] = round($feedKonsumsiKg, 1);
            $chartFeedTerjual[] = round($feedSoldTotalKg, 1);
            $chartFeedTotalKeluar[] = round($feedTotalKeluarKg, 1);

            // 3. Obat & Vaksin (Satuan: Item/Dosis)
            $fsObatList = $farmStockByDate->get($dt, collect())->whereIn('category', ['obat', 'vaksin', 'vitamin']);
            $obatMasukDay = (float) $fsObatList->where('type', 'masuk')->sum('total_qty');
            $obatManualKeluarDay = (float) $fsObatList->where('type', 'keluar')->sum('total_qty');

            $htDay = $healthByDate->get($dt, collect());
            $obatKonsumsiDay = 0;
            foreach ($htDay as $ht) {
                $v = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
                $obatKonsumsiDay += ($v > 0 ? $v : 1);
            }
            $obatTotalKeluarDay = $obatKonsumsiDay + $obatManualKeluarDay;

            $chartObatMasuk[] = round($obatMasukDay, 1);
            $chartObatKonsumsi[] = round($obatKonsumsiDay, 1);
            $chartObatTotalKeluar[] = round($obatTotalKeluarDay, 1);

            // 4. Semua Aliran Barang (Overview Gabungan)
            $allMasuk = $eggMasukPeti + round($feedMasukKg / 50, 1) + $obatMasukDay;
            $allDigunakan = $eggRusakPeti + round($feedKonsumsiKg / 50, 1) + $obatKonsumsiDay;
            $allKeluar = $eggTotalKeluarPeti + round($feedTotalKeluarKg / 50, 1) + $obatTotalKeluarDay;
            $allTerjual = $eggTotalSoldPeti + round($feedSoldTotalKg / 50, 1);

            $chartAllMasuk[] = round($allMasuk, 1);
            $chartAllDigunakan[] = round($allDigunakan, 1);
            $chartAllKeluar[] = round($allKeluar, 1);
            $chartAllTerjual[] = round($allTerjual, 1);

            $cursor->addDay();
        }

        // Totals per real data stream (14 hari terakhir)
        $sumTelurMasuk = round(array_sum($chartEggMasuk), 1);
        $sumTelurRusakPeti = round(array_sum($chartEggRusak), 2);
        $sumTelurRusakButir = round($sumTelurRusakPeti * 25);
        $sumTelurTerjual = round(array_sum($chartEggTerjual), 1);

        $sumPakanMasuk = round(array_sum($chartFeedMasuk), 1);
        $sumPakanKonsumsi = round(array_sum($chartFeedKonsumsi), 1);
        $sumPakanTerjual = round(array_sum($chartFeedTerjual), 1);

        $sumObatMasuk = round(array_sum($chartObatMasuk), 1);
        $sumObatKonsumsi = round(array_sum($chartObatKonsumsi), 1);

        $streamTotals = [
            'telur_masuk' => $telurMasuk,
            'telur_masuk_kg' => $telurMasukKg,
            'telur_rusak_peti' => $telurRusakPeti,
            'telur_rusak_kg' => $telurRusakKg,
            'telur_rusak_butir' => $telurRusakButir,
            'telur_terjual' => $telurPetiSold,
            'telur_terjual_kg' => $telurKgSold,
            'pakan_masuk' => $pakanMasuk,
            'pakan_konsumsi' => $pakanConsumptionKg,
            'pakan_terjual' => (int) $pakanKarungSold,
            'pakan_terjual_kg' => (float) $pakanKgSold,
            'pakan_terjual_total_kg' => round(($pakanKarungSold * 50) + $pakanKgSold, 1),
            'obat_masuk' => $obatMasuk,
            'obat_konsumsi' => $obatKeluar,
        ];

        $dateParams = array_filter(['start_date' => $startDate, 'end_date' => $endDate]);

        $chartDataSets = [
            'overview' => [
                'title' => 'TREN SEMUA ALIRAN BARANG GUDANG (8 DATA STREAM)',
                'is_overview' => true,
                'datasets' => [
                    [
                        'label' => 'Telur Masuk (Peti)',
                        'tab_url' => route('warehouse.telur', array_merge(['tab' => 'masuk'], $dateParams)),
                        'data' => $chartEggMasuk,
                        'borderColor' => '#059669',
                        'backgroundColor' => 'rgba(5, 150, 105, 0.05)',
                        'yAxisID' => 'y',
                        'unit' => 'Peti',
                    ],
                    [
                        'label' => 'Telur Rusak (Peti)',
                        'tab_url' => route('warehouse.telur', array_merge(['tab' => 'keluar'], $dateParams)),
                        'data' => $chartEggRusak,
                        'borderColor' => '#e11d48',
                        'backgroundColor' => 'rgba(225, 29, 72, 0.05)',
                        'borderDash' => [4, 4],
                        'yAxisID' => 'y',
                        'unit' => 'Peti',
                    ],
                    [
                        'label' => 'Telur Terjual (Peti)',
                        'tab_url' => route('warehouse.telur', array_merge(['tab' => 'penjualan'], $dateParams)),
                        'data' => $chartEggTerjual,
                        'borderColor' => '#d97706',
                        'backgroundColor' => 'rgba(217, 119, 6, 0.05)',
                        'yAxisID' => 'y',
                        'unit' => 'Peti',
                    ],
                    [
                        'label' => 'Pakan Masuk (Kg)',
                        'tab_url' => route('warehouse.pakan', array_merge(['tab' => 'masuk'], $dateParams)),
                        'data' => $chartFeedMasuk,
                        'borderColor' => '#0284c7',
                        'backgroundColor' => 'rgba(2, 132, 199, 0.05)',
                        'yAxisID' => 'y1',
                        'unit' => 'Kg',
                    ],
                    [
                        'label' => 'Pemberian Pakan (Kg)',
                        'tab_url' => route('warehouse.pakan', array_merge(['tab' => 'keluar'], $dateParams)),
                        'data' => $chartFeedKonsumsi,
                        'borderColor' => '#7c3aed',
                        'backgroundColor' => 'rgba(124, 58, 237, 0.05)',
                        'yAxisID' => 'y1',
                        'unit' => 'Kg',
                    ],
                    [
                        'label' => 'Pakan Terjual (Kg)',
                        'tab_url' => route('warehouse.pakan', array_merge(['tab' => 'penjualan'], $dateParams)),
                        'data' => $chartFeedTerjual,
                        'borderColor' => '#ea580c',
                        'backgroundColor' => 'rgba(234, 88, 12, 0.05)',
                        'yAxisID' => 'y1',
                        'unit' => 'Kg',
                    ],
                    [
                        'label' => 'Obat Masuk (Item)',
                        'tab_url' => route('warehouse.obat', array_merge(['tab' => 'masuk'], $dateParams)),
                        'data' => $chartObatMasuk,
                        'borderColor' => '#0d9488',
                        'backgroundColor' => 'rgba(13, 148, 136, 0.05)',
                        'yAxisID' => 'y',
                        'unit' => 'Item',
                    ],
                    [
                        'label' => 'Pemakaian Obat (Dosis)',
                        'tab_url' => route('warehouse.obat', array_merge(['tab' => 'keluar'], $dateParams)),
                        'data' => $chartObatKonsumsi,
                        'borderColor' => '#db2777',
                        'backgroundColor' => 'rgba(219, 39, 119, 0.05)',
                        'yAxisID' => 'y',
                        'unit' => 'Dosis',
                    ],
                ]
            ],
            'telur' => [
                'title' => 'TREN ALIRAN GUDANG TELUR (PETI)',
                'is_overview' => false,
                'unit' => 'Peti',
                'datasets' => [
                    [
                        'label' => 'Telur Masuk / Produksi (Peti)',
                        'tab_url' => route('warehouse.telur', array_merge(['tab' => 'masuk'], $dateParams)),
                        'data' => $chartEggMasuk,
                        'borderColor' => '#059669',
                        'backgroundColor' => 'rgba(5, 150, 105, 0.08)',
                        'yAxisID' => 'y',
                        'unit' => 'Peti',
                    ],
                    [
                        'label' => 'Telur Rusak / Pecah (Peti)',
                        'tab_url' => route('warehouse.telur', array_merge(['tab' => 'keluar'], $dateParams)),
                        'data' => $chartEggRusak,
                        'borderColor' => '#e11d48',
                        'backgroundColor' => 'rgba(225, 29, 72, 0.08)',
                        'borderDash' => [4, 4],
                        'yAxisID' => 'y',
                        'unit' => 'Peti',
                    ],
                    [
                        'label' => 'Telur Terjual (Peti)',
                        'tab_url' => route('warehouse.telur', array_merge(['tab' => 'penjualan'], $dateParams)),
                        'data' => $chartEggTerjual,
                        'borderColor' => '#d97706',
                        'backgroundColor' => 'rgba(217, 119, 6, 0.08)',
                        'yAxisID' => 'y',
                        'unit' => 'Peti',
                    ],
                ]
            ],
            'pakan' => [
                'title' => 'TREN ALIRAN GUDANG PAKAN (KG)',
                'is_overview' => false,
                'unit' => 'Kg',
                'datasets' => [
                    [
                        'label' => 'Pakan Masuk / Beli (Kg)',
                        'tab_url' => route('warehouse.pakan', array_merge(['tab' => 'masuk'], $dateParams)),
                        'data' => $chartFeedMasuk,
                        'borderColor' => '#0284c7',
                        'backgroundColor' => 'rgba(2, 132, 199, 0.08)',
                        'yAxisID' => 'y',
                        'unit' => 'Kg',
                    ],
                    [
                        'label' => 'Pemberian Pakan Kandang (Kg)',
                        'tab_url' => route('warehouse.pakan', array_merge(['tab' => 'keluar'], $dateParams)),
                        'data' => $chartFeedKonsumsi,
                        'borderColor' => '#7c3aed',
                        'backgroundColor' => 'rgba(124, 58, 237, 0.08)',
                        'yAxisID' => 'y',
                        'unit' => 'Kg',
                    ],
                    [
                        'label' => 'Pakan Terjual (Kg)',
                        'tab_url' => route('warehouse.pakan', array_merge(['tab' => 'penjualan'], $dateParams)),
                        'data' => $chartFeedTerjual,
                        'borderColor' => '#ea580c',
                        'backgroundColor' => 'rgba(234, 88, 12, 0.08)',
                        'yAxisID' => 'y',
                        'unit' => 'Kg',
                    ],
                ]
            ],
            'obat' => [
                'title' => 'TREN ALIRAN OBAT & VAKSIN (ITEM / DOSIS)',
                'is_overview' => false,
                'unit' => 'Item / Dosis',
                'datasets' => [
                    [
                        'label' => 'Obat Masuk (Item)',
                        'tab_url' => route('warehouse.obat', array_merge(['tab' => 'masuk'], $dateParams)),
                        'data' => $chartObatMasuk,
                        'borderColor' => '#0d9488',
                        'backgroundColor' => 'rgba(13, 148, 136, 0.08)',
                        'yAxisID' => 'y',
                        'unit' => 'Item',
                    ],
                    [
                        'label' => 'Pemakaian Obat Kandang (Dosis)',
                        'tab_url' => route('warehouse.obat', array_merge(['tab' => 'keluar'], $dateParams)),
                        'data' => $chartObatKonsumsi,
                        'borderColor' => '#db2777',
                        'backgroundColor' => 'rgba(219, 39, 119, 0.08)',
                        'yAxisID' => 'y',
                        'unit' => 'Dosis',
                    ],
                ]
            ],
        ];

        $displayTelurMasuk = $telurMasuk;
        $displayTelurRusakPeti = $telurRusakPeti;
        $displayTelurRusakButir = $telurRusakButir;
        $displayTelurTerjual = $telurPetiSold;

        $displayPakanMasuk = $pakanMasuk;
        $displayPakanKonsumsi = $pakanConsumptionKg;
        $displayPakanTerjual = (int) $pakanKarungSold;
        $displayPakanTerjualKg = (float) $pakanKgSold;
        $displayPakanTerjualTotalKg = round(($pakanKarungSold * 50) + $pakanKgSold, 1);
        $displayPakanTerjualFormatted = number_format((int) $pakanKarungSold, 0, ',', '.') . ' Karung' . ($pakanKgSold > 0 ? ' & ' . number_format($pakanKgSold, 1, ',', '.') . ' Kg' : '');

        $displayObatMasuk = $obatMasuk;
        $displayObatKonsumsi = $obatKeluar;

        $chartTotals = [
            'overview' => [
                'masuk' => [
                    ['label' => 'Telur', 'val' => number_format((int) $telurMasuk, 0, ',', '.') . ' Peti' . ($telurMasukKg > 0 ? ' & ' . number_format($telurMasukKg, 1, ',', '.') . ' Kg' : ''), 'url' => route('warehouse.telur', array_merge(['tab' => 'masuk'], $dateParams))],
                    ['label' => 'Pakan', 'val' => number_format($displayPakanMasuk, 1, ',', '.') . ' Kg', 'url' => route('warehouse.pakan', array_merge(['tab' => 'masuk'], $dateParams))],
                    ['label' => 'Obat', 'val' => number_format($displayObatMasuk, 1, ',', '.') . ' Item', 'url' => route('warehouse.obat', array_merge(['tab' => 'masuk'], $dateParams))],
                ],
                'digunakan' => [
                    ['label' => 'Pakan', 'val' => number_format($displayPakanKonsumsi, 1, ',', '.') . ' Kg', 'url' => route('warehouse.pakan', array_merge(['tab' => 'keluar'], $dateParams))],
                    ['label' => 'Telur Rusak', 'val' => number_format((int) $telurRusakPeti, 0, ',', '.') . ' Peti' . ($telurRusakKg > 0 ? ' & ' . number_format($telurRusakKg, 1, ',', '.') . ' Kg' : '') . ' (' . number_format($telurRusakButir, 0, ',', '.') . ' Btr)', 'url' => route('warehouse.telur', array_merge(['tab' => 'keluar'], $dateParams))],
                    ['label' => 'Obat', 'val' => number_format($displayObatKonsumsi, 1, ',', '.') . ' Dosis', 'url' => route('warehouse.obat', array_merge(['tab' => 'keluar'], $dateParams))],
                ],
                'keluar' => [
                    ['label' => 'Telur Total', 'val' => number_format((int) $telurKeluar, 0, ',', '.') . ' Peti' . ($telurKeluarKg > 0 ? ' & ' . number_format($telurKeluarKg, 1, ',', '.') . ' Kg' : ''), 'url' => route('warehouse.telur', array_merge(['tab' => 'semua'], $dateParams))],
                    ['label' => 'Pakan Total', 'val' => number_format($displayPakanKonsumsi + $displayPakanTerjualTotalKg, 1, ',', '.') . ' Kg', 'url' => route('warehouse.pakan', array_merge(['tab' => 'semua'], $dateParams))],
                    ['label' => 'Obat Pakai', 'val' => number_format($displayObatKonsumsi, 1, ',', '.') . ' Dosis', 'url' => route('warehouse.obat', array_merge(['tab' => 'keluar'], $dateParams))],
                ],
                'terjual' => [
                    ['label' => 'Telur Terjual', 'val' => number_format((int) $telurPetiSold, 0, ',', '.') . ' Peti' . ($telurKgSold > 0 ? ' & ' . number_format($telurKgSold, 1, ',', '.') . ' Kg' : ''), 'url' => route('warehouse.telur', array_merge(['tab' => 'penjualan'], $dateParams))],
                    ['label' => 'Pakan Terjual', 'val' => $displayPakanTerjualFormatted, 'url' => route('warehouse.pakan', array_merge(['tab' => 'penjualan'], $dateParams))],
                ],
            ],
            'telur' => [
                'masuk' => number_format((int) $telurMasuk, 0, ',', '.') . ' Peti' . ($telurMasukKg > 0 ? ' & ' . number_format($telurMasukKg, 1, ',', '.') . ' Kg' : ''),
                'digunakan' => number_format((int) $telurRusakPeti, 0, ',', '.') . ' Peti' . ($telurRusakKg > 0 ? ' & ' . number_format($telurRusakKg, 1, ',', '.') . ' Kg' : '') . ' (' . number_format($telurRusakButir, 0, ',', '.') . ' Btr)',
                'keluar' => number_format((int) $telurKeluar, 0, ',', '.') . ' Peti' . ($telurKeluarKg > 0 ? ' & ' . number_format($telurKeluarKg, 1, ',', '.') . ' Kg' : ''),
                'terjual' => number_format((int) $telurPetiSold, 0, ',', '.') . ' Peti' . ($telurKgSold > 0 ? ' & ' . number_format($telurKgSold, 1, ',', '.') . ' Kg' : ''),
            ],
            'pakan' => [
                'masuk' => number_format($displayPakanMasuk, 1, ',', '.') . ' Kg',
                'digunakan' => number_format($displayPakanKonsumsi, 1, ',', '.') . ' Kg',
                'keluar' => number_format($displayPakanKonsumsi + $displayPakanTerjualTotalKg, 1, ',', '.') . ' Kg',
                'terjual' => $displayPakanTerjualFormatted,
            ],
            'obat' => [
                'masuk' => number_format($displayObatMasuk, 1, ',', '.') . ' Item',
                'digunakan' => number_format($displayObatKonsumsi, 1, ',', '.') . ' Dosis',
                'keluar' => number_format($displayObatKonsumsi, 1, ',', '.') . ' Dosis',
                'terjual' => '0 Item',
            ],
        ];

        // Mutasi stok internal terbaru gabungan (terfilter rentang tanggal)
        $recentTransactions = $this->getUnifiedRecentTransactions(10, $startDate, $endDate);

        // Transaksi penjualan terbaru dari nochifram (terfilter rentang tanggal)
        $recentSales = OutboundIntegrationService::getSalesTransactions(null, $startDate, $endDate, 10);

        return view('warehouse.index', compact(
            'user',
            'telurMasuk', 'telurMasukButir', 'telurMasukKg', 'telurKeluar', 'telurKeluarKg', 'telurKeluarEggs', 'telurStok', 'telurStokKgTotal', 'telurStokButir', 'telurPetiSold', 'telurKgSold', 'telurRusakPeti', 'telurRusakKg', 'telurRusakButir', 'telurRevenue',
            'pakanMasuk', 'pakanMasukKarung', 'pakanKeluar', 'pakanTotalKarungKeluar', 'pakanStok', 'pakanStokKarung', 'pakanKarungSold', 'pakanKgSold', 'pakanConsumptionKg', 'pakanConsumptionKarung', 'pakanRevenue', 'feedSummary',
            'obatMasuk', 'obatKeluar', 'obatStok',
            'karantinaMasuk', 'karantinaKeluar', 'karantinaStok',
            'recentTransactions', 'recentSales',
            'chartLabels', 'chartDataSets', 'chartTotals', 'streamTotals',
            'startDate', 'endDate', 'defaultStartDate', 'defaultEndDate', 'diffDays', 'formattedStartDate', 'formattedEndDate', 'hasDateFilter'
        ));
    }

    /**
     * Helper mutasi aktivitas terkini gabungan (mendukung filter rentang tanggal)
     */
    private function getUnifiedRecentTransactions($limit = 10, $startDate = null, $endDate = null)
    {
        $transactions = collect();

        // 1. Dari FarmStock
        $fsQ = FarmStock::with('user')->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        if ($startDate && $endDate) {
            $fsQ->whereBetween('date', [$startDate, $endDate]);
        }
        $farmStocks = $fsQ->take($limit)->get();
        foreach ($farmStocks as $fs) {
            $transactions->push((object) [
                'id' => $fs->id,
                'category' => $fs->category,
                'type' => $fs->type,
                'item_name' => $fs->item_name,
                'quantity' => (float) $fs->quantity,
                'unit' => $fs->unit,
                'date' => Carbon::parse($fs->date),
                'created_at' => $fs->created_at ? Carbon::parse($fs->created_at) : Carbon::parse($fs->date),
                'source' => $fs->source ?: 'Gudang',
                'notes' => $fs->notes,
                'user' => $fs->user,
            ]);
        }

        // 2. Dari EggProduction
        $epQ = EggProduction::with(['coop', 'user'])->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        if ($startDate && $endDate) {
            $epQ->whereBetween('date', [$startDate, $endDate]);
        }
        $eggProds = $epQ->take($limit)->get();
        foreach ($eggProds as $ep) {
            $transactions->push((object) [
                'id' => 'ep_' . $ep->id,
                'category' => 'telur',
                'type' => 'masuk',
                'item_name' => 'Produksi Telur: ' . ($ep->coop ? $ep->coop->name : 'Kandang'),
                'quantity' => (float) $ep->crates_count,
                'unit' => 'Peti',
                'date' => Carbon::parse($ep->date),
                'created_at' => $ep->created_at ? Carbon::parse($ep->created_at) : Carbon::parse($ep->date),
                'source' => $ep->coop ? $ep->coop->name : 'Kandang',
                'notes' => 'Panen ' . number_format($ep->total_eggs, 0, ',', '.') . ' Butir' . ($ep->broken_eggs > 0 ? ' (Rusak: ' . $ep->broken_eggs . ' Btr)' : '') . ($ep->notes ? ' • ' . $ep->notes : ''),
                'user' => $ep->user,
            ]);
        }

        // 3. Dari FeedConsumption
        $fcQ = FeedConsumption::with(['coop', 'user'])->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        if ($startDate && $endDate) {
            $fcQ->whereBetween('date', [$startDate, $endDate]);
        }
        $feedCons = $fcQ->take($limit)->get();
        foreach ($feedCons as $fc) {
            $transactions->push((object) [
                'id' => 'fc_' . $fc->id,
                'category' => 'pakan',
                'type' => 'keluar',
                'item_name' => 'Pemberian Pakan: ' . $fc->feed_name,
                'quantity' => (float) $fc->quantity_kg,
                'unit' => 'Kg',
                'date' => Carbon::parse($fc->date),
                'created_at' => $fc->created_at ? Carbon::parse($fc->created_at) : Carbon::parse($fc->date),
                'source' => $fc->coop ? $fc->coop->name : 'Kandang',
                'notes' => 'Pemberian ' . ($fc->feeding_time ?? 'harian') . ($fc->notes ? ' • ' . $fc->notes : ''),
                'user' => $fc->user,
            ]);
        }

        // 4. Dari HealthTreatment
        $htQ = HealthTreatment::with(['coop', 'user'])->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        if ($startDate && $endDate) {
            $htQ->whereBetween('date', [$startDate, $endDate]);
        }
        $healths = $htQ->take($limit)->get();
        foreach ($healths as $ht) {
            $v = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
            $transactions->push((object) [
                'id' => 'ht_' . $ht->id,
                'category' => 'obat',
                'type' => 'keluar',
                'item_name' => ($ht->type ? ucfirst($ht->type) . ': ' : 'Obat: ') . $ht->medicine_name,
                'quantity' => ($v > 0 ? $v : 1),
                'unit' => 'Dosis',
                'date' => Carbon::parse($ht->date),
                'created_at' => $ht->created_at ? Carbon::parse($ht->created_at) : Carbon::parse($ht->date),
                'source' => $ht->coop ? $ht->coop->name : 'Kandang',
                'notes' => $ht->notes ?: 'Aplikasi di kandang',
                'user' => $ht->user,
            ]);
        }

        return $transactions->sortByDesc(fn($t) => $t->date->toDateString() . ' ' . $t->created_at->format('H:i:s'))->take($limit)->values();
    }

    /**
     * 2. Sub-halaman Gudang Telur
     */
    public function telur(Request $request)
    {
        $user = Auth::user() ?? User::first();
        $tab = $request->query('tab', 'semua');
        if ($tab === 'rusak') $tab = 'keluar';
        $search = $request->query('q');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $collection = collect();

        // 1. Data dari FarmStock (khusus kategori telur)
        $fsQuery = FarmStock::with('user')->where('category', 'telur');
        if ($startDate && $endDate) {
            $fsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        if ($tab === 'masuk') {
            $fsQuery->where('type', 'masuk');
        } elseif ($tab === 'keluar') {
            $fsQuery->where('type', 'keluar');
        }
        foreach ($fsQuery->get() as $fs) {
            $collection->push((object) [
                'id' => $fs->id,
                'raw_id' => $fs->id,
                'source_type' => 'farm_stock',
                'item_name' => $fs->item_name,
                'type' => $fs->type,
                'quantity' => (float) $fs->quantity,
                'unit' => $fs->unit,
                'date' => Carbon::parse($fs->date),
                'created_at' => $fs->created_at ? Carbon::parse($fs->created_at) : Carbon::parse($fs->date),
                'source' => $fs->source ?: 'Gudang',
                'notes' => $fs->notes,
                'user' => $fs->user,
                'is_nonaktif' => str_starts_with(trim($fs->notes ?? ''), '[NONAKTIF]'),
            ]);
        }

        // 2. Data dari EggProduction (produksi kandang & telur rusak)
        $epQuery = EggProduction::with(['coop', 'flock', 'user']);
        if ($startDate && $endDate) {
            $epQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $eggProductions = $epQuery->get();
        foreach ($eggProductions as $ep) {
            // A. Telur Masuk (Produksi utuh/peti)
            if ($tab === 'semua' || $tab === 'masuk') {
                $brokenInfo = $ep->broken_eggs > 0 ? (' • Telur Rusak: ' . number_format($ep->broken_eggs, 0, ',', '.') . ' Butir') : '';
                $collection->push((object) [
                    'id' => 'ep_' . $ep->id,
                    'raw_id' => $ep->id,
                    'source_type' => 'egg_production',
                    'item_name' => 'Produksi Telur - ' . ($ep->coop ? $ep->coop->name : 'Kandang'),
                    'type' => 'masuk',
                    'quantity' => (float) $ep->crates_count,
                    'unit' => 'Peti',
                    'date' => Carbon::parse($ep->date),
                    'created_at' => $ep->created_at ? Carbon::parse($ep->created_at) : Carbon::parse($ep->date),
                    'source' => $ep->coop ? ($ep->coop->name . ($ep->flock ? ' (' . $ep->flock->name . ')' : '')) : 'Kandang',
                    'notes' => 'Panen Telur Utuh: ' . number_format($ep->good_eggs ?? $ep->total_eggs, 0, ',', '.') . ' Butir' . ($ep->weight_kg > 0 ? ' (' . number_format($ep->weight_kg, 1, ',', '.') . ' Kg)' : '') . $brokenInfo . ($ep->notes ? ' • ' . $ep->notes : ''),
                    'user' => $ep->user,
                    'is_nonaktif' => str_starts_with(trim($ep->notes ?? ''), '[NONAKTIF]'),
                    'good_eggs' => $ep->good_eggs,
                    'broken_eggs' => $ep->broken_eggs,
                    'abnormal_eggs' => $ep->abnormal_eggs ?? 0,
                    'crates_count' => (float) $ep->crates_count,
                    'weight_kg' => $ep->weight_kg,
                    'coop_id' => $ep->coop_id,
                    'flock_id' => $ep->flock_id,
                ]);
            }

            // B. Telur Rusak / Pecah (Hanya untuk tab khusus 'keluar' / telur rusak)
            if ($tab === 'keluar' && $ep->broken_eggs > 0) {
                $collection->push((object) [
                    'id' => 'ep_broken_' . $ep->id,
                    'raw_id' => $ep->id,
                    'source_type' => 'egg_production',
                    'item_name' => 'Telur Rusak / Pecah - ' . ($ep->coop ? $ep->coop->name : 'Kandang'),
                    'type' => 'keluar',
                    'quantity' => (float) $ep->broken_eggs,
                    'unit' => 'Butir',
                    'date' => Carbon::parse($ep->date),
                    'created_at' => $ep->created_at ? Carbon::parse($ep->created_at) : Carbon::parse($ep->date),
                    'source' => $ep->coop ? ($ep->coop->name . ($ep->flock ? ' (' . $ep->flock->name . ')' : '')) : 'Kandang',
                    'notes' => 'Telur retak/pecah saat pengumpulan di kandang' . ($ep->notes ? ' • ' . $ep->notes : ''),
                    'user' => $ep->user,
                    'is_nonaktif' => str_starts_with(trim($ep->notes ?? ''), '[NONAKTIF]'),
                    'good_eggs' => $ep->good_eggs,
                    'broken_eggs' => $ep->broken_eggs,
                    'abnormal_eggs' => $ep->abnormal_eggs ?? 0,
                    'crates_count' => (float) $ep->crates_count,
                    'weight_kg' => $ep->weight_kg,
                    'coop_id' => $ep->coop_id,
                    'flock_id' => $ep->flock_id,
                ]);
            }
        }

        // Filter pencarian
        if (!empty($search)) {
            $s = strtolower($search);
            $collection = $collection->filter(function ($item) use ($s) {
                return str_contains(strtolower($item->item_name), $s) ||
                       str_contains(strtolower($item->source ?? ''), $s) ||
                       str_contains(strtolower($item->notes ?? ''), $s) ||
                       str_contains(strtolower($item->user ? ($item->user->username ?: $item->user->name) : ''), $s);
            });
        }

        // Urutkan tanggal desc, created_at desc
        $sorted = $collection->sortByDesc(function ($item) {
            return $item->date->toDateString() . ' ' . ($item->created_at ? $item->created_at->format('H:i:s') : '00:00:00');
        })->values();

        // Paginasi
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $items = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Ringkasan Telur Terintegrasi Penjualan nochifram
        $eggSummary = OutboundIntegrationService::getEggOutboundSummary($startDate, $endDate);
        $totalMasuk = $eggSummary['total_produced_crates'];
        $totalMasukKg = $eggSummary['total_produced_kg'];
        $totalKeluar = $eggSummary['total_keluar_peti'];
        $totalKeluarKg = $eggSummary['total_keluar_kg'];
        $stokSaatIni = $eggSummary['current_stock_peti'];
        $stokSaatIniKg = $eggSummary['current_stock_kg_total'];
        $stokSaatIniButir = $eggSummary['current_stock_eggs'];
        $petiSold = $eggSummary['peti_sold'];
        $kgSold = $eggSummary['kg_sold'];
        $totalRevenue = $eggSummary['total_revenue'];
        $transactionCount = $eggSummary['transaction_count'];
        $totalEggsCount = $eggSummary['total_produced_eggs'];
        $totalBrokenEggs = $eggSummary['total_broken_eggs'] ?? 0;
        $totalBrokenPeti = $eggSummary['total_broken_peti'] ?? 0;
        $totalBrokenKg = $eggSummary['total_broken_kg'] ?? 0;

        // Data Penjualan Telur dari aplikasi nochifram
        $salesList = OutboundIntegrationService::getSalesTransactions('telur', $startDate, $endDate, 50);
        $tripList = OutboundIntegrationService::getTripOutbounds('telur', 10);

        $flocks = \App\Models\Flock::where('is_active', true)->get();
        $coops = Coop::where('is_active', true)->get();

        return view('warehouse.telur', compact(
            'user', 'items', 'tab', 'search', 'startDate', 'endDate',
            'totalMasuk', 'totalMasukKg', 'totalKeluar', 'totalKeluarKg', 'totalBrokenEggs', 'totalBrokenPeti', 'totalBrokenKg', 'stokSaatIni', 'stokSaatIniKg', 'stokSaatIniButir',
            'petiSold', 'kgSold', 'totalRevenue', 'transactionCount', 'totalEggsCount',
            'salesList', 'tripList', 'coops', 'flocks'
        ));
    }

    /**
     * 3. Sub-halaman Gudang Pakan
     */
    public function pakan(Request $request)
    {
        $user = Auth::user() ?? User::first();
        $tab = $request->query('tab', 'semua');
        if ($tab === 'pemberian') $tab = 'keluar';
        $search = $request->query('q');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $collection = collect();

        // 1. Data Pembelian & Mutasi Pakan dari FarmStock
        $fsQuery = FarmStock::with('user')->whereRaw('LOWER(category) = ?', ['pakan']);
        if ($startDate && $endDate) {
            $fsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        if ($tab === 'masuk') {
            $fsQuery->whereRaw('LOWER(type) = ?', ['masuk']);
        } elseif ($tab === 'keluar') {
            $fsQuery->whereRaw('LOWER(type) = ?', ['keluar'])
                    ->where(function($q) {
                        $q->whereNull('notes')->orWhere('notes', 'not like', '[AUTO-KONSUMSI]%');
                    });
        } else {
            $fsQuery->where(function($q) {
                $q->whereNull('notes')->orWhere('notes', 'not like', '[AUTO-KONSUMSI]%');
            });
        }

        foreach ($fsQuery->get() as $fs) {
            $collection->push((object) [
                'id' => $fs->id,
                'raw_id' => $fs->id,
                'source_type' => 'farm_stock',
                'item_name' => $fs->item_name,
                'type' => $fs->type,
                'quantity' => (float) $fs->quantity,
                'unit' => $fs->unit,
                'date' => Carbon::parse($fs->date),
                'created_at' => $fs->created_at ? Carbon::parse($fs->created_at) : Carbon::parse($fs->date),
                'source' => $fs->source ?: 'Gudang',
                'notes' => $fs->notes,
                'user' => $fs->user,
                'is_nonaktif' => str_starts_with(trim($fs->notes ?? ''), '[NONAKTIF]'),
            ]);
        }

        // 2. Data Pemberian Pakan Harian dari FeedConsumption
        if ($tab === 'semua' || $tab === 'keluar') {
            $fcQuery = FeedConsumption::with(['coop', 'flock', 'user']);
            if ($startDate && $endDate) {
                $fcQuery->whereBetween('date', [$startDate, $endDate]);
            }
            $feedConsumptions = $fcQuery->get();

            // Kelompokkan per [Tanggal + Blok] agar sesi pagi & sore terintegrasi rapi per blok
            $groupedByDateCoop = $feedConsumptions->groupBy(function($fc) {
                return Carbon::parse($fc->date)->toDateString() . '_' . ($fc->coop_id ?? '0');
            });

            foreach ($groupedByDateCoop as $group) {
                $first = $group->first();
                $totalQty = (float) $group->sum('quantity_kg');
                $coop = $first->coop;
                $flock = $first->flock;

                $sessionsList = [];
                $subRecords = [];
                foreach ($group as $item) {
                    $timeStr = $item->feeding_time ?: 'Harian';
                    $sessionsList[] = $timeStr . ': ' . number_format($item->quantity_kg, 1, ',', '.') . ' Kg';
                    $subRecords[] = [
                        'id' => 'fc_' . $item->id,
                        'raw_id' => $item->id,
                        'feeding_time' => $item->feeding_time,
                        'quantity_kg' => (float) $item->quantity_kg,
                        'feed_name' => $item->feed_name,
                        'notes' => $item->notes,
                        'date' => $item->date,
                        'user' => $item->user,
                    ];
                }
                $sessionsStr = implode(' • ', $sessionsList);

                $collection->push((object) [
                    'id' => 'fc_' . $first->id,
                    'raw_id' => $first->id,
                    'source_type' => 'feed_consumption',
                    'item_name' => 'Pemberian Pakan - ' . ($coop ? $coop->name : 'Kandang'),
                    'type' => 'keluar',
                    'quantity' => $totalQty,
                    'unit' => 'Kg',
                    'date' => Carbon::parse($first->date),
                    'created_at' => $first->created_at ? Carbon::parse($first->created_at) : Carbon::parse($first->date),
                    'source' => $coop ? ($coop->name . ($flock ? ' (' . $flock->name . ')' : '')) : 'Kandang',
                    'notes' => 'Rincian: ' . $sessionsStr . ($first->notes ? ' • ' . $first->notes : ''),
                    'user' => $first->user,
                    'is_nonaktif' => str_starts_with(trim($first->notes ?? ''), '[NONAKTIF]'),
                    'coop_id' => $first->coop_id,
                    'coop_name' => $coop ? $coop->name : 'Tanpa Blok',
                    'flock_id' => $first->flock_id,
                    'flock_name' => $flock ? $flock->name : '',
                    'feed_name' => $first->feed_name,
                    'feeding_time' => $sessionsStr,
                    'quantity_kg' => $totalQty,
                    'sub_records' => $subRecords,
                    'has_multiple_sessions' => $group->count() > 1,
                ]);
            }
        }

        // Filter pencarian
        if (!empty($search)) {
            $s = strtolower($search);
            $collection = $collection->filter(function ($item) use ($s) {
                return str_contains(strtolower($item->item_name), $s) ||
                       str_contains(strtolower($item->source ?? ''), $s) ||
                       str_contains(strtolower($item->notes ?? ''), $s) ||
                       str_contains(strtolower($item->user ? ($item->user->username ?: $item->user->name) : ''), $s);
            });
        }

        // Urutkan tanggal desc, created_at desc
        $sorted = $collection->sortByDesc(function ($item) {
            return $item->date->toDateString() . ' ' . ($item->created_at ? $item->created_at->format('H:i:s') : '00:00:00');
        })->values();

        // Paginasi
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $items = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Ringkasan Pakan Terintegrasi Penjualan & Konsumsi Kandang
        $feedSummary = OutboundIntegrationService::getFeedOutboundSummary($startDate, $endDate);
        $totalMasuk = $feedSummary['purchased_kg'];
        $purchasedKarung = $feedSummary['purchased_karung'];
        $totalKeluar = $feedSummary['total_keluar_kg'];
        $stokSaatIni = $feedSummary['current_stock_kg'];
        $currentStockKarung = $feedSummary['current_stock_karung'];
        $karungSold = $feedSummary['karung_sold'];
        $kgSold = $feedSummary['kg_sold'];
        $soldRevenue = $feedSummary['total_revenue'];
        $consumptionKg = $feedSummary['consumption_kg'];
        $consumptionKarung = $feedSummary['consumption_karung'];

        // Data Penjualan Pakan dari aplikasi nochifram
        $salesList = OutboundIntegrationService::getSalesTransactions('pakan', $startDate, $endDate, 50);
        $tripList = OutboundIntegrationService::getTripOutbounds('pakan', 10);

        $flocks = \App\Models\Flock::where('is_active', true)->get();
        $coops = Coop::where('is_active', true)->get();

        // PENGELOMPOKAN DATA PER BLOK (Untuk Tampilan Semua Data & Keluar)
        $selectedBlock = $request->query('block', 'all');
        $groupedByBlock = [];

        // 1. Kelompokkan per Blok Kandang (Blok A, Blok B, Blok C, Blok D, Blok E, Blok F)
        foreach ($coops as $coop) {
            $coopItems = $collection->filter(function($it) use ($coop) {
                return (int) ($it->coop_id ?? 0) === (int) $coop->id;
            })->sortByDesc(function ($item) {
                return $item->date->toDateString() . ' ' . ($item->created_at ? $item->created_at->format('H:i:s') : '00:00:00');
            })->values();

            $totalKg = (float) $coopItems->sum('quantity');

            $groupedByBlock['coop_' . $coop->id] = (object) [
                'key' => 'coop_' . $coop->id,
                'type' => 'coop',
                'coop_id' => $coop->id,
                'name' => $coop->name,
                'code' => $coop->code ?: str_replace('Blok ', '', $coop->name),
                'flock_name' => $coop->flock ? $coop->flock->name : '',
                'flock_code' => $coop->flock ? $coop->flock->code : '',
                'active_chickens' => (int) $coop->active_chickens,
                'total_kg' => $totalKg,
                'record_count' => $coopItems->count(),
                'items' => $coopItems,
            ];
        }

        // 2. Pembelian & Mutasi Pakan Masuk (Gudang)
        $masukItems = $collection->filter(fn($it) => $it->type === 'masuk')->sortByDesc(function ($item) {
            return $item->date->toDateString() . ' ' . ($item->created_at ? $item->created_at->format('H:i:s') : '00:00:00');
        })->values();
        if ($masukItems->isNotEmpty() || $tab === 'masuk') {
            $groupedByBlock['masuk'] = (object) [
                'key' => 'masuk',
                'type' => 'masuk',
                'coop_id' => null,
                'name' => 'Pembelian & Pakan Masuk (Gudang)',
                'code' => 'Gudang',
                'flock_name' => 'Pakan Masuk',
                'flock_code' => 'IN',
                'active_chickens' => 0,
                'total_kg' => (float) $masukItems->sum('quantity'),
                'record_count' => $masukItems->count(),
                'items' => $masukItems,
            ];
        }

        // 3. Pengeluaran Umum Tanpa Blok (bila ada)
        $otherItems = $collection->filter(function($it) {
            return $it->type === 'keluar' && empty($it->coop_id);
        })->sortByDesc('date')->values();
        if ($otherItems->isNotEmpty()) {
            $groupedByBlock['other'] = (object) [
                'key' => 'other',
                'type' => 'other',
                'coop_id' => null,
                'name' => 'Pemakaian Umum / Non-Blok',
                'code' => 'Lainnya',
                'flock_name' => 'Kandang',
                'flock_code' => 'ETC',
                'active_chickens' => 0,
                'total_kg' => (float) $otherItems->sum('quantity'),
                'record_count' => $otherItems->count(),
                'items' => $otherItems,
            ];
        }

        // Hitung total data untuk badge tab
        $countMasuk = $collection->where('type', 'masuk')->count();
        $countKeluar = $collection->where('type', 'keluar')->count();
        $countSemua = $collection->count();

        return view('warehouse.pakan', compact(
            'user', 'items', 'tab', 'search', 'startDate', 'endDate',
            'totalMasuk', 'totalKeluar', 'stokSaatIni', 'currentStockKarung',
            'karungSold', 'kgSold', 'soldRevenue', 'consumptionKg', 'consumptionKarung', 'purchasedKarung', 'feedSummary',
            'salesList', 'tripList', 'coops', 'flocks', 'countMasuk', 'countKeluar', 'countSemua',
            'groupedByBlock', 'selectedBlock'
        ));
    }

    /**
     * 4. Sub-halaman Gudang Obat, Vaksin & Vitamin
     */
    public function obat(Request $request)
    {
        $user = Auth::user() ?? User::first();
        $tab = $request->query('tab', 'semua');
        $search = $request->query('q');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $collection = collect();

        // 1. Data Pembelian & Stok Obat/Vaksin/Vitamin dari FarmStock
        $fsQuery = FarmStock::with('user')->whereIn('category', ['obat', 'vaksin', 'vitamin']);
        if ($startDate && $endDate) {
            $fsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        if ($tab === 'masuk') {
            $fsQuery->where('type', 'masuk');
        } elseif ($tab === 'keluar') {
            $fsQuery->where('type', 'keluar');
        }

        foreach ($fsQuery->get() as $fs) {
            $collection->push((object) [
                'id' => $fs->id,
                'raw_id' => $fs->id,
                'source_type' => 'farm_stock',
                'category' => $fs->category,
                'item_name' => $fs->item_name,
                'type' => $fs->type,
                'quantity' => (float) $fs->quantity,
                'unit' => $fs->unit,
                'date' => Carbon::parse($fs->date),
                'created_at' => $fs->created_at ? Carbon::parse($fs->created_at) : Carbon::parse($fs->date),
                'source' => $fs->source ?: 'CV Medika Farma',
                'notes' => $fs->notes,
                'user' => $fs->user,
                'is_nonaktif' => str_starts_with(trim($fs->notes ?? ''), '[NONAKTIF]'),
            ]);
        }

        // 2. Data Pemakaian Obat/Vaksin/Vitamin dari HealthTreatment
        if ($tab === 'semua' || $tab === 'keluar') {
            $htQuery = HealthTreatment::with(['coop', 'flock', 'user']);
            if ($startDate && $endDate) {
                $htQuery->whereBetween('date', [$startDate, $endDate]);
            }
            $healthTreatments = $htQuery->get();
            foreach ($healthTreatments as $ht) {
                $val = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
                if ($val <= 0) $val = 1;

                $collection->push((object) [
                    'id' => 'ht_' . $ht->id,
                    'raw_id' => $ht->id,
                    'source_type' => 'health_treatment',
                    'category' => strtolower($ht->type ?: 'obat'),
                    'item_name' => ($ht->type ? ucfirst($ht->type) . ': ' : 'Obat: ') . $ht->medicine_name . ($ht->dosage ? ' (' . $ht->dosage . ')' : ''),
                    'type' => 'keluar',
                    'quantity' => $val,
                    'unit' => 'Dosis',
                    'date' => Carbon::parse($ht->date),
                    'created_at' => $ht->created_at ? Carbon::parse($ht->created_at) : Carbon::parse($ht->date),
                    'source' => $ht->coop ? ($ht->coop->name . ($ht->flock ? ' (' . $ht->flock->name . ')' : '')) : 'Kandang',
                    'notes' => ($ht->application_method ? 'Aplikasi: ' . $ht->application_method . '. ' : '') . ($ht->notes ?? 'Pemberian ke ayam kandang'),
                    'user' => $ht->user,
                    'is_nonaktif' => str_starts_with(trim($ht->notes ?? ''), '[NONAKTIF]'),
                    'coop_id' => $ht->coop_id,
                    'flock_id' => $ht->flock_id,
                    'medicine_name' => $ht->medicine_name,
                    'medicine_type' => $ht->type,
                    'dosage' => $ht->dosage,
                    'application_method' => $ht->application_method,
                ]);
            }
        }

        // Filter pencarian
        if (!empty($search)) {
            $s = strtolower($search);
            $collection = $collection->filter(function ($item) use ($s) {
                return str_contains(strtolower($item->item_name), $s) ||
                       str_contains(strtolower($item->source ?? ''), $s) ||
                       str_contains(strtolower($item->notes ?? ''), $s) ||
                       str_contains(strtolower($item->category ?? ''), $s) ||
                       str_contains(strtolower($item->user ? ($item->user->username ?: $item->user->name) : ''), $s);
            });
        }

        // Urutkan tanggal desc, created_at desc
        $sorted = $collection->sortByDesc(function ($item) {
            return $item->date->toDateString() . ' ' . ($item->created_at ? $item->created_at->format('H:i:s') : '00:00:00');
        })->values();

        // Paginasi
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $items = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Ringkasan Obat, Vaksin & Vitamin (Sesuai Periode Tanggal)
        $fsMasukQ = FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'masuk');
        $fsKeluarQ = FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'keluar');
        $htSummaryQ = HealthTreatment::query();
        if ($startDate && $endDate) {
            $fsMasukQ->whereBetween('date', [$startDate, $endDate]);
            $fsKeluarQ->whereBetween('date', [$startDate, $endDate]);
            $htSummaryQ->whereBetween('date', [$startDate, $endDate]);
        }
        $totalMasuk = (float) $fsMasukQ->sum('quantity');
        $totalKeluarManual = (float) $fsKeluarQ->sum('quantity');
        
        $obatKeluarKandang = 0;
        foreach ($htSummaryQ->get() as $ht) {
            $val = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
            if ($val == 0) $val = 1;
            $obatKeluarKandang += $val;
        }
        
        $totalKeluar = $totalKeluarManual + $obatKeluarKandang;
        $stokSaatIni = round($totalMasuk - $totalKeluar, 1);

        $flocks = \App\Models\Flock::where('is_active', true)->get();
        $coops = Coop::where('is_active', true)->get();

        return view('warehouse.obat', compact(
            'user', 'items', 'tab', 'search', 'startDate', 'endDate', 'totalMasuk', 'totalKeluar', 'stokSaatIni', 'coops', 'flocks'
        ));
    }

    /**
     * 5. Sub-halaman Gudang Ayam Karantina
     */
    public function karantina(Request $request)
    {
        $user = Auth::user() ?? User::first();
        $tab = $request->query('tab', 'semua');
        $search = $request->query('q');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        Quarantine::ensureTableExists();

        $collection = collect();

        // 1. Data Mortalitas Harian (Mati & Afkir) dari tabel mortalities
        if ($tab === 'semua' || $tab === 'mati' || $tab === 'afkir') {
            $mQuery = Mortality::with(['coop', 'flock', 'user']);
            if ($startDate && $endDate) {
                $mQuery->whereBetween('date', [$startDate, $endDate]);
            }
            if ($tab === 'mati') {
                $mQuery->where(function($q) {
                    $q->where('type', 'mati')->orWhereNull('type');
                });
            } elseif ($tab === 'afkir') {
                $mQuery->where('type', 'afkir');
            }

            foreach ($mQuery->get() as $m) {
                $isNonaktif = str_starts_with(trim($m->notes ?? ''), '[NONAKTIF]');
                $displayNotes = $isNonaktif ? trim(substr(trim($m->notes), strlen('[NONAKTIF]'))) : $m->notes;
                $coopName = $m->coop ? $m->coop->name : 'Kandang';
                $flockName = $m->flock ? $m->flock->name : ($m->coop && $m->coop->flock ? $m->coop->flock->name : null);
                $labelStatus = $m->type === 'afkir' ? 'Ayam Afkir (Culling)' : 'Ayam Mati Harian';

                $collection->push((object) [
                    'id' => 'mort_' . $m->id,
                    'raw_id' => $m->id,
                    'source_type' => 'mortality',
                    'item_name' => $labelStatus . ' - ' . $coopName,
                    'status' => $m->type ?: 'mati',
                    'type' => 'keluar',
                    'quantity' => (int) $m->count,
                    'unit' => 'Ekor',
                    'date' => Carbon::parse($m->date),
                    'created_at' => $m->created_at ? Carbon::parse($m->created_at) : Carbon::parse($m->date),
                    'time' => $m->time ? substr($m->time, 0, 5) : ($m->created_at ? $m->created_at->format('H:i') : '00:00'),
                    'source' => $coopName . ($flockName ? ' (' . $flockName . ')' : ''),
                    'battery_number' => null,
                    'cause' => $m->cause,
                    'action_taken' => null,
                    'notes' => $displayNotes,
                    'raw_notes' => $m->notes,
                    'user' => $m->user,
                    'is_nonaktif' => $isNonaktif,
                    'coop_id' => $m->coop_id,
                    'flock_id' => $m->flock_id,
                ]);
            }
        }

        // 2. Data Karantina (Sakit, Sembuh, Mati Isolasi) dari tabel quarantines
        $qQuery = Quarantine::with(['coop', 'flock', 'user']);
        if ($startDate && $endDate) {
            $qQuery->whereBetween('date', [$startDate, $endDate]);
        }
        if ($tab === 'sakit') {
            $qQuery->where('status', 'sakit');
        } elseif ($tab === 'sembuh') {
            $qQuery->where('status', 'sembuh');
        } elseif ($tab === 'mati') {
            $qQuery->where('status', 'mati');
        }

        foreach ($qQuery->get() as $q) {
            $isNonaktif = str_starts_with(trim($q->notes ?? ''), '[NONAKTIF]');
            $displayNotes = $isNonaktif ? trim(substr(trim($q->notes), strlen('[NONAKTIF]'))) : $q->notes;
            $coopName = $q->coop ? $q->coop->name : 'Kandang';
            $flockName = $q->flock ? $q->flock->name : ($q->coop && $q->coop->flock ? $q->coop->flock->name : null);

            $collection->push((object) [
                'id' => 'quar_' . $q->id,
                'raw_id' => $q->id,
                'source_type' => 'quarantine',
                'item_name' => ($q->status === 'sakit' ? 'Ayam Sakit Masuk' : ($q->status === 'sembuh' ? 'Ayam Sembuh Keluar' : 'Ayam Mati Karantina')) . ' - ' . $coopName,
                'status' => $q->status,
                'type' => $q->status === 'sakit' ? 'masuk' : 'keluar',
                'quantity' => (int) $q->count,
                'unit' => 'Ekor',
                'date' => Carbon::parse($q->date),
                'created_at' => $q->created_at ? Carbon::parse($q->created_at) : Carbon::parse($q->date),
                'time' => $q->time ? substr($q->time, 0, 5) : ($q->created_at ? $q->created_at->format('H:i') : '00:00'),
                'source' => $coopName . ($flockName ? ' (' . $flockName . ')' : ''),
                'battery_number' => $q->battery_number,
                'cause' => $q->cause,
                'action_taken' => $q->action_taken,
                'notes' => $displayNotes,
                'raw_notes' => $q->notes,
                'user' => $q->user,
                'is_nonaktif' => $isNonaktif,
                'coop_id' => $q->coop_id,
                'flock_id' => $q->flock_id,
            ]);
        }

        // Filter pencarian
        if (!empty($search)) {
            $s = strtolower($search);
            $collection = $collection->filter(function ($item) use ($s) {
                return str_contains(strtolower($item->item_name), $s) ||
                       str_contains(strtolower($item->source ?? ''), $s) ||
                       str_contains(strtolower($item->battery_number ?? ''), $s) ||
                       str_contains(strtolower($item->cause ?? ''), $s) ||
                       str_contains(strtolower($item->action_taken ?? ''), $s) ||
                       str_contains(strtolower($item->notes ?? ''), $s) ||
                       str_contains(strtolower($item->user ? ($item->user->username ?: $item->user->name) : ''), $s);
            });
        }

        // Urutkan tanggal desc, created_at desc
        $sorted = $collection->sortByDesc(function ($item) {
            return $item->date->toDateString() . ' ' . ($item->created_at ? $item->created_at->format('H:i:s') : '00:00:00');
        })->values();

        // Paginasi
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 15;
        $items = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // Ringkasan Statistik
        $stokSaatIni = Quarantine::getCurrentCount();

        $mStatQ = Mortality::query();
        $qStatQ = Quarantine::query();
        if ($startDate && $endDate) {
            $mStatQ->whereBetween('date', [$startDate, $endDate]);
            $qStatQ->whereBetween('date', [$startDate, $endDate]);
        }
        $mActiveQ = clone $mStatQ;
        $mActiveQ->where(function($q) {
            $q->whereNull('notes')->orWhere('notes', 'not like', '[NONAKTIF]%');
        });
        $qActiveQ = clone $qStatQ;
        $qActiveQ->where(function($q) {
            $q->whereNull('notes')->orWhere('notes', 'not like', '[NONAKTIF]%');
        });

        $totalMatiPure = (int) (clone $mActiveQ)->where(function($q) { $q->where('type', 'mati')->orWhereNull('type'); })->sum('count') + (int) (clone $qActiveQ)->where('status', 'mati')->sum('count');
        $totalAfkir = (int) (clone $mActiveQ)->where('type', 'afkir')->sum('count');
        $totalMati = $totalMatiPure + $totalAfkir; // Total Mortalitas (Mati + Afkir)
        $totalSakit = (int) (clone $qActiveQ)->where('status', 'sakit')->sum('count');
        $totalSembuh = (int) (clone $qActiveQ)->where('status', 'sembuh')->sum('count');
        $transactionCount = $sorted->count();

        $flocks = Flock::where('is_active', true)->get();
        $coops = Coop::where('is_active', true)->get();

        return view('warehouse.karantina', compact(
            'user', 'items', 'tab', 'search', 'startDate', 'endDate',
            'stokSaatIni', 'totalSakit', 'totalSembuh', 'totalMati', 'totalMatiPure', 'totalAfkir', 'transactionCount',
            'coops', 'flocks'
        ));
    }

    /**
     * Store transaksi stok baru
     */
    public function store(Request $request)
    {
        if ($request->input('category') === 'karantina') {
            $validated = $request->validate([
                'coop_id' => 'required|exists:coops,id',
                'status' => 'required|in:sakit,sembuh,mati',
                'count' => 'required|integer|min:1',
                'date' => 'required|date',
                'time' => 'nullable|string',
                'battery_number' => 'nullable|string|max:100',
                'cause' => 'nullable|string|max:255',
                'action_taken' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            Quarantine::ensureTableExists();

            $coop = Coop::findOrFail($validated['coop_id']);
            $count = (int) $validated['count'];
            $status = $validated['status'];
            $userId = Auth::id() ?? User::where('role', 'user')->orWhere('username', 'petugas')->value('id') ?? User::value('id');

            // Sinkronkan active_chickens di kandang
            if ($status === 'sakit') {
                if ($coop->active_chickens >= $count) {
                    $coop->decrement('active_chickens', $count);
                }
            } elseif ($status === 'sembuh') {
                $coop->increment('active_chickens', $count);
            }

            Quarantine::create([
                'flock_id' => $coop->flock_id,
                'coop_id' => $coop->id,
                'user_id' => $userId,
                'date' => $validated['date'],
                'time' => $validated['time'] ?? Carbon::now()->format('H:i:s'),
                'battery_number' => $validated['battery_number'] ?? null,
                'count' => $count,
                'status' => $status,
                'cause' => $validated['cause'] ?? null,
                'action_taken' => $validated['action_taken'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return back()->with('success', 'Data transaksi ayam karantina berhasil dicatat!');
        }

        $validated = $request->validate([
            'category' => 'required|in:telur,pakan,obat,vaksin,vitamin',
            'type' => 'required|in:masuk,keluar',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'date' => 'required|date',
            'time' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user() ?? User::where('role', 'user')->orWhere('username', 'petugas')->first() ?? User::first();
        $userId = $user ? $user->id : null;

        $createdAt = Carbon::parse($validated['date']);
        if (!empty($validated['time'])) {
            $timeParts = explode(':', $validated['time']);
            $createdAt->setTime((int) ($timeParts[0] ?? 0), (int) ($timeParts[1] ?? 0));
        } else {
            $createdAt->setTime(Carbon::now()->hour, Carbon::now()->minute);
        }

        FarmStock::create([
            'user_id' => $userId,
            'date' => $validated['date'],
            'category' => $validated['category'],
            'item_name' => $validated['item_name'],
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'source' => $validated['source'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        $namaJenis = ucfirst($validated['category']);
        $targetTab = $validated['type'] === 'masuk' ? 'masuk' : 'keluar';

        $redirectParams = ['tab' => $targetTab];

        // Jika ada filter tanggal yang aktif, pastikan transaksi baru tidak tersembunyi
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $transDate = $validated['date'];

            if ($transDate < $startDate) {
                $startDate = $transDate;
            }
            if ($transDate > $endDate) {
                $endDate = $transDate;
            }

            $redirectParams['start_date'] = $startDate;
            $redirectParams['end_date'] = $endDate;
        }

        if ($validated['category'] === 'pakan') {
            $labelType = $validated['type'] === 'masuk' ? 'Pakan Masuk (Beli)' : 'Pakan Keluar';
            return redirect()->route('warehouse.pakan', $redirectParams)
                ->with('success', "Transaksi {$labelType} berhasil disimpan!");
        } elseif ($validated['category'] === 'telur') {
            return redirect()->route('warehouse.telur', $redirectParams)
                ->with('success', "Transaksi Gudang Telur berhasil disimpan!");
        } elseif (in_array($validated['category'], ['obat', 'vaksin', 'vitamin'])) {
            return redirect()->route('warehouse.obat', $redirectParams)
                ->with('success', "Transaksi Gudang Obat & Vaksin berhasil disimpan!");
        }

        return back()->with('success', "Transaksi Gudang {$namaJenis} berhasil disimpan!");
    }

    /**
     * Update transaksi stok
     */
    public function update(Request $request, $id)
    {
        if (str_starts_with($id, 'ep_')) {
            $realId = substr($id, 3);
            $isBroken = str_starts_with($realId, 'broken_');
            if ($isBroken) $realId = substr($realId, 7);
            $ep = EggProduction::findOrFail($realId);

            if ($request->has('coop_id')) {
                $coop = Coop::find($request->coop_id);
                if ($coop) {
                    $ep->coop_id = $coop->id;
                    $ep->flock_id = $coop->flock_id;
                }
            }

            if ($request->has('good_eggs')) $ep->good_eggs = (int) $request->good_eggs;
            if ($request->has('broken_eggs')) $ep->broken_eggs = (int) $request->broken_eggs;
            
            if (\Illuminate\Support\Facades\Schema::hasColumn('egg_productions', 'abnormal_eggs') && $request->has('abnormal_eggs')) {
                $ep->abnormal_eggs = (int) $request->abnormal_eggs;
            }

            if ($request->has('quantity')) {
                if ($isBroken) {
                    $ep->broken_eggs = (int) $request->quantity;
                } else {
                    $ep->crates_count = (float) $request->quantity;
                }
            }

            if ($request->has('crates_count')) {
                $ep->crates_count = (float) $request->crates_count;
            }

            if ($request->has('weight_kg')) {
                $ep->weight_kg = (float) $request->weight_kg > 0 ? (float) $request->weight_kg : null;
            }

            if ($request->has('time')) {
                $ep->time = $request->time;
            }

            $abnormalVal = isset($ep->abnormal_eggs) ? (int) $ep->abnormal_eggs : 0;
            $ep->total_eggs = (int) ($ep->good_eggs + $ep->broken_eggs + $abnormalVal);
            if ($ep->crates_count <= 0 && empty($ep->weight_kg)) {
                $ep->crates_count = round($ep->total_eggs / 25, 0);
            }

            // Normalisasi 10 kg = 1 Peti & Peti selalu bilangan bulat (tanpa koma)
            $ep->crates_count = (int) round($ep->crates_count);
            if ($ep->weight_kg !== null && $ep->weight_kg >= 10) {
                $extraP = (int) floor($ep->weight_kg / 10);
                $ep->crates_count += $extraP;
                $remKg = round($ep->weight_kg - ($extraP * 10), 1);
                $ep->weight_kg = $remKg > 0 ? $remKg : null;
            } elseif ($ep->weight_kg !== null) {
                $ep->weight_kg = round($ep->weight_kg, 1);
                if ($ep->weight_kg <= 0) {
                    $ep->weight_kg = null;
                }
            }

            if ($request->has('notes')) $ep->notes = $request->notes;
            if ($request->has('date')) $ep->date = $request->date;
            $ep->save();
            return back()->with('success', 'Data produksi telur berhasil diperbarui!');
        } elseif (str_starts_with($id, 'fc_')) {
            $fc = FeedConsumption::findOrFail(substr($id, 3));
            if ($request->has('quantity_kg')) $fc->quantity_kg = (float) $request->quantity_kg;
            elseif ($request->has('quantity')) $fc->quantity_kg = (float) $request->quantity;
            
            if ($request->has('feed_name')) $fc->feed_name = $request->feed_name;
            elseif ($request->has('item_name')) $fc->feed_name = $request->item_name;

            if ($request->has('coop_id')) {
                $coop = Coop::find($request->coop_id);
                if ($coop) {
                    $fc->coop_id = $coop->id;
                    $fc->flock_id = $coop->flock_id;
                }
            }
            if ($request->has('feeding_time')) $fc->feeding_time = $request->feeding_time;
            if ($request->has('notes')) $fc->notes = $request->notes;
            if ($request->has('date')) $fc->date = $request->date;
            $fc->save();
            return back()->with('success', 'Data pemberian pakan berhasil diperbarui!');
        } elseif (str_starts_with($id, 'ht_')) {
            $ht = HealthTreatment::findOrFail(substr($id, 3));
            if ($request->has('medicine_name')) $ht->medicine_name = $request->medicine_name;
            elseif ($request->has('item_name')) $ht->medicine_name = $request->item_name;

            if ($request->has('type')) $ht->type = $request->type;
            elseif ($request->has('category')) $ht->type = $request->category;

            if ($request->has('dosage')) {
                $dosageVal = (string) $request->dosage;
                if ($request->has('unit') && !empty($request->unit) && !str_contains(strtolower($dosageVal), strtolower($request->unit))) {
                    $dosageVal .= ' ' . $request->unit;
                }
                $ht->dosage = $dosageVal;
            } elseif ($request->has('quantity')) {
                $ht->dosage = $request->quantity . ($request->has('unit') ? ' ' . $request->unit : '');
            }

            if ($request->has('application_method')) $ht->application_method = $request->application_method;
            if ($request->has('coop_id')) {
                $coop = Coop::find($request->coop_id);
                if ($coop) {
                    $ht->coop_id = $coop->id;
                    $ht->flock_id = $coop->flock_id;
                }
            }
            if ($request->has('notes')) $ht->notes = $request->notes;
            if ($request->has('date')) $ht->date = $request->date;
            $ht->save();
            return back()->with('success', 'Data pemakaian obat berhasil diperbarui!');
        } elseif (str_starts_with($id, 'quar_')) {
            $realId = substr($id, 5);
            $quar = Quarantine::findOrFail($realId);

            $oldCoopId = $quar->coop_id;
            $oldStatus = $quar->status;
            $oldCount = (int) $quar->count;

            $newCount = $request->has('count') ? (int) $request->count : ($request->has('quantity') ? (int) $request->quantity : $oldCount);
            $newStatus = $request->input('status', $oldStatus);
            $newCoopId = $request->input('coop_id', $oldCoopId);

            // Rollback dampak populasi lama jika ada coop
            if ($oldCoopId) {
                $oldCoop = Coop::find($oldCoopId);
                if ($oldCoop) {
                    if ($oldStatus === 'sakit') {
                        $oldCoop->increment('active_chickens', $oldCount);
                    } elseif ($oldStatus === 'sembuh') {
                        $oldCoop->decrement('active_chickens', $oldCount);
                    }
                }
            }

            // Terapkan dampak populasi baru
            if ($newCoopId) {
                $newCoop = Coop::find($newCoopId);
                if ($newCoop) {
                    $quar->coop_id = $newCoop->id;
                    $quar->flock_id = $newCoop->flock_id;

                    if ($newStatus === 'sakit') {
                        $newCoop->decrement('active_chickens', $newCount);
                    } elseif ($newStatus === 'sembuh') {
                        $newCoop->increment('active_chickens', $newCount);
                    }
                }
            }

            $quar->count = max(1, $newCount);
            $quar->status = $newStatus;
            if ($request->has('battery_number')) $quar->battery_number = $request->battery_number;
            if ($request->has('cause')) $quar->cause = $request->cause;
            if ($request->has('action_taken')) $quar->action_taken = $request->action_taken;
            if ($request->has('notes')) $quar->notes = $request->notes;
            if ($request->has('date')) $quar->date = $request->date;
            if ($request->has('time')) $quar->time = $request->time;
            $quar->save();

            return back()->with('success', 'Data ayam karantina berhasil diperbarui!');
        } elseif (str_starts_with($id, 'mort_')) {
            $realId = substr($id, 5);
            $mort = Mortality::findOrFail($realId);

            $oldCoopId = $mort->coop_id;
            $oldCount = (int) $mort->count;

            $newCount = $request->has('count') ? (int) $request->count : ($request->has('quantity') ? (int) $request->quantity : $oldCount);
            $newCoopId = $request->input('coop_id', $oldCoopId);

            // Rollback populasi lama
            if ($oldCoopId) {
                $oldCoop = Coop::find($oldCoopId);
                if ($oldCoop) {
                    $oldCoop->increment('active_chickens', $oldCount);
                }
            }

            // Terapkan populasi baru
            if ($newCoopId) {
                $newCoop = Coop::find($newCoopId);
                if ($newCoop) {
                    $mort->coop_id = $newCoop->id;
                    $mort->flock_id = $newCoop->flock_id;
                    $newCoop->decrement('active_chickens', $newCount);
                }
            }

            $mort->count = max(1, $newCount);
            if ($request->has('status')) $mort->type = $request->status;
            elseif ($request->has('type')) $mort->type = $request->type;

            if ($request->has('cause')) $mort->cause = $request->cause;
            if ($request->has('notes')) $mort->notes = $request->notes;
            if ($request->has('date')) $mort->date = $request->date;
            $mort->save();

            return back()->with('success', 'Data mortalitas berhasil diperbarui!');
        }

        $stock = FarmStock::findOrFail($id);

        $itemName = $request->input('item_name') ?: ($request->input('feed_name') ?: ($request->input('medicine_name') ?: $stock->item_name));
        $oldType = $stock->type;
        $type = $request->input('type') ?: $stock->type;
        $quantity = $request->input('quantity_kg') ?: ($request->input('quantity') ?: ($request->input('dosage') ? (float) preg_replace('/[^0-9.]/', '', $request->input('dosage')) : $stock->quantity));
        $unit = $request->input('unit') ?: $stock->unit;
        $date = $request->input('date') ?: $stock->date;

        if ($request->has('time') && !empty($request->time)) {
            $createdAt = Carbon::parse($date);
            $timeParts = explode(':', $request->time);
            $createdAt->setTime((int) ($timeParts[0] ?? 0), (int) ($timeParts[1] ?? 0));
            $stock->created_at = $createdAt;
        }

        $stock->item_name = $itemName;
        $stock->type = $type;
        $stock->quantity = (float) $quantity;
        $stock->unit = $unit;
        $stock->date = $date;
        if ($request->has('source')) $stock->source = $request->source;
        if ($request->has('notes')) $stock->notes = $request->notes;
        $stock->save();

        if ($request->has('redirect_tab') || ($oldType !== $type)) {
            $targetTab = $request->input('redirect_tab', ($type === 'masuk' ? 'masuk' : 'keluar'));
            return redirect()->route('warehouse.' . $stock->category, array_merge(['tab' => $targetTab], $request->only(['start_date', 'end_date'])))
                ->with('success', "Data transaksi {$stock->item_name} berhasil diperbarui (Status: " . ($stock->type === 'masuk' ? 'Masuk / Beli' : 'Keluar') . ")!");
        }

        return back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    /**
     * Toggle status aktif/nonaktif data secara aman tanpa mengubah skema DB
     */
    public function toggleStatus($id)
    {
        if (str_starts_with($id, 'ep_') || str_starts_with($id, 'fc_') || str_starts_with($id, 'ht_') || str_starts_with($id, 'quar_') || str_starts_with($id, 'mort_')) {
            $model = null;
            if (str_starts_with($id, 'ep_')) {
                $realId = substr($id, 3);
                if (str_starts_with($realId, 'broken_')) $realId = substr($realId, 7);
                $model = EggProduction::find($realId);
            } elseif (str_starts_with($id, 'fc_')) {
                $model = FeedConsumption::find(substr($id, 3));
            } elseif (str_starts_with($id, 'ht_')) {
                $model = HealthTreatment::find(substr($id, 3));
            } elseif (str_starts_with($id, 'quar_')) {
                $model = Quarantine::find(substr($id, 5));
            } elseif (str_starts_with($id, 'mort_')) {
                $model = Mortality::find(substr($id, 5));
            }

            if ($model) {
                $currentNotes = $model->notes ?? '';
                if (str_starts_with(trim($currentNotes), '[NONAKTIF]')) {
                    $model->notes = trim(substr(trim($currentNotes), strlen('[NONAKTIF]')));
                    $msg = 'Data berhasil diaktifkan kembali.';
                    if ($model instanceof Quarantine && $model->coop_id) {
                        $coop = Coop::find($model->coop_id);
                        if ($coop) {
                            if ($model->status === 'sakit') $coop->decrement('active_chickens', (int) $model->count);
                            elseif ($model->status === 'sembuh') $coop->increment('active_chickens', (int) $model->count);
                        }
                    } elseif ($model instanceof Mortality && $model->coop_id) {
                        $coop = Coop::find($model->coop_id);
                        if ($coop) $coop->decrement('active_chickens', (int) $model->count);
                    }
                } else {
                    $model->notes = '[NONAKTIF] ' . $currentNotes;
                    $msg = 'Data berhasil dinonaktifkan.';
                    if ($model instanceof Quarantine && $model->coop_id) {
                        $coop = Coop::find($model->coop_id);
                        if ($coop) {
                            if ($model->status === 'sakit') $coop->increment('active_chickens', (int) $model->count);
                            elseif ($model->status === 'sembuh') $coop->decrement('active_chickens', (int) $model->count);
                        }
                    } elseif ($model instanceof Mortality && $model->coop_id) {
                        $coop = Coop::find($model->coop_id);
                        if ($coop) $coop->increment('active_chickens', (int) $model->count);
                    }
                }
                $model->save();
                return back()->with('success', $msg);
            }
        }

        $stock = FarmStock::findOrFail($id);
        $currentNotes = $stock->notes ?? '';

        if (str_starts_with(trim($currentNotes), '[NONAKTIF]')) {
            $stock->notes = trim(substr(trim($currentNotes), strlen('[NONAKTIF]')));
            $msg = 'Data transaksi berhasil diaktifkan kembali.';
        } else {
            $stock->notes = '[NONAKTIF] ' . $currentNotes;
            $msg = 'Data transaksi berhasil dinonaktifkan.';
        }

        $stock->save();
        return back()->with('success', $msg);
    }

    /**
     * Hapus transaksi stok
     */
    public function destroy($id)
    {
        if (str_starts_with($id, 'ep_')) {
            $realId = substr($id, 3);
            if (str_starts_with($realId, 'broken_')) {
                $realId = substr($realId, 7);
            }
            EggProduction::find($realId)?->delete();
            return back()->with('success', 'Data produksi telur berhasil dihapus!');
        } elseif (str_starts_with($id, 'fc_')) {
            $realId = substr($id, 3);
            FeedConsumption::find($realId)?->delete();
            return back()->with('success', 'Data pemberian pakan berhasil dihapus!');
        } elseif (str_starts_with($id, 'ht_')) {
            $realId = substr($id, 3);
            HealthTreatment::find($realId)?->delete();
            return back()->with('success', 'Data penggunaan obat berhasil dihapus!');
        } elseif (str_starts_with($id, 'quar_')) {
            $realId = substr($id, 5);
            $quar = Quarantine::find($realId);
            if ($quar) {
                if ($quar->coop_id) {
                    $coop = Coop::find($quar->coop_id);
                    if ($coop) {
                        if ($quar->status === 'sakit') {
                            $coop->increment('active_chickens', (int) $quar->count);
                        } elseif ($quar->status === 'sembuh') {
                            $coop->decrement('active_chickens', (int) $quar->count);
                        }
                    }
                }
                $quar->delete();
            }
            return back()->with('success', 'Data riwayat karantina berhasil dihapus!');
        } elseif (str_starts_with($id, 'mort_')) {
            $realId = substr($id, 5);
            $mort = Mortality::find($realId);
            if ($mort) {
                if ($mort->coop_id) {
                    $coop = Coop::find($mort->coop_id);
                    if ($coop) {
                        $coop->increment('active_chickens', (int) $mort->count);
                    }
                }
                $mort->delete();
            }
            return back()->with('success', 'Data mortalitas berhasil dihapus!');
        }

        $stock = FarmStock::findOrFail($id);
        $stock->delete();

        return back()->with('success', 'Data transaksi gudang berhasil dihapus!');
    }
}
