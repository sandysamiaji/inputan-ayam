<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Services\AuditService;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(mixed $model): void
    {
        if (AuditService::$isRestoring || $model instanceof AuditLog) {
            return;
        }

        $module = AuditService::getModuleForModel($model);
        $description = AuditService::describeModel($model, 'CREATE');

        AuditService::log(
            'CREATE',
            $module,
            $description,
            $model,
            $model->getAttributes()
        );
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(mixed $model): void
    {
        if (AuditService::$isRestoring || $model instanceof AuditLog) {
            return;
        }

        $dirty = $model->getDirty();
        unset($dirty['updated_at'], $dirty['created_at']);

        // Jika tidak ada kolom substantif yang berubah, abaikan
        if (empty($dirty)) {
            return;
        }

        $old = array_intersect_key($model->getOriginal(), $dirty);
        $module = AuditService::getModuleForModel($model);
        $description = AuditService::describeModel($model, 'UPDATE');

        AuditService::log(
            'UPDATE',
            $module,
            $description,
            $model,
            null,
            ['old' => $old, 'new' => $dirty]
        );
    }

    /**
     * Handle the Model "deleting" event.
     * Mengambil snapshot atribut lengkap sebelum baris dihapus dari database.
     */
    public function deleting(mixed $model): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        $module = AuditService::getModuleForModel($model);
        $description = AuditService::describeModel($model, 'DELETE');

        AuditService::log(
            'DELETE',
            $module,
            $description,
            $model,
            $model->getAttributes()
        );
    }
}
