<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quarantine extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'coop_id',
        'user_id',
        'date',
        'time',
        'battery_number',
        'count',
        'status',
        'cause',
        'action_taken',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'count' => 'integer',
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

    /**
     * Hitung total populasi ayam yang saat ini sedang berada di karantina
     * (Total Sakit - Total Sembuh - Total Mati di Karantina)
     */
    public static function getCurrentCount(): int
    {
        try {
            $sakit = (int) static::where('status', 'sakit')->sum('count');
            $sembuh = (int) static::where('status', 'sembuh')->sum('count');
            $mati = (int) static::where('status', 'mati')->sum('count');

            return max(0, $sakit - $sembuh - $mati);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
