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

        // Total produksi telur kandang (Barang Masuk & Telur Rusak)
        $prodQuery = EggProduction::query();
        if ($startDate && $endDate) {
            $prodQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $totalProducedCrates = (float) $prodQuery->sum('crates_count');
        $totalProducedEggs = (int) $prodQuery->sum('total_eggs');
        $totalProducedWeightKg = (float) $prodQuery->sum('weight_kg');
        $totalBrokenEggs = (int) $prodQuery->sum('broken_eggs');

        // Mutasi manual FarmStock telur (Butir rusak) jika ada
        $manualKeluarButirQuery = FarmStock::where('category', 'telur')->where('type', 'keluar')->where(function ($q) {
            $q->where('unit', 'Butir')->orWhere('unit', 'butir')->orWhere('unit', 'Btr');
        });
        if ($startDate && $endDate) {
            $manualKeluarButirQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $manualKeluarButir = (int) $manualKeluarButirQuery->sum('quantity');
        $totalBrokenEggs += $manualKeluarButir;
        $totalBrokenPeti = round($totalBrokenEggs / 25, 2);

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

        // Normalisasi Penjualan (10 kg = 1 Peti, Peti bilangan bulat)
        if ($kgSold >= 10) {
            $extraSoldPeti = (int) floor($kgSold / 10);
            $petiSold = (int) round($petiSold + $extraSoldPeti);
            $kgSold = round($kgSold - ($extraSoldPeti * 10), 1);
        } else {
            $petiSold = (int) round($petiSold);
            $kgSold = round($kgSold, 1);
        }

        // Normalisasi Manual Keluar
        if ($manualKeluarKg >= 10) {
            $extraManKeluarPeti = (int) floor($manualKeluarKg / 10);
            $manualKeluarPeti = (int) round($manualKeluarPeti + $extraManKeluarPeti);
            $manualKeluarKg = round($manualKeluarKg - ($extraManKeluarPeti * 10), 1);
        } else {
            $manualKeluarPeti = (int) round($manualKeluarPeti);
            $manualKeluarKg = round($manualKeluarKg, 1);
        }

        // Total telur masuk bersih (10 kg = 1 Peti, Peti bilangan bulat)
        $rawMasukPeti = (float) ($totalProducedCrates + $farmStockMasukPeti);
        $rawMasukKg = (float) ($totalProducedWeightKg + $farmStockMasukKg);
        if ($rawMasukKg >= 10) {
            $extraMasukPeti = (int) floor($rawMasukKg / 10);
            $totalMasukPeti = (int) round($rawMasukPeti + $extraMasukPeti);
            $totalMasukKg = round($rawMasukKg - ($extraMasukPeti * 10), 1);
        } else {
            $totalMasukPeti = (int) round($rawMasukPeti);
            $totalMasukKg = round($rawMasukKg, 1);
        }

        // Total telur keluar bersih (10 kg = 1 Peti, Peti bilangan bulat)
        // Menggabungkan Penjualan + Telur Rusak + Mutasi Keluar Manual
        $rawKeluarPeti = (float) ($petiSold + $manualKeluarPeti + $totalBrokenPeti);
        $rawKeluarKg = (float) ($kgSold + $manualKeluarKg);
        if ($rawKeluarKg >= 10) {
            $extraKeluarPeti = (int) floor($rawKeluarKg / 10);
            $totalKeluarPeti = (int) round($rawKeluarPeti + $extraKeluarPeti);
            $totalKeluarKg = round($rawKeluarKg - ($extraKeluarPeti * 10), 1);
        } else {
            $totalKeluarPeti = (int) round($rawKeluarPeti);
            $totalKeluarKg = round($rawKeluarKg, 1);
        }
        $totalKeluarEggs = 0;

        // Stok saat ini (Peti selalu integer, konversi 10 kg = 1 Peti)
        $netTotalKg = round((($totalMasukPeti * 10) + $totalMasukKg) - (($totalKeluarPeti * 10) + $totalKeluarKg), 1);
        if ($netTotalKg >= 0) {
            $currentStockPeti = (int) floor($netTotalKg / 10);
            $currentStockKgTotal = round($netTotalKg - ($currentStockPeti * 10), 1);
        } else {
            $absNetKg = abs($netTotalKg);
            $currentStockPeti = - (int) floor($absNetKg / 10);
            $currentStockKgTotal = - round($absNetKg - (abs($currentStockPeti) * 10), 1);
        }
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
            'total_broken_eggs' => $totalBrokenEggs,
            'total_broken_peti' => $totalBrokenPeti,
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

        // Helper konversi kuantitas stok pakan ke Kg sesuai satuan (Karung/Sak = x kgPerKarung, Ton = x 1000, Kg = x 1)
        $convertStockToKg = function ($item) use ($kgPerKarung) {
            $qty = (float) $item->quantity;
            $u = strtolower(trim($item->unit ?? ''));
            if (in_array($u, ['karung', 'sak', 'krg'])) {
                return $qty * $kgPerKarung;
            } elseif ($u === 'ton') {
                return $qty * 1000;
            }
            return $qty;
        };

        // 3. Pakan Masuk MURNI dari input riil FarmStock (tanpa hardcoded fake baseline)
        $stockMasukQuery = FarmStock::whereRaw('LOWER(category) = ?', ['pakan'])->whereRaw('LOWER(type) = ?', ['masuk']);
        if ($startDate && $endDate) {
            $stockMasukQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $purchasedKg = (float) $stockMasukQuery->get()->sum($convertStockToKg);
        $purchasedKarung = round($purchasedKg / $kgPerKarung, 1);

        // 4. Mutasi manual keluar di FarmStock jika ada
        $stockKeluarQuery = FarmStock::whereRaw('LOWER(category) = ?', ['pakan'])
            ->whereRaw('LOWER(type) = ?', ['keluar'])
            ->where(function($q) {
                $q->whereNull('notes')
                  ->orWhere('notes', 'not like', '[AUTO-KONSUMSI]%');
            });
            
        if ($startDate && $endDate) {
            $stockKeluarQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $manualKeluarKg = (float) $stockKeluarQuery->get()->sum($convertStockToKg);

        // Total Pakan Keluar (Konsumsi Kandang + Penjualan Luar + Manual Keluar Gudang)
        $totalKeluarKg = $consumptionKg + $soldInKg + $manualKeluarKg;
        
        $totalKeluarKarung = round($totalKeluarKg / $kgPerKarung, 1);

        // Sisa stok pakan (bisa minus / defisit jika belum ada input pakan masuk)
        $currentStockKg = round($purchasedKg - $totalKeluarKg, 1);
        $currentStockKarung = round($currentStockKg / $kgPerKarung, 1);

        // 5. Breakdown Per Jenis Pakan (Pakan Layer vs Pakan Grower / Starter)
        // A. Penjualan Grower / Starter
        $karungSoldGrower = (float) (DB::table('sale_items')->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.category', 'pakan')->where('sale_items.unit', 'Karung')
            ->where(function($q){ $q->where('sale_items.item_name', 'like', '%grower%')->orWhere('sale_items.item_name', 'like', '%starter%')->orWhere('sale_items.item_name', 'like', '%pullet%'); })
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) { $q->whereBetween('sales.date', [$startDate, $endDate]); })
            ->sum('sale_items.quantity'));
        $kgSoldGrower = (float) (DB::table('sale_items')->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.category', 'pakan')->where('sale_items.unit', 'Kg')
            ->where(function($q){ $q->where('sale_items.item_name', 'like', '%grower%')->orWhere('sale_items.item_name', 'like', '%starter%')->orWhere('sale_items.item_name', 'like', '%pullet%'); })
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) { $q->whereBetween('sales.date', [$startDate, $endDate]); })
            ->sum('sale_items.quantity'));
        $soldInKgGrower = ($karungSoldGrower * $kgPerKarung) + $kgSoldGrower;

        $karungSoldLayer = max(0, $karungSold - $karungSoldGrower);
        $kgSoldLayer = max(0, $kgSold - $kgSoldGrower);
        $soldInKgLayer = max(0, $soldInKg - $soldInKgGrower);

        // B. Konsumsi Grower vs Layer
        $consumptionKgGrower = (float) (FeedConsumption::query()
            ->where(function($q){ $q->where('feed_name', 'like', '%grower%')->orWhere('feed_name', 'like', '%starter%')->orWhere('feed_name', 'like', '%pullet%'); })
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) { $q->whereBetween('date', [$startDate, $endDate]); })
            ->sum('quantity_kg'));
        $consumptionKarungGrower = round($consumptionKgGrower / $kgPerKarung, 1);

        $consumptionKgLayer = max(0, $consumptionKg - $consumptionKgGrower);
        $consumptionKarungLayer = round($consumptionKgLayer / $kgPerKarung, 1);

        // C. Pembelian / Masuk Grower vs Layer
        $purchasedKgGrower = (float) (FarmStock::whereRaw('LOWER(category) = ?', ['pakan'])->whereRaw('LOWER(type) = ?', ['masuk'])
            ->where(function($q){ $q->where('item_name', 'like', '%grower%')->orWhere('item_name', 'like', '%starter%')->orWhere('item_name', 'like', '%pullet%'); })
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) { $q->whereBetween('date', [$startDate, $endDate]); })
            ->get()
            ->sum($convertStockToKg));
        $purchasedKarungGrower = round($purchasedKgGrower / $kgPerKarung, 1);

        $purchasedKgLayer = max(0, $purchasedKg - $purchasedKgGrower);
        $purchasedKarungLayer = round($purchasedKgLayer / $kgPerKarung, 1);

        // D. Manual Keluar Grower vs Layer
        $manualKeluarKgGrower = (float) (FarmStock::whereRaw('LOWER(category) = ?', ['pakan'])->whereRaw('LOWER(type) = ?', ['keluar'])
            ->where(function($q){ $q->whereNull('notes')->orWhere('notes', 'not like', '[AUTO-KONSUMSI]%'); })
            ->where(function($q){ $q->where('item_name', 'like', '%grower%')->orWhere('item_name', 'like', '%starter%')->orWhere('item_name', 'like', '%pullet%'); })
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) { $q->whereBetween('date', [$startDate, $endDate]); })
            ->get()
            ->sum($convertStockToKg));

        $manualKeluarKgLayer = max(0, $manualKeluarKg - $manualKeluarKgGrower);

        // E. Total Keluar & Sisa Stok Grower vs Layer
        $totalKeluarKgGrower = $consumptionKgGrower + $soldInKgGrower + $manualKeluarKgGrower;
        $totalKeluarKarungGrower = round($totalKeluarKgGrower / $kgPerKarung, 1);
        $currentStockKgGrower = round($purchasedKgGrower - $totalKeluarKgGrower, 1);
        $currentStockKarungGrower = round($currentStockKgGrower / $kgPerKarung, 1);

        $totalKeluarKgLayer = $consumptionKgLayer + $soldInKgLayer + $manualKeluarKgLayer;
        $totalKeluarKarungLayer = round($totalKeluarKgLayer / $kgPerKarung, 1);
        $currentStockKgLayer = round($purchasedKgLayer - $totalKeluarKgLayer, 1);
        $currentStockKarungLayer = round($currentStockKgLayer / $kgPerKarung, 1);

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

            // Layer Breakdown
            'purchased_kg_layer' => $purchasedKgLayer,
            'purchased_karung_layer' => $purchasedKarungLayer,
            'consumption_kg_layer' => $consumptionKgLayer,
            'consumption_karung_layer' => $consumptionKarungLayer,
            'karung_sold_layer' => $karungSoldLayer,
            'kg_sold_layer' => $kgSoldLayer,
            'sold_in_kg_layer' => $soldInKgLayer,
            'total_keluar_kg_layer' => $totalKeluarKgLayer,
            'total_keluar_karung_layer' => $totalKeluarKarungLayer,
            'current_stock_kg_layer' => $currentStockKgLayer,
            'current_stock_karung_layer' => $currentStockKarungLayer,

            // Grower / Starter Breakdown
            'purchased_kg_grower' => $purchasedKgGrower,
            'purchased_karung_grower' => $purchasedKarungGrower,
            'consumption_kg_grower' => $consumptionKgGrower,
            'consumption_karung_grower' => $consumptionKarungGrower,
            'karung_sold_grower' => $karungSoldGrower,
            'kg_sold_grower' => $kgSoldGrower,
            'sold_in_kg_grower' => $soldInKgGrower,
            'total_keluar_kg_grower' => $totalKeluarKgGrower,
            'total_keluar_karung_grower' => $totalKeluarKarungGrower,
            'current_stock_kg_grower' => $currentStockKgGrower,
            'current_stock_karung_grower' => $currentStockKarungGrower,
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
            $query->leftJoin('users', 'sales.user_id', '=', 'users.id');
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'name')) {
                $query->addSelect('users.name as user_name');
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'username')) {
                $query->addSelect('users.username as user_username');
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('trips')) {
            $query->leftJoin('trips', 'sales.trip_id', '=', 'trips.id');
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && \Illuminate\Support\Facades\Schema::hasColumn('trips', 'user_id')) {
                $query->leftJoin('users as trip_users', 'trips.user_id', '=', 'trip_users.id');
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'username')) {
                    $query->addSelect('trip_users.username as trip_user_username');
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'name')) {
                    $query->addSelect('trip_users.name as trip_user_name');
                }
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('trips', 'trip_code')) {
                $query->addSelect('trips.trip_code');
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('trips', 'vehicle')) {
                $query->addSelect('trips.vehicle as driver_name');
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('trips', 'driver_name')) {
                $query->addSelect('trips.driver_name');
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('trips', 'route')) {
                $query->addSelect('trips.route as destination');
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('trips', 'destination')) {
                $query->addSelect('trips.destination');
            }
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
