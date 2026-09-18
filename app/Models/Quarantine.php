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
     * Pastikan tabel quarantines selalu tersedia di database secara otomatis
     * sehingga aplikasi tidak pernah error 1146 bahkan jika artisan migrate belum dijalankan.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('quarantines')) {
                \Illuminate\Support\Facades\Schema::create('quarantines', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('flock_id')->nullable();
                    $table->unsignedBigInteger('coop_id')->nullable();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->date('date');
                    $table->time('time')->nullable();
                    $table->string('battery_number', 100)->nullable();
                    $table->integer('count')->default(1);
                    $table->string('status', 50)->default('sakit');
                    $table->string('cause', 255)->nullable();
                    $table->string('action_taken', 255)->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            // Silently handle if permission denied or already created
        }
    }

    /**
     * Hitung total populasi ayam yang saat ini sedang berada di karantina
     * (Total Sakit - Total Sembuh - Total Mati di Karantina)
     */
    public static function getCurrentCount(): int
    {
        try {
            static::ensureTableExists();
            if (!\Illuminate\Support\Facades\Schema::hasTable('quarantines')) {
                return 0;
            }
            $sakit = (int) static::where('status', 'sakit')->sum('count');
            $sembuh = (int) static::where('status', 'sembuh')->sum('count');
            $mati = (int) static::where('status', 'mati')->sum('count');

            return max(0, $sakit - $sembuh - $mati);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Ambil record karantina per tanggal dengan proteksi tabel aman
     */
    public static function getRecordsByDate($date)
    {
        try {
            static::ensureTableExists();
            if (!\Illuminate\Support\Facades\Schema::hasTable('quarantines')) {
                return collect();
            }
            return static::with(['coop', 'user'])->whereDate('date', $date)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
