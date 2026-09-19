<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'model_type',
        'model_id',
        'table_name',
        'original_data',
        'changes',
        'ip_address',
        'user_agent',
        'is_restored',
        'restored_at',
        'restored_by',
        'restored_by_name',
    ];

    protected $casts = [
        'original_data' => 'array',
        'changes' => 'array',
        'is_restored' => 'boolean',
        'restored_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restorer()
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    /**
     * Pastikan tabel audit_logs selalu tersedia di database secara otomatis
     * sehingga aplikasi tidak pernah mengalami error 1146.
     */
    public static function ensureTableExists(): void
    {
        try {
            if (!Schema::hasTable('audit_logs')) {
                Schema::create('audit_logs', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->string('user_name', 100)->nullable();
                    $table->string('user_role', 50)->nullable();
                    $table->string('action', 50);
                    $table->string('module', 50);
                    $table->text('description');
                    $table->string('model_type', 150)->nullable();
                    $table->unsignedBigInteger('model_id')->nullable();
                    $table->string('table_name', 100)->nullable();
                    $table->longText('original_data')->nullable();
                    $table->longText('changes')->nullable();
                    $table->string('ip_address', 45)->nullable();
                    $table->text('user_agent')->nullable();
                    $table->boolean('is_restored')->default(false);
                    $table->timestamp('restored_at')->nullable();
                    $table->unsignedBigInteger('restored_by')->nullable();
                    $table->string('restored_by_name', 100)->nullable();
                    $table->timestamps();

                    $table->index(['action', 'module']);
                    $table->index(['created_at']);
                    $table->index(['is_restored']);
                });
            }
        } catch (\Throwable $e) {
            // Silently handle if table already created or db connection issues
        }
    }
}
