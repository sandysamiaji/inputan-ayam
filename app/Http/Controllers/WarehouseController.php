<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FarmStock;
use App\Models\User;
use App\Models\Coop;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Services\OutboundIntegrationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
    public function index()
    {
        $user = Auth::user() ?? User::where('role', 'user')->orWhere('username', 'petugas')->first() ?? User::first();

        // 1. Gudang Telur (Terintegrasi Penjualan nochifram)
        $eggSummary = OutboundIntegrationService::getEggOutboundSummary();
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
        $feedSummary = OutboundIntegrationService::getFeedOutboundSummary();
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

        // 3. Gudang Obat, Vaksin & Vitamin (Satuan: Item / Botol) - Bisa minus jika keluar melebihi masuk
        $obatMasuk = (float) FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'masuk')->sum('quantity');
        $obatKeluarManual = (float) FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'keluar')->sum('quantity');
        
        $healthTreatments = \App\Models\HealthTreatment::all();
        $obatKeluarKandang = 0;
        foreach ($healthTreatments as $ht) {
            $val = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
            if ($val == 0) $val = 1;
            $obatKeluarKandang += $val;
        }
        
        $obatKeluar = $obatKeluarManual + $obatKeluarKandang;
        $obatStok = round($obatMasuk - $obatKeluar, 1);

        // Mutasi stok internal terbaru
        $recentTransactions = FarmStock::with('user')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Transaksi penjualan terbaru dari nochifram
        $recentSales = OutboundIntegrationService::getSalesTransactions(null, null, null, 6);

        return view('warehouse.index', compact(
            'user',
            'telurMasuk', 'telurMasukButir', 'telurMasukKg', 'telurKeluar', 'telurKeluarKg', 'telurKeluarEggs', 'telurStok', 'telurStokKgTotal', 'telurStokButir', 'telurPetiSold', 'telurKgSold', 'telurRevenue',
            'pakanMasuk', 'pakanMasukKarung', 'pakanKeluar', 'pakanTotalKarungKeluar', 'pakanStok', 'pakanStokKarung', 'pakanKarungSold', 'pakanKgSold', 'pakanConsumptionKg', 'pakanConsumptionKarung', 'pakanRevenue',
            'obatMasuk', 'obatKeluar', 'obatStok',
            'recentTransactions', 'recentSales'
        ));
    }

    /**
     * 2. Sub-halaman Gudang Telur
     */
    public function telur(Request $request)
    {
        $user = Auth::user() ?? User::first();
        $tab = $request->query('tab', 'semua');
        $search = $request->query('q');

        $query = FarmStock::with('user')->where('category', 'telur');

        if ($tab === 'masuk') {
            $query->where('type', 'masuk');
        } elseif ($tab === 'keluar') {
            $query->where('type', 'keluar');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ringkasan Telur Terintegrasi Penjualan nochifram
        $eggSummary = OutboundIntegrationService::getEggOutboundSummary();
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
        $salesList = OutboundIntegrationService::getSalesTransactions('telur', null, null, 30);
        $tripList = OutboundIntegrationService::getTripOutbounds('telur', 10);

        $coops = Coop::where('is_active', true)->get();

        return view('warehouse.telur', compact(
            'user', 'items', 'tab', 'search', 
            'totalMasuk', 'totalMasukKg', 'totalKeluar', 'totalKeluarKg', 'stokSaatIni', 'stokSaatIniKg', 'stokSaatIniButir',
            'petiSold', 'kgSold', 'totalRevenue', 'transactionCount', 'totalEggsCount',
            'salesList', 'tripList', 'coops'
        ));
    }

    /**
     * 3. Sub-halaman Gudang Pakan
     */
    public function pakan(Request $request)
    {
        $user = Auth::user() ?? User::first();
        $tab = $request->query('tab', 'semua');
        $search = $request->query('q');

        $query = FarmStock::with('user')->where('category', 'pakan');

        if ($tab === 'masuk') {
            $query->where('type', 'masuk');
        } elseif ($tab === 'keluar') {
            $query->where('type', 'keluar');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ringkasan Pakan Terintegrasi Konsumsi Kandang & Penjualan Luar nochifram
        $feedSummary = OutboundIntegrationService::getFeedOutboundSummary();
        $totalMasuk = $feedSummary['purchased_kg'];
        $totalKeluar = $feedSummary['total_keluar_kg'];
        $stokSaatIni = $feedSummary['current_stock_kg'];
        $currentStockKarung = $feedSummary['current_stock_karung'];
        $karungSold = $feedSummary['karung_sold'];
        $kgSold = $feedSummary['kg_sold'];
        $soldRevenue = $feedSummary['total_revenue'];
        $consumptionKg = $feedSummary['consumption_kg'];
        $consumptionKarung = $feedSummary['consumption_karung'];
        $purchasedKarung = $feedSummary['purchased_karung'];

        // Data Penjualan Pakan & Trip Pakan dari nochifram
        $salesList = OutboundIntegrationService::getSalesTransactions('pakan', null, null, 20);
        $tripList = OutboundIntegrationService::getTripOutbounds('pakan', 10);

        $coops = Coop::where('is_active', true)->get();

        return view('warehouse.pakan', compact(
            'user', 'items', 'tab', 'search', 
            'totalMasuk', 'totalKeluar', 'stokSaatIni', 'currentStockKarung',
            'karungSold', 'kgSold', 'soldRevenue', 'consumptionKg', 'consumptionKarung', 'purchasedKarung',
            'salesList', 'tripList', 'coops'
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

        $query = FarmStock::with('user')->whereIn('category', ['obat', 'vaksin', 'vitamin']);

        if ($tab === 'masuk') {
            $query->where('type', 'masuk');
        } elseif ($tab === 'keluar') {
            $query->where('type', 'keluar');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ringkasan Obat, Vaksin & Vitamin
        $totalMasuk = (float) FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'masuk')->sum('quantity');
        $totalKeluarManual = (float) FarmStock::whereIn('category', ['obat', 'vaksin', 'vitamin'])->where('type', 'keluar')->sum('quantity');
        
        $healthTreatments = \App\Models\HealthTreatment::all();
        $obatKeluarKandang = 0;
        foreach ($healthTreatments as $ht) {
            $val = (float) preg_replace('/[^0-9.]/', '', $ht->dosage);
            if ($val == 0) $val = 1;
            $obatKeluarKandang += $val;
        }
        
        $totalKeluar = $totalKeluarManual + $obatKeluarKandang;
        $stokSaatIni = round($totalMasuk - $totalKeluar, 1);

        $coops = Coop::where('is_active', true)->get();

        return view('warehouse.obat', compact(
            'user', 'items', 'tab', 'search', 'totalMasuk', 'totalKeluar', 'stokSaatIni', 'coops'
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
        $stock = FarmStock::findOrFail($id);

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'type' => 'required|in:masuk,keluar',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'date' => 'required|date',
            'time' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (!empty($validated['time'])) {
            $createdAt = Carbon::parse($validated['date']);
            $timeParts = explode(':', $validated['time']);
            $createdAt->setTime((int) ($timeParts[0] ?? 0), (int) ($timeParts[1] ?? 0));
            $stock->created_at = $createdAt;
        }

        $stock->item_name = $validated['item_name'];
        $stock->type = $validated['type'];
        $stock->quantity = $validated['quantity'];
        $stock->unit = $validated['unit'];
        $stock->date = $validated['date'];
        $stock->source = $validated['source'] ?? null;
        $stock->notes = $validated['notes'] ?? null;
        $stock->save();

        return back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    /**
     * Toggle status aktif/nonaktif data secara aman tanpa mengubah skema DB
     */
    public function toggleStatus($id)
    {
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
        $stock = FarmStock::findOrFail($id);
        $category = $stock->category;
        $stock->delete();

        return back()->with('success', 'Data transaksi gudang berhasil dihapus!');
    }
}
