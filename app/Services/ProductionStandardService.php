<?php

namespace App\Services;

use App\Models\Coop;

class ProductionStandardService
{
    /**
     * Ambil data standar lengkap untuk minggu umur tertentu (13 - 90 minggu)
     */
    public static function getStandardForWeek(int $week): array
    {
        $w = max(13, min(90, $week));

        if ($w >= 13 && $w <= 15) {
            $progress = ($w - 13) / 2;
            $gram = (int) round(75 + $progress * 5);
            $beratTelur = '-';
            $beratTelurVal = 0;
            $fase = 'Grower Akhir (Pra-Laying)';
            $pill = 'GROWER';
            $pillClass = 'bg-blue-50 text-blue-700 border-blue-200';
            $ket = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam. Jangan menaikkan pakan terlalu ekstrem.';
            $bbTarget = round(1.10 + $progress * 0.33, 2);
            $bbMin = round($bbTarget - 0.07, 2);
            $bbMax = round($bbTarget + 0.07, 2);
            $tips = 'Grower / Pullet';
            $statusText = "Kondisi ayam minggu ke-{$w}: Fase {$fase}. {$ket}";
        } elseif ($w >= 16 && $w <= 17) {
            $progress = ($w - 16) / 1;
            $gram = (int) round(85 + $progress * 5);
            $beratTelurVal = 0;
            $beratTelur = '-';
            $fase = 'Persiapan Bertelur (Pre-Lay)';
            $pill = 'PRE-LAY';
            $pillClass = 'bg-amber-50 text-amber-700 border-amber-200';
            $ket = 'Fokus pada pembentukan kerangka tubuh dan keseragaman bobot badan ayam.';
            $bbTarget = round(1.48 + $progress * 0.10, 2);
            $bbMin = round($bbTarget - 0.06, 2);
            $bbMax = round($bbTarget + 0.06, 2);
            $tips = 'Pre-Lay / Layer Awal';
            $statusText = "Kondisi ayam minggu ke-{$w}: Fase {$fase}. {$ket} Acuan pakan: {$gram} g/ekor.";
        } elseif ($w >= 18 && $w <= 20) {
            $progress = ($w - 18) / 2;
            $gram = (int) round(95 + $progress * 5);
            $beratTelurVal = (float) round(46 + $progress * 9, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Awal Bertelur (Puncak Naik)';
            $pill = 'AWAL BERTELUR';
            $pillClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $ket = 'Ayam membutuhkan energi dan nutrisi tertinggi untuk pembentukan telur pertama dan mencapai puncak.';
            $bbTarget = round(1.62 + $progress * 0.10, 2);
            $bbMin = round($bbTarget - 0.07, 2);
            $bbMax = round($bbTarget + 0.07, 2);
            $tips = 'Layer Phase 1';
            $statusText = "Kondisi ayam minggu ke-{$w}: {$fase}. {$ket} Acuan pakan: {$gram} g/ekor.";
        } elseif ($w >= 21 && $w <= 40) {
            $progress = ($w - 21) / 19;
            $gram = (int) round(110 + $progress * 5);
            $beratTelurVal = (float) round(59.5 + $progress * 4, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Puncak Produksi (Egg Peak)';
            $pill = 'PUNCAK PRODUKSI';
            $pillClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $ket = 'Konsumsi pakan stabil di kisaran 110-115 gram. Energi tertinggi dibutuhkan.';
            $bbTarget = round(1.74 + $progress * 0.08, 2);
            $bbMin = round($bbTarget - 0.08, 2);
            $bbMax = round($bbTarget + 0.08, 2);
            $tips = 'Layer Phase 1';
            $statusText = "Kondisi ayam minggu ke-{$w}: {$fase}! {$ket}";
        } elseif ($w >= 41 && $w <= 60) {
            $progress = ($w - 41) / 19;
            $gram = (int) round(115 + $progress * 5);
            $beratTelurVal = (float) round(64.5 + $progress * 0.8, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Laying Phase 2 (Pasca Puncak)';
            $pill = 'PASCA PUNCAK';
            $pillClass = 'bg-teal-50 text-teal-700 border-teal-200';
            $ket = 'Persentase bertelur mulai menurun secara perlahan, namun ukuran telur bertambah besar.';
            $bbTarget = round(1.83 + $progress * 0.10, 2);
            $bbMin = round($bbTarget - 0.09, 2);
            $bbMax = round($bbTarget + 0.09, 2);
            $tips = 'Layer Phase 2. Ayam butuh asupan kalsium (Ca) makro yang lebih tinggi untuk kekuatan kerabang telur.';
            $statusText = "Kondisi ayam minggu ke-{$w}: {$fase}. {$ket} Asupan kalsium makro dibutuhkan.";
        } else {
            $progress = ($w - 61) / 29;
            $gram = (int) round(115 + $progress * 5);
            $beratTelurVal = (float) round(65.3 + $progress * 0.7, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Laying Phase 3 (Fase Akhir) / Afkir';
            $pill = 'FASE AKHIR';
            $pillClass = 'bg-slate-100 text-slate-700 border-slate-200';
            $ket = 'Fase akhir produksi sebelum peremajaan. Persentase turun, ukuran besar.';
            $bbTarget = round(1.94 + $progress * 0.08, 2);
            $bbMin = round($bbTarget - 0.09, 2);
            $bbMax = round($bbTarget + 0.09, 2);
            $tips = 'Layer Phase 3 / Akhir. Jaga kekuatan kerabang dengan kalsium makro.';
            $statusText = "Kondisi ayam minggu ke-{$w}: {$fase}. {$ket}";
        }

        return [
            'week' => $w,
            'fase' => $fase,
            'pill' => $pill,
            'pill_class' => $pillClass,
            'keterangan' => $ket,
            'berat_telur' => $beratTelur,
            'berat_telur_val' => $beratTelurVal,
            'gram_pakan' => $gram,
            'pagi_gram' => round($gram / 2, 1),
            'sore_gram' => round($gram / 2, 1),
            'bb_min' => $bbMin,
            'bb_target' => $bbTarget,
            'bb_max' => $bbMax,
            'tips' => $tips,
            'status_message' => $statusText,
        ];
    }

    /**
     * Hitung umur minggu dinamis untuk sebuah coop berdasarkan flock.start_date
     * dan tanggal referensi (tanggal yang dipilih user).
     *
     * Jika flock tidak punya start_date, fallback ke chicken_age_weeks statis di DB.
     */
    public static function getDynamicAgeWeeks(Coop $coop, $referenceDate = null): int
    {
        $refDate = $referenceDate ? \Carbon\Carbon::parse($referenceDate) : \Carbon\Carbon::today();

        if ($coop->flock && $coop->flock->start_date) {
            $startDate = \Carbon\Carbon::parse($coop->flock->start_date);
            $weeks = (int) $startDate->diffInWeeks($refDate);
            return max(1, $weeks); // minimal 1 minggu
        }

        // Fallback: hitung dari chicken_age_weeks statis + selisih hari dari updated_at
        return max(1, (int) $coop->chicken_age_weeks);
    }

    /**
     * Hitung ringkasan kondisi farm berdasarkan seluruh blok kandang aktif di DB.
     *
     * @param string|null $referenceDate  Tanggal referensi (misal tanggal dipilih user di dashboard).
     *                                     Null = hari ini.
     */
    public static function getActiveFarmCondition($referenceDate = null): array
    {
        $coops = Coop::with('flock')->where('is_active', true)->get();

        if ($coops->isEmpty()) {
            return [
                'dominant_week' => 21,
                'min_week' => 21,
                'max_week' => 21,
                'standard' => self::getStandardForWeek(21),
                'status_message' => self::getStandardForWeek(21)['status_message'],
                'coop_standards' => [],
            ];
        }

        // Hitung umur dinamis per coop berdasarkan flock.start_date + referenceDate
        $dynamicAges = [];
        foreach ($coops as $c) {
            $dynamicAges[$c->id] = self::getDynamicAgeWeeks($c, $referenceDate);
        }

        $ages = collect($dynamicAges)->values();
        $minWeek = $ages->min();
        $maxWeek = $ages->max();
        $avgWeek = (int) round($ages->avg());

        // Cari minggu yang paling banyak populasinya
        $agePopulations = [];
        foreach ($coops as $c) {
            $w = $dynamicAges[$c->id];
            $agePopulations[$w] = ($agePopulations[$w] ?? 0) + (int) $c->active_chickens;
        }
        arsort($agePopulations);
        $dominantWeek = key($agePopulations) ?: $avgWeek;

        $standard = self::getStandardForWeek($dominantWeek);

        // Map standar per coop (menggunakan umur dinamis)
        $coopStandards = [];
        foreach ($coops as $c) {
            $coopStandards[$c->id] = self::getStandardForWeek($dynamicAges[$c->id]);
        }

        // Susun teks otomatis
        $statusMessage = $standard['status_message'];

        return [
            'dominant_week' => $dominantWeek,
            'min_week' => $minWeek,
            'max_week' => $maxWeek,
            'avg_week' => $avgWeek,
            'standard' => $standard,
            'status_message' => $statusMessage,
            'coop_standards' => $coopStandards,
            'dynamic_ages' => $dynamicAges,
        ];
    }
}
