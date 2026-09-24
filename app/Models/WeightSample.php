<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightSample extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'coop_id',
        'battery_number',
        'sample_index',
        'user_id',
        'date',
        'sample_count',
        'average_weight_kg',
        'egg_weight_gram',
        'uniformity_percentage',
        'age_weeks',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'sample_count' => 'integer',
        'sample_index' => 'integer',
        'average_weight_kg' => 'decimal:3',
        'egg_weight_gram' => 'decimal:2',
        'uniformity_percentage' => 'decimal:2',
        'age_weeks' => 'integer',
    ];

    public static function ensureColumnsExist(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('weight_samples')) {
                \Illuminate\Support\Facades\Schema::table('weight_samples', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('weight_samples', 'battery_number')) {
                        $table->string('battery_number')->nullable()->after('coop_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('weight_samples', 'sample_index')) {
                        $table->tinyInteger('sample_index')->nullable()->after('battery_number');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('weight_samples', 'egg_weight_gram')) {
                        $table->decimal('egg_weight_gram', 6, 2)->nullable()->after('average_weight_kg');
                    }
                });
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('WeightSample ensureColumnsExist error: ' . $e->getMessage());
        }
    }

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
