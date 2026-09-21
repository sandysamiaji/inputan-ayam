<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai raw setting berdasarkan key
     */
    public static function getVal(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /**
     * Ambil nilai numerik float (otomatis parsing koma/titik dan unit text)
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        $val = static::getVal($key);
        if ($val === null || $val === '') return $default;
        $clean = str_replace(',', '.', preg_replace('/[^0-9.,]/', '', (string)$val));
        return is_numeric($clean) ? (float)$clean : $default;
    }

    /**
     * Ambil nilai integer
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return (int) round(static::getFloat($key, (float)$default));
    }

    /**
     * Berat pakan per karung (kg) dari Database Settings
     * Default standar: 50 kg
     */
    public static function getKgPerKarung(): float
    {
        $kg = static::getFloat('berat_per_karung', 50.0);
        return $kg > 0 ? $kg : 50.0;
    }

    /**
     * Isi butir per tray dari Database Settings
     * Default standar: 30 butir
     */
    public static function getIsiTray(): int
    {
        $tray = static::getInt('isi_tray', 30);
        return $tray > 0 ? $tray : 30;
    }

    /**
     * Berat telur acuan per butir (kg) dari Database Settings
     * Default standar: 0.06 kg (60 gram)
     */
    public static function getBeratTelurKg(): float
    {
        $kg = static::getFloat('berat_telur', 0.06);
        return $kg > 0 ? $kg : 0.06;
    }

    /**
     * Format kuantitas pakan dalam kg menjadi representasi karung bulat + sisa kg (tanpa koma di karung)
     * Contoh:
     * - 5.500 kg => "110 krg"
     * - 1.403 kg => "28 krg + 3 kg"
     * - 4.097 kg => "81 krg + 47 kg"
     * - 2.550 kg => "51 krg"
     * - 485 kg   => "9 krg + 35 kg"
     * - 2.065 kg => "41 krg + 15 kg"
     */
    public static function formatKarungKg(float|int|string|null $kg, ?float $kgPerKarung = null, string $krgUnit = 'krg'): string
    {
        $numKg = (float) $kg;
        $kPerKrg = $kgPerKarung ?: static::getKgPerKarung();
        if ($kPerKrg <= 0) $kPerKrg = 50.0;

        $isNegative = $numKg < 0;
        $absKg = abs($numKg);

        $krg = (int) floor($absKg / $kPerKrg);
        $sisaKg = round(fmod($absKg, $kPerKrg), 1);
        if ($sisaKg >= $kPerKrg) {
            $krg += 1;
            $sisaKg = 0.0;
        }

        $krgFormatted = number_format($krg, 0, ',', '.');
        $sisaKgFormatted = $sisaKg == floor($sisaKg) ? number_format($sisaKg, 0, ',', '.') : number_format($sisaKg, 1, ',', '.');

        if ($krg > 0 && $sisaKg > 0) {
            $result = "{$krgFormatted} {$krgUnit} + {$sisaKgFormatted} kg";
            return $isNegative ? "-({$result})" : $result;
        } elseif ($krg > 0) {
            $result = "{$krgFormatted} {$krgUnit}";
            return $isNegative ? "-{$result}" : $result;
        } elseif ($sisaKg > 0) {
            $result = "0 {$krgUnit} + {$sisaKgFormatted} kg";
            return $isNegative ? "-({$result})" : $result;
        } else {
            return "0 {$krgUnit}";
        }
    }
}
