<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyStandard extends Model
{
    use HasFactory;

    protected $table = 'weekly_standards';

    protected $fillable = [
        'week',
        'phase',
        'pill',
        'feed_type',
        'hd_target',
        'egg_weight',
        'feed_gram',
        'weight_min',
        'weight_target',
        'weight_max',
        'description',
    ];

    protected $casts = [
        'week' => 'integer',
        'hd_target' => 'float',
        'feed_gram' => 'float',
        'weight_min' => 'float',
        'weight_target' => 'float',
        'weight_max' => 'float',
    ];

    /**
     * Scope untuk mengurutkan berdasarkan umur minggu
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('week', 'asc');
    }

    /**
     * Hitung porsi pakan pagi (40%)
     */
    public function getFeedPagiAttribute(): float
    {
        return round($this->feed_gram * 0.40, 1);
    }

    /**
     * Hitung porsi pakan sore (60%)
     */
    public function getFeedSoreAttribute(): float
    {
        return round($this->feed_gram * 0.60, 1);
    }

    /**
     * Helper warna pill badge fase
     */
    public function getPillBadgeClassAttribute(): string
    {
        return match ($this->pill) {
            'GROWER' => 'bg-blue-50 text-blue-700 border-blue-200',
            'PRE-LAY' => 'bg-amber-50 text-amber-700 border-amber-200',
            'AWAL BERTELUR' => 'bg-orange-50 text-orange-700 border-orange-200',
            'PUNCAK PRODUKSI' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'PASCA PUNCAK' => 'bg-teal-50 text-teal-700 border-teal-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
