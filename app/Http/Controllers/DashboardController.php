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
use App\Models\FarmStock;
use App\Models\Sale;
use App\Models\User;
use App\Models\Quarantine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $eggProdRecords = EggProduction::with(['coop', 'user'])->whereDate('date', $selectedDate)->get();
        $totalEggCrates = (float) $eggProdRecords->sum('crates_count');
        $totalEggKg = (float) $eggProdRecords->sum('weight_kg');
        $totalEggCount = (int) $eggProdRecords->sum('total_eggs');
        $brokenEggCount = (int) $eggProdRecords->sum('broken_eggs');
        $goodEggCount = (int) $eggProdRecords->sum('good_eggs');

        // 2. Ringkasan Pemakaian Pakan Hari Ini
        $feedConsRecords = FeedConsumption::with(['coop', 'user'])->whereDate('date', $selectedDate)->get();
        $totalFeedKg = (float) $feedConsRecords->sum('quantity_kg');

        // 3. Ringkasan Mortalitas Hari Ini
        $mortalityRecords = Mortality::with(['coop', 'user'])->whereDate('date', $selectedDate)->get();
        $matiHariIni = (int) $mortalityRecords->where('type', 'mati')->sum('count');
        $afkirHariIni = (int) $mortalityRecords->where('type', 'afkir')->sum('count');
        $totalMortalityCount = $matiHariIni + $afkirHariIni;

        // 3b. Ringkasan Karantina Hari Ini & Total Saat Ini
        $currentQuarantineCount = Quarantine::getCurrentCount();
        $quarantineRecords = Quarantine::getRecordsByDate($selectedDate);
        $todaySickCount = (int) $quarantineRecords->where('status', 'sakit')->sum('count');
        $todayRecoveredCount = (int) $quarantineRecords->where('status', 'sembuh')->sum('count');

        // 4. Ringkasan Berat Badan Terkini
        $latestWeight = WeightSample::whereDate('date', '<=', $selectedDate)
            ->latest('date')
            ->first();
        $averageWeightKg = $latestWeight ? (float) $latestWeight->average_weight_kg : 1.620;
        $weightSampleRecords = WeightSample::with(['coop', 'user'])->whereDate('date', $selectedDate)->get();

        // 5. Ringkasan Vaksin & Obat Hari Ini
        $healthTreatments = HealthTreatment::with(['coop', 'user'])->whereDate('date', $selectedDate)->get();
        $totalHealthActivities = $healthTreatments->count();

        // 6. Aktivitas Terakhir (Timeline Gabungan: Produksi, Pakan, Mortalitas, Obat, Gudang, & Penjualan)
        $farmStockRecords = FarmStock::with('user')->whereDate('date', $selectedDate)->get();
        $saleRecords = \App\Models\Sale::with(['items', 'user'])->whereDate('date', $selectedDate)->get();

        // Map trip user IDs to usernames for sales transactions
        $allSaleTripIds = $saleRecords->pluck('trip_id')->filter()->unique();
        $tripUsersMap = collect();
        if ($allSaleTripIds->isNotEmpty() && \Illuminate\Support\Facades\Schema::hasTable('trips')) {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $tripUsersMap = DB::table('trips')
                    ->leftJoin('users', 'trips.user_id', '=', 'users.id')
                    ->whereIn('trips.id', $allSaleTripIds)
                    ->select('trips.id as trip_id', DB::raw("COALESCE(users.username, users.name) as uname"))
                    ->pluck('uname', 'trip_id');
            }
        }

        $getUserName = function($userObj) {
            if (!$userObj) return null;
            $uname = $userObj->username ?: $userObj->name;
            if (!$uname) return null;
            return str_starts_with($uname, '@') ? $uname : '@' . $uname;
        };

        $getTripUserName = function($tripId) use ($tripUsersMap) {
            if (!$tripId || !isset($tripUsersMap[$tripId])) return null;
            $uname = $tripUsersMap[$tripId];
            if (!$uname) return null;
            return str_starts_with($uname, '@') ? $uname : '@' . $uname;
        };

        $activities = collect();

        foreach ($eggProdRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->time ? substr($item->time, 0, 5) : ($item->created_at ? $item->created_at->format('H:i') : '00:00');
            $valParts = [];
            if ($item->crates_count > 0) {
                $valParts[] = number_format($item->crates_count, 0, ',', '.') . ' Peti';
            }
            if ($item->weight_kg > 0) {
                $kgFormatted = $item->weight_kg == floor($item->weight_kg) ? number_format($item->weight_kg, 0, ',', '.') : number_format($item->weight_kg, 1, ',', '.');
                $valParts[] = $kgFormatted . ' Kg';
            }
            $valStr = !empty($valParts) ? '+' . implode(' & ', $valParts) : '+' . number_format($item->crates_count, 0, ',', '.') . ' Peti';

            $activities->push([
                'id' => 'egg_' . $item->id,
                'category' => 'egg',
                'title' => 'Produksi Telur',
                'subtitle' => $coopName . ($item->notes ? ' • ' . $item->notes : ''),
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => $valStr,
                'subvalue' => number_format($item->total_eggs, 0, ',', '.') . ' Butir',
                'user_username' => $getUserName($item->user),
                'trip_username' => null,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($feedConsRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->time ? substr($item->time, 0, 5) : ($item->created_at ? $item->created_at->format('H:i') : '00:00');
            $activities->push([
                'id' => 'feed_' . $item->id,
                'category' => 'feed',
                'title' => 'Pemakaian Pakan',
                'subtitle' => $coopName . ' • ' . $item->feed_name,
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => '-' . number_format($item->quantity_kg, 0, ',', '.') . ' Kg',
                'subvalue' => $item->feeding_time,
                'user_username' => $getUserName($item->user),
                'trip_username' => null,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($mortalityRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->time ? substr($item->time, 0, 5) : ($item->created_at ? $item->created_at->format('H:i') : '00:00');
            $activities->push([
                'id' => 'mort_' . $item->id,
                'category' => 'mortality',
                'title' => 'Mortalitas Ayam',
                'subtitle' => $coopName . ($item->cause ? ' • ' . $item->cause : ''),
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => '-' . $item->count . ' Ekor',
                'subvalue' => ucfirst($item->type),
                'user_username' => $getUserName($item->user),
                'trip_username' => null,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($healthTreatments as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Semua Blok';
            $timeStr = $item->time ? substr($item->time, 0, 5) : ($item->created_at ? $item->created_at->format('H:i') : '00:00');
            $activities->push([
                'id' => 'health_' . $item->id,
                'category' => 'health',
                'title' => ucfirst($item->type) . ' / Obat',
                'subtitle' => $coopName . ' • ' . $item->medicine_name,
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => $item->dosage ?: '1 Kegiatan',
                'subvalue' => $item->application_method,
                'user_username' => $getUserName($item->user),
                'trip_username' => null,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($quarantineRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->time ? substr($item->time, 0, 5) : ($item->created_at ? $item->created_at->format('H:i') : '00:00');
            $isSakit = $item->status === 'sakit';
            $batteryInfo = $item->battery_number ? ' • Baterai ' . $item->battery_number : '';
            $activities->push([
                'id' => 'quarantine_' . $item->id,
                'category' => 'quarantine',
                'title' => $isSakit ? 'Karantina (Ayam Sakit)' : 'Karantina (Ayam Sembuh)',
                'subtitle' => $coopName . $batteryInfo . ($item->cause ? ' • ' . $item->cause : ''),
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => ($isSakit ? '+' : '-') . $item->count . ' Ekor',
                'subvalue' => $isSakit ? 'Masuk Isolasi' : 'Kembali ke Kandang',
                'user_username' => $getUserName($item->user),
                'trip_username' => null,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' ' . ($item->time ?: '00:00:00')),
            ]);
        }

        foreach ($weightSampleRecords as $item) {
            $coopName = $item->coop ? $item->coop->name : 'Kandang';
            $timeStr = $item->created_at ? $item->created_at->format('H:i') : '00:00';
            $batteryInfo = $item->battery_number ? ' • Baterai ' . $item->battery_number : '';
            $eggGramInfo = $item->egg_weight_gram ? ' • Telur ' . $item->egg_weight_gram . 'g' : '';
            $activities->push([
                'id' => 'weight_' . $item->id,
                'category' => 'weight',
                'title' => 'Sampel Ayam (' . $coopName . ')',
                'subtitle' => 'Umur ' . ($item->age_weeks ?: '-') . ' mgg' . $batteryInfo . $eggGramInfo . ($item->notes ? ' • ' . $item->notes : ''),
                'datetime' => $carbonDate->format('d/m/Y') . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => number_format($item->average_weight_kg, 1, ',', '.') . ' Kg',
                'subvalue' => 'Bobot Ayam',
                'user_username' => $getUserName($item->user),
                'trip_username' => null,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : strtotime($item->date . ' 00:00:00'),
            ]);
        }

        foreach ($farmStockRecords as $item) {
            $timeStr = $item->created_at ? $item->created_at->format('H:i') : '00:00';
            $itemDateStr = $item->date ? $item->date->format('d/m/Y') : $carbonDate->format('d/m/Y');
            $activities->push([
                'id' => 'stock_' . $item->id,
                'category' => $item->type === 'masuk' ? 'stock_masuk' : 'stock_keluar',
                'title' => 'Mutasi Gudang (' . ucfirst($item->category) . ')',
                'subtitle' => $item->item_name . ($item->source ? ' • ' . $item->source : ($item->notes ? ' • ' . $item->notes : '')),
                'datetime' => $itemDateStr . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => ($item->type === 'masuk' ? '+' : '-') . number_format($item->quantity, 0, ',', '.') . ' ' . ($item->unit ?: 'Unit'),
                'subvalue' => ($item->type === 'masuk' ? 'Barang Masuk' : 'Barang Keluar') . ($item->source ? ' (' . $item->source . ')' : ''),
                'user_username' => $getUserName($item->user),
                'trip_username' => null,
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : ($item->date ? strtotime($item->date->format('Y-m-d') . ' 00:00:00') : 0),
            ]);
        }

        foreach ($saleRecords as $item) {
            $timeStr = $item->created_at ? $item->created_at->format('H:i') : '00:00';
            $itemDateStr = $item->date ? $item->date->format('d/m/Y') : $carbonDate->format('d/m/Y');
            $firstItem = $item->items->first();
            $firstItemName = $firstItem ? $firstItem->item_name : '';
            $subTitleStr = ($item->customer_name ?: 'Pelanggan') . ($firstItemName ? ' • ' . $firstItemName : '');
            
            $qtyDisplayParts = [];
            if ($item->items->count() > 0) {
                $groupedItems = [];
                foreach ($item->items as $saleItem) {
                    $unit = $saleItem->unit ?: 'Item';
                    if (!isset($groupedItems[$unit])) {
                        $groupedItems[$unit] = 0;
                    }
                    $groupedItems[$unit] += $saleItem->quantity;
                }
                
                $isFirst = true;
                foreach ($groupedItems as $unit => $qty) {
                    $formattedQty = number_format($qty, 0, ',', '.');
                    if ($isFirst) {
                        $qtyDisplayParts[] = '-' . $formattedQty . ' ' . $unit;
                        $isFirst = false;
                    } else {
                        // To match "-8 Peti dan 2 karung" we omit the negative sign on subsequent items,
                        // or include it based on the first prompt "-8 Peti dan -2 karung". Let's use the latter for consistency of negative meaning deduction.
                        // Actually, I'll just use the exact format requested: "-8 Peti dan 2 karung"
                        $qtyDisplayParts[] = $formattedQty . ' ' . $unit;
                    }
                }
            }
            
            $qtyDisplay = !empty($qtyDisplayParts) ? implode(' dan ', $qtyDisplayParts) : '-';

            $activities->push([
                'id' => 'sale_' . $item->id,
                'category' => 'sale',
                'title' => 'Penjualan ' . ucfirst($item->category ?: 'Telur'),
                'subtitle' => $subTitleStr,
                'datetime' => $itemDateStr . ' ' . $timeStr,
                'time' => $timeStr,
                'value' => $qtyDisplay,
                'subvalue' => ($item->invoice_no ? '#' . $item->invoice_no : '') . ($item->payment_status ? ($item->invoice_no ? ' • ' : '') . ucfirst($item->payment_status) : ''),
                'user_username' => $getUserName($item->user),
                'trip_username' => $getTripUserName($item->trip_id),
                'raw_timestamp' => $item->created_at ? $item->created_at->timestamp : ($item->date ? strtotime($item->date->format('Y-m-d') . ' 00:00:00') : 0),
            ]);
        }

        // Fallback untuk aktivitas jika kosong dihapus agar filter tanggal berfungsi semestinya.
        $activities = $activities->sortByDesc('raw_timestamp')->take(15)->values();

        // 7. Data Master untuk modal quick action
        $flocks = Flock::with(['coops' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();

        $coops = Coop::with('flock')->where('is_active', true)->get();

        // 8. Integrasi Data Gudang & Penjualan dari nochifram
        $eggSummary = \App\Services\OutboundIntegrationService::getEggOutboundSummary();
        $feedSummary = \App\Services\OutboundIntegrationService::getFeedOutboundSummary();

        $totalEggProducedAllTime = $eggSummary['total_produced_crates'];
        $totalEggProducedKgAllTime = (float) $eggSummary['total_produced_kg'];
        $totalEggSoldAllTime = $eggSummary['peti_sold'];
        $eggKgSold = $eggSummary['kg_sold'];
        $currentEggStockCrates = $eggSummary['current_stock_peti'];
        $currentEggStockKg = (float) $eggSummary['current_stock_kg_total'];

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

        // Hitung HD & Pakan aktual hari ini per coop dan per flock (HANYA jika telur/pakan sudah diinput)
        $coopHdData = [];
        $coopEggTodayData = [];
        $coopFeedTodayData = [];
        $coopFeedPagiData = [];
        $coopFeedSoreData = [];
        $coopFeedHasPagiData = [];
        $coopFeedHasSoreData = [];
        $coopFeedPagiUserData = [];
        $coopFeedSoreUserData = [];
        $coopEggCratesData = [];
        $coopEggKgData = [];
        $coopEggUserInputData = [];
        $coopFeedUserInputData = [];

        foreach ($coops as $c) {
            $todayEgg = (int) $eggProdRecords->where('coop_id', $c->id)->sum('total_eggs');
            $coopEggTodayData[$c->id] = $todayEgg;
            if ($todayEgg > 0 && $c->active_chickens > 0) {
                $coopHdData[$c->id] = round(($todayEgg / $c->active_chickens) * 100, 1);
            } else {
                $coopHdData[$c->id] = null; // Belum diinput oleh user
            }

            // Realisasi Pakan per Blok
            $cFeeds = $feedConsRecords->where('coop_id', $c->id);
            $coopFeedTodayData[$c->id] = (float) $cFeeds->sum('quantity_kg');

            // Pisahkan input pakan Pagi dan Sore berdasarkan feeding_time (case-insensitive)
            $pagiFeeds = $cFeeds->filter(function ($item) {
                return stripos($item->feeding_time ?? '', 'pagi') !== false;
            });
            $soreFeeds = $cFeeds->filter(function ($item) {
                return stripos($item->feeding_time ?? '', 'sore') !== false;
            });

            $coopFeedHasPagiData[$c->id] = $pagiFeeds->isNotEmpty();
            $coopFeedHasSoreData[$c->id] = $soreFeeds->isNotEmpty();
            $coopFeedPagiData[$c->id] = (float) $pagiFeeds->sum('quantity_kg');
            $coopFeedSoreData[$c->id] = (float) $soreFeeds->sum('quantity_kg');

            // Penginput Pagi
            $pagiUsers = $pagiFeeds->map(function ($f) {
                return $f->user ? ($f->user->username ?: $f->user->name) : null;
            })->filter()->unique()->values()->all();
            $coopFeedPagiUserData[$c->id] = !empty($pagiUsers) ? implode(', ', $pagiUsers) : null;

            // Penginput Sore
            $soreUsers = $soreFeeds->map(function ($f) {
                return $f->user ? ($f->user->username ?: $f->user->name) : null;
            })->filter()->unique()->values()->all();
            $coopFeedSoreUserData[$c->id] = !empty($soreUsers) ? implode(', ', $soreUsers) : null;

            // Hitung realisasi Peti & Kg per blok (10 kg = 1 Peti, Peti integer)
            $coopProds = $eggProdRecords->where('coop_id', $c->id);
            $cKgRaw = (float) $coopProds->sum('weight_kg');
            $cPetiRaw = (int) round($coopProds->sum('crates_count'));
            if ($cKgRaw >= 10) {
                $extraP = (int) floor($cKgRaw / 10);
                $cPetiRaw += $extraP;
                $cKgRaw = round($cKgRaw - ($extraP * 10), 1);
            } else {
                $cKgRaw = round($cKgRaw, 1);
            }
            $coopEggCratesData[$c->id] = $cPetiRaw;
            $coopEggKgData[$c->id] = $cKgRaw;

            $firstEgg = $coopProds->first();
            $coopEggUserInputData[$c->id] = $firstEgg && $firstEgg->user ? ($firstEgg->user->username ?: $firstEgg->user->name) : null;

            $allFeedUsers = $cFeeds->map(function ($f) {
                return $f->user ? ($f->user->username ?: $f->user->name) : null;
            })->filter()->unique()->values()->all();
            $coopFeedUserInputData[$c->id] = !empty($allFeedUsers) ? implode(', ', $allFeedUsers) : null;
        }

        // Data Sampel Berat Badan Terkini Per 6 Blok (Mendukung hingga 3 Sampel per Blok & Rata-rata)
        WeightSample::ensureColumnsExist();
        $coopWeightData = [];
        $coopWeightDetails = [];
        foreach ($coops as $c) {
            $latestSampleDate = WeightSample::where('coop_id', $c->id)
                ->whereDate('date', '<=', $selectedDate)
                ->latest('date')
                ->value('date');

            if ($latestSampleDate) {
                $samplesQuery = WeightSample::with('user')
                    ->where('coop_id', $c->id)
                    ->whereDate('date', $latestSampleDate)
                    ->orderBy('sample_index', 'asc')
                    ->orderBy('id', 'asc')
                    ->take(3)
                    ->get();

                if ($samplesQuery->count() < 3) {
                    $allRecent = WeightSample::with('user')
                        ->where('coop_id', $c->id)
                        ->whereDate('date', '<=', $selectedDate)
                        ->latest('date')
                        ->latest('id')
                        ->take(3)
                        ->get()
                        ->sortBy(function($item) {
                            return $item->sample_index ?? $item->id;
                        })
                        ->values();
                    if ($allRecent->count() > $samplesQuery->count()) {
                        $samplesQuery = $allRecent;
                    }
                }

                $samplesList = [];
                $totalW = 0;
                $countW = 0;
                $totalE = 0;
                $countE = 0;
                $batteryArr = [];
                $latestUser = null;
                $notesArr = [];

                foreach ($samplesQuery as $idx => $s) {
                    $wVal = (float) $s->average_weight_kg;
                    $eVal = $s->egg_weight_gram !== null ? (float) $s->egg_weight_gram : null;
                    if ($wVal > 0) {
                        $totalW += $wVal;
                        $countW++;
                    }
                    if ($eVal !== null && $eVal > 0) {
                        $totalE += $eVal;
                        $countE++;
                    }
                    if ($s->battery_number) {
                        $batteryArr[] = $s->battery_number;
                    }
                    if ($s->notes) {
                        $notesArr[] = $s->notes;
                    }
                    if (!$latestUser && $s->user) {
                        $latestUser = $s->user->username ?: $s->user->name;
                    }

                    $samplesList[] = [
                        'id' => $s->id,
                        'sample_index' => $s->sample_index ?? ($idx + 1),
                        'battery_number' => $s->battery_number,
                        'weight_kg' => $wVal,
                        'egg_weight_gram' => $eVal,
                        'date' => $s->date ? Carbon::parse($s->date)->format('d/m/Y') : null,
                        'user' => $s->user ? ($s->user->username ?: $s->user->name) : null,
                    ];
                }

                $avgWeight = $countW > 0 ? round($totalW / $countW, 2) : 0;
                $avgEgg = $countE > 0 ? round($totalE / $countE, 1) : null;

                // Hitung Keseragaman (Uniformity %)
                $uniformity = null;
                if ($countW >= 2 && $avgWeight > 0) {
                    $minBound = $avgWeight * 0.90;
                    $maxBound = $avgWeight * 1.10;
                    $inRange = 0;
                    foreach ($samplesList as $sl) {
                        if ($sl['weight_kg'] >= $minBound && $sl['weight_kg'] <= $maxBound) {
                            $inRange++;
                        }
                    }
                    $uniformity = round(($inRange / $countW) * 100, 1);
                }

                $firstSample = $samplesQuery->first();
                $coopWeightData[$c->id] = $avgWeight;
                $coopWeightDetails[$c->id] = [
                    'weight_kg' => $avgWeight,
                    'egg_weight_gram' => $avgEgg,
                    'battery_number' => !empty($batteryArr) ? implode(' • ', $batteryArr) : ($firstSample->battery_number ?? null),
                    'battery_list' => $batteryArr,
                    'age_weeks' => $firstSample->age_weeks ?? $c->chicken_age_weeks,
                    'date' => $firstSample->date ? Carbon::parse($firstSample->date)->format('d/m/Y') : null,
                    'notes' => !empty($notesArr) ? implode('; ', array_unique($notesArr)) : $firstSample->notes,
                    'user' => $latestUser,
                    'samples' => $samplesList,
                    'sample_count' => count($samplesList),
                    'uniformity_percentage' => $uniformity,
                ];
            } else {
                $coopWeightData[$c->id] = null;
                $coopWeightDetails[$c->id] = null;
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
            'totalEggKg',
            'totalEggCount',
            'brokenEggCount',
            'goodEggCount',
            'totalFeedKg',
            'totalMortalityCount',
            'matiHariIni',
            'afkirHariIni',
            'averageWeightKg',
            'totalHealthActivities',
            'activities',
            'flocks',
            'coops',
            'totalEggProducedAllTime',
            'totalEggProducedKgAllTime',
            'totalEggSoldAllTime',
            'eggKgSold',
            'currentEggStockCrates',
            'currentEggStockKg',
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
            'feedSummary',
            'totalFarmPakanKg',
            'totalFarmKarungStr',
            'kgPerKarung',
            'coopHdData',
            'flockHdData',
            'coopEggTodayData',
            'coopFeedTodayData',
            'coopFeedPagiData',
            'coopFeedSoreData',
            'coopFeedHasPagiData',
            'coopFeedHasSoreData',
            'coopFeedPagiUserData',
            'coopFeedSoreUserData',
            'coopEggCratesData',
            'coopEggKgData',
            'coopEggUserInputData',
            'coopFeedUserInputData',
            'currentQuarantineCount',
            'todaySickCount',
            'todayRecoveredCount',
            'coopWeightData',
            'coopWeightDetails'
        ));
    }

    /**
     * Simpan Produksi Telur Cepat
     */
    public function storeEggProduction(Request $request)
    {
        // Jika good_eggs tidak dikirim tetapi total_eggs dikirim (misal modal panen cepat di flocks)
        if (!$request->has('good_eggs') && $request->has('total_eggs')) {
            $tot = (int) $request->input('total_eggs', 0);
            $brk = (int) $request->input('broken_eggs', 0);
            $abn = (int) $request->input('abnormal_eggs', 0);
            $request->merge([
                'good_eggs' => max(0, $tot - $brk - $abn)
            ]);
        }

        $validated = $request->validate([
            'coop_id' => 'required|exists:coops,id',
            'good_eggs' => 'required|numeric|min:0',
            'broken_eggs' => 'nullable|numeric|min:0',
            'abnormal_eggs' => 'nullable|numeric|min:0',
            'crates_count' => 'nullable|numeric|min:0',
            'weight_kg' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'time' => 'nullable',
            'notes' => 'nullable|string',
        ]);

        $coop = Coop::findOrFail($validated['coop_id']);
        $targetDate = $validated['date'] ?? Carbon::today()->toDateString();

        // Penjagaan ganda: Cek apakah produksi telur untuk Blok & Tanggal ini sudah pernah diinput
        $existingEgg = EggProduction::where('coop_id', $coop->id)
            ->whereDate('date', $targetDate)
            ->first();

        if ($existingEgg) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Data produksi telur untuk {$coop->name} pada tanggal " . Carbon::parse($targetDate)->format('d-m-Y') . " sudah pernah diinput sebelumnya!",
                ], 422);
            }
            return redirect()->back()->with('error', "Data produksi telur untuk {$coop->name} pada tanggal " . Carbon::parse($targetDate)->format('d-m-Y') . " sudah pernah diinput sebelumnya!");
        }

        $goodEggs = (int) $validated['good_eggs'];
        $brokenEggs = (int) ($validated['broken_eggs'] ?? 0);
        $abnormalEggs = (int) ($validated['abnormal_eggs'] ?? 0);
        $totalEggs = $goodEggs + $brokenEggs + $abnormalEggs;

        $weightKg = isset($validated['weight_kg']) && (float) $validated['weight_kg'] > 0
            ? (float) $validated['weight_kg']
            : null;

        // Jika peti diisi > 0 gunakan nilai tersebut
        // Jika tidak diisi atau 0:
        // - jika weight_kg diisi, biarkan peti 0
        // - jika weight_kg juga tidak diisi, estimasikan otomatis
        if (isset($validated['crates_count']) && (float) $validated['crates_count'] > 0) {
            $cratesCount = (float) $validated['crates_count'];
        } elseif ($weightKg !== null) {
            $cratesCount = 0;
        } else {
            $cratesCount = round($totalEggs / 25, 0);
        }

        // Aturan Konversi: Peti selalu bulat (tanpa koma), setiap 10 kg otomatis menjadi 1 Peti
        $cratesCount = (int) round($cratesCount);
        if ($weightKg !== null && $weightKg >= 10) {
            $extraPeti = (int) floor($weightKg / 10);
            $cratesCount += $extraPeti;
            $weightKg = round($weightKg - ($extraPeti * 10), 1);
            if ($weightKg <= 0) {
                $weightKg = null;
            }
        } elseif ($weightKg !== null) {
            $weightKg = round($weightKg, 1);
            if ($weightKg <= 0) {
                $weightKg = null;
            }
        }

        $dataToInsert = [
            'flock_id' => $coop->flock_id,
            'coop_id' => $coop->id,
            'user_id' => Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id'),
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
            'time' => $validated['time'] ?? Carbon::now()->format('H:i:s'),
            'total_eggs' => $totalEggs,
            'broken_eggs' => $brokenEggs,
            'good_eggs' => $goodEggs,
            'crates_count' => $cratesCount,
            'weight_kg' => $weightKg,
            'notes' => $validated['notes'] ?? null,
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('egg_productions', 'abnormal_eggs')) {
            $dataToInsert['abnormal_eggs'] = $abnormalEggs;
        }

        $eggProduction = EggProduction::create($dataToInsert);

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
        $targetDate = $validated['date'] ?? Carbon::today()->toDateString();
        $feedingTime = $validated['feeding_time'] ?? 'Pagi';

        if (!empty($validated['coop_id'])) {
            $coop = Coop::find($validated['coop_id']);
            $flockId = $coop ? $coop->flock_id : null;

            // Penjagaan ganda: Cek apakah pemakaian pakan untuk Blok, Tanggal & Waktu ini sudah pernah diinput
            $existingFeed = FeedConsumption::where('coop_id', $validated['coop_id'])
                ->whereDate('date', $targetDate)
                ->whereRaw('LOWER(feeding_time) = ?', [strtolower(trim($feedingTime))])
                ->first();

            if ($existingFeed) {
                $coopName = $coop ? $coop->name : 'Blok';
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Pemakaian pakan ({$feedingTime}) untuk {$coopName} pada tanggal " . Carbon::parse($targetDate)->format('d-m-Y') . " sudah pernah diinput sebelumnya!",
                    ], 422);
                }
                return redirect()->back()->with('error', "Pemakaian pakan ({$feedingTime}) untuk {$coopName} pada tanggal " . Carbon::parse($targetDate)->format('d-m-Y') . " sudah pernah diinput sebelumnya!");
            }
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
     * Simpan Mortalitas / Karantina Cepat
     */
    public function storeMortality(Request $request)
    {
        $validated = $request->validate([
            'coop_id' => 'required|exists:coops,id',
            'count' => 'required|integer|min:1',
            'type' => 'nullable|string|in:mati,afkir,sakit,sembuh',
            'cause' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'battery_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $coop = Coop::findOrFail($validated['coop_id']);
        $type = $validated['type'] ?? 'mati';
        $count = (int) $validated['count'];
        $date = $validated['date'] ?? Carbon::today()->toDateString();
        $userId = Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id');
        $batteryNumber = !empty($validated['battery_number']) ? trim($validated['battery_number']) : null;

        if ($type === 'sakit') {
            Quarantine::ensureTableExists();

            // 1. Kurangi populasi aktif kandang
            if ($coop->active_chickens >= $count) {
                $coop->decrement('active_chickens', $count);
            }

            // 2. Simpan ke riwayat karantina
            $record = Quarantine::create([
                'flock_id' => $coop->flock_id,
                'coop_id' => $coop->id,
                'user_id' => $userId,
                'date' => $date,
                'time' => Carbon::now()->format('H:i:s'),
                'battery_number' => $batteryNumber,
                'count' => $count,
                'status' => 'sakit',
                'cause' => $validated['cause'] ?? 'Sakit',
                'notes' => $validated['notes'] ?? null,
            ]);

            $batInfo = $batteryNumber ? " (Baterai: {$batteryNumber})" : '';
            $msg = "{$count} ekor ayam sakit{$batInfo} berhasil dicatat dan dipindahkan ke Karantina!";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'data' => $record,
                ]);
            }
            return redirect()->back()->with('success', $msg);

        } elseif ($type === 'sembuh') {
            Quarantine::ensureTableExists();

            // 1. Tambah kembali populasi aktif kandang
            $coop->increment('active_chickens', $count);

            // 2. Simpan ke riwayat karantina (Status: sembuh)
            $record = Quarantine::create([
                'flock_id' => $coop->flock_id,
                'coop_id' => $coop->id,
                'user_id' => $userId,
                'date' => $date,
                'time' => Carbon::now()->format('H:i:s'),
                'battery_number' => $batteryNumber,
                'count' => $count,
                'status' => 'sembuh',
                'cause' => $validated['cause'] ?? 'Sembuh',
                'notes' => $validated['notes'] ?? null,
            ]);

            $batInfo = $batteryNumber ? " (Baterai: {$batteryNumber})" : '';
            $msg = "{$count} ekor ayam sembuh berhasil dikembalikan ke {$coop->name}{$batInfo}!";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'data' => $record,
                ]);
            }
            return redirect()->back()->with('success', $msg);

        } else {
            // Standar Mortalitas (Mati / Afkir)
            $mortality = Mortality::create([
                'flock_id' => $coop->flock_id,
                'coop_id' => $coop->id,
                'user_id' => $userId,
                'date' => $date,
                'time' => Carbon::now()->format('H:i:s'),
                'count' => $count,
                'type' => $type,
                'cause' => $validated['cause'] ?? 'Wajar',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Kurangi jumlah ayam aktif di blok
            if ($coop->active_chickens >= $count) {
                $coop->decrement('active_chickens', $count);
            }

            $labelType = $type === 'afkir' ? 'Afkir' : 'Kematian';
            $msg = "Data mortalitas ({$labelType}) {$count} ekor berhasil dicatat!";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'data' => $mortality,
                ]);
            }
            return redirect()->back()->with('success', $msg);
        }
    }

    /**
     * Simpan Berat Badan Cepat / Sampel Mingguan
     */
    public function storeWeightSample(Request $request)
    {
        // Pastikan kolom baru sudah ada pada tabel weight_samples
        WeightSample::ensureColumnsExist();

        // Normalisasi format desimal koma (misal: 1,75 atau 1,1) menjadi titik
        $inputData = $request->all();
        foreach (['sample_1_weight', 'sample_1_egg', 'sample_2_weight', 'sample_2_egg', 'sample_3_weight', 'sample_3_egg', 'average_weight_kg', 'egg_weight_gram'] as $k) {
            if (isset($inputData[$k]) && is_string($inputData[$k])) {
                $inputData[$k] = str_replace(',', '.', trim($inputData[$k]));
            }
        }
        if (!empty($inputData['samples']) && is_array($inputData['samples'])) {
            foreach ($inputData['samples'] as $idx => $s) {
                if (isset($s['weight_kg']) && is_string($s['weight_kg'])) {
                    $inputData['samples'][$idx]['weight_kg'] = str_replace(',', '.', trim($s['weight_kg']));
                }
                if (isset($s['egg_weight_gram']) && is_string($s['egg_weight_gram'])) {
                    $inputData['samples'][$idx]['egg_weight_gram'] = str_replace(',', '.', trim($s['egg_weight_gram']));
                }
            }
        }
        $request->merge($inputData);

        $rules = [
            'coop_id' => 'required|exists:coops,id',
            'sample_count' => 'nullable|integer|min:1',
            'age_weeks' => 'nullable|integer',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
            'samples' => 'nullable|array',
            'samples.*.weight_kg' => 'nullable|numeric|min:0.1',
            'samples.*.egg_weight_gram' => 'nullable|numeric|min:0',
            'samples.*.battery_number' => 'nullable|string|max:100',
            // Fallback flat fields
            'sample_1_weight' => 'nullable|numeric|min:0.1',
            'sample_1_egg' => 'nullable|numeric|min:0',
            'sample_1_battery' => 'nullable|string|max:100',
            'sample_2_weight' => 'nullable|numeric|min:0.1',
            'sample_2_egg' => 'nullable|numeric|min:0',
            'sample_2_battery' => 'nullable|string|max:100',
            'sample_3_weight' => 'nullable|numeric|min:0.1',
            'sample_3_egg' => 'nullable|numeric|min:0',
            'sample_3_battery' => 'nullable|string|max:100',
            'average_weight_kg' => 'nullable|numeric|min:0.1',
            'egg_weight_gram' => 'nullable|numeric|min:0',
            'battery_number' => 'nullable|string|max:100',
        ];

        $validated = $request->validate($rules);
        $coop = Coop::findOrFail($validated['coop_id']);
        $date = $validated['date'] ?? Carbon::today()->toDateString();
        $userId = Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id');
        $ageWeeks = $validated['age_weeks'] ?? $coop->chicken_age_weeks;
        $notes = $validated['notes'] ?? null;

        // Kumpulkan sampel dari input (bisa dari array 'samples', flat inputs 'sample_1_...', atau single)
        $parsedSamples = [];

        if (!empty($validated['samples']) && is_array($validated['samples'])) {
            foreach ($validated['samples'] as $idx => $s) {
                if (!empty($s['weight_kg']) && (float)$s['weight_kg'] > 0) {
                    $parsedSamples[] = [
                        'sample_index' => $idx + 1,
                        'battery_number' => !empty($s['battery_number']) ? trim($s['battery_number']) : 'Titik ' . ($idx + 1),
                        'weight_kg' => (float)$s['weight_kg'],
                        'egg_weight_gram' => !empty($s['egg_weight_gram']) ? (float)$s['egg_weight_gram'] : null,
                    ];
                }
            }
        } elseif (!empty($request->input('sample_1_weight')) || !empty($request->input('sample_2_weight')) || !empty($request->input('sample_3_weight'))) {
            for ($i = 1; $i <= 3; $i++) {
                $wKey = "sample_{$i}_weight";
                $bKey = "sample_{$i}_battery";
                $eKey = "sample_{$i}_egg";
                $wVal = $request->input($wKey);
                $eVal = $request->input($eKey);
                if (!empty($wVal) && (float)$wVal > 0) {
                    $parsedSamples[] = [
                        'sample_index' => $i,
                        'battery_number' => $request->input($bKey) ?: "Titik {$i}",
                        'weight_kg' => (float)$wVal,
                        'egg_weight_gram' => (!empty($eVal) || $eVal === '0' || $eVal === 0) ? (float)$eVal : null,
                    ];
                }
            }
        } elseif (!empty($validated['average_weight_kg'])) {
            $parsedSamples[] = [
                'sample_index' => 1,
                'battery_number' => $validated['battery_number'] ?? null,
                'weight_kg' => (float)$validated['average_weight_kg'],
                'egg_weight_gram' => $validated['egg_weight_gram'] ?? null,
            ];
        }

        if (empty($parsedSamples)) {
            return back()->withErrors(['average_weight_kg' => 'Minimal 1 bobot sampel ayam harus diisi!'])->withInput();
        }

        // Hitung rata-rata dan keseragaman (uniformity)
        $countSamples = count($parsedSamples);
        $sumW = array_sum(array_column($parsedSamples, 'weight_kg'));
        $meanW = $countSamples > 0 ? ($sumW / $countSamples) : 0;
        $uniformity = null;
        if ($countSamples >= 2 && $meanW > 0) {
            $minB = $meanW * 0.90;
            $maxB = $meanW * 1.10;
            $inR = 0;
            foreach ($parsedSamples as $ps) {
                if ($ps['weight_kg'] >= $minB && $ps['weight_kg'] <= $maxB) {
                    $inR++;
                }
            }
            $uniformity = round(($inR / $countSamples) * 100, 1);
        }

        $createdRecords = [];
        foreach ($parsedSamples as $sample) {
            $createdRecords[] = WeightSample::create([
                'flock_id' => $coop->flock_id,
                'coop_id' => $coop->id,
                'user_id' => $userId,
                'date' => $date,
                'battery_number' => $sample['battery_number'],
                'sample_index' => $sample['sample_index'],
                'sample_count' => 1,
                'average_weight_kg' => $sample['weight_kg'],
                'egg_weight_gram' => $sample['egg_weight_gram'],
                'uniformity_percentage' => $uniformity,
                'age_weeks' => $ageWeeks,
                'notes' => $notes,
            ]);
        }

        $lastWeight = end($createdRecords);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data {$countSamples} sampel timbang ayam mingguan Blok {$coop->name} berhasil disimpan!",
                'data' => $lastWeight,
                'count' => $countSamples,
                'average_weight_kg' => round($meanW, 2),
            ]);
        }

        return redirect()->route('dashboard')->with('success', "Data {$countSamples} sampel timbang ayam Blok {$coop->name} (Rata-rata: " . number_format($meanW, 2, ',', '.') . " kg) berhasil disimpan!");
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

        $dosage = $validated['dosage'] ?? '1';
        if ($request->filled('unit')) {
            $unit = trim($request->input('unit'));
            if (!empty($unit) && !str_contains(strtolower($dosage), strtolower($unit))) {
                $dosage = trim($dosage . ' ' . $unit);
            }
        } elseif (!preg_match('/[a-zA-Z]/', $dosage)) {
            // Jika hanya angka, coba cocokkan satuan dari katalog master
            $medDetail = \App\Services\MedicineCatalogService::findMedicineByName($validated['medicine_name']);
            if ($medDetail && !empty($medDetail['unit'])) {
                $dosage = trim($dosage . ' ' . $medDetail['unit']);
            } else {
                $dosage = trim($dosage . ' Botol');
            }
        }

        $medType = $validated['type'] ?? 'obat';
        if ($medType === 'all' || empty($medType)) {
            $medDetail = \App\Services\MedicineCatalogService::findMedicineByName($validated['medicine_name']);
            $medType = $medDetail['category_key'] ?? 'obat';
        }

        $health = HealthTreatment::create([
            'flock_id' => $flockId,
            'coop_id' => $validated['coop_id'] ?? null,
            'user_id' => Auth::id() ?? User::where('username', 'petugas')->value('id') ?? User::value('id'),
            'date' => $validated['date'] ?? Carbon::today()->toDateString(),
            'time' => Carbon::now()->format('H:i:s'),
            'type' => $medType,
            'medicine_name' => $validated['medicine_name'],
            'dosage' => $dosage,
            'application_method' => $validated['application_method'] ?? 'Air minum',
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data pemakaian {$health->medicine_name} ({$health->dosage}) berhasil disimpan dan otomatis memotong stok Gudang Obat!",
                'data' => $health,
            ]);
        }

        return redirect()->back()->with('success', "Data pemakaian {$health->medicine_name} ({$health->dosage}) berhasil disimpan dan otomatis memotong stok Gudang Obat!");
    }
}
