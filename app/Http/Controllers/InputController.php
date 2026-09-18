<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flock;
use App\Models\Coop;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\Mortality;
use App\Models\HealthTreatment;
use App\Models\FarmStock;
use App\Services\OutboundIntegrationService;
use Carbon\Carbon;

class InputController extends Controller
{
    /**
     * Halaman Utama Input Mobile
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $date = $request->query('date', $today);
        $carbonDate = Carbon::parse($date);
        $type = $request->query('type', 'produksi'); // produksi, pakan, mortalitas, obat

        // Data Kloter & Blok Kandang
        $flocks = Flock::with('coops')->where('is_active', true)->get();
        $coops = Coop::where('is_active', true)->get();
        $totalChickens = (int) $coops->sum('active_chickens');

        // Ringkasan Stok Telur Real-time
        $eggSummary = OutboundIntegrationService::getEggOutboundSummary();
        $telurStokSaatIni = $eggSummary['current_stock_peti'];
        $telurMasukHariIni = (float) EggProduction::whereDate('date', $date)->sum('crates_count');
        $telurKeluarHariIni = (float) FarmStock::where('category', 'telur')
            ->where('type', 'keluar')
            ->whereDate('date', $date)
            ->sum('quantity');
        $telurTerjualKg = (float) $eggSummary['kg_sold'];

        // Ringkasan Stok Pakan Real-time
        $feedSummary = OutboundIntegrationService::getFeedOutboundSummary();
        $pakanStokKg = (float) $feedSummary['current_stock_kg'];
        $pakanPemakaianHariIni = (float) FeedConsumption::whereDate('date', $date)->sum('quantity_kg');

        // Ringkasan Populasi Real-time
        $kloter1Pop = (int) ($flocks->where('code', 'K1')->first() ? $flocks->where('code', 'K1')->first()->coops->sum('active_chickens') : 2249);
        $kloter2Pop = (int) ($flocks->where('code', 'K2')->first() ? $flocks->where('code', 'K2')->first()->coops->sum('active_chickens') : 1768);
        $mortalitasHariIni = (int) Mortality::whereIn('type', ['mati', 'afkir'])->whereDate('date', $date)->sum('count');
        $totalKarantinaSaatIni = \App\Models\Quarantine::getCurrentCount();

        // Ringkasan Stok Obat Real-time
        $medicines = [
            [
                'id' => 1,
                'name' => 'Vitamin B Complex + Electrolyte',
                'category' => 'Vitamin',
                'stock' => 10,
                'unit' => 'Botol',
                'dosage' => '1 g / 2 L air',
                'application' => 'Air minum pagi',
                'schedule' => '2×/minggu / cuaca panas',
            ],
            [
                'id' => 2,
                'name' => 'ND IB Vaccine',
                'category' => 'Vaksin',
                'stock' => 6,
                'unit' => 'Botol',
                'dosage' => '1.000–2.000 dosis/botol',
                'application' => 'Tetes mata / air minum',
                'schedule' => 'Umur 4 · 16 · 24 · 40 minggu',
            ],
            [
                'id' => 3,
                'name' => 'ND Lasota',
                'category' => 'Vaksin',
                'stock' => 4,
                'unit' => 'Botol',
                'dosage' => '1 botol / 1.000 ekor',
                'application' => 'Tetes mata',
                'schedule' => 'Umur 18–20 minggu (Booster)',
            ],
            [
                'id' => 4,
                'name' => 'Calcium & Mineral Premix Layer',
                'category' => 'Mineral / Premix',
                'stock' => 15,
                'unit' => 'Kg',
                'dosage' => '2 kg / 100 kg pakan',
                'application' => 'Campur pakan',
                'schedule' => 'Setiap hari fase bertelur',
            ],
        ];

        // Format tanggal Indonesia
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $formattedDate = "{$carbonDate->day} " . ($bulanIndo[$carbonDate->month] ?? $carbonDate->format('F')) . " {$carbonDate->year}";

        // Ambil standar pakan berdasarkan umur (dynamic)
        $farmCondition = \App\Services\ProductionStandardService::getActiveFarmCondition($date);
        $coopStandards = $farmCondition['coop_standards'];
        $dynamicAges = $farmCondition['dynamic_ages'] ?? [];

        foreach ($coops as $c) {
            if (isset($dynamicAges[$c->id])) {
                $c->chicken_age_weeks = $dynamicAges[$c->id];
            }
        }
        foreach ($flocks as $f) {
            foreach ($f->coops as $fc) {
                if (isset($dynamicAges[$fc->id])) {
                    $fc->chicken_age_weeks = $dynamicAges[$fc->id];
                }
            }
        }

        return view('input.index', compact(
            'flocks', 'coops', 'totalChickens', 'type', 'date', 'formattedDate',
            'telurStokSaatIni', 'telurMasukHariIni', 'telurKeluarHariIni', 'telurTerjualKg',
            'pakanStokKg', 'pakanPemakaianHariIni',
            'kloter1Pop', 'kloter2Pop', 'mortalitasHariIni', 'totalKarantinaSaatIni',
            'medicines', 'farmCondition', 'coopStandards'
        ));
    }
}
