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
     * Dapatkan umur ayam dalam minggu, otomatis mengikuti umur flock-nya.
     */
    public function getChickenAgeWeeksAttribute($value)
    {
        if ($this->flock) {
            return $this->flock->current_age_weeks;
        }
        return $value;
    }
}
