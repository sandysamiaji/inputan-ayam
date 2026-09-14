<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InputController;

/*
|--------------------------------------------------------------------------
| Web Routes - Nochi Farm Input
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Halaman Input Mobile
Route::get('/input', [InputController::class, 'index'])->name('input.index');

// Endpoint Input Aksi Cepat Kandang
Route::post('/production/store', [DashboardController::class, 'storeEggProduction'])->name('production.store');
Route::post('/feed/store', [DashboardController::class, 'storeFeedConsumption'])->name('feed.store');
Route::post('/mortality/store', [DashboardController::class, 'storeMortality'])->name('mortality.store');
Route::post('/weight/store', [DashboardController::class, 'storeWeightSample'])->name('weight.store');
Route::post('/health/store', [DashboardController::class, 'storeHealthTreatment'])->name('health.store');

// Modul Gudang (Warehouse)
Route::prefix('gudang')->name('warehouse.')->group(function () {
    Route::get('/', [WarehouseController::class, 'index'])->name('index');
    Route::get('/telur', [WarehouseController::class, 'telur'])->name('telur');
    Route::get('/pakan', [WarehouseController::class, 'pakan'])->name('pakan');
    Route::get('/obat', [WarehouseController::class, 'obat'])->name('obat');
    Route::post('/store', [WarehouseController::class, 'store'])->name('store');
    Route::put('/{id}/update', [WarehouseController::class, 'update'])->name('update');
    Route::patch('/{id}/toggle-status', [WarehouseController::class, 'toggleStatus'])->name('toggle-status');
    Route::delete('/{id}/destroy', [WarehouseController::class, 'destroy'])->name('destroy');
});

// Modul Rekap Data (Reporting & Analytics)
Route::prefix('rekap')->name('rekap.')->group(function () {
    Route::get('/', [RekapController::class, 'index'])->name('index');
    Route::get('/detail', [RekapController::class, 'detail'])->name('detail');
    Route::get('/export-excel', [RekapController::class, 'exportExcel'])->name('export-excel');
});

// Modul Master Data
Route::prefix('master')->name('master.')->group(function () {
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
    Route::get('/standar-produksi', function() { return redirect('/master#card-standar-produksi'); })->name('standar-produksi');
    Route::get('/standar-pakan', function() { return redirect('/master#card-standar-pakan'); })->name('standar-pakan');
    Route::get('/standar-bb', function() { return redirect('/master#card-standar-bb'); })->name('standar-bb');
    Route::get('/vaksin-obat', function() { return redirect('/master#card-vaksin-obat'); })->name('vaksin-obat');
    Route::get('/pengaturan', function() { return redirect('/master#card-pengaturan'); })->name('pengaturan');
});

// Modul Profil Petugas
Route::prefix('profil')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
    Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');
});



