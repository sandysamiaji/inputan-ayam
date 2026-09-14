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

        if ($w >= 13 && $w <= 17) {
            $progress = ($w - 13) / 4;
            $gram = (int) round(70 + $progress * 15);
            $beratTelur = '-';
            $beratTelurVal = 0;
            $fase = 'Pullet / Grower';
            $pill = 'GROWER';
            $pillClass = 'bg-blue-50 text-blue-700 border-blue-200';
            $ket = 'Pertumbuhan kerangka & organ tubuh';
            $bbTarget = round(1.10 + $progress * 0.33, 2);
            $bbMin = round($bbTarget - 0.07, 2);
            $bbMax = round($bbTarget + 0.07, 2);
            $tips = 'Fokus pada pencapaian kerangka tubuh, keseragaman bobot badan, dan vaksinasi pra-layer.';
            $statusText = "Kondisi ayam minggu ke-{$w}: Fase Pullet / Grower — {$ket}. Fokus pada keseragaman bobot dan jadwal vaksinasi.";
        } elseif ($w >= 18 && $w <= 20) {
            $progress = ($w - 18) / 2;
            $gram = (int) round(90 + $progress * 10);
            $beratTelurVal = (float) round(46 + $progress * 9, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Pra-Layer (Awal Telur)';
            $pill = 'AWAL BERTELUR';
            $pillClass = 'bg-amber-50 text-amber-700 border-amber-200';
            $ket = 'Awal produksi, adaptasi pakan layer';
            $bbTarget = round(1.48 + $progress * 0.10, 2);
            $bbMin = round($bbTarget - 0.06, 2);
            $bbMax = round($bbTarget + 0.06, 2);
            $tips = 'Masa adaptasi pakan layer dan stimulasi cahaya bertahap. Pastikan pencahayaan dan kalsium optimal.';
            $statusText = "Kondisi ayam minggu ke-{$w}: Fase Pra-Layer — {$ket}! Acuan pakan: {$gram} g/ekor, berat telur: {$beratTelur}.";
        } elseif ($w >= 21 && $w <= 25) {
            $progress = ($w - 21) / 4;
            $gram = $w === 21 ? 105 : (int) round(105 + $progress * 9);
            $beratTelurVal = $w === 21 ? 60.0 : (float) round(59.5 + $progress * 3, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Produksi Naik';
            $pill = 'PRODUKSI NAIK';
            $pillClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $ket = 'Produksi meningkat pesat menuju puncak';
            $bbTarget = round(1.62 + $progress * 0.10, 2);
            $bbMin = round($bbTarget - 0.07, 2);
            $bbMax = round($bbTarget + 0.07, 2);
            $tips = 'Jaga konsistensi pakan dan kebersihan nipple air minum. Masa subur dan produksi naik pesat!';
            $statusText = "Kondisi ayam minggu ke-{$w}: Sedang masa subur & {$fase}! {$ket}. Acuan berat telur: {$beratTelur}, pakan: {$gram} g/ekor. Jaga konsistensi pakan dan kebersihan nipple air minum.";
        } elseif ($w >= 26 && $w <= 45) {
            $progress = ($w - 26) / 19;
            $gram = 115;
            $beratTelurVal = (float) round(63 + $progress * 1.5, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Puncak Produksi (Peak)';
            $pill = 'PUNCAK PRODUKSI';
            $pillClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $ket = 'Performa telur puncak stabil (HD > 92%)';
            $bbTarget = round(1.74 + $progress * 0.08, 2);
            $bbMin = round($bbTarget - 0.08, 2);
            $bbMax = round($bbTarget + 0.08, 2);
            $tips = 'Pertahankan kualitas nutrisi pakan, ventilasi udara kandang, dan ketepatan jam pemberian pakan.';
            $statusText = "Kondisi ayam minggu ke-{$w}: Fase Puncak Produksi (Peak) — {$ket}! Acuan berat telur: {$beratTelur}, pakan: {$gram} g/ekor.";
        } elseif ($w >= 46 && $w <= 70) {
            $progress = ($w - 46) / 24;
            $gram = 115;
            $beratTelurVal = (float) round(64.5 + $progress * 0.8, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Produksi Stabil';
            $pill = 'PRODUKSI STABIL';
            $pillClass = 'bg-teal-50 text-teal-700 border-teal-200';
            $ket = 'Produksi stabil, perhatikan asupan kalsium';
            $bbTarget = round(1.83 + $progress * 0.10, 2);
            $bbMin = round($bbTarget - 0.09, 2);
            $bbMax = round($bbTarget + 0.09, 2);
            $tips = 'Pantau ketebalan cangkang telur dan kesehatan saluran pencernaan serta pernapasan ayam.';
            $statusText = "Kondisi ayam minggu ke-{$w}: Fase Produksi Stabil — {$ket}. Acuan berat telur: {$beratTelur}, pakan: {$gram} g/ekor.";
        } else {
            $progress = ($w - 71) / 19;
            $gram = (int) round(114 - $progress * 2);
            $beratTelurVal = (float) round(65.3 + $progress * 0.7, 1);
            $beratTelur = "{$beratTelurVal} g";
            $fase = 'Post-Peak / Afkir';
            $pill = 'POST PEAK';
            $pillClass = 'bg-slate-100 text-slate-700 border-slate-200';
            $ket = 'Fase akhir produksi sebelum peremajaan';
            $bbTarget = round(1.94 + $progress * 0.08, 2);
            $bbMin = round($bbTarget - 0.09, 2);
            $bbMax = round($bbTarget + 0.09, 2);
            $tips = 'Evaluasi feed conversion ratio (FCR) dan kelayakan produksi harian per ekor.';
            $statusText = "Kondisi ayam minggu ke-{$w}: Fase Post-Peak — {$ket}. Pantau efisiensi pakan dan seleksi afkir secara berkala.";
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
     * Hitung ringkasan kondisi farm berdasarkan seluruh blok kandang aktif di DB
     */
    public static function getActiveFarmCondition(): array
    {
        $coops = Coop::where('is_active', true)->get();

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

        $ages = $coops->pluck('chicken_age_weeks')->map(fn($a) => (int) $a);
        $minWeek = $ages->min();
        $maxWeek = $ages->max();
        $avgWeek = (int) round($ages->avg());

        // Cari minggu yang paling banyak populasinya
        $agePopulations = [];
        foreach ($coops as $c) {
            $w = (int) $c->chicken_age_weeks;
            $agePopulations[$w] = ($agePopulations[$w] ?? 0) + (int) $c->active_chickens;
        }
        arsort($agePopulations);
        $dominantWeek = key($agePopulations) ?: $avgWeek;

        $standard = self::getStandardForWeek($dominantWeek);

        // Map standar per coop
        $coopStandards = [];
        foreach ($coops as $c) {
            $coopStandards[$c->id] = self::getStandardForWeek((int) $c->chicken_age_weeks);
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
        ];
    }
}
