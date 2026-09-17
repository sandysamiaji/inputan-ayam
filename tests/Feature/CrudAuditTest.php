<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Coop;
use App\Models\Flock;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\Mortality;
use App\Models\WeightSample;
use App\Models\HealthTreatment;
use App\Models\FarmStock;
use App\Models\WeeklyStandard;
use Illuminate\Support\Facades\DB;

class CrudAuditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Pastikan ada setidaknya 1 User, 1 Flock, dan 1 Coop aktif
        if (!User::first()) {
            User::create([
                'name' => 'Petugas Kandang Test',
                'username' => 'petugas',
                'email' => 'petugas@nochifarm.com',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]);
        }

        $flock = Flock::where('is_active', true)->first();
        if (!$flock) {
            $flock = Flock::create([
                'name' => 'Klotter Test',
                'code' => 'KT1',
                'start_date' => '2026-01-01',
                'initial_population' => 1000,
                'current_population' => 1000,
                'is_active' => true,
            ]);
        }

        $coop = Coop::where('is_active', true)->first();
        if (!$coop) {
            Coop::create([
                'flock_id' => $flock->id,
                'name' => 'Blok Test 1',
                'capacity' => 1000,
                'active_chickens' => 950,
                'chicken_age_weeks' => 24,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Test 1: Verifikasi semua halaman GET dapat diakses dengan normal (200 OK)
     */
    public function test_all_get_routes_are_accessible(): void
    {
        $routes = [
            '/',
            '/input',
            '/gudang',
            '/gudang/telur',
            '/gudang/pakan',
            '/gudang/obat',
            '/rekap',
            '/rekap/detail',
            '/master',
            '/master/info-farm',
            '/master/flocks',
            '/master/standards',
            '/master/standar-produksi',
            '/master/standar-pakan',
            '/master/standar-bb',
            '/master/medicines',
            '/master/settings',
            '/profil',
        ];

        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $this->assertContains(
                $response->getStatusCode(), 
                [200, 302], 
                "Route {$uri} returned unexpected status {$response->getStatusCode()}"
            );
        }
    }

    /**
     * Test 2: Operasi CRUD Produksi Telur (Dashboard, Rekap, Warehouse)
     */
    public function test_egg_production_crud(): void
    {
        $coop = Coop::where('is_active', true)->first();

        // 1. Create standard with crates_count and weight_kg
        $response = $this->post(route('production.store'), [
            'coop_id' => $coop->id,
            'good_eggs' => 300,
            'broken_eggs' => 15,
            'abnormal_eggs' => 5,
            'crates_count' => 10.0,
            'weight_kg' => 15.5,
            'date' => '2026-09-17',
            'notes' => 'Panen reguler',
        ]);
        $response->assertSessionHas('success');

        $ep = EggProduction::latest('id')->first();
        $this->assertNotNull($ep);
        $this->assertEquals(300, $ep->good_eggs);
        $this->assertEquals(320, $ep->total_eggs);
        $this->assertEquals(15.5, (float) $ep->weight_kg);

        // 2. Create via Master Flocks modal payload (total_eggs without good_eggs, with weight_kg)
        $responseFlockModal = $this->post(route('production.store'), [
            'coop_id' => $coop->id,
            'total_eggs' => 500,
            'broken_eggs' => 20,
            'crates_count' => 16.0,
            'weight_kg' => 24.0,
            'date' => '2026-09-17',
            'notes' => 'Panen modal flock',
        ]);
        $responseFlockModal->assertSessionHas('success');
        $epFlock = EggProduction::latest('id')->first();
        $this->assertEquals(480, $epFlock->good_eggs);
        $this->assertEquals(500, $epFlock->total_eggs);
        $this->assertEquals(24.0, (float) $epFlock->weight_kg);

        // 3. Update via Rekap
        $updateResp = $this->put(route('rekap.data.production.update', $ep->id), [
            'total_eggs' => 350,
            'broken_eggs' => 10,
        ]);
        $updateResp->assertSessionHas('success');
        $ep->refresh();
        $this->assertEquals(350, $ep->total_eggs);
        $this->assertEquals(340, $ep->good_eggs);

        // 4. Update via Warehouse including weight_kg
        $whUpdateResp = $this->put(route('warehouse.update', "ep_{$ep->id}"), [
            'good_eggs' => 360,
            'broken_eggs' => 10,
            'crates_count' => 12.0,
            'weight_kg' => 18.25,
            'notes' => 'Updated from gudang',
        ]);
        $whUpdateResp->assertSessionHas('success');
        $ep->refresh();
        $this->assertEquals(360, $ep->good_eggs);
        $this->assertEquals(18.25, (float) $ep->weight_kg);

        // Verify views contain new fields
        $inputPage = $this->get('/input');
        $inputPage->assertSee('Retak/Pecah (Butir)');
        $inputPage->assertSee('Jumlah kg (Opsional)');

        $whPage = $this->get('/gudang/telur');
        $whPage->assertSee('Jumlah kg (Opsional)');

        // 5. Toggle Status
        $toggleResp = $this->patch(route('warehouse.toggle-status', "ep_{$ep->id}"));
        $toggleResp->assertSessionHas('success');
        $ep->refresh();
        $this->assertStringStartsWith('[NONAKTIF]', trim($ep->notes ?? ''));

        // Toggle back to active
        $this->patch(route('warehouse.toggle-status', "ep_{$ep->id}"));
        $ep->refresh();
        $this->assertStringStartsNotWith('[NONAKTIF]', trim($ep->notes ?? ''));

        // 6. Delete
        $delResp = $this->delete(route('rekap.data.production.destroy', $ep->id));
        $delResp->assertSessionHas('success');
        $this->assertNull(EggProduction::find($ep->id));

        // Delete second
        $this->delete(route('warehouse.destroy', "ep_{$epFlock->id}"));
        $this->assertNull(EggProduction::find($epFlock->id));
    }

    /**
     * Test 3: Operasi CRUD Pemakaian Pakan (Dashboard, Rekap, Warehouse)
     */
    public function test_feed_consumption_crud(): void
    {
        $coop = Coop::where('is_active', true)->first();

        // 1. Create
        $response = $this->post(route('feed.store'), [
            'coop_id' => $coop->id,
            'feed_name' => 'Pakan Layer Super',
            'quantity_kg' => 60.5,
            'feeding_time' => 'Pagi',
            'date' => '2026-09-17',
            'notes' => 'Pemberian pakan pagi',
        ]);
        $response->assertSessionHas('success');

        $fc = FeedConsumption::latest('id')->first();
        $this->assertNotNull($fc);
        $this->assertEquals(60.5, $fc->quantity_kg);

        // Verify Auto-Sync to FarmStock
        $fs = FarmStock::where('category', 'pakan')->where('notes', 'like', '%[AUTO-KONSUMSI]%')->latest('id')->first();
        $this->assertNotNull($fs);
        $this->assertEquals(60.5, $fs->quantity);

        // 2. Update via Rekap
        $this->put(route('rekap.data.feed.update', $fc->id), [
            'quantity_kg' => 65.0,
        ])->assertSessionHas('success');
        $fc->refresh();
        $this->assertEquals(65.0, $fc->quantity_kg);

        // 3. Update via Warehouse
        $this->put(route('warehouse.update', "fc_{$fc->id}"), [
            'quantity_kg' => 70.0,
            'feed_name' => 'Pakan Layer Extra',
        ])->assertSessionHas('success');
        $fc->refresh();
        $this->assertEquals(70.0, $fc->quantity_kg);
        $this->assertEquals('Pakan Layer Extra', $fc->feed_name);

        // 4. Delete
        $this->delete(route('rekap.data.feed.destroy', $fc->id))->assertSessionHas('success');
        $this->assertNull(FeedConsumption::find($fc->id));
    }

    /**
     * Test 4: Operasi CRUD Mortalitas Ayam (Dashboard & Rekap)
     */
    public function test_mortality_crud(): void
    {
        $coop = Coop::where('is_active', true)->first();
        $initialPopulation = $coop->active_chickens;

        // 1. Create
        $response = $this->post(route('mortality.store'), [
            'coop_id' => $coop->id,
            'count' => 3,
            'type' => 'mati',
            'cause' => 'Sakit wajar',
            'date' => '2026-09-17',
            'notes' => 'Pencatatan mortalitas',
        ]);
        $response->assertSessionHas('success');

        $mort = Mortality::latest('id')->first();
        $this->assertNotNull($mort);
        $this->assertEquals(3, $mort->count);

        // Verify coop population decremented
        $coop->refresh();
        $this->assertEquals($initialPopulation - 3, $coop->active_chickens);

        // 2. Update via Rekap
        $this->put(route('rekap.data.mortality.update', $mort->id), [
            'count' => 4,
            'cause' => 'Afkir fisik',
        ])->assertSessionHas('success');
        $mort->refresh();
        $this->assertEquals(4, $mort->count);
        $this->assertEquals('Afkir fisik', $mort->cause);

        // 3. Delete
        $this->delete(route('rekap.data.mortality.destroy', $mort->id))->assertSessionHas('success');
        $this->assertNull(Mortality::find($mort->id));
    }

    /**
     * Test 5: Operasi CRUD Sampling Bobot Ayam (Dashboard & Rekap)
     */
    public function test_weight_sample_crud(): void
    {
        $coop = Coop::where('is_active', true)->first();

        // 1. Create
        $this->post(route('weight.store'), [
            'coop_id' => $coop->id,
            'average_weight_kg' => 1.685,
            'sample_count' => 60,
            'date' => '2026-09-17',
            'notes' => 'Sampling mingguan',
        ])->assertSessionHas('success');

        $ws = WeightSample::latest('id')->first();
        $this->assertNotNull($ws);
        $this->assertEquals(1.685, $ws->average_weight_kg);

        // 2. Update via Rekap
        $this->put(route('rekap.data.weight.update', $ws->id), [
            'average_weight_kg' => 1.720,
            'uniformity_percentage' => 93.0,
        ])->assertSessionHas('success');
        $ws->refresh();
        $this->assertEquals(1.720, $ws->average_weight_kg);
        $this->assertEquals(93.0, $ws->uniformity_percentage);

        // 3. Delete
        $this->delete(route('rekap.data.weight.destroy', $ws->id))->assertSessionHas('success');
        $this->assertNull(WeightSample::find($ws->id));
    }

    /**
     * Test 6: Operasi CRUD Vaksin & Obat (Dashboard, Rekap, Warehouse)
     */
    public function test_health_treatment_crud(): void
    {
        $coop = Coop::where('is_active', true)->first();

        // 1. Create
        $this->post(route('health.store'), [
            'coop_id' => $coop->id,
            'medicine_name' => 'Vitamin C + Anti Stres',
            'type' => 'vitamin',
            'dosage' => '2 Botol',
            'application_method' => 'Air Minum',
            'date' => '2026-09-17',
            'notes' => 'Pencegahan stres cuaca',
        ])->assertSessionHas('success');

        $ht = HealthTreatment::latest('id')->first();
        $this->assertNotNull($ht);
        $this->assertEquals('Vitamin C + Anti Stres', $ht->medicine_name);

        // 2. Update via Rekap
        $this->put(route('rekap.data.health.update', $ht->id), [
            'medicine_name' => 'Vitamin C Plus',
            'dosage' => '3 Botol',
        ])->assertSessionHas('success');
        $ht->refresh();
        $this->assertEquals('Vitamin C Plus', $ht->medicine_name);

        // 3. Update via Warehouse
        $this->put(route('warehouse.update', "ht_{$ht->id}"), [
            'medicine_name' => 'Vitamin C Ultra',
            'dosage' => 4,
            'unit' => 'Botol',
        ])->assertSessionHas('success');
        $ht->refresh();
        $this->assertStringContainsString('Botol', $ht->dosage);

        // 4. Delete
        $this->delete(route('rekap.data.health.destroy', $ht->id))->assertSessionHas('success');
        $this->assertNull(HealthTreatment::find($ht->id));
    }

    /**
     * Test 7: Operasi CRUD Gudang FarmStock
     */
    public function test_warehouse_farm_stock_crud(): void
    {
        // 1. Create FarmStock
        $this->post(route('warehouse.store'), [
            'category' => 'pakan',
            'type' => 'masuk',
            'item_name' => 'Pakan Konsentrat Broiler Crud',
            'quantity' => 150.0,
            'unit' => 'Kg',
            'date' => '2026-09-17',
            'source' => 'Pemasok Test',
            'notes' => 'Uji CRUD Gudang',
        ])->assertSessionHas('success');

        $fs = FarmStock::where('item_name', 'Pakan Konsentrat Broiler Crud')->latest('id')->first();
        $this->assertNotNull($fs);

        // 2. Update FarmStock
        $this->put(route('warehouse.update', $fs->id), [
            'item_name' => 'Pakan Konsentrat Broiler Updated',
            'quantity' => 180.0,
            'unit' => 'Kg',
            'type' => 'masuk',
            'date' => '2026-09-17',
            'notes' => 'Catatan diperbarui',
        ])->assertSessionHas('success');
        $fs->refresh();
        $this->assertEquals(180.0, $fs->quantity);
        $this->assertEquals('Pakan Konsentrat Broiler Updated', $fs->item_name);

        // 3. Toggle Status
        $this->patch(route('warehouse.toggle-status', $fs->id))->assertSessionHas('success');
        $fs->refresh();
        $this->assertStringStartsWith('[NONAKTIF]', trim($fs->notes ?? ''));

        // 4. Delete
        $this->delete(route('warehouse.destroy', $fs->id))->assertSessionHas('success');
        $this->assertNull(FarmStock::find($fs->id));
    }

    /**
     * Test 8: Operasi CRUD Master Data Flocks & Coops
     */
    public function test_master_flocks_and_coops_crud(): void
    {
        // 1. Create Flock
        $this->post(route('master.flocks.store'), [
            'name' => 'Klotter Baru CRUD Test',
            'code' => 'KCRUD1',
            'start_date' => '2026-03-01',
            'initial_age_weeks' => 16,
            'initial_population' => 800,
            'breed' => 'Lohmann Brown',
            'notes' => 'Flock Uji',
        ])->assertSessionHas('success');

        $flock = Flock::where('code', 'KCRUD1')->first();
        $this->assertNotNull($flock);

        // 2. Update Flock
        $this->put(route('master.flocks.update', $flock->id), [
            'name' => 'Klotter Baru CRUD Updated',
            'code' => 'KCRUD1',
            'start_date' => '2026-03-01',
            'initial_age_weeks' => 17,
            'initial_population' => 850,
            'breed' => 'Lohmann Brown',
        ])->assertSessionHas('success');
        $flock->refresh();
        $this->assertEquals(850, $flock->initial_population);

        // 3. Create Coop under this Flock
        $this->post(route('master.coops.store'), [
            'flock_id' => $flock->id,
            'name' => 'Blok CRUD 1',
            'capacity' => 500,
            'active_chickens' => 450,
            'chicken_age_weeks' => 20,
        ])->assertSessionHas('success');

        $coop = Coop::where('name', 'Blok CRUD 1')->first();
        $this->assertNotNull($coop);

        // 4. Update Coop
        $this->put(route('master.coops.update', $coop->id), [
            'flock_id' => $flock->id,
            'name' => 'Blok CRUD 1 Updated',
            'capacity' => 520,
            'active_chickens' => 480,
            'chicken_age_weeks' => 21,
        ])->assertSessionHas('success');
        $coop->refresh();
        $this->assertEquals('Blok CRUD 1 Updated', $coop->name);
        $this->assertEquals(480, $coop->active_chickens);

        // 5. Delete Coop
        $this->delete(route('master.coops.destroy', $coop->id))->assertSessionHas('success');
        $this->assertNull(Coop::find($coop->id));

        // 6. Delete Flock
        $this->delete(route('master.flocks.destroy', $flock->id))->assertSessionHas('success');
        $this->assertNull(Flock::find($flock->id));
    }

    /**
     * Test 9: Update Master Settings & Weekly Standards
     */
    public function test_master_standards_and_settings_update(): void
    {
        // 1. Update Info Farm
        $this->post(route('master.info-farm.update'), [
            'farm_name' => 'NOCHI FARM INDONESIA',
            'farm_tagline' => 'Peternak Telur Berkualitas',
            'pullet_in_date' => '2026-04-20',
            'pullet_initial_age_weeks' => 0,
            'dashboard_motivation_message' => 'Semangat menjaga kebersihan!',
            'dashboard_chicken_status_message' => 'Kondisi ayam sehat prima.',
        ])->assertSessionHas('success');

        $farmName = DB::table('settings')->where('key', 'farm_name')->value('value');
        $this->assertEquals('NOCHI FARM INDONESIA', $farmName);

        // 2. Update Weekly Standard
        $this->post(route('master.weekly-standards.update', 22), [
            'hd_target' => 94.0,
            'egg_weight' => '61.5',
            'feed_gram' => 110,
            'feed_type' => 'Layer Phase 1',
            'weight_min' => 1.65,
            'weight_target' => 1.75,
            'weight_max' => 1.85,
            'phase' => 'Puncak Produksi',
            'pill' => 'Masa Puncak',
        ])->assertSessionHas('success');

        $ws = WeeklyStandard::where('week', 22)->first();
        $this->assertNotNull($ws);
        $this->assertEquals(94.0, $ws->hd_target);

        // 3. Update System Settings
        $this->post(route('master.settings.update'), [
            'isi_tray' => '30',
            'berat_telur' => '0.06',
            'berat_per_karung' => '50',
            'hd_target' => '95',
            'hd_warning' => '90',
            'hd_minimum' => '88',
            'reject_maximum' => '2',
        ])->assertSessionHas('success');

        $trayVal = DB::table('settings')->where('key', 'isi_tray')->value('value');
        $this->assertStringContainsString('30', $trayVal);
    }
}
