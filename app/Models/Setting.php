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
}
