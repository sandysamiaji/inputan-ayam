<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'start_date',
        'initial_age_weeks',
        'initial_population',
        'current_population',
        'breed',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
        'initial_age_weeks' => 'integer',
    ];

    /**
     * Hitung usia ayam yang sebenarnya:
     * Usia saat input (initial_age_weeks) + minggu berlalu sejak tanggal masuk (start_date)
     */
    public function getCurrentAgeWeeksAttribute(): int
    {
        if (!$this->start_date) {
            return (int) ($this->initial_age_weeks ?? 0);
        }
        $weeksSinceEntry = (int) \Carbon\Carbon::parse($this->start_date)->diffInWeeks(now());
        return (int) ($this->initial_age_weeks ?? 0) + $weeksSinceEntry;
    }

    public function coops()
    {
        return $this->hasMany(Coop::class);
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
}
