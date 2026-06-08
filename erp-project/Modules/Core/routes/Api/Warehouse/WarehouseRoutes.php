<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Warehouse\WarehouseController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('core/warehouse')->name('warehouse.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{id}', [WarehouseController::class, 'show'])->name('show');
        Route::put('/{id}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [WarehouseController::class, 'changeStatus'])->name('change-status');
    });
});
