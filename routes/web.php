<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditController;

/*
|--------------------------------------------------------------------------
| Web Routes - Nochi Farm Input
|--------------------------------------------------------------------------
*/

// Autentikasi Publik (Tema Merah Marun Putih)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Internal Terproteksi (Wajib Login)
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard Ringkasan
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:menu_dashboard');

    // 2. Halaman Input Mobile
    Route::get('/input', [InputController::class, 'index'])
        ->name('input.index')
        ->middleware('permission:menu_input');

    // Endpoint Input Transaksi
    Route::post('/production/store', [DashboardController::class, 'storeEggProduction'])
        ->name('production.store')
        ->middleware('permission:input_form_egg');

    Route::post('/feed/store', [DashboardController::class, 'storeFeedConsumption'])
        ->name('feed.store')
        ->middleware('permission:input_form_feed');

    Route::post('/mortality/store', [DashboardController::class, 'storeMortality'])
        ->name('mortality.store')
        ->middleware('permission:input_form_mortality');

    Route::post('/weight/store', [DashboardController::class, 'storeWeightSample'])
        ->name('weight.store')
        ->middleware('permission:input_form_weight');

    Route::post('/health/store', [DashboardController::class, 'storeHealthTreatment'])
        ->name('health.store')
        ->middleware('permission:input_form_health');

    // 3. Modul Gudang (Warehouse)
    Route::prefix('gudang')->name('warehouse.')->middleware('permission:menu_warehouse')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/telur', [WarehouseController::class, 'telur'])
            ->name('telur')
            ->middleware('permission:feature_warehouse_telur');
        Route::get('/pakan', [WarehouseController::class, 'pakan'])
            ->name('pakan')
            ->middleware('permission:feature_warehouse_pakan');
        Route::get('/obat', [WarehouseController::class, 'obat'])
            ->name('obat')
            ->middleware('permission:feature_warehouse_obat');
        Route::get('/karantina', [WarehouseController::class, 'karantina'])
            ->name('karantina')
            ->middleware('permission:warehouse_click_quarantine');
        Route::get('/mortalitas', [WarehouseController::class, 'karantina'])
            ->name('mortalitas');

        Route::post('/store', [WarehouseController::class, 'store'])
            ->name('store')
            ->middleware('permission:feature_warehouse_manage');
        Route::put('/{id}/update', [WarehouseController::class, 'update'])
            ->name('update')
            ->middleware('permission:feature_warehouse_manage');
        Route::patch('/{id}/toggle-status', [WarehouseController::class, 'toggleStatus'])
            ->name('toggle-status')
            ->middleware('permission:feature_warehouse_manage');
        Route::delete('/{id}/destroy', [WarehouseController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:feature_warehouse_manage');
    });

    // 4. Modul Rekap Data (Reporting & Analytics)
    Route::prefix('rekap')->name('rekap.')->middleware('permission:menu_rekap')->group(function () {
        Route::get('/', [RekapController::class, 'index'])->name('index');
        Route::get('/detail', [RekapController::class, 'detail'])
            ->name('detail')
            ->middleware('permission:feature_rekap_detail');
        Route::get('/export-excel', [RekapController::class, 'exportExcel'])
            ->name('export-excel')
            ->middleware('permission:feature_rekap_export');
        
        // Fitur Kelola Data Historis
        Route::put('/data/production/{id}', [RekapController::class, 'updateEggProduction'])
            ->name('data.production.update')
            ->middleware('permission:feature_rekap_manage');
        Route::delete('/data/production/{id}', [RekapController::class, 'destroyEggProduction'])
            ->name('data.production.destroy')
            ->middleware('permission:feature_rekap_manage');
        
        Route::put('/data/feed/{id}', [RekapController::class, 'updateFeedConsumption'])
            ->name('data.feed.update')
            ->middleware('permission:feature_rekap_manage');
        Route::delete('/data/feed/{id}', [RekapController::class, 'destroyFeedConsumption'])
            ->name('data.feed.destroy')
            ->middleware('permission:feature_rekap_manage');
        
        Route::put('/data/mortality/{id}', [RekapController::class, 'updateMortality'])
            ->name('data.mortality.update')
            ->middleware('permission:feature_rekap_manage');
        Route::delete('/data/mortality/{id}', [RekapController::class, 'destroyMortality'])
            ->name('data.mortality.destroy')
            ->middleware('permission:feature_rekap_manage');
        
        Route::put('/data/weight/{id}', [RekapController::class, 'updateWeightSample'])
            ->name('data.weight.update')
            ->middleware('permission:feature_rekap_manage');
        Route::delete('/data/weight/{id}', [RekapController::class, 'destroyWeightSample'])
            ->name('data.weight.destroy')
            ->middleware('permission:feature_rekap_manage');
        
        Route::put('/data/health/{id}', [RekapController::class, 'updateHealthTreatment'])
            ->name('data.health.update')
            ->middleware('permission:feature_rekap_manage');
        Route::delete('/data/health/{id}', [RekapController::class, 'destroyHealthTreatment'])
            ->name('data.health.destroy')
            ->middleware('permission:feature_rekap_manage');
    });

    // 5. Modul Master Data
    Route::prefix('master')->name('master.')->middleware('permission:menu_master')->group(function () {
        Route::get('/', [MasterController::class, 'index'])->name('index');
        Route::get('/info-farm', [MasterController::class, 'infoFarm'])->name('info-farm');
        Route::post('/info-farm/update', [MasterController::class, 'updateInfoFarm'])->name('info-farm.update');
        Route::get('/flocks', [MasterController::class, 'flocks'])->name('flocks');
        Route::post('/flocks/store', [MasterController::class, 'storeFlock'])->name('flocks.store');
        Route::put('/flocks/{id}/update', [MasterController::class, 'updateFlock'])->name('flocks.update');
        Route::delete('/flocks/{id}/destroy', [MasterController::class, 'destroyFlock'])->name('flocks.destroy');
        Route::post('/coops/store', [MasterController::class, 'storeCoop'])->name('coops.store');
        Route::put('/coops/{id}/update', [MasterController::class, 'updateCoop'])->name('coops.update');
        Route::delete('/coops/{id}/destroy', [MasterController::class, 'destroyCoop'])->name('coops.destroy');
        Route::get('/standards', [MasterController::class, 'standards'])->name('standards');
        Route::post('/standards/update', [MasterController::class, 'updateStandards'])->name('standards.update');
        Route::get('/medicines', [MasterController::class, 'medicines'])->name('medicines');
        Route::get('/settings', [MasterController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [MasterController::class, 'updateSettings'])->name('settings.update');
        Route::get('/standar-produksi', [MasterController::class, 'standarProduksi'])->name('standar-produksi');
        Route::get('/standar-pakan', [MasterController::class, 'standarPakan'])->name('standar-pakan');
        Route::get('/standar-bb', [MasterController::class, 'standarBB'])->name('standar-bb');
        Route::post('/weekly-standards/{week}/update', [MasterController::class, 'updateWeeklyStandard'])->name('weekly-standards.update');
        Route::get('/vaksin-obat', function() { return redirect('/master#card-vaksin-obat'); })->name('vaksin-obat');
        Route::get('/pengaturan', function() { return redirect('/master#card-pengaturan'); })->name('pengaturan');

        // Manajemen Hak Akses & Pengguna (Admin Only)
        Route::get('/hak-akses', [MasterController::class, 'permissions'])->name('permissions');
        Route::post('/hak-akses/{userId}/toggle', [MasterController::class, 'togglePermission'])->name('permissions.toggle');
        Route::post('/hak-akses/{userId}/bulk', [MasterController::class, 'bulkPermission'])->name('permissions.bulk');
        Route::post('/users/store', [MasterController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}/update', [MasterController::class, 'updateUser'])->name('users.update');
        Route::patch('/users/{id}/toggle-active', [MasterController::class, 'toggleActiveUser'])->name('users.toggle-active');

        // Audit Riwayat & Restore Data Terhapus
        Route::get('/audit', [AuditController::class, 'index'])
            ->name('audit')
            ->middleware('permission:feature_master_audit');
        Route::get('/audit/{id}', [AuditController::class, 'show'])
            ->name('audit.show')
            ->middleware('permission:feature_master_audit');
        Route::post('/audit/{id}/restore', [AuditController::class, 'restore'])
            ->name('audit.restore')
            ->middleware('permission:feature_audit_restore');

        // Restore Data Operasional dari Excel (NF-DAT-002)
        Route::post('/restore-excel', [MasterController::class, 'restoreExcel'])->name('restore-excel');
        Route::get('/restore-excel/template', [MasterController::class, 'downloadSampleTemplate'])->name('restore-excel.template');
    });

    // Alias cepat /audit
    Route::get('/audit', function() {
        return redirect()->route('master.audit');
    });

    // 6. Modul Profil Petugas
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');
    });

});

// Route bantuan utilitas
Route::get('/clear-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return 'Cache cleared!';
});

Route::get('/seed-standards', function() { 
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'WeeklyStandardSeeder']); 
    return 'Data standar berhasil dimasukkan ke database! Silakan kembali ke halaman sebelumnya dan refresh.'; 
});