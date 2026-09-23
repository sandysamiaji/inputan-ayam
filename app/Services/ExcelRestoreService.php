<?php

namespace App\Services;

use App\Models\Flock;
use App\Models\Coop;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\Mortality;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\ProductionStandardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExcelRestoreService
{
    /**
     * Baca semua baris dari file Excel (.xlsx, .xls) atau CSV.
     * Menggunakan PhpSpreadsheet jika tersedia, atau native ZipArchive XML parser jika tidak.
     */
    public static function readRawRows(string $filePath): array
    {
        // 1. Coba PhpSpreadsheet terlebih dahulu jika library tersedia
        if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = [];
                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $cells = [];
                    foreach ($cellIterator as $cell) {
                        $val = $cell->getValue();
                        if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                            try {
                                $val = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
                            } catch (\Throwable $e) {}
                        }
                        $cells[] = $val;
                    }
                    $rows[] = $cells;
                }
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (\Throwable $e) {
                // Fallback ke native parser
            }
        }

        // 2. Fallback parser native XLSX menggunakan ZipArchive & SimpleXMLElement bawaan PHP
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext === 'xlsx' || $ext === 'xlsm') {
            $xlsxRows = self::readXlsxNative($filePath);
            if (!empty($xlsxRows)) {
                return $xlsxRows;
            }
        }

        // 3. Fallback CSV parser
        return self::readCsvNative($filePath);
    }

    /**
     * Parser native XLSX tanpa dependensi pihak ketiga
     */
    public static function readXlsxNative(string $filePath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // A. Shared Strings
        $sharedStrings = [];
        $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXml !== false) {
            $sxml = @simplexml_load_string($stringsXml);
            if ($sxml && isset($sxml->si)) {
                foreach ($sxml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $t = '';
                        foreach ($si->r as $r) {
                            $t .= (string)$r->t;
                        }
                        $sharedStrings[] = $t;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // B. Temukan Sheet Target (Pilih sheet DATABASE jika ada, atau sheet pertama)
        $sheetXmlStr = null;
        $wbRelsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
        $wbXml = $zip->getFromName('xl/workbook.xml');

        if ($wbXml && $wbRelsXml) {
            $wb = @simplexml_load_string($wbXml);
            $rels = @simplexml_load_string($wbRelsXml);
            $relMap = [];
            if ($rels && isset($rels->Relationship)) {
                foreach ($rels->Relationship as $rel) {
                    $relMap[(string)$rel['Id']] = (string)$rel['Target'];
                }
            }

            $firstRId = null;
            $dbRId = null;
            if ($wb && isset($wb->sheets->sheet)) {
                foreach ($wb->sheets->sheet as $sh) {
                    $name = (string)$sh['name'];
                    $rId = (string)$sh->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                    if (!$firstRId) $firstRId = $rId;
                    if (stripos($name, 'DATABASE') !== false || stripos($name, 'DATA') !== false) {
                        $dbRId = $rId;
                    }
                }
            }

            $chosenRId = $dbRId ?: $firstRId;
            if ($chosenRId && isset($relMap[$chosenRId])) {
                $target = $relMap[$chosenRId];
                if (!str_starts_with($target, 'xl/')) {
                    $target = 'xl/' . ltrim($target, '/');
                }
                $sheetXmlStr = $zip->getFromName($target);
            }
        }

        if (!$sheetXmlStr) {
            $sheetXmlStr = $zip->getFromName('xl/worksheets/sheet1.xml');
            if (!$sheetXmlStr) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                        $sheetXmlStr = $zip->getFromIndex($i);
                        break;
                    }
                }
            }
        }

        $zip->close();

        if (!$sheetXmlStr) {
            return [];
        }

        $ws = @simplexml_load_string($sheetXmlStr);
        if (!$ws || !isset($ws->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($ws->sheetData->row as $row) {
            $rowNum = isset($row['r']) ? (int)$row['r'] : (count($rows) + 1);
            $cells = [];
            $maxCol = 0;

            foreach ($row->c as $c) {
                $cellRef = (string)$c['r']; // misal A4, C4, AC4
                $colLetters = preg_replace('/[0-9]/', '', $cellRef);
                $colIdx = self::colLetterToIndex($colLetters);

                $type = (string)$c['t'];
                $val = isset($c->v) ? (string)$c->v : '';

                if ($type === 's') {
                    $val = isset($sharedStrings[(int)$val]) ? $sharedStrings[(int)$val] : '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $val = (string)$c->is->t;
                } elseif ($type === 'b') {
                    $val = $val === '1';
                }

                $cells[$colIdx] = $val;
                if ($colIdx > $maxCol) {
                    $maxCol = $colIdx;
                }
            }

            // Normalisasi array kolom agar index presisi dari 0 s/d maxCol
            $normalizedRow = [];
            for ($i = 0; $i <= $maxCol; $i++) {
                $normalizedRow[$i] = $cells[$i] ?? null;
            }
            $rows[$rowNum - 1] = $normalizedRow;
        }

        // Pastikan urutan array terurut berdasarkan key baris
        ksort($rows);
        return array_values($rows);
    }

    /**
     * Parser native CSV
     */
    public static function readCsvNative(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);
            rewind($handle);
            $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

            while (($data = fgetcsv($handle, 10000, $delimiter)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Konversi kode kolom Excel (A, B, ..., Z, AA, AB, AC) ke index 0-based
     */
    public static function colLetterToIndex(string $letters): int
    {
        $letters = strtoupper(trim($letters));
        $len = strlen($letters);
        $idx = 0;
        for ($i = 0; $i < $len; $i++) {
            $idx = $idx * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return $idx - 1;
    }

    /**
     * Membersihkan kloter / kandang duplikat otomatis jika sempat terbuat sebelumnya
     */
    public static function cleanupDuplicateFlocksAndCoops(): void
    {
        try {
            // Ambil kloter 1 utama (ID terkecil)
            $primaryFlock1 = Flock::where(function($q) {
                $q->where('name', 'LIKE', '%Kloter 1%')
                  ->orWhere('name', 'LIKE', '%Klotter 1%');
            })->orderBy('id', 'asc')->first();

            if (!$primaryFlock1) return;

            // Kloter duplikat selain kloter utama
            $duplicateFlocks = Flock::where('id', '!=', $primaryFlock1->id)
                ->where(function($q) {
                    $q->where('name', 'LIKE', '%Kloter 1%')
                      ->orWhere('name', 'LIKE', '%Klotter 1%');
                })->get();

            // Kandang utama Blok A, B, C di kloter 1
            $mainCoops = [
                'A' => Coop::where('flock_id', $primaryFlock1->id)->where(function($q) { $q->where('code', 'A')->orWhere('name', 'LIKE', '%Blok A%'); })->first(),
                'B' => Coop::where('flock_id', $primaryFlock1->id)->where(function($q) { $q->where('code', 'B')->orWhere('name', 'LIKE', '%Blok B%'); })->first(),
                'C' => Coop::where('flock_id', $primaryFlock1->id)->where(function($q) { $q->where('code', 'C')->orWhere('name', 'LIKE', '%Blok C%'); })->first(),
            ];

            foreach ($duplicateFlocks as $dupFlock) {
                $dupCoops = Coop::where('flock_id', $dupFlock->id)->get();
                foreach ($dupCoops as $dupCoop) {
                    $letter = self::extractBlockLetter($dupCoop->code, $dupCoop->name);
                    $targetCoop = $mainCoops[$letter] ?? null;

                    if ($targetCoop && $targetCoop->id !== $dupCoop->id) {
                        // Pindahkan / sinkronkan data telur yang pernah masuk ke kandang duplikat
                        $dupEggs = EggProduction::where('coop_id', $dupCoop->id)->get();
                        foreach ($dupEggs as $de) {
                            $exists = EggProduction::where('coop_id', $targetCoop->id)->whereDate('date', $de->date)->first();
                            if ($exists) {
                                $de->delete();
                            } else {
                                $de->update(['coop_id' => $targetCoop->id, 'flock_id' => $primaryFlock1->id]);
                            }
                        }

                        // Pindahkan / sinkronkan data pakan
                        $dupFeeds = FeedConsumption::where('coop_id', $dupCoop->id)->get();
                        foreach ($dupFeeds as $df) {
                            $exists = FeedConsumption::where('coop_id', $targetCoop->id)
                                ->whereDate('date', $df->date)
                                ->whereRaw('LOWER(feeding_time) = ?', [strtolower($df->feeding_time)])
                                ->first();
                            if ($exists) {
                                $df->delete();
                            } else {
                                $df->update(['coop_id' => $targetCoop->id, 'flock_id' => $primaryFlock1->id]);
                            }
                        }

                        // Pindahkan / sinkronkan data mortalitas
                        $dupMorts = Mortality::where('coop_id', $dupCoop->id)->get();
                        foreach ($dupMorts as $dm) {
                            $exists = Mortality::where('coop_id', $targetCoop->id)
                                ->whereDate('date', $dm->date)
                                ->where('type', $dm->type)
                                ->first();
                            if ($exists) {
                                $dm->delete();
                            } else {
                                $dm->update(['coop_id' => $targetCoop->id, 'flock_id' => $primaryFlock1->id]);
                            }
                        }

                        $dupCoop->delete();
                    }
                }

                $dupFlock->delete();
            }
        } catch (\Throwable $e) {
            // Abaikan error agar proses utama tidak terhambat
        }
    }

    /**
     * Proses Restore Data dari file Excel Operasional (NF-DAT-002)
     */
    public static function processRestore(string $filePath, bool $overwrite = true): array
    {
        // 0. Bersihkan kloter / kandang duplikat jika pernah terbuat sebelumnya
        self::cleanupDuplicateFlocksAndCoops();

        $rawRows = self::readRawRows($filePath);
        if (empty($rawRows)) {
            return [
                'success' => false,
                'message' => 'File Excel kosong atau tidak dapat dibaca.',
                'stats' => [],
            ];
        }

        // 1. Temukan baris header
        $headerRowIdx = null;
        $colMap = [];

        foreach ($rawRows as $idx => $row) {
            $rowStr = strtolower(implode(' ', array_filter($row, 'is_scalar')));
            if (str_contains($rowStr, 'tanggal') || str_contains($rowStr, 'baik') || str_contains($rowStr, 'populasi')) {
                $headerRowIdx = $idx;
                break;
            }
        }

        // Jika tidak ditemukan teks header, gunakan baris 2 (row index 2 = baris ke-3 Excel)
        if ($headerRowIdx === null) {
            $headerRowIdx = count($rawRows) > 2 ? 2 : 0;
        }

        // Petakan kolom berdasarkan teks header
        $headerCells = $rawRows[$headerRowIdx] ?? [];
        foreach ($headerCells as $cIdx => $cellVal) {
            $val = strtolower(trim((string)$cellVal));
            if (empty($val)) continue;

            if (str_contains($val, 'tanggal') || $val === 'date') $colMap['tanggal'] = $cIdx;
            elseif (str_contains($val, 'mode') || str_contains($val, 'kloter') || str_contains($val, 'flock')) $colMap['flock'] = $cIdx;
            elseif (str_contains($val, 'mingg') || str_contains($val, 'week')) $colMap['mingg'] = $cIdx;
            elseif (str_contains($val, 'umur') || str_contains($val, 'age')) $colMap['umur'] = $cIdx;
            elseif (str_contains($val, 'blok') || $val === 'kandang') $colMap['blok'] = $cIdx;
            elseif (str_contains($val, 'populasi') || str_contains($val, 'ekor')) $colMap['populasi'] = $cIdx;
            elseif ($val === 'baik' || str_contains($val, 'telur baik')) $colMap['baik'] = $cIdx;
            elseif ($val === 'retak' || str_contains($val, 'telur retak')) $colMap['retak'] = $cIdx;
            elseif ($val === 'pecah' || str_contains($val, 'telur pecah')) $colMap['pecah'] = $cIdx;
            elseif ($val === 'kotor') $colMap['kotor'] = $cIdx;
            elseif ($val === 'total' && !isset($colMap['total'])) $colMap['total'] = $cIdx;
            elseif (str_contains($val, 'petug') || str_contains($val, 'petugas') || $val === 'user') $colMap['petugas'] = $cIdx;
            elseif (str_contains($val, 'catat') || str_contains($val, 'notes')) $colMap['catatan'] = $cIdx;
            elseif (str_contains($val, 'jam') || str_contains($val, 'time')) $colMap['jam'] = $cIdx;
            elseif ((str_contains($val, 'pagi') && str_contains($val, 'pakan')) || ($val === 'pakan pagi')) $colMap['pakan_pagi'] = $cIdx;
            elseif ((str_contains($val, 'sore') && str_contains($val, 'pakan')) || ($val === 'pakan sore')) $colMap['pakan_sore'] = $cIdx;
            elseif (str_contains($val, 'mati') || str_contains($val, 'mortalitas')) $colMap['mati'] = $cIdx;
            elseif (str_contains($val, 'afkir')) $colMap['afkir'] = $cIdx;
        }

        // Fallback index default sesuai template Nochi Farm NF-DAT-002 jika header tidak lengkap
        // A=0: ID, B=1: Hari, C=2: Tanggal, D=3: Mode, E=4: Mingg, F=5: Umur, G=6: Blok, H=7: Populasi
        // I=8: Baik, J=9: Retak, K=10: Pecah, L=11: Kotor, M=12: Total, P=15: Petug, Q=16: Catatan
        // R=17: Jam Input, T=19: Pakan Pagi, U=20: Pakan Sore, Z=25: Ayam Mati
        if (!isset($colMap['tanggal'])) $colMap['tanggal'] = 2;
        if (!isset($colMap['flock'])) $colMap['flock'] = 3;
        if (!isset($colMap['mingg'])) $colMap['mingg'] = 4;
        if (!isset($colMap['umur'])) $colMap['umur'] = 5;
        if (!isset($colMap['blok'])) $colMap['blok'] = 6;
        if (!isset($colMap['populasi'])) $colMap['populasi'] = 7;
        if (!isset($colMap['baik'])) $colMap['baik'] = 8;
        if (!isset($colMap['retak'])) $colMap['retak'] = 9;
        if (!isset($colMap['pecah'])) $colMap['pecah'] = 10;
        if (!isset($colMap['petugas'])) $colMap['petugas'] = 15;
        if (!isset($colMap['catatan'])) $colMap['catatan'] = 16;
        if (!isset($colMap['jam'])) $colMap['jam'] = 17;
        // PAKSA kolom T (index 19) untuk Pakan Pagi dan kolom U (index 20) untuk Pakan Sore
        // sesuai template resmi NF-DAT-002 — tidak boleh di-override oleh header detection
        $colMap['pakan_pagi'] = 19;
        $colMap['pakan_sore'] = 20;
        if (!isset($colMap['mati'])) $colMap['mati'] = 25;

        // Cache Master Data
        $allFlocks = Flock::all();
        $allCoops = Coop::all();
        $defaultUser = Auth::user() ?? User::where('role', 'admin')->first() ?? User::first();
        $defaultUserId = $defaultUser ? $defaultUser->id : 1;

        $stats = [
            'total_rows' => 0,
            'processed_rows' => 0,
            'egg_created' => 0,
            'egg_updated' => 0,
            'feed_created' => 0,
            'feed_updated' => 0,
            'mortality_created' => 0,
            'mortality_updated' => 0,
            'coops_affected' => [],
            'dates_affected' => [],
        ];

        DB::beginTransaction();
        try {
            $dataRows = array_slice($rawRows, $headerRowIdx + 1);

            foreach ($dataRows as $row) {
                $stats['total_rows']++;

                // A. Tanggal
                $rawDate = $row[$colMap['tanggal']] ?? null;
                $formattedDate = self::normalizeDate($rawDate);
                if (!$formattedDate) {
                    continue; // Lewati baris tanpa tanggal valid
                }

                // B. Identifikasi Blok (Kandang)
                // Di aplikasi kita, tiap Blok sudah terdaftar resmi:
                // Kloter 1: Blok A, Blok B, Blok C
                // Kloter 2: Blok D, Blok E, Blok F
                $rawBlok = trim((string)($row[$colMap['blok']] ?? ''));
                $rawUmur = trim((string)($row[$colMap['umur']] ?? ''));

                $blockLetter = self::extractBlockLetter($rawBlok, $rawUmur);

                // Cari kandang di database berdasarkan kode blok (A, B, C, D, E, F) atau nama
                $coop = $allCoops->first(function($c) use ($blockLetter) {
                    $code = strtoupper(trim((string)$c->code));
                    $name = strtoupper(trim((string)$c->name));
                    return $code === $blockLetter
                        || $name === 'BLOK ' . $blockLetter
                        || $name === $blockLetter;
                });

                if (!$coop) {
                    $coop = $allCoops->first(function($c) use ($blockLetter) {
                        return preg_match('/\b' . preg_quote($blockLetter, '/') . '\b/i', $c->name);
                    });
                }

                if (!$coop) {
                    continue; // Jangan sembarangan fallback ke Blok A! Lewati baris jika blok tidak valid di sistem kita
                }

                $rawPopulasi = self::parseNumeric($row[$colMap['populasi']] ?? 0);
                if ($rawPopulasi > 0 && ($coop->capacity <= 0 || $coop->active_chickens <= 0)) {
                    $coop->update([
                        'capacity' => (int)$rawPopulasi,
                        'active_chickens' => (int)$rawPopulasi,
                    ]);
                }

                // D. Jam Input & Petugas
                $rawJam = $row[$colMap['jam']] ?? null;
                $timeStr = self::normalizeTime($rawJam);

                $rawPetugas = trim((string)($row[$colMap['petugas']] ?? ''));
                $userId = $defaultUserId;
                if (!empty($rawPetugas)) {
                    $matchedUser = User::where('name', 'LIKE', '%' . $rawPetugas . '%')
                        ->orWhere('username', 'LIKE', '%' . $rawPetugas . '%')
                        ->first();
                    if ($matchedUser) {
                        $userId = $matchedUser->id;
                    }
                }

                $notes = trim((string)($row[$colMap['catatan']] ?? ''));

                // ========================================================
                // 1. DATA PRODUKSI TELUR (Baik, Retak, Pecah, Total)
                // ========================================================
                $goodEggs = (int) self::parseNumeric($row[$colMap['baik']] ?? 0);
                $retakEggs = (int) self::parseNumeric($row[$colMap['retak']] ?? 0);
                $pecahEggs = (int) self::parseNumeric($row[$colMap['pecah']] ?? 0);
                $totalEggs = (int) self::parseNumeric($row[$colMap['total']] ?? ($goodEggs + $retakEggs + $pecahEggs));
                if ($totalEggs == 0 && ($goodEggs > 0 || $retakEggs > 0 || $pecahEggs > 0)) {
                    $totalEggs = $goodEggs + $retakEggs + $pecahEggs;
                }

                // Ekstraksi Umur Ayam (Minggu) dari Kolom Mingg atau Kolom Umur (F4, misal '18 A')
                $ageWeeks = 0;
                if (isset($colMap['mingg']) && isset($row[$colMap['mingg']])) {
                    $rawM = preg_replace('/[^0-9]/', '', (string)$row[$colMap['mingg']]);
                    if (!empty($rawM)) {
                        $ageWeeks = (int) $rawM;
                    }
                }
                if ($ageWeeks <= 0 && isset($colMap['umur']) && isset($row[$colMap['umur']])) {
                    if (preg_match('/^(\d+)/', trim((string)$row[$colMap['umur']]), $m)) {
                        $ageWeeks = (int) $m[1];
                    }
                }
                if ($ageWeeks <= 0 && !empty($coop->chicken_age_weeks)) {
                    $ageWeeks = (int) $coop->chicken_age_weeks;
                }
                if ($ageWeeks <= 0) {
                    $ageWeeks = 21;
                }

                // Update usia ayam di kandang jika lebih mutakhir
                if ($ageWeeks > 0 && ($coop->chicken_age_weeks === null || $ageWeeks > $coop->chicken_age_weeks)) {
                    $coop->update(['chicken_age_weeks' => $ageWeeks]);
                }

                // Ambil Standar Berat Telur (Gram) per Butir dari Master Data Acuan Umur (WeeklyStandard)
                $standard = ProductionStandardService::getStandardForWeek($ageWeeks);
                $stdEggGram = (!empty($standard['berat_telur_val']) && (float) $standard['berat_telur_val'] > 0)
                    ? (float) $standard['berat_telur_val']
                    : 60.0;

                // Hitung estimasi berat (Kg) dan Peti:
                // Input di Excel adalah BUTIR telur.
                // Estimasi Berat Telur (Kg) = butir telur baik x berat standar master (gram) / 1000
                $targetEggCount = $goodEggs > 0 ? $goodEggs : $totalEggs;
                $totalWeightKg = round(($targetEggCount * $stdEggGram) / 1000, 2);

                // Standar 1 Peti = 10 Kg (default_weight_per_peti)
                $petiWeightKg = (float) Setting::getFloat('default_weight_per_peti', 10);
                if ($petiWeightKg <= 0) $petiWeightKg = 10;

                $cratesCount = 0;
                $weightKg = null;

                if ($totalWeightKg >= $petiWeightKg) {
                    $cratesCount = (int) floor($totalWeightKg / $petiWeightKg);
                    $remKg = round($totalWeightKg - ($cratesCount * $petiWeightKg), 1);
                    $weightKg = $remKg > 0 ? $remKg : null;
                } elseif ($totalWeightKg > 0) {
                    $cratesCount = 0;
                    $weightKg = round($totalWeightKg, 1);
                }

                if ($totalEggs > 0 || $goodEggs > 0 || $retakEggs > 0 || $pecahEggs > 0) {
                    $existingEgg = EggProduction::where('coop_id', $coop->id)
                        ->whereDate('date', $formattedDate)
                        ->first();

                    if ($existingEgg) {
                        if ($overwrite) {
                            $existingEgg->update([
                                'flock_id' => $coop->flock_id,
                                'user_id' => $userId,
                                'time' => $timeStr,
                                'good_eggs' => $goodEggs,
                                'abnormal_eggs' => $retakEggs,
                                'broken_eggs' => $pecahEggs,
                                'total_eggs' => $totalEggs,
                                'crates_count' => $cratesCount,
                                'weight_kg' => $weightKg,
                                'notes' => $notes ?: $existingEgg->notes,
                            ]);
                            $stats['egg_updated']++;
                        }
                    } else {
                        EggProduction::create([
                            'flock_id' => $coop->flock_id,
                            'coop_id' => $coop->id,
                            'user_id' => $userId,
                            'date' => $formattedDate,
                            'time' => $timeStr,
                            'good_eggs' => $goodEggs,
                            'abnormal_eggs' => $retakEggs,
                            'broken_eggs' => $pecahEggs,
                            'total_eggs' => $totalEggs,
                            'crates_count' => $cratesCount,
                            'weight_kg' => $weightKg,
                            'notes' => $notes ?: 'Import Excel Database Operasional',
                        ]);
                        $stats['egg_created']++;
                    }
                }

                // ========================================================
                // 2. DATA PEMAKAIAN PAKAN (Pakan Pagi & Pakan Sore)
                // Kolom T (index 19) = Pakan Pagi, Kolom U (index 20) = Pakan Sore
                // Sesuai template resmi NF-DAT-002
                // ========================================================
                $rawPakanPagi = $row[19] ?? null; // Kolom T
                $rawPakanSore = $row[20] ?? null; // Kolom U
                $pakanPagi = self::parseNumeric($rawPakanPagi);
                $pakanSore = self::parseNumeric($rawPakanSore);

                // Pakan Pagi: Update jika record sudah ada (terlepas nilainya), Insert hanya jika nilai > 0
                $existingPagi = FeedConsumption::where('coop_id', $coop->id)
                    ->whereDate('date', $formattedDate)
                    ->whereRaw('LOWER(feeding_time) = ?', ['pagi'])
                    ->first();

                if ($existingPagi) {
                    // Update record yang sudah ada dengan nilai dari kolom T
                    if ($overwrite && $rawPakanPagi !== null) {
                        $existingPagi->update([
                            'flock_id' => $coop->flock_id,
                            'user_id' => $userId,
                            'quantity_kg' => $pakanPagi,
                            'notes' => $notes ?: $existingPagi->notes,
                        ]);
                        $stats['feed_updated']++;
                    }
                } elseif ($pakanPagi > 0) {
                    // Insert hanya jika nilai pakan > 0
                    FeedConsumption::create([
                        'flock_id' => $coop->flock_id,
                        'coop_id' => $coop->id,
                        'user_id' => $userId,
                        'date' => $formattedDate,
                        'time' => '07:30:00',
                        'feeding_time' => 'Pagi',
                        'feed_name' => 'Pakan Layer',
                        'quantity_kg' => $pakanPagi,
                        'notes' => $notes ?: 'Import Excel Pakan Pagi',
                    ]);
                    $stats['feed_created']++;
                }

                // Pakan Sore: Update jika record sudah ada (terlepas nilainya), Insert hanya jika nilai > 0
                $existingSore = FeedConsumption::where('coop_id', $coop->id)
                    ->whereDate('date', $formattedDate)
                    ->whereRaw('LOWER(feeding_time) = ?', ['sore'])
                    ->first();

                if ($existingSore) {
                    // Update record yang sudah ada dengan nilai dari kolom U
                    if ($overwrite && $rawPakanSore !== null) {
                        $existingSore->update([
                            'flock_id' => $coop->flock_id,
                            'user_id' => $userId,
                            'quantity_kg' => $pakanSore,
                            'notes' => $notes ?: $existingSore->notes,
                        ]);
                        $stats['feed_updated']++;
                    }
                } elseif ($pakanSore > 0) {
                    // Insert hanya jika nilai pakan > 0
                    FeedConsumption::create([
                        'flock_id' => $coop->flock_id,
                        'coop_id' => $coop->id,
                        'user_id' => $userId,
                        'date' => $formattedDate,
                        'time' => '15:30:00',
                        'feeding_time' => 'Sore',
                        'feed_name' => 'Pakan Layer',
                        'quantity_kg' => $pakanSore,
                        'notes' => $notes ?: 'Import Excel Pakan Sore',
                    ]);
                    $stats['feed_created']++;
                }

                // ========================================================
                // 3. DATA MORTALITAS (Ayam Mati)
                // ========================================================
                $ayamMati = (int) self::parseNumeric($row[$colMap['mati']] ?? 0);
                if ($ayamMati > 0) {
                    $existingMortality = Mortality::where('coop_id', $coop->id)
                        ->whereDate('date', $formattedDate)
                        ->where('type', 'mati')
                        ->first();

                    if ($existingMortality) {
                        if ($overwrite) {
                            $existingMortality->update([
                                'flock_id' => $coop->flock_id,
                                'user_id' => $userId,
                                'count' => $ayamMati,
                                'notes' => $notes ?: $existingMortality->notes,
                            ]);
                            $stats['mortality_updated']++;
                        }
                    } else {
                        Mortality::create([
                            'flock_id' => $coop->flock_id,
                            'coop_id' => $coop->id,
                            'user_id' => $userId,
                            'date' => $formattedDate,
                            'time' => $timeStr,
                            'count' => $ayamMati,
                            'type' => 'mati',
                            'cause' => 'Wajar',
                            'notes' => $notes ?: 'Import Excel Mortalitas',
                        ]);
                        $stats['mortality_created']++;
                    }
                }

                $stats['processed_rows']++;
                $stats['coops_affected'][$coop->name] = true;
                $stats['dates_affected'][$formattedDate] = true;
            }

            DB::commit();
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return [
                'success' => false,
                'message' => 'Gagal memproses data Excel: ' . $e->getMessage(),
                'stats' => [],
            ];
        }

        // Catat di Audit Log (di luar transaksi utama agar tidak mengganggu restore)
        try {
            AuditLog::ensureTableExists();
            AuditLog::create([
                'user_id' => $defaultUserId,
                'user_name' => $defaultUser ? $defaultUser->name : 'Admin',
                'user_role' => $defaultUser ? $defaultUser->role : 'admin',
                'action' => 'RESTORE',
                'entity_type' => 'EXCEL_RESTORE',
                'module' => 'Restore Database Excel',
                'description' => "Berhasil me-restore {$stats['processed_rows']} baris data operasional dari file Excel NF-DAT-002.",
                'original_data' => null,
                'changes' => $stats,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'System',
            ]);
        } catch (\Throwable $e) {}

        return [
            'success' => true,
            'message' => "Berhasil me-restore {$stats['processed_rows']} baris data operasional ke database!",
            'stats' => [
                'total_rows' => $stats['total_rows'],
                'processed_rows' => $stats['processed_rows'],
                'egg_total' => $stats['egg_created'] + $stats['egg_updated'],
                'feed_total' => $stats['feed_created'] + $stats['feed_updated'],
                'mortality_total' => $stats['mortality_created'] + $stats['mortality_updated'],
                'coops_count' => count($stats['coops_affected']),
                'dates_count' => count($stats['dates_affected']),
            ],
        ];
    }

    /**
     * Ekstraksi Huruf Blok (A, B, C, D, E, F) dari kolom Blok atau kolom Umur (misal '18 A', '18 B')
     */
    public static function extractBlockLetter(string $rawBlok, string $rawUmur = ''): string
    {
        $rawBlok = trim($rawBlok);
        $rawUmur = trim($rawUmur);

        // 1. Jika rawBlok adalah persis 1 huruf tunggal (A - Z)
        if (preg_match('/^[A-Za-z]$/', $rawBlok)) {
            return strtoupper($rawBlok);
        }

        // 2. Jika rawBlok mengandung pola "Blok A", "Blok B", "Kandang C", dst.
        if (!empty($rawBlok)) {
            if (preg_match('/(?:blok|kandang|coop)?\s*([A-Za-z])\b/i', $rawBlok, $m)) {
                return strtoupper($m[1]);
            }
        }

        // 3. Cek dari string umur, misal '18 A', '18 B', '18C', '19 D', '20-E', '21 F'
        if (!empty($rawUmur)) {
            if (preg_match('/\b\d+\s*[-_]?\s*([A-Za-z])\b/i', $rawUmur, $m)) {
                return strtoupper($m[1]);
            }
            if (preg_match('/([A-Za-z])\b/i', $rawUmur, $m)) {
                return strtoupper($m[1]);
            }
        }

        // 4. Cari huruf alfabet apa pun di rawBlok selain kata "blok" / "kandang"
        $cleanBlok = preg_replace('/blok|kandang|coop/i', '', $rawBlok);
        if (preg_match('/([A-Za-z])/', $cleanBlok, $m)) {
            return strtoupper($m[1]);
        }

        return 'A';
    }

    /**
     * Parse nilai angka yang mungkin menggunakan koma (misal 28,3 menjadi 28.3)
     */
    public static function parseNumeric($val): float
    {
        if (empty($val)) return 0.0;
        if (is_numeric($val)) return (float)$val;
        $str = str_replace([' ', '%'], '', (string)$val);
        $str = str_replace(',', '.', $str);
        return (float)$str;
    }

    /**
     * Normalisasi format Tanggal ke Y-m-d
     */
    public static function normalizeDate($val): ?string
    {
        if (empty($val)) return null;

        // Serial number tanggal Excel (misal 46250)
        if (is_numeric($val) && (float)$val > 20000 && (float)$val < 80000) {
            $unix = ((float)$val - 25569) * 86400;
            return gmdate('Y-m-d', (int)$unix);
        }

        $val = trim((string)$val);

        // Format d/m/Y (15/08/2026 atau 15-08-2026)
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        // Format Y-m-d (2026-08-15)
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $val, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }

        try {
            return Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Normalisasi format Jam ke H:i:s
     */
    public static function normalizeTime($val): string
    {
        if (empty($val)) return '12:00:00';

        // Fraksi waktu Excel (misal 0.8949)
        if (is_numeric($val) && (float)$val < 1 && (float)$val >= 0) {
            $seconds = (int) round((float)$val * 86400);
            return gmdate('H:i:s', $seconds);
        }

        $val = trim((string)$val);
        if (preg_match('/(\d{1,2}:\d{2}(:\d{2})?)/', $val, $m)) {
            $timePart = $m[1];
            if (substr_count($timePart, ':') === 1) {
                return $timePart . ':00';
            }
            return $timePart;
        }

        return '12:00:00';
    }
}
