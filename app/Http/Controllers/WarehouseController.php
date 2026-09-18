<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FarmStock;
use App\Models\User;
use App\Models\Coop;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\HealthTreatment;
use App\Models\Quarantine;
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

        // Parameter Rentang Tanggal (Default 14 Hari Terakhir)
        $defaultStartDate = Carbon::today()->subDays(13)->toDateString();
        $defaultEndDate = Carbon::today()->toDateString();

        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);

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

        $namaHari = [
            'Sunday' => 'Min', 'Monday' => 'Sen', 'Tuesday' => 'Sel',
            'Wednesday' => 'Rab', 'Thursday' => 'Kam', 'Friday' => 'Jum', 'Saturday' => 'Sab'
        ];
        $bulanShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $formattedStartDate = ($namaHari[$startCarbon->format('l')] ?? $startCarbon->format('D')) . ', ' . $startCarbon->day . ' ' . ($bulanShort[$startCarbon->month] ?? $startCarbon->format('M')) . ' ' . $startCarbon->year;
        $formattedEndDate = ($namaHari[$endCarbon->format('l')] ?? $endCarbon->format('D')) . ', ' . $endCarbon->day . ' ' . ($bulanShort[$endCarbon->month] ?? $endCarbon->format('M')) . ' ' . $endCarbon->year;

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
        $obatMasuk = (float) FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])
            ->where('type', 'masuk')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('quantity');
        $obatKeluarManual = (float) FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])
            ->where('type', 'keluar')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('quantity');
        
        $healthTreatments = \App\Models\HealthTreatment::whereBetween('date', [$startDate, $endDate])->get();
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
            $karantinaMasuk = (int) Quarantine::where('status', 'sakit')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('count');
            $karantinaKeluar = (int) Quarantine::where('status', 'sembuh')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('count');
            $karantinaStok = Quarantine::getCurrentCount();
        } catch (\Throwable $e) {
            $karantinaMasuk = 0;
            $karantinaKeluar = 0;
            $karantinaStok = 0;
        }

        // Pre-query data aliran barang berdasarkan rentang tanggal
        $eggProdByDate = EggProduction::whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(date) as dt'),
                DB::raw('SUM(crates_count) as total_crates'),
                DB::raw('SUM(broken_eggs) as total_broken'),
                DB::raw('SUM(good_eggs) as total_good')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->get()
            ->keyBy('dt');

        $feedConsByDate = FeedConsumption::whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(date) as dt'),
                DB::raw('SUM(quantity_kg) as total_kg')
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

        $healthByDate = HealthTreatment::whereBetween('date', [$startDate, $endDate])
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

        $bulanShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agt', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $cursor = $startCarbon->copy();
        while ($cursor->lte($endCarbon)) {
            $dt = $cursor->toDateString();
            $chartLabels[] = $cursor->day . ' ' . ($bulanShort[$cursor->month] ?? $cursor->format('M'));

            // 1. Telur (Satuan: Peti)
            $ep = $eggProdByDate->get($dt);
            $fsEggList = $farmStockByDate->get($dt, collect())->where('category', 'telur');
            $fsEggMasuk = (float) $fsEggList->where('type', 'masuk')->sum('total_qty');
            $fsEggKeluar = (float) $fsEggList->where('type', 'keluar')->sum('total_qty');

            $eggMasukPeti = ($ep ? (float) $ep->total_crates : 0.0) + $fsEggMasuk;
            $eggRusakButir = $ep ? (int) $ep->total_broken : 0;
            $eggRusakPeti = round($eggRusakButir / 25, 2);

            $salesListDay = $salesByDate->get($dt, collect());
            $eggSalesDay = $salesListDay->where('category', 'telur');
            $eggPetiSold = (float) $eggSalesDay->where('unit', 'Peti')->sum('total_qty');
            $eggKgSold = (float) $eggSalesDay->where('unit', 'Kg')->sum('total_qty');
            $eggTotalSoldPeti = $eggPetiSold + round($eggKgSold / 15, 2);

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
            'telur_masuk' => $sumTelurMasuk,
            'telur_rusak_peti' => $sumTelurRusakPeti,
            'telur_rusak_butir' => $sumTelurRusakButir,
            'telur_terjual' => $sumTelurTerjual,
            'pakan_masuk' => $sumPakanMasuk,
            'pakan_konsumsi' => $sumPakanKonsumsi,
            'pakan_terjual' => $sumPakanTerjual,
            'obat_masuk' => $sumObatMasuk,
            'obat_konsumsi' => $sumObatKonsumsi,
        ];

        $dateParams = ['start_date' => $startDate, 'end_date' => $endDate];

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

        $chartTotals = [
            'overview' => [
                'masuk' => [
                    ['label' => 'Telur', 'val' => number_format($sumTelurMasuk, 1, ',', '.') . ' Peti', 'url' => route('warehouse.telur', array_merge(['tab' => 'masuk'], $dateParams))],
                    ['label' => 'Pakan', 'val' => number_format($sumPakanMasuk, 1, ',', '.') . ' Kg', 'url' => route('warehouse.pakan', array_merge(['tab' => 'masuk'], $dateParams))],
                    ['label' => 'Obat', 'val' => number_format($sumObatMasuk, 1, ',', '.') . ' Item', 'url' => route('warehouse.obat', array_merge(['tab' => 'masuk'], $dateParams))],
                ],
                'digunakan' => [
                    ['label' => 'Pakan', 'val' => number_format($sumPakanKonsumsi, 1, ',', '.') . ' Kg', 'url' => route('warehouse.pakan', array_merge(['tab' => 'keluar'], $dateParams))],
                    ['label' => 'Telur Rusak', 'val' => number_format($sumTelurRusakButir, 0, ',', '.') . ' Btr', 'url' => route('warehouse.telur', array_merge(['tab' => 'keluar'], $dateParams))],
                    ['label' => 'Obat', 'val' => number_format($sumObatKonsumsi, 1, ',', '.') . ' Dosis', 'url' => route('warehouse.obat', array_merge(['tab' => 'keluar'], $dateParams))],
                ],
                'keluar' => [
                    ['label' => 'Telur Total', 'val' => number_format($sumTelurRusakPeti + $sumTelurTerjual, 1, ',', '.') . ' Peti', 'url' => route('warehouse.telur', array_merge(['tab' => 'semua'], $dateParams))],
                    ['label' => 'Pakan Total', 'val' => number_format($sumPakanKonsumsi + $sumPakanTerjual, 1, ',', '.') . ' Kg', 'url' => route('warehouse.pakan', array_merge(['tab' => 'semua'], $dateParams))],
                    ['label' => 'Obat Pakai', 'val' => number_format($sumObatKonsumsi, 1, ',', '.') . ' Dosis', 'url' => route('warehouse.obat', array_merge(['tab' => 'keluar'], $dateParams))],
                ],
                'terjual' => [
                    ['label' => 'Telur Terjual', 'val' => number_format($sumTelurTerjual, 1, ',', '.') . ' Peti', 'url' => route('warehouse.telur', array_merge(['tab' => 'penjualan'], $dateParams))],
                    ['label' => 'Pakan Terjual', 'val' => number_format($sumPakanTerjual, 1, ',', '.') . ' Kg', 'url' => route('warehouse.pakan', array_merge(['tab' => 'penjualan'], $dateParams))],
                ],
            ],
            'telur' => [
                'masuk' => number_format($sumTelurMasuk, 1, ',', '.') . ' Peti',
                'digunakan' => number_format($sumTelurRusakPeti, 2, ',', '.') . ' Peti (' . number_format($sumTelurRusakButir, 0, ',', '.') . ' Btr)',
                'keluar' => number_format($sumTelurRusakPeti + $sumTelurTerjual, 1, ',', '.') . ' Peti',
                'terjual' => number_format($sumTelurTerjual, 1, ',', '.') . ' Peti',
            ],
            'pakan' => [
                'masuk' => number_format($sumPakanMasuk, 1, ',', '.') . ' Kg',
                'digunakan' => number_format($sumPakanKonsumsi, 1, ',', '.') . ' Kg',
                'keluar' => number_format($sumPakanKonsumsi + $sumPakanTerjual, 1, ',', '.') . ' Kg',
                'terjual' => number_format($sumPakanTerjual, 1, ',', '.') . ' Kg',
            ],
            'obat' => [
                'masuk' => number_format($sumObatMasuk, 1, ',', '.') . ' Item',
                'digunakan' => number_format($sumObatKonsumsi, 1, ',', '.') . ' Dosis',
                'keluar' => number_format($sumObatKonsumsi, 1, ',', '.') . ' Dosis',
                'terjual' => '0 Item',
            ],
        ];

        // Mutasi stok internal terbaru gabungan (terfilter rentang tanggal)
        $recentTransactions = $this->getUnifiedRecentTransactions(10, $startDate, $endDate);

        // Transaksi penjualan terbaru dari nochifram (terfilter rentang tanggal)
        $recentSales = OutboundIntegrationService::getSalesTransactions(null, $startDate, $endDate, 10);

        return view('warehouse.index', compact(
            'user',
            'telurMasuk', 'telurMasukButir', 'telurMasukKg', 'telurKeluar', 'telurKeluarKg', 'telurKeluarEggs', 'telurStok', 'telurStokKgTotal', 'telurStokButir', 'telurPetiSold', 'telurKgSold', 'telurRevenue',
            'pakanMasuk', 'pakanMasukKarung', 'pakanKeluar', 'pakanTotalKarungKeluar', 'pakanStok', 'pakanStokKarung', 'pakanKarungSold', 'pakanKgSold', 'pakanConsumptionKg', 'pakanConsumptionKarung', 'pakanRevenue',
            'obatMasuk', 'obatKeluar', 'obatStok',
            'karantinaMasuk', 'karantinaKeluar', 'karantinaStok',
            'recentTransactions', 'recentSales',
            'chartLabels', 'chartDataSets', 'chartTotals', 'streamTotals',
            'startDate', 'endDate', 'defaultStartDate', 'defaultEndDate', 'diffDays', 'formattedStartDate', 'formattedEndDate'
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
                'notes' => 'Panen ' . number_format($ep->total_eggs, 0, ',', '.') . ' Butir' . ($ep->notes ? ' • ' . $ep->notes : ''),
                'user' => $ep->user,
            ]);
            if ($ep->broken_eggs > 0) {
                $transactions->push((object) [
                    'id' => 'ep_broken_' . $ep->id,
                    'category' => 'telur',
                    'type' => 'keluar',
                    'item_name' => 'Telur Rusak / Pecah: ' . ($ep->coop ? $ep->coop->name : 'Kandang'),
                    'quantity' => (float) $ep->broken_eggs,
                    'unit' => 'Butir',
                    'date' => Carbon::parse($ep->date),
                    'created_at' => $ep->created_at ? Carbon::parse($ep->created_at) : Carbon::parse($ep->date),
                    'source' => $ep->coop ? $ep->coop->name : 'Kandang',
                    'notes' => 'Telur rusak/pecah saat pengumpulan',
                    'user' => $ep->user,
                ]);
            }
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
                    'notes' => 'Panen Telur Utuh: ' . number_format($ep->good_eggs ?? $ep->total_eggs, 0, ',', '.') . ' Butir' . ($ep->weight_kg > 0 ? ' (' . number_format($ep->weight_kg, 1, ',', '.') . ' Kg)' : '') . ($ep->notes ? ' • ' . $ep->notes : ''),
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

            // B. Telur Rusak / Pecah (Untuk tab keluar / data rusak & semua)
            if (($tab === 'semua' || $tab === 'keluar') && $ep->broken_eggs > 0) {
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
                       str_contains(strtolower($item->notes ?? ''), $s);
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

        // Data Penjualan Telur dari aplikasi nochifram
        $salesList = OutboundIntegrationService::getSalesTransactions('telur', $startDate, $endDate, 50);
        $tripList = OutboundIntegrationService::getTripOutbounds('telur', 10);

        $flocks = \App\Models\Flock::where('is_active', true)->get();
        $coops = Coop::where('is_active', true)->get();

        return view('warehouse.telur', compact(
            'user', 'items', 'tab', 'search', 'startDate', 'endDate',
            'totalMasuk', 'totalMasukKg', 'totalKeluar', 'totalKeluarKg', 'stokSaatIni', 'stokSaatIniKg', 'stokSaatIniButir',
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
        $fsQuery = FarmStock::with('user')->where('category', 'pakan');
        if ($startDate && $endDate) {
            $fsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        if ($tab === 'masuk') {
            $fsQuery->where('type', 'masuk');
        } elseif ($tab === 'keluar') {
            $fsQuery->where('type', 'keluar')
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
            foreach ($feedConsumptions as $fc) {
                $collection->push((object) [
                    'id' => 'fc_' . $fc->id,
                    'raw_id' => $fc->id,
                    'source_type' => 'feed_consumption',
                    'item_name' => 'Pemberian Pakan: ' . $fc->feed_name . ' (' . ($fc->feeding_time ?? 'Harian') . ')',
                    'type' => 'keluar',
                    'quantity' => (float) $fc->quantity_kg,
                    'unit' => 'Kg',
                    'date' => Carbon::parse($fc->date),
                    'created_at' => $fc->created_at ? Carbon::parse($fc->created_at) : Carbon::parse($fc->date),
                    'source' => $fc->coop ? ($fc->coop->name . ($fc->flock ? ' (' . $fc->flock->name . ')' : '')) : 'Kandang',
                    'notes' => 'Pemberian pakan ' . ($fc->feeding_time ?? 'pagi/sore') . ' untuk ayam kandang' . ($fc->notes ? ' • ' . $fc->notes : ''),
                    'user' => $fc->user,
                    'is_nonaktif' => str_starts_with(trim($fc->notes ?? ''), '[NONAKTIF]'),
                    'coop_id' => $fc->coop_id,
                    'flock_id' => $fc->flock_id,
                    'feed_name' => $fc->feed_name,
                    'feeding_time' => $fc->feeding_time,
                    'quantity_kg' => (float) $fc->quantity_kg,
                ]);
            }
        }

        // Filter pencarian
        if (!empty($search)) {
            $s = strtolower($search);
            $collection = $collection->filter(function ($item) use ($s) {
                return str_contains(strtolower($item->item_name), $s) ||
                       str_contains(strtolower($item->source ?? ''), $s) ||
                       str_contains(strtolower($item->notes ?? ''), $s);
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

        return view('warehouse.pakan', compact(
            'user', 'items', 'tab', 'search', 'startDate', 'endDate',
            'totalMasuk', 'totalKeluar', 'stokSaatIni', 'currentStockKarung',
            'karungSold', 'kgSold', 'soldRevenue', 'consumptionKg', 'consumptionKarung', 'purchasedKarung',
            'salesList', 'tripList', 'coops', 'flocks'
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
                       str_contains(strtolower($item->category ?? ''), $s);
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
     * Store transaksi stok baru
     */
    public function store(Request $request)
    {
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
        }

        $stock = FarmStock::findOrFail($id);

        $itemName = $request->input('item_name') ?: ($request->input('feed_name') ?: ($request->input('medicine_name') ?: $stock->item_name));
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

        return back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    /**
     * Toggle status aktif/nonaktif data secara aman tanpa mengubah skema DB
     */
    public function toggleStatus($id)
    {
        if (str_starts_with($id, 'ep_') || str_starts_with($id, 'fc_') || str_starts_with($id, 'ht_')) {
            $model = null;
            if (str_starts_with($id, 'ep_')) {
                $realId = substr($id, 3);
                if (str_starts_with($realId, 'broken_')) $realId = substr($realId, 7);
                $model = EggProduction::find($realId);
            } elseif (str_starts_with($id, 'fc_')) {
                $model = FeedConsumption::find(substr($id, 3));
            } elseif (str_starts_with($id, 'ht_')) {
                $model = HealthTreatment::find(substr($id, 3));
            }

            if ($model) {
                $currentNotes = $model->notes ?? '';
                if (str_starts_with(trim($currentNotes), '[NONAKTIF]')) {
                    $model->notes = trim(substr(trim($currentNotes), strlen('[NONAKTIF]')));
                    $msg = 'Data berhasil diaktifkan kembali.';
                } else {
                    $model->notes = '[NONAKTIF] ' . $currentNotes;
                    $msg = 'Data berhasil dinonaktifkan.';
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
        }

        $stock = FarmStock::findOrFail($id);
        $stock->delete();

        return back()->with('success', 'Data transaksi gudang berhasil dihapus!');
    }
}
