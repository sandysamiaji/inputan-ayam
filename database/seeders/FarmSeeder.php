<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flock;
use App\Models\Coop;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\Mortality;
use App\Models\WeightSample;
use App\Models\HealthTreatment;
use App\Models\FarmStock;
use App\Models\User;
use Carbon\Carbon;

class FarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $petugas = User::where('username', 'petugas')->first() ?? User::first();
        $userId = $petugas ? $petugas->id : null;
        $today = Carbon::today();

        // 1. Klotter 1 (K1)
        $flock = Flock::updateOrCreate(
            ['name' => 'Klotter 1'],
            [
                'code' => 'K1',
                'start_date' => $today->copy()->subWeeks(21),
                'initial_population' => 2250,
                'current_population' => 2249,
                'breed' => 'Lohmann Brown',
                'notes' => 'Ayam petelur produktif flock 1',
                'is_active' => true,
            ]
        );

        // Blok A, B, C (K1)
        $blokA = Coop::updateOrCreate(
            ['flock_id' => $flock->id, 'name' => 'Blok A'],
            [
                'code' => 'A',
                'capacity' => 762,
                'active_chickens' => 762,
                'chicken_age_weeks' => 21,
                'is_active' => true,
            ]
        );

        $blokB = Coop::updateOrCreate(
            ['flock_id' => $flock->id, 'name' => 'Blok B'],
            [
                'code' => 'B',
                'capacity' => 744,
                'active_chickens' => 743,
                'chicken_age_weeks' => 21,
                'is_active' => true,
            ]
        );

        $blokC = Coop::updateOrCreate(
            ['flock_id' => $flock->id, 'name' => 'Blok C'],
            [
                'code' => 'C',
                'capacity' => 744,
                'active_chickens' => 744,
                'chicken_age_weeks' => 21,
                'is_active' => true,
            ]
        );

        // 2. Klotter 2 (K2)
        $flock2 = Flock::updateOrCreate(
            ['name' => 'Klotter 2'],
            [
                'code' => 'K2',
                'start_date' => $today->copy()->subWeeks(21),
                'initial_population' => 1770,
                'current_population' => 1768,
                'breed' => 'Lohmann Brown',
                'notes' => 'Ayam petelur produktif flock 2',
                'is_active' => true,
            ]
        );

        // Blok D, E, F (K2)
        $blokD = Coop::updateOrCreate(
            ['flock_id' => $flock2->id, 'name' => 'Blok D'],
            [
                'code' => 'D',
                'capacity' => 750,
                'active_chickens' => 748,
                'chicken_age_weeks' => 21,
                'is_active' => true,
            ]
        );

        $blokE = Coop::updateOrCreate(
            ['flock_id' => $flock2->id, 'name' => 'Blok E'],
            [
                'code' => 'E',
                'capacity' => 492,
                'active_chickens' => 492,
                'chicken_age_weeks' => 21,
                'is_active' => true,
            ]
        );

        $blokF = Coop::updateOrCreate(
            ['flock_id' => $flock2->id, 'name' => 'Blok F'],
            [
                'code' => 'F',
                'capacity' => 528,
                'active_chickens' => 528,
                'chicken_age_weeks' => 21,
                'is_active' => true,
            ]
        );

        // 3. Produksi Telur Hari Ini (Total ~2.460 Peti / 62.000 Butir)
        if (EggProduction::whereDate('date', $today)->count() === 0) {
            EggProduction::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokA->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '06:30:00',
                'total_eggs' => 21500,
                'broken_eggs' => 150,
                'good_eggs' => 21350,
                'crates_count' => 850,
                'notes' => 'Produksi telur pagi Blok A',
            ]);

            EggProduction::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokB->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '06:45:00',
                'total_eggs' => 20500,
                'broken_eggs' => 120,
                'good_eggs' => 20380,
                'crates_count' => 810,
                'notes' => 'Produksi telur pagi Blok B',
            ]);

            EggProduction::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokC->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '07:00:00',
                'total_eggs' => 20000,
                'broken_eggs' => 110,
                'good_eggs' => 19890,
                'crates_count' => 800,
                'notes' => 'Produksi telur pagi Blok C',
            ]);
        }

        // 4. Pemakaian Pakan Hari Ini (Total 1.820 Kg)
        if (FeedConsumption::whereDate('date', $today)->count() === 0) {
            FeedConsumption::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokA->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '07:10:00',
                'feeding_time' => 'Pagi',
                'feed_name' => 'Pakan Layer',
                'quantity_kg' => 610.00,
                'notes' => 'Pemberian pakan pagi',
            ]);

            FeedConsumption::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokB->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '07:15:00',
                'feeding_time' => 'Pagi',
                'feed_name' => 'Pakan Layer',
                'quantity_kg' => 605.00,
                'notes' => 'Pemberian pakan pagi',
            ]);

            FeedConsumption::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokC->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '07:20:00',
                'feeding_time' => 'Pagi',
                'feed_name' => 'Pakan Layer',
                'quantity_kg' => 605.00,
                'notes' => 'Pemberian pakan pagi',
            ]);
        }

        // 5. Mortalitas Hari Ini (Total 8 Ekor)
        if (Mortality::whereDate('date', $today)->count() === 0) {
            Mortality::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokA->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '07:30:00',
                'count' => 3,
                'type' => 'mati',
                'cause' => 'Stres panas wajar',
                'notes' => 'Kandang A1',
            ]);

            Mortality::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokB->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '07:45:00',
                'count' => 3,
                'type' => 'mati',
                'cause' => 'Kematian wajar',
                'notes' => 'Kandang B2',
            ]);

            Mortality::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokC->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '08:00:00',
                'count' => 2,
                'type' => 'mati',
                'cause' => 'Kematian wajar',
                'notes' => 'Kandang C1',
            ]);
        }

        // 6. Berat Badan Hari Ini (1,62 Kg rata-rata)
        if (WeightSample::whereDate('date', $today)->count() === 0) {
            WeightSample::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokA->id,
                'user_id' => $userId,
                'date' => $today,
                'sample_count' => 50,
                'average_weight_kg' => 1.620,
                'uniformity_percentage' => 88.5,
                'age_weeks' => 21,
                'notes' => 'Timbang bobot rutin mingguan',
            ]);
        }

        // 7. Vaksin & Obat Hari Ini (1 Kegiatan)
        if (HealthTreatment::whereDate('date', $today)->count() === 0) {
            HealthTreatment::create([
                'flock_id' => $flock->id,
                'coop_id' => $blokA->id,
                'user_id' => $userId,
                'date' => $today,
                'time' => '08:30:00',
                'type' => 'vitamin',
                'medicine_name' => 'Vitamin B Complex',
                'dosage' => '10 Botol',
                'application_method' => 'Air Minum',
                'notes' => 'Pemberian vitamin B kompleks harian',
            ]);
        }
    }
}
