<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EggProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'coop_id',
        'user_id',
        'date',
        'time',
        'total_eggs',
        'broken_eggs',
        'abnormal_eggs',
        'good_eggs',
        'crates_count',
        'weight_kg',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'total_eggs' => 'integer',
        'broken_eggs' => 'integer',
        'abnormal_eggs' => 'integer',
        'good_eggs' => 'integer',
        'crates_count' => 'decimal:2',
        'weight_kg' => 'decimal:2',
    ];

    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }

    public function coop()
    {
        return $this->belongsTo(Coop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
