<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\FarmStock;
use Carbon\Carbon;

class OutboundIntegrationService
{
    /**
     * Ringkasan Barang Keluar Telur (Penjualan nochifram + Mutasi Gudang)
     */
    public static function getEggOutboundSummary($startDate = null, $endDate = null)
    {
        $queryPeti = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.category', 'telur')
            ->where('sale_items.unit', 'Peti');

        $queryKg = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.category', 'telur')
            ->where('sale_items.unit', 'Kg');

        $queryRevenue = DB::table('sales')
            ->where('category', 'telur');

        if ($startDate && $endDate) {
            $queryPeti->whereBetween('sales.date', [$startDate, $endDate]);
            $queryKg->whereBetween('sales.date', [$startDate, $endDate]);
            $queryRevenue->whereBetween('sales.date', [$startDate, $endDate]);
        }

        $petiSold = (float) $queryPeti->sum('sale_items.quantity');
        $kgSold = (float) $queryKg->sum('sale_items.quantity');
        $totalRevenue = (float) $queryRevenue->sum('sales.total_amount');
        $transactionCount = (int) $queryRevenue->count();

        // Mutasi keluar manual farm_stocks jika ada (Peti & Kg terpisah)
        $manualKeluarPetiQuery = FarmStock::where('category', 'telur')->where('type', 'keluar')->where(function ($q) {
            $q->where('unit', 'Peti')->orWhere('unit', 'peti');
        });
        $manualKeluarKgQuery = FarmStock::where('category', 'telur')->where('type', 'keluar')->where(function ($q) {
            $q->where('unit', 'Kg')->orWhere('unit', 'kg');
        });
        if ($startDate && $endDate) {
            $manualKeluarPetiQuery->whereBetween('date', [$startDate, $endDate]);
            $manualKeluarKgQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $manualKeluarPeti = (float) $manualKeluarPetiQuery->sum('quantity');
        $manualKeluarKg = (float) $manualKeluarKgQuery->sum('quantity');

        // Total produksi telur kandang (Barang Masuk)
        $prodQuery = EggProduction::query();
        if ($startDate && $endDate) {
            $prodQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $totalProducedCrates = (float) $prodQuery->sum('crates_count');
        $totalProducedEggs = (int) $prodQuery->sum('total_eggs');
        $totalProducedWeightKg = (float) $prodQuery->sum('weight_kg');

        // Mutasi masuk manual farm_stocks jika ada (Peti & Kg terpisah)
        $farmStockMasukPetiQuery = FarmStock::where('category', 'telur')->where('type', 'masuk')->where(function ($q) {
            $q->where('unit', 'Peti')->orWhere('unit', 'peti');
        });
        $farmStockMasukKgQuery = FarmStock::where('category', 'telur')->where('type', 'masuk')->where(function ($q) {
            $q->where('unit', 'Kg')->orWhere('unit', 'kg');
        });
        if ($startDate && $endDate) {
            $farmStockMasukPetiQuery->whereBetween('date', [$startDate, $endDate]);
            $farmStockMasukKgQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $farmStockMasukPeti = (float) $farmStockMasukPetiQuery->sum('quantity');
        $farmStockMasukKg = (float) $farmStockMasukKgQuery->sum('quantity');

        // Total telur masuk bersih (Peti & Kg)
        $totalMasukPeti = $totalProducedCrates + $farmStockMasukPeti;
        $totalMasukKg = $totalProducedWeightKg + $farmStockMasukKg;

        // Total telur keluar bersih (Peti & Kg)
        $totalKeluarPeti = $petiSold + $manualKeluarPeti;
        $totalKeluarKg = $kgSold + $manualKeluarKg;
        $totalKeluarEggs = 0;

        // Stok saat ini (Peti & Kg terpisah secara presisi, tanpa double-counting atau butir)
        $currentStockPeti = round($totalMasukPeti - $totalKeluarPeti, 1);
        $currentStockKgTotal = round($totalMasukKg - $totalKeluarKg, 1);
        $currentStockEggs = 0;

        return [
            'peti_sold' => $petiSold,
            'kg_sold' => $kgSold,
            'total_revenue' => $totalRevenue,
            'transaction_count' => $transactionCount,
            'manual_keluar_peti' => $manualKeluarPeti,
            'manual_keluar_kg' => $manualKeluarKg,
            'total_keluar_peti' => $totalKeluarPeti,
            'total_keluar_kg' => $totalKeluarKg,
            'total_keluar_eggs' => $totalKeluarEggs,
            'total_produced_crates' => $totalMasukPeti,
            'total_produced_eggs' => $totalProducedEggs,
            'total_produced_kg' => $totalMasukKg,
            'current_stock_peti' => $currentStockPeti,
            'current_stock_kg_total' => $currentStockKgTotal,
            'current_stock_eggs' => $currentStockEggs,
        ];
    }

    /**
     * Ringkasan Barang Keluar Pakan (Penjualan nochifram + Konsumsi Ayam Kandang)
     */
    public static function getFeedOutboundSummary($startDate = null, $endDate = null)
    {
        // 1. Penjualan Pakan (Karung & Kg)
        $queryKarung = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.category', 'pakan')
            ->where('sale_items.unit', 'Karung');

        $queryKg = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.category', 'pakan')
            ->where('sale_items.unit', 'Kg');

        $queryRevenue = DB::table('sales')
            ->where('category', 'pakan');

        if ($startDate && $endDate) {
            $queryKarung->whereBetween('sales.date', [$startDate, $endDate]);
            $queryKg->whereBetween('sales.date', [$startDate, $endDate]);
            $queryRevenue->whereBetween('sales.date', [$startDate, $endDate]);
        }

        $karungSold = (float) $queryKarung->sum('sale_items.quantity');
        $kgSold = (float) $queryKg->sum('sale_items.quantity');
        $totalRevenue = (float) $queryRevenue->sum('sales.total_amount');

        // Konversi Karung ke Kg sesuai setting database
        $kgPerKarung = \App\Models\Setting::getKgPerKarung();
        $soldInKg = ($karungSold * $kgPerKarung) + $kgSold;

        // 2. Konsumsi Pakan oleh Ayam di Kandang
        $consQuery = FeedConsumption::query();
        if ($startDate && $endDate) {
            $consQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $consumptionKg = (float) $consQuery->sum('quantity_kg');
        $consumptionKarung = round($consumptionKg / $kgPerKarung, 1);

        // 3. Pakan Masuk MURNI dari input riil FarmStock (tanpa hardcoded fake baseline)
        $stockMasukQuery = FarmStock::where('category', 'pakan')->where('type', 'masuk');
        if ($startDate && $endDate) {
            $stockMasukQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $purchasedKg = (float) $stockMasukQuery->sum('quantity');
        $purchasedKarung = round($purchasedKg / $kgPerKarung, 1);

        // 4. Mutasi manual keluar di FarmStock jika ada
        $stockKeluarQuery = FarmStock::where('category', 'pakan')
            ->where('type', 'keluar')
            ->where(function($q) {
                $q->whereNull('notes')
                  ->orWhere('notes', 'not like', '[AUTO-KONSUMSI]%');
            });
            
        if ($startDate && $endDate) {
            $stockKeluarQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $manualKeluarKg = (float) $stockKeluarQuery->sum('quantity');

        // Total Pakan Keluar (Konsumsi Kandang + Penjualan Luar + Manual Keluar Gudang)
        $totalKeluarKg = $consumptionKg + $soldInKg + $manualKeluarKg;
        
        $totalKeluarKarung = round($totalKeluarKg / $kgPerKarung, 1);

        // Sisa stok pakan (bisa minus / defisit jika belum ada input pakan masuk)
        $currentStockKg = round($purchasedKg - $totalKeluarKg, 1);
        $currentStockKarung = round($currentStockKg / $kgPerKarung, 1);

        return [
            'karung_sold' => $karungSold,
            'kg_sold' => $kgSold,
            'sold_in_kg' => $soldInKg,
            'total_revenue' => $totalRevenue,
            'consumption_kg' => $consumptionKg,
            'consumption_karung' => $consumptionKarung,
            'purchased_kg' => $purchasedKg,
            'purchased_karung' => $purchasedKarung,
            'total_keluar_kg' => $totalKeluarKg,
            'total_keluar_karung' => $totalKeluarKarung,
            'current_stock_kg' => $currentStockKg,
            'current_stock_karung' => $currentStockKarung,
        ];
    }

    /**
     * Ambil Riwayat Penjualan Barang Keluar dari tabel sales + sale_items
     */
    public static function getSalesTransactions($category = null, $startDate = null, $endDate = null, $limit = 50)
    {
        $query = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                'sales.id as sale_id',
                'sales.invoice_no',
                'sales.date',
                'sales.category',
                'sales.customer_name',
                'sales.customer_phone',
                'sales.payment_method',
                'sales.payment_status',
                'sales.notes',
                'sales.created_at',
                'sale_items.item_name',
                'sale_items.unit',
                'sale_items.quantity',
                'sale_items.unit_price',
                'sale_items.total_price'
            );

        if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
            $query->leftJoin('users', 'sales.user_id', '=', 'users.id')
                ->addSelect('users.name as user_name', 'users.username as user_username');
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('trips')) {
            $query->leftJoin('trips', 'sales.trip_id', '=', 'trips.id')
                ->addSelect('trips.trip_code', 'trips.driver_name', 'trips.destination');
        }

        $query->orderBy('sales.date', 'desc')
            ->orderBy('sales.id', 'desc');

        if ($category) {
            $query->where('sales.category', $category);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('sales.date', [$startDate, $endDate]);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Ambil Riwayat Distribusi Armada (Trips)
     */
    public static function getTripOutbounds($type = null, $limit = 10)
    {
        $query = DB::table('trips')
            ->select(
                'id',
                'trip_code',
                'date',
                'type',
                'vehicle',
                'route',
                'initial_peti',
                'initial_kg',
                'initial_weight',
                'peti_returned',
                'kiloan_returned',
                'status',
                'created_at'
            )
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($type) {
            $query->where('type', $type);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }
}
