<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coop extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'name',
        'code',
        'capacity',
        'active_chickens',
        'chicken_age_weeks',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'active_chickens' => 'integer',
        'chicken_age_weeks' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Override umur dinamis di memori saat memilih tanggal tertentu
     * (misalnya tanggal di masa depan pada Dashboard atau Rekap).
     */
    protected ?int $dynamicAgeOverride = null;

    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }

    public function eggProductions()
    {
        return $this->hasMany(EggProduction::class);
    }

    public function feedConsumptions()
    {
        return $this->hasMany(FeedConsumption::class);
    }

    public function mortalities()
    {
        return $this->hasMany(Mortality::class);
    }

    public function weightSamples()
    {
        return $this->hasMany(WeightSample::class);
    }

    public function healthTreatments()
    {
        return $this->hasMany(HealthTreatment::class);
    }

    /**
     * Mutator agar ketika controller/view mengeset $coop->chicken_age_weeks = $val,
     * override dinamis ini tersimpan di model tanpa merusak sinkronisasi flock default.
     */
    public function setChickenAgeWeeksAttribute($value)
    {
        $this->dynamicAgeOverride = (int) $value;
        $this->attributes['chicken_age_weeks'] = (int) $value;
    }

    /**
     * Dapatkan umur ayam dalam minggu untuk blok kandang ini.
     * Aturan bisnis:
     * 1. Jika ada override dinamis (misal user memilih tanggal di masa depan di Dashboard), gunakan nilai dinamis.
     * 2. Setiap blok kandang yang tergabung dalam Klotter WAJIB otomatis mengikuti umur Klotternya
     *    (semua blok dalam Klotter 1 seragam mengikuti Klotter 1, semua blok dalam Klotter 2 seragam mengikuti Klotter 2, TIDAK BOLEH BELANG!).
     * 3. Fallback hanya untuk blok kandang non-klotter.
     */
    public function getChickenAgeWeeksAttribute($value)
    {
        if ($this->dynamicAgeOverride !== null) {
            return $this->dynamicAgeOverride;
        }

        if ($this->flock) {
            return (int) $this->flock->current_age_weeks;
        }

        return (int) $value;
    }
}
