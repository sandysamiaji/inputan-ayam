<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterSettingSeeder extends Seeder
{
    /**
     * Run the database seeds for farm settings.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $defaultSettings = [
            'farm_name' => 'NOCHI FARM',
            'farm_tagline' => 'Peternak Telur Berkualitas',
            'dashboard_motivation_message' => 'Semangat bekerja dan tetap jaga kebersihan serta performa kandang hari ini!',
            'dashboard_chicken_status_message' => 'Kondisi ayam saat ini memasuki umur minggu ke-21 (Masa Awal Bertelur Produktif / Subur). Pastikan pencahayaan dan asupan kalsium optimal.',
            'dashboard_info_schedule_start' => '06:00',
            'dashboard_info_schedule_end' => '18:00',
            'dashboard_info_active' => '1',
            'standard_production_egg_crates' => '850',
            'standard_feed_gram_per_chicken' => '115',
            'standard_avg_weight_kg' => '1.62',
            'standard_weight_tolerance' => '0.05',
            'isi_tray' => '30 butir',
            'berat_telur' => '0,06 kg',
            'berat_per_karung' => '50 kg',
            'hd_target' => '95%',
            'hd_warning' => '90%',
            'hd_minimum' => '88%',
            'reject_maximum' => '2%',
        ];

        foreach ($defaultSettings as $key => $val) {
            $exists = DB::table('settings')->where('key', $key)->exists();
            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $val,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
