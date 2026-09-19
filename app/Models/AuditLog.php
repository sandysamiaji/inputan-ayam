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
     * dan semua kolom penting (seperti module, action, description, dll.)
     * langsung ditambahkan jika sebelumnya sudah ada tabel audit_logs lama.
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
                    $table->string('action', 50)->default('GENERAL');
                    $table->string('module', 50)->default('umum');
                    $table->text('description')->nullable();
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
                return;
            }

            // Dapatkan daftar nama kolom yang ada saat ini secara aman
            $existingColumns = [];
            try {
                $existingColumns = array_map('strtolower', Schema::getColumnListing('audit_logs'));
            } catch (\Throwable $ex) {
                // Fallback via DB::select jika getColumnListing gagal
                try {
                    $rawCols = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `audit_logs`");
                    foreach ($rawCols as $rc) {
                        $row = (array) $rc;
                        $fieldVal = $row['Field'] ?? $row['field'] ?? $row['FIELD'] ?? null;
                        if ($fieldVal) {
                            $existingColumns[] = strtolower($fieldVal);
                        }
                    }
                } catch (\Throwable $rawEx) {
                    // ignore
                }
            }

            $columnsToAdd = [
                'user_id' => "ALTER TABLE `audit_logs` ADD COLUMN `user_id` BIGINT(20) UNSIGNED NULL",
                'user_name' => "ALTER TABLE `audit_logs` ADD COLUMN `user_name` VARCHAR(100) NULL",
                'user_role' => "ALTER TABLE `audit_logs` ADD COLUMN `user_role` VARCHAR(50) NULL",
                'action' => "ALTER TABLE `audit_logs` ADD COLUMN `action` VARCHAR(50) NOT NULL DEFAULT 'GENERAL'",
                'module' => "ALTER TABLE `audit_logs` ADD COLUMN `module` VARCHAR(50) NOT NULL DEFAULT 'umum'",
                'description' => "ALTER TABLE `audit_logs` ADD COLUMN `description` TEXT NULL",
                'model_type' => "ALTER TABLE `audit_logs` ADD COLUMN `model_type` VARCHAR(150) NULL",
                'model_id' => "ALTER TABLE `audit_logs` ADD COLUMN `model_id` BIGINT(20) UNSIGNED NULL",
                'table_name' => "ALTER TABLE `audit_logs` ADD COLUMN `table_name` VARCHAR(100) NULL",
                'original_data' => "ALTER TABLE `audit_logs` ADD COLUMN `original_data` LONGTEXT NULL",
                'changes' => "ALTER TABLE `audit_logs` ADD COLUMN `changes` LONGTEXT NULL",
                'ip_address' => "ALTER TABLE `audit_logs` ADD COLUMN `ip_address` VARCHAR(45) NULL",
                'user_agent' => "ALTER TABLE `audit_logs` ADD COLUMN `user_agent` TEXT NULL",
                'is_restored' => "ALTER TABLE `audit_logs` ADD COLUMN `is_restored` TINYINT(1) NOT NULL DEFAULT 0",
                'restored_at' => "ALTER TABLE `audit_logs` ADD COLUMN `restored_at` TIMESTAMP NULL DEFAULT NULL",
                'restored_by' => "ALTER TABLE `audit_logs` ADD COLUMN `restored_by` BIGINT(20) UNSIGNED NULL",
                'restored_by_name' => "ALTER TABLE `audit_logs` ADD COLUMN `restored_by_name` VARCHAR(100) NULL",
            ];

            foreach ($columnsToAdd as $col => $alterSql) {
                if (empty($existingColumns) || !in_array($col, $existingColumns)) {
                    try {
                        \Illuminate\Support\Facades\DB::statement($alterSql);
                        $existingColumns[] = $col;
                    } catch (\Throwable $alterEx) {
                        // Kolom mungkin sudah ada atau nama berbeda, lewati agar tidak memblok kolom lain
                    }
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
