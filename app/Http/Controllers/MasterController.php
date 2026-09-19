<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Flock;
use App\Models\Coop;
use App\Models\WeeklyStandard;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\ProductionStandardService;
use Carbon\Carbon;

//test
class MasterController extends Controller
{
    /**
     * Helper untuk ambil setting dari DB
     */
    private function getSetting($key, $default = null)
    {
        $row = DB::table('settings')->where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /**
     * Helper untuk simpan setting ke DB
     */
    private function setSetting($key, $value)
    {
        $now = Carbon::now();
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => $now]
        );
    }

    /**
     * 1. Halaman Menu Utama Master (Sesuai Mockup Nochi Farm Master Mobile v6)
     */
    public function index(Request $request)
    {
        $section = $request->query('section', 'hub');

        $flocks = Flock::with('coops')->where('is_active', true)->get();
        $coops = Coop::where('is_active', true)->get();
        $totalChickens = (int) $coops->sum('active_chickens');
        $avgAgeWeeks = (int) ($coops->avg('chicken_age_weeks') ?: 21);

        $farmName = $this->getSetting('farm_name', 'NOCHI FARM');
        $motivation = $this->getSetting('dashboard_motivation_message', 'Semangat bekerja dan tetap jaga kebersihan serta performa kandang hari ini!');

        $systemSettings = [
            'isi_tray' => $this->getSetting('isi_tray', '30 butir'),
            'berat_telur' => $this->getSetting('berat_telur', '0,06 kg'),
            'berat_per_karung' => $this->getSetting('berat_per_karung', '50 kg'),
            'hd_target' => $this->getSetting('hd_target', '95%'),
            'hd_warning' => $this->getSetting('hd_warning', '90%'),
            'hd_minimum' => $this->getSetting('hd_minimum', '88%'),
            'reject_maximum' => $this->getSetting('reject_maximum', '2%'),
        ];

        $medicines = $this->getMedicinesList();

        $coopSummary = $coops->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'shortName' => $c->code ?: str_replace('Blok ', '', $c->name),
                'active_chickens' => (int) $c->active_chickens,
                'age_weeks' => (int) $c->chicken_age_weeks,
            ];
        })->values()->toArray();

        $weeklyStandards = WeeklyStandard::ordered()->get();
        $currentStd = ProductionStandardService::getStandardForWeek($avgAgeWeeks);

        return view('master.index', compact(
            'flocks', 'coops', 'totalChickens', 'avgAgeWeeks', 'farmName', 'motivation', 'systemSettings', 'medicines', 'coopSummary', 'weeklyStandards', 'currentStd', 'section'
        ));
    }

    /**
     * 2. Halaman Info Farm (Pengaturan Informasi Umum Peternakan & Dashboard)
     */
    public function infoFarm()
    {
        $settings = [
            'farm_name' => $this->getSetting('farm_name', 'NOCHI FARM'),
            'farm_tagline' => $this->getSetting('farm_tagline', 'Peternak Telur Berkualitas'),
            'pullet_in_date' => $this->getSetting('pullet_in_date', '2026-04-20'),
            'pullet_initial_age_weeks' => $this->getSetting('pullet_initial_age_weeks', '0'),
            'dashboard_motivation_message' => $this->getSetting('dashboard_motivation_message', 'Semangat bekerja dan tetap jaga kebersihan serta performa kandang hari ini!'),
            'dashboard_chicken_status_message' => $this->getSetting('dashboard_chicken_status_message', 'Kondisi ayam saat ini memasuki umur minggu ke-21 (Masa Awal Bertelur Produktif / Subur). Pastikan pencahayaan dan asupan kalsium optimal.'),
            'dashboard_info_schedule_start' => $this->getSetting('dashboard_info_schedule_start', '06:00'),
            'dashboard_info_schedule_end' => $this->getSetting('dashboard_info_schedule_end', '18:00'),
            'dashboard_info_active' => $this->getSetting('dashboard_info_active', '1'),
        ];

        $coops = Coop::where('is_active', true)->get();
        $avgAgeWeeks = (int) ($coops->avg('chicken_age_weeks') ?: 21);
        $farmCondition = \App\Services\ProductionStandardService::getActiveFarmCondition();

        return view('master.info-farm', compact('settings', 'avgAgeWeeks', 'farmCondition'));
    }

    /**
     * Simpan pembaruan Info Farm
     */
    public function updateInfoFarm(Request $request)
    {
        $validated = $request->validate([
            'farm_name' => 'required|string|max:255',
            'farm_tagline' => 'nullable|string|max:255',
            'pullet_in_date' => 'required|date',
            'pullet_initial_age_weeks' => 'nullable|integer|min:0',
            'dashboard_motivation_message' => 'required|string|max:1000',
            'dashboard_chicken_status_message' => 'required|string|max:1000',
            'dashboard_info_schedule_start' => 'nullable|string',
            'dashboard_info_schedule_end' => 'nullable|string',
            'dashboard_info_active' => 'nullable|boolean',
        ]);

        $this->setSetting('farm_name', $validated['farm_name']);
        $this->setSetting('farm_tagline', $validated['farm_tagline'] ?? '');
        $this->setSetting('pullet_in_date', $validated['pullet_in_date']);
        $this->setSetting('pullet_initial_age_weeks', $validated['pullet_initial_age_weeks'] ?? 0);
        $this->setSetting('dashboard_motivation_message', $validated['dashboard_motivation_message']);
        $this->setSetting('dashboard_chicken_status_message', $validated['dashboard_chicken_status_message']);
        $this->setSetting('dashboard_info_schedule_start', $validated['dashboard_info_schedule_start'] ?? '06:00');
        $this->setSetting('dashboard_info_schedule_end', $validated['dashboard_info_schedule_end'] ?? '18:00');
        $this->setSetting('dashboard_info_active', $request->has('dashboard_info_active') ? '1' : '0');

        return back()->with('success', 'Data Info Farm dan pesan Dashboard berhasil diperbarui!');
    }

    /**
     * 3. Halaman Blok & Klotter (Flock)
     */
    public function flocks()
    {
        $flocks = Flock::with('coops')->get();
        $coops = Coop::with('flock')->get();

        return view('master.flocks', compact('flocks', 'coops'));
    }

    /**
     * Simpan Klotter (Flock) Baru
     */
    public function storeFlock(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'initial_age_weeks' => 'nullable|integer|min:0',
            'initial_population' => 'required|integer|min:0',
            'breed' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        if (empty($validated['code'])) {
            $count = Flock::count() + 1;
            $validated['code'] = 'K' . $count;
        }

        $validated['current_population'] = $validated['initial_population'];
        $validated['is_active'] = true;

        $flock = Flock::create($validated);

        return back()->with('success', "Klotter {$flock->name} berhasil ditambahkan!");
    }

    /**
     * Update Klotter (Flock)
     */
    public function updateFlock(Request $request, $id)
    {
        $flock = Flock::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'initial_age_weeks' => 'nullable|integer|min:0',
            'initial_population' => 'required|integer|min:0',
            'breed' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $flock->update($validated);

        return back()->with('success', "Data Klotter {$flock->name} berhasil diperbarui!");
    }

    /**
     * Hapus / Nonaktifkan Klotter (Flock)
     */
    public function destroyFlock($id)
    {
        $flock = Flock::with('coops')->findOrFail($id);

        if ($flock->coops()->count() > 0) {
            $flock->update(['is_active' => false]);
            return back()->with('success', "Klotter {$flock->name} dinonaktifkan karena memiliki data blok kandang.");
        }

        $flock->delete();
        return back()->with('success', "Klotter {$flock->name} berhasil dihapus!");
    }

    /**
     * Simpan Coop / Blok Kandang Baru
     */
    public function storeCoop(Request $request)
    {
        $validated = $request->validate([
            'flock_id' => 'required|exists:flocks,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'active_chickens' => 'required|integer|min:0',
            'chicken_age_weeks' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = true;

        $coop = Coop::create($validated);

        // Perbarui total current_population pada Flock
        $flock = Flock::find($validated['flock_id']);
        if ($flock) {
            $flock->update([
                'current_population' => (int) $flock->coops()->sum('active_chickens')
            ]);
        }

        return back()->with('success', "Blok {$coop->name} berhasil ditambahkan ke " . ($flock ? $flock->name : 'Klotter') . "!");
    }

    /**
     * Update data Coop / Blok
     */
    public function updateCoop(Request $request, $id)
    {
        $coop = Coop::findOrFail($id);

        $validated = $request->validate([
            'flock_id' => 'nullable|exists:flocks,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'active_chickens' => 'required|integer|min:0',
            'chicken_age_weeks' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $coop->update($validated);

        if ($coop->flock_id) {
            $flock = Flock::find($coop->flock_id);
            if ($flock) {
                $flock->update([
                    'current_population' => (int) $flock->coops()->sum('active_chickens')
                ]);
            }
        }

        return back()->with('success', "Data {$coop->name} berhasil diperbarui!");
    }

    /**
     * Hapus / Nonaktifkan Coop / Blok
     */
    public function destroyCoop($id)
    {
        $coop = Coop::findOrFail($id);

        if ($coop->eggProductions()->exists() || $coop->feedConsumptions()->exists()) {
            $coop->update(['is_active' => false]);
            return back()->with('success', "Blok {$coop->name} dinonaktifkan karena memiliki riwayat transaksi kandang.");
        }

        $coop->delete();
        return back()->with('success', "Blok {$coop->name} berhasil dihapus!");
    }

    /**
     * 4. Halaman Master Standar Produksi, Pakan & Bobot Badan
     */
    public function standards(Request $request, $defaultTab = 'produksi')
    {
        $activeTab = $request->query('tab', $defaultTab);
        if (!in_array($activeTab, ['produksi', 'pakan', 'bb', 'global'])) {
            $activeTab = 'produksi';
        }

        $weeklyStandards = WeeklyStandard::ordered()->get();
        $coops = Coop::where('is_active', true)->get();
        $totalChickens = (int) $coops->sum('active_chickens');
        $avgAgeWeeks = (int) ($coops->avg('chicken_age_weeks') ?: 21);

        $currentStd = ProductionStandardService::getStandardForWeek($avgAgeWeeks);

        $standards = [
            'standard_production_egg_crates' => $this->getSetting('standard_production_egg_crates', '850'),
            'standard_feed_gram_per_chicken' => $this->getSetting('standard_feed_gram_per_chicken', '115'),
            'standard_avg_weight_kg' => $this->getSetting('standard_avg_weight_kg', '1.62'),
            'standard_weight_tolerance' => $this->getSetting('standard_weight_tolerance', '0.05'),
        ];

        $systemSettings = [
            'isi_tray' => $this->getSetting('isi_tray', '30 butir'),
            'berat_telur' => $this->getSetting('berat_telur', '0,06 kg'),
            'berat_per_karung' => $this->getSetting('berat_per_karung', '50 kg'),
            'hd_target' => $this->getSetting('hd_target', '95%'),
            'hd_warning' => $this->getSetting('hd_warning', '90%'),
            'hd_minimum' => $this->getSetting('hd_minimum', '88%'),
            'reject_maximum' => $this->getSetting('reject_maximum', '2%'),
        ];

        return view('master.standards', compact(
            'weeklyStandards',
            'coops',
            'totalChickens',
            'avgAgeWeeks',
            'currentStd',
            'standards',
            'systemSettings',
            'activeTab'
        ));
    }

    /**
     * Standar Produksi (Tab: Produksi)
     */
    public function standarProduksi(Request $request)
    {
        return $this->standards($request, 'produksi');
    }

    /**
     * Standar Pakan (Tab: Pakan)
     */
    public function standarPakan(Request $request)
    {
        return $this->standards($request, 'pakan');
    }

    /**
     * Standar BB (Tab: BB)
     */
    public function standarBB(Request $request)
    {
        return $this->standards($request, 'bb');
    }

    /**
     * Simpan pembaruan Standar Mingguan (Ajax / Form Submit)
     */
    public function updateWeeklyStandard(Request $request, $week)
    {
        $validated = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'hd_target' => 'required|numeric|min:0|max:100',
            'egg_weight' => 'nullable|string|max:20',
            'feed_gram' => 'required|numeric|min:0|max:300',
            'feed_type' => 'nullable|string|max:100',
            'weight_min' => 'required|numeric|min:0|max:10',
            'weight_target' => 'required|numeric|min:0|max:10',
            'weight_max' => 'required|numeric|min:0|max:10',
            'phase' => 'nullable|string|max:255',
            'pill' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ])->validate();

        $record = WeeklyStandard::updateOrCreate(
            ['week' => (int) $week],
            $validated
        );

        // Invalidate service cache
        ProductionStandardService::getStandardForWeek((int) $week);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Master standar minggu ke-{$week} berhasil diperbarui!",
                'data' => $record,
            ]);
        }

        $tab = $request->input('from_tab', 'produksi');
        return redirect()->route('master.standards', ['tab' => $tab])->with('success', "Master standar minggu ke-{$week} berhasil disimpan!");
    }

    /**
     * Simpan standar parameter global
     */
    public function updateStandards(Request $request)
    {
        $validated = $request->validate([
            'standard_production_egg_crates' => 'required|numeric|min:1',
            'standard_feed_gram_per_chicken' => 'required|numeric|min:1',
            'standard_avg_weight_kg' => 'required|numeric|min:0.1',
            'standard_weight_tolerance' => 'required|numeric|min:0.01',
        ]);

        foreach ($validated as $k => $v) {
            $this->setSetting($k, (string) $v);
        }

        return back()->with('success', 'Parameter standar operasional kandang berhasil disimpan!');
    }

    /**
     * Helper daftar obat master
     */
    private function getMedicinesList(): array
    {
        return [
            [
                'name' => 'ND IB Vaccine (Newcastle Disease & Infectious Bronchitis)',
                'category' => 'Vaksin',
                'dosage' => '1000 - 2000 Dosis per Botol',
                'application' => 'Tetes Mata / Air Minum',
                'schedule' => 'Umur 4, 16, 24, 40 Minggu',
                'notes' => 'Pencegahan virus tetelo dan radang pernapasan layer',
            ],
            [
                'name' => 'ND Lasota',
                'category' => 'Vaksin',
                'dosage' => '1 Botol per 1000 Ekor',
                'application' => 'Tetes Mata',
                'schedule' => 'Umur 18 - 20 Minggu (Booster)',
                'notes' => 'Vaksinasi booster menjelang masa puncak bertelur',
            ],
            [
                'name' => 'Vitamin B Complex + Elektrolit',
                'category' => 'Vitamin & Suplemen',
                'dosage' => '1 gram per 2 Liter Air Minum',
                'application' => 'Air Minum Pagi Hari',
                'schedule' => 'Rutin 2x Seminggu atau Saat Cuaca Panas',
                'notes' => 'Mencegah stres panas (heat stress) dan memacu nafsu makan',
            ],
            [
                'name' => 'Kalsium & Mineral Premix Layer',
                'category' => 'Mineral',
                'dosage' => '2 kg per 100 kg Pakan Konsentrat',
                'application' => 'Campuran Pakan Kering',
                'schedule' => 'Setiap hari selama fase bertelur',
                'notes' => 'Memperkuat cangkang telur agar tidak mudah retak',
            ],
            [
                'name' => 'Disinfektan Kandang (Glutaraldehyde & QAC)',
                'category' => 'Sanitasi',
                'dosage' => '10 ml per 5 Liter Air',
                'application' => 'Semprot / Fogging Lingkungan',
                'schedule' => '1x Seminggu saat kandang kosong atau sela lorong',
                'notes' => 'Sterilisasi bakteri dan virus pembawa penyakit unggas',
            ]
        ];
    }

    /**
     * 6. Halaman Vaksin & Obat
     */
    public function medicines()
    {
        $medicines = $this->getMedicinesList();

        return view('master.medicines', compact('medicines'));
    }

    /**
     * 7. Halaman Pengaturan Aplikasii
     */
    public function settings()
    {
        $settings = [
            'farm_name' => $this->getSetting('farm_name', 'NOCHI FARM'),
            'farm_tagline' => $this->getSetting('farm_tagline', 'Peternak Telur Berkualitas'),
        ];

        $systemSettings = [
            'isi_tray' => $this->getSetting('isi_tray', '30 butir'),
            'berat_telur' => $this->getSetting('berat_telur', '0,06 kg'),
            'berat_per_karung' => $this->getSetting('berat_per_karung', '50 kg'),
            'hd_target' => $this->getSetting('hd_target', '95%'),
            'hd_warning' => $this->getSetting('hd_warning', '90%'),
            'hd_minimum' => $this->getSetting('hd_minimum', '88%'),
            'reject_maximum' => $this->getSetting('reject_maximum', '2%'),
        ];

        return view('master.settings', compact('settings', 'systemSettings'));
    }

    /**
     * Simpan pembaruan Pengaturan Sistem & Parameter Engine
     */
    public function updateSettings(Request $request)
    {
        $parseNum = function ($val, $default = 0) {
            if ($val === null || $val === '') return $default;
            if (is_numeric($val)) return (float) $val;
            $clean = str_replace(',', '.', preg_replace('/[^0-9.,]/', '', (string)$val));
            return is_numeric($clean) ? (float) $clean : $default;
        };

        $isiTray = (int) $parseNum($request->input('isi_tray'), 30);
        $beratTelur = $parseNum($request->input('berat_telur'), 0.06);
        $beratPerKarung = $parseNum($request->input('berat_per_karung'), 50);
        $hdTarget = $parseNum($request->input('hd_target'), 95);
        $hdWarning = $parseNum($request->input('hd_warning'), 90);
        $hdMinimum = $parseNum($request->input('hd_minimum'), 88);
        $rejectMax = $parseNum($request->input('reject_maximum'), 2);

        // Validasi batas logis
        if ($isiTray < 1) $isiTray = 30;
        if ($beratTelur <= 0) $beratTelur = 0.06;
        if ($beratPerKarung <= 0) $beratPerKarung = 50;

        $this->setSetting('isi_tray', $isiTray . ' butir');
        $this->setSetting('berat_telur', str_replace('.', ',', (string) $beratTelur) . ' kg');
        $this->setSetting('berat_per_karung', str_replace('.', ',', (string) $beratPerKarung) . ' kg');
        $this->setSetting('hd_target', $hdTarget . '%');
        $this->setSetting('hd_warning', $hdWarning . '%');
        $this->setSetting('hd_minimum', $hdMinimum . '%');
        $this->setSetting('reject_maximum', $rejectMax . '%');

        if ($request->input('from_section') === 'pengaturan') {
            return redirect('/master?section=pengaturan#card-pengaturan')->with('success', 'Parameter Pengaturan Sistem berhasil diperbarui!');
        }

        return redirect()->back()->with('success', 'Parameter Pengaturan Sistem berhasil diperbarui!');
    }

    /**
     * Tampilkan Halaman Manajemen Hak Akses Pengguna & Toggle Menu/Fitur
     */
    public function permissions(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Halaman Hak Akses hanya dapat dikelola oleh Administrator.');
        }

        $users = User::orderBy('role', 'asc')->orderBy('name', 'asc')->get();

        $selectedUserId = $request->query('user_id');
        if (!$selectedUserId) {
            $defaultNonAdmin = $users->firstWhere('role', '!=', 'admin');
            $selectedUser = $defaultNonAdmin ?: $users->first();
        } else {
            $selectedUser = User::find($selectedUserId) ?: $users->first();
        }

        $allPermissions = \App\Services\PermissionService::getAllPermissions();
        $userPermissionsMap = [];

        if ($selectedUser) {
            $existingPerms = \App\Models\UserPermission::where('user_id', $selectedUser->id)
                ->pluck('is_enabled', 'permission_key')
                ->toArray();

            foreach ($allPermissions as $catKey => $cat) {
                foreach ($cat['items'] as $itemKey => $item) {
                    if (isset($existingPerms[$itemKey])) {
                        $userPermissionsMap[$itemKey] = (bool) $existingPerms[$itemKey];
                    } else {
                        $userPermissionsMap[$itemKey] = (bool) $item['default'];
                    }
                }
            }
        }

        return view('master.permissions', compact('users', 'selectedUser', 'allPermissions', 'userPermissionsMap'));
    }

    /**
     * AJAX Toggle Switch Hak Akses Fitur Per User
     */
    public function togglePermission(Request $request, $userId)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya Administrator yang berhak mengubah hak akses.'], 403);
        }

        $user = User::findOrFail($userId);
        $permissionKey = $request->input('permission_key');
        $isEnabled = $request->boolean('is_enabled');

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Administrator memiliki akses penuh dan tidak dapat dibatasi.',
                'is_admin' => true,
            ], 422);
        }

        \App\Models\UserPermission::updateOrCreate(
            ['user_id' => $user->id, 'permission_key' => $permissionKey],
            ['is_enabled' => $isEnabled]
        );

        return response()->json([
            'success' => true,
            'message' => 'Hak akses fitur berhasil diperbarui.',
            'permission_key' => $permissionKey,
            'is_enabled' => $isEnabled,
        ]);
    }

    /**
     * Aksi Massal (Bulk) Pengaturan Izin: Aktifkan Semua, Nonaktifkan Semua, Template Presets
     */
    public function bulkPermission(Request $request, $userId)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Hanya Administrator yang berhak mengubah hak akses.');
        }

        $user = User::findOrFail($userId);
        if ($user->role === 'admin') {
            return back()->with('error', 'Akun Administrator memiliki akses penuh secara permanen.');
        }

        $action = $request->input('action');
        $allPermissions = \App\Services\PermissionService::getAllPermissions();

        if ($action === 'enable_all') {
            foreach ($allPermissions as $cat) {
                foreach ($cat['items'] as $key => $item) {
                    \App\Models\UserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['is_enabled' => true]
                    );
                }
            }
            return back()->with('success', "Seluruh hak akses untuk {$user->name} berhasil DIAKTIFKAN.");
        }

        if ($action === 'disable_all') {
            foreach ($allPermissions as $cat) {
                foreach ($cat['items'] as $key => $item) {
                    \App\Models\UserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['is_enabled' => false]
                    );
                }
            }
            return back()->with('success', "Seluruh hak akses untuk {$user->name} berhasil DINONAKTIFKAN.");
        }

        if ($action === 'template_field') {
            // Preset Petugas Kandang (Hanya Dashboard, Input Telur/Pakan/Kematian, dan Gudang dasar)
            $allowedKeys = [
                'menu_dashboard', 'menu_input', 'menu_warehouse',
                'feature_quick_egg', 'feature_quick_feed', 'feature_quick_mortality', 'feature_quick_weight', 'feature_quick_health',
                'feature_warehouse_telur', 'feature_warehouse_pakan', 'feature_warehouse_obat',
                'input_form_quarantine',
            ];
            foreach ($allPermissions as $cat) {
                foreach ($cat['items'] as $key => $item) {
                    \App\Models\UserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['is_enabled' => in_array($key, $allowedKeys)]
                    );
                }
            }
            return back()->with('success', "Preset 'Petugas Lapangan' berhasil diterapkan untuk {$user->name}.");
        }

        if ($action === 'enable_category' || $action === 'disable_category') {
            $categoryKey = $request->input('category');
            if (isset($allPermissions[$categoryKey])) {
                $enable = ($action === 'enable_category');
                foreach ($allPermissions[$categoryKey]['items'] as $key => $item) {
                    \App\Models\UserPermission::updateOrCreate(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['is_enabled' => $enable]
                    );
                }
                $catLabel = $allPermissions[$categoryKey]['label'];
                $statusStr = $enable ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';
                return back()->with('success', "Kategori '{$catLabel}' berhasil {$statusStr} untuk {$user->name}.");
            }
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }

    /**
     * Tambah Pengguna Baru
     */
    public function storeUser(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Hanya Administrator yang berhak menambah pengguna.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'required|in:admin,user',
        ], [
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 4 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => $validated['email'] ?: strtolower($validated['username']) . '@nochifarm.com',
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->route('master.permissions', ['user_id' => $user->id])
            ->with('success', "Pengguna {$user->name} (@{$user->username}) berhasil ditambahkan!");
    }

    /**
     * Update Data / Reset Password Pengguna oleh Admin
     */
    public function updateUser(Request $request, $id)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Hanya Administrator yang berhak mengubah data pengguna.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => "required|string|max:50|alpha_dash|unique:users,username,{$user->id}",
            'email' => "nullable|email|max:255|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:4',
            'role' => 'required|in:admin,user',
            'is_active' => 'required|boolean',
        ]);

        $user->name = $validated['name'];
        $user->username = strtolower($validated['username']);
        if (!empty($validated['email'])) {
            $user->email = $validated['email'];
        }
        $user->role = $validated['role'];
        $user->is_active = (bool) $validated['is_active'];

        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('master.permissions', ['user_id' => $user->id])
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    /**
     * Toggle Status Aktif/Nonaktif Akun Pengguna
     */
    public function toggleActiveUser(Request $request, $id)
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            return back()->with('error', 'Hanya Administrator yang berhak mengubah status akun.');
        }

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri yang sedang aktif digunakan.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('master.permissions', ['user_id' => $user->id])
            ->with('success', "Akun {$user->name} berhasil {$statusStr}.");
    }
}
