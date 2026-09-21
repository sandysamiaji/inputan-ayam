<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'category',
        'item_name',
        'type',
        'quantity',
        'unit',
        'source',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
