<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Coop;
use App\Models\EggProduction;
use App\Models\FeedConsumption;
use App\Models\Mortality;
use App\Models\Quarantine;
use App\Models\WeightSample;
use App\Models\HealthTreatment;
use App\Models\FarmStock;
use App\Models\Flock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Flag untuk menandai proses pemulihan sedang berlangsung
     * agar event created tidak menghasilkan duplikasi log saat restore.
     */
    public static bool $isRestoring = false;

    /**
     * Catat aktivitas audit secara umum
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        mixed $model = null,
        mixed $originalData = null,
        mixed $changes = null,
        ?User $user = null
    ): ?AuditLog {
        try {
            AuditLog::ensureTableExists();

            $user = $user ?? Auth::user();
            $ip = Request::ip() ?? '127.0.0.1';
            $userAgent = Request::userAgent() ?? 'System';

            $modelType = $model ? get_class($model) : null;
            $modelId = $model ? ($model->id ?? null) : null;
            $tableName = $model ? (method_exists($model, 'getTable') ? $model->getTable() : null) : null;

            return AuditLog::create([
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? ($user->username ? '@' . ltrim($user->username, '@') : $user->name) : 'Sistem / Anonim',
                'user_role' => $user ? $user->role : 'user',
                'action' => strtoupper($action),
                'module' => strtolower($module),
                'description' => $description,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'table_name' => $tableName,
                'original_data' => $originalData,
                'changes' => $changes,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'is_restored' => false,
            ]);
        } catch (\Throwable $e) {
            // Jangan menghentikan proses aplikasi jika terjadi kesalahan pencatatan log
            report($e);
            return null;
        }
    }

    /**
     * Tentukan nama modul sistem berdasarkan tipe model
     */
    public static function getModuleForModel(mixed $model): string
    {
        if ($model instanceof EggProduction) return 'telur';
        if ($model instanceof FeedConsumption) return 'pakan';
        if ($model instanceof Mortality) return 'mortalitas';
        if ($model instanceof Quarantine) return 'karantina';
        if ($model instanceof WeightSample) return 'bobot';
        if ($model instanceof HealthTreatment) return 'obat';
        if ($model instanceof FarmStock) return 'gudang';
        if ($model instanceof Coop || $model instanceof Flock) return 'master';
        if ($model instanceof User) return 'pengguna';
        return 'umum';
    }

    /**
     * Buat deskripsi manusiawi informatif dalam bahasa Indonesia
     */
    public static function describeModel(mixed $model, string $action): string
    {
        $actionText = match (strtoupper($action)) {
            'CREATE' => 'Menambahkan',
            'UPDATE' => 'Memperbarui',
            'DELETE' => 'Menghapus',
            'RESTORE' => 'Memulihkan',
            default => 'Memproses',
        };

        if ($model instanceof EggProduction) {
            $coopName = $model->coop ? $model->coop->name : 'Kandang';
            $tgl = $model->date ? Carbon::parse($model->date)->translatedFormat('d M Y') : 'hari ini';
            $total = number_format($model->total_eggs ?? ($model->good_eggs + ($model->broken_eggs ?? 0)), 0, ',', '.');
            $peti = (float) ($model->crates_count ?? 0);
            return "{$actionText} data produksi telur {$coopName} ({$total} butir / {$peti} peti) tanggal {$tgl}";
        }

        if ($model instanceof FeedConsumption) {
            $coopName = $model->coop ? $model->coop->name : 'Kandang';
            $feedName = $model->feed_name ?? 'Pakan';
            $qty = number_format($model->quantity_kg ?? 0, 1, ',', '.');
            $waktu = $model->feeding_time ? " ({$model->feeding_time})" : '';
            $tgl = $model->date ? Carbon::parse($model->date)->translatedFormat('d M Y') : 'hari ini';
            return "{$actionText} data pemberian pakan {$feedName} ({$qty} kg){$waktu} pada {$coopName} tanggal {$tgl}";
        }

        if ($model instanceof Mortality) {
            $coopName = $model->coop ? $model->coop->name : 'Kandang';
            $count = number_format($model->count ?? 1, 0, ',', '.');
            $type = $model->type === 'afkir' ? 'ayam afkir (culling)' : 'kematian ayam';
            $cause = $model->cause ? " (Penyebab: {$model->cause})" : '';
            $tgl = $model->date ? Carbon::parse($model->date)->translatedFormat('d M Y') : 'hari ini';
            return "{$actionText} data {$type} sejumlah {$count} ekor pada {$coopName}{$cause} tanggal {$tgl}";
        }

        if ($model instanceof Quarantine) {
            $coopName = $model->coop ? $model->coop->name : 'Kandang';
            $count = number_format($model->count ?? 1, 0, ',', '.');
            $statusLabel = match ($model->status) {
                'sakit' => 'sakit masuk isolasi',
                'sembuh' => 'sembuh kembali ke kandang',
                'mati' => 'mati di isolasi',
                default => 'karantina',
            };
            $battery = $model->battery_number ? " [Baterai: {$model->battery_number}]" : '';
            $tgl = $model->date ? Carbon::parse($model->date)->translatedFormat('d M Y') : 'hari ini';
            return "{$actionText} data ayam {$statusLabel} sejumlah {$count} ekor pada {$coopName}{$battery} tanggal {$tgl}";
        }

        if ($model instanceof WeightSample) {
            $coopName = $model->coop ? $model->coop->name : 'Kandang';
            $avg = number_format($model->average_weight_kg ?? 0, 3, ',', '.');
            $unif = $model->uniformity_percentage ? " (Keseragaman: {$model->uniformity_percentage}%)" : '';
            $tgl = $model->date ? Carbon::parse($model->date)->translatedFormat('d M Y') : 'hari ini';
            return "{$actionText} data sampling bobot ayam {$coopName} rata-rata {$avg} kg{$unif} tanggal {$tgl}";
        }

        if ($model instanceof HealthTreatment) {
            $coopName = $model->coop ? $model->coop->name : 'Kandang';
            $med = $model->medicine_name ?? 'Obat/Vaksin';
            $dose = $model->dosage ? " dosis {$model->dosage}" : '';
            $tgl = $model->date ? Carbon::parse($model->date)->translatedFormat('d M Y') : 'hari ini';
            return "{$actionText} data perlakuan medis {$med}{$dose} pada {$coopName} tanggal {$tgl}";
        }

        if ($model instanceof FarmStock) {
            $cat = ucfirst($model->category ?? 'barang');
            $item = $model->item_name ?? 'Item';
            $qty = number_format($model->quantity ?? 0, 1, ',', '.') . ' ' . ($model->unit ?? 'satuan');
            $type = $model->type === 'masuk' ? 'masuk' : 'keluar';
            return "{$actionText} transaksi gudang {$cat} ({$type}): {$item} sebanyak {$qty}";
        }

        if ($model instanceof Coop) {
            return "{$actionText} data blok kandang: {$model->name} (Kapasitas: " . number_format($model->capacity ?? 0, 0, ',', '.') . " ekor)";
        }

        if ($model instanceof Flock) {
            return "{$actionText} data klotter farm: {$model->name} (Kode: {$model->code})";
        }

        if ($model instanceof User) {
            $username = $model->username ? "@{$model->username}" : $model->name;
            return "{$actionText} akun pengguna {$model->name} ({$username}, Role: {$model->role})";
        }

        $baseName = class_basename($model);
        return "{$actionText} data {$baseName} #" . ($model->id ?? '');
    }

    /**
     * Memulihkan / me-restore data yang telah dihapus kembali ke database
     */
    public static function restoreRecord(int $auditLogId, ?User $actor = null): array
    {
        $actor = $actor ?? Auth::user();

        if (!$actor) {
            return ['success' => false, 'message' => 'Autentikasi diperlukan untuk memulihkan data.'];
        }

        // Cek otorisasi restore (Role Admin atau memiliki permission feature_audit_restore)
        if ($actor->role !== 'admin' && !$actor->canAccess('feature_audit_restore')) {
            return ['success' => false, 'message' => 'Akses Ditolak: Anda tidak memiliki izin untuk memulihkan data yang terhapus.'];
        }

        $auditLog = AuditLog::find($auditLogId);
        if (!$auditLog) {
            return ['success' => false, 'message' => 'Catatan riwayat audit tidak ditemukan.'];
        }

        if ($auditLog->action !== 'DELETE') {
            return ['success' => false, 'message' => 'Hanya riwayat data yang berstatus HAPUS (DELETE) yang dapat dipulihkan.'];
        }

        if ($auditLog->is_restored) {
            return ['success' => false, 'message' => 'Data ini sudah pernah dipulihkan sebelumnya oleh ' . ($auditLog->restored_by_name ?? 'Admin') . '.'];
        }

        $modelClass = $auditLog->model_type;
        $snapshot = $auditLog->original_data;

        if (!$modelClass || !class_exists($modelClass)) {
            return ['success' => false, 'message' => 'Tipe model data tidak dikenali atau tabel asal sudah tidak tersedia.'];
        }

        if (empty($snapshot) || !is_array($snapshot)) {
            return ['success' => false, 'message' => 'Snapshot data asli kosong sehingga tidak dapat direkonstruksi.'];
        }

        try {
            self::$isRestoring = true;

            // Bersihkan kolom yang tidak perlu di-insert secara manual
            $attributes = $snapshot;
            unset($attributes['created_at'], $attributes['updated_at']);

            // Jika primary key belum terisi oleh baris baru, coba pertahankan ID aslinya
            $originalId = $auditLog->model_id;
            if ($originalId && !$modelClass::find($originalId)) {
                $attributes['id'] = $originalId;
            } else {
                unset($attributes['id']);
            }

            // Validasi relasi foreign key jika ada (coop_id, flock_id)
            if (!empty($attributes['coop_id'])) {
                $coopExists = Coop::find($attributes['coop_id']);
                if (!$coopExists) {
                    // Fallback ke coop aktif pertama jika blok lama sudah dihapus
                    $fallbackCoop = Coop::where('is_active', true)->first();
                    if ($fallbackCoop) {
                        $attributes['coop_id'] = $fallbackCoop->id;
                        $attributes['flock_id'] = $fallbackCoop->flock_id;
                    }
                }
            }

            // Buat instance model baru dan unguard atribut
            $model = new $modelClass();
            $modelClass::unguarded(function () use ($model, $attributes) {
                $model->fill($attributes);
                $model->save();
            });

            // Sinkronisasi dampak populasi ayam aktif kandang jika mortalitas / karantina dipulihkan
            if ($model instanceof Mortality && $model->coop_id) {
                $coop = Coop::find($model->coop_id);
                if ($coop) {
                    // Mengembalikan catatan kematian berarti ayam di kandang kembali berkurang
                    $coop->decrement('active_chickens', (int) $model->count);
                }
            } elseif ($model instanceof Quarantine && $model->coop_id) {
                $coop = Coop::find($model->coop_id);
                if ($coop) {
                    if ($model->status === 'sakit') {
                        $coop->decrement('active_chickens', (int) $model->count);
                    } elseif ($model->status === 'sembuh') {
                        $coop->increment('active_chickens', (int) $model->count);
                    }
                }
            }

            self::$isRestoring = false;

            // Update status log audit sebagai sudah dipulihkan
            $auditLog->update([
                'is_restored' => true,
                'restored_at' => Carbon::now(),
                'restored_by' => $actor->id,
                'restored_by_name' => $actor->username ? '@' . ltrim($actor->username, '@') : $actor->name,
            ]);

            // Catat log aktivitas restore baru
            self::log(
                'RESTORE',
                $auditLog->module,
                "Memulihkan data terhapus kembali ke database: " . $auditLog->description,
                $model,
                $model->getAttributes(),
                null,
                $actor
            );

            return [
                'success' => true,
                'message' => 'Data berhasil dipulihkan seperti semula ke sistem database!',
                'model' => $model,
            ];

        } catch (\Throwable $e) {
            self::$isRestoring = false;
            report($e);
            return [
                'success' => false,
                'message' => 'Gagal memulihkan data: ' . $e->getMessage(),
            ];
        }
    }
}
