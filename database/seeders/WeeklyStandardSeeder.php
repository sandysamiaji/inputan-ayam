<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WeeklyStandardSeeder extends Seeder
{
    /**
     * Run the database seeds for weekly layer standards.
     */
    public function run(): void
    {
        $now = Carbon::now();

        for ($w = 13; $w <= 90; $w++) {
            if ($w >= 13 && $w <= 15) {
                $progress = ($w - 13) / 2;
                $phase = 'Grower Akhir (Pra-Laying)';
                $pill = 'GROWER';
                $feedType = 'Grower / Pullet';
                $feedGram = round(75 + $progress * 5, 1);
                $hdTarget = 0.0;
                $eggWeight = '-';
                $bbTarget = round(1.10 + $progress * 0.33, 2);
                $bbMin = round($bbTarget - 0.07, 2);
                $bbMax = round($bbTarget + 0.07, 2);
                $desc = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam. Jangan menaikkan pakan terlalu ekstrem agar ayam tidak kegemukan sebelum bertelur.';
            } elseif ($w >= 16 && $w <= 17) {
                $progress = ($w - 16) / 1;
                $phase = 'Persiapan Bertelur (Pre-Lay)';
                $pill = 'PRE-LAY';
                $feedType = 'Pre-Lay / Layer Awal';
                $feedGram = round(85 + $progress * 5, 1);
                $hdTarget = 0.0;
                $eggWeight = '-';
                $bbTarget = round(1.48 + $progress * 0.10, 2);
                $bbMin = round($bbTarget - 0.06, 2);
                $bbMax = round($bbTarget + 0.06, 2);
                $desc = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam. Persiapan kematangan saluran telur.';
            } elseif ($w >= 18 && $w <= 20) {
                $progress = ($w - 18) / 2;
                $phase = 'Awal Bertelur (Puncak Naik)';
                $pill = 'AWAL BERTELUR';
                $feedType = 'Layer Phase 1';
                $feedGram = round(95 + $progress * 5, 1);
                $hdTarget = match ($w) {
                    18 => 5.0,
                    19 => 35.0,
                    20 => 75.0,
                    default => 50.0,
                };
                $eggWeight = match ($w) {
                    18 => '46.0',
                    19 => '50.5',
                    20 => '55.0',
                    default => '50.0',
                };
                $bbTarget = round(1.62 + $progress * 0.10, 2);
                $bbMin = round($bbTarget - 0.07, 2);
                $bbMax = round($bbTarget + 0.07, 2);
                $desc = 'Ayam membutuhkan energi dan nutrisi tertinggi untuk pembentukan telur pertama dan mencapai puncak produksi harian.';
            } elseif ($w >= 21 && $w <= 40) {
                $progress = ($w - 21) / 19;
                $phase = 'Puncak Produksi (Egg Peak)';
                $pill = 'PUNCAK PRODUKSI';
                $feedType = 'Layer Phase 1';
                $feedGram = round(110 + $progress * 5, 1);
                
                if ($w === 21) $hdTarget = 90.0;
                elseif ($w === 22) $hdTarget = 94.0;
                elseif ($w >= 23 && $w <= 28) $hdTarget = 96.0;
                elseif ($w >= 29 && $w <= 35) $hdTarget = round(95.5 - ($w - 29) * 0.25, 1);
                else $hdTarget = round(93.8 - ($w - 36) * 0.35, 1);

                $eggWeight = (string) round(59.5 + $progress * 4.0, 1);
                $bbTarget = round(1.74 + $progress * 0.08, 2);
                $bbMin = round($bbTarget - 0.08, 2);
                $bbMax = round($bbTarget + 0.08, 2);
                $desc = 'Masa puncak bertelur. Konsumsi pakan stabil di kisaran 110-115 gram. Energi dan nutrisi tertinggi dibutuhkan.';
            } elseif ($w >= 41 && $w <= 60) {
                $progress = ($w - 41) / 19;
                $phase = 'Laying Phase 2 (Pasca Puncak)';
                $pill = 'PASCA PUNCAK';
                $feedType = 'Layer Phase 2';
                $feedGram = round(115 + $progress * 5, 1);
                $hdTarget = round(90.5 - $progress * 6.5, 1);
                $eggWeight = (string) round(64.5 + $progress * 0.8, 1);
                $bbTarget = round(1.83 + $progress * 0.10, 2);
                $bbMin = round($bbTarget - 0.09, 2);
                $bbMax = round($bbTarget + 0.09, 2);
                $desc = 'Persentase bertelur mulai menurun secara perlahan, namun ukuran telur bertambah besar. Di fase ini, ayam butuh asupan Kalsium (Ca) makro lebih tinggi (grit batu kapur / kulit kerang) untuk menjaga kekuatan kerabang telur agar tidak mudah retak.';
            } else {
                $progress = ($w - 61) / 29;
                $phase = 'Laying Phase 3 (Fase Akhir)';
                $pill = 'FASE AKHIR';
                $feedType = 'Layer Phase 3 / Akhir';
                $feedGram = round(116 + $progress * 4, 1);
                $hdTarget = round(83.5 - $progress * 13.5, 1);
                $eggWeight = (string) round(65.4 + $progress * 0.6, 1);
                $bbTarget = round(1.94 + $progress * 0.08, 2);
                $bbMin = round($bbTarget - 0.09, 2);
                $bbMax = round($bbTarget + 0.09, 2);
                $desc = 'Fase akhir produksi sebelum peremajaan (afkir). Jaga pakan layer akhir dan kekuatan kerabang telur.';
            }

            DB::table('weekly_standards')->updateOrInsert(
                ['week' => $w],
                [
                    'phase' => $phase,
                    'pill' => $pill,
                    'feed_type' => $feedType,
                    'hd_target' => $hdTarget,
                    'egg_weight' => $eggWeight,
                    'feed_gram' => $feedGram,
                    'weight_min' => $bbMin,
                    'weight_target' => $bbTarget,
                    'weight_max' => $bbMax,
                    'description' => $desc,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
