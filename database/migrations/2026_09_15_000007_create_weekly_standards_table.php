<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('weekly_standards')) {
            Schema::create('weekly_standards', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('week')->unique();
                $table->string('phase');
                $table->string('pill');
                $table->string('feed_type')->nullable();
                $table->decimal('hd_target', 5, 2)->default(0);
                $table->string('egg_weight')->nullable();
                $table->decimal('feed_gram', 6, 2)->default(0);
                $table->decimal('weight_min', 5, 2)->default(0);
                $table->decimal('weight_target', 5, 2)->default(0);
                $table->decimal('weight_max', 5, 2)->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('weekly_standards', function (Blueprint $table) {
                if (!Schema::hasColumn('weekly_standards', 'feed_type')) {
                    $table->string('feed_type')->nullable()->after('pill');
                }
                if (!Schema::hasColumn('weekly_standards', 'hd_target')) {
                    $table->decimal('hd_target', 5, 2)->default(0)->after('feed_type');
                }
            });
        }

        // Generate data umur 13 sampai 90 minggu sesuai standar acuan Nochi Farm
        $now = Carbon::now();
        $defaultRecords = [];

        for ($w = 13; $w <= 90; $w++) {
            if ($w >= 13 && $w <= 15) {
                $progress = ($w - 13) / 2;
                $phase = 'Grower Akhir (Pra-Laying)';
                $pill = 'GROWER';
                $feedType = 'Grower / Pullet';
                $feedGram = round(75 + $progress * 5, 1); // 75 - 80 g
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
                $feedGram = round(85 + $progress * 5, 1); // 85 - 90 g
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
                $feedGram = round(95 + $progress * 5, 1); // 95 - 100 g
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
                $feedGram = round(110 + $progress * 5, 1); // 110 - 115 g
                
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
                $feedGram = round(115 + $progress * 5, 1); // 115 - 120 g
                $hdTarget = round(90.5 - $progress * 6.5, 1); // 90.5% -> 84.0%
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
                $feedGram = round(116 + $progress * 4, 1); // 116 - 120 g
                $hdTarget = round(83.5 - $progress * 13.5, 1); // 83.5% -> 70.0%
                $eggWeight = (string) round(65.4 + $progress * 0.6, 1);
                $bbTarget = round(1.94 + $progress * 0.08, 2);
                $bbMin = round($bbTarget - 0.09, 2);
                $bbMax = round($bbTarget + 0.09, 2);
                $desc = 'Fase akhir produksi sebelum peremajaan (afkir). Jaga pakan layer akhir dan kekuatan kerabang telur.';
            }

            $defaultRecords[] = [
                'week' => $w,
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
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert atau update data agar migrasi idempotent
        foreach ($defaultRecords as $rec) {
            $exists = DB::table('weekly_standards')->where('week', $rec['week'])->first();
            if ($exists) {
                // Update jika kolom baru belum terisi
                DB::table('weekly_standards')->where('week', $rec['week'])->update([
                    'phase' => $rec['phase'],
                    'pill' => $rec['pill'],
                    'feed_type' => $rec['feed_type'],
                    'hd_target' => $rec['hd_target'],
                    'feed_gram' => $rec['feed_gram'],
                    'egg_weight' => $rec['egg_weight'],
                    'weight_min' => $rec['weight_min'],
                    'weight_target' => $rec['weight_target'],
                    'weight_max' => $rec['weight_max'],
                    'description' => $rec['description'],
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('weekly_standards')->insert($rec);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_standards');
    }
};
