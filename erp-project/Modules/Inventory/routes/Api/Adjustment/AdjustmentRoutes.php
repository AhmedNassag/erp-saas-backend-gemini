<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Adjustment\AdjustmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/adjustment')->name('adjustment.')->group(function () {
        Route::get('/', [AdjustmentController::class, 'index'])->name('index');
        Route::post('/', [AdjustmentController::class, 'store'])->name('store');
        Route::get('/{id}', [AdjustmentController::class, 'show'])->name('show');
        Route::put('/{id}', [AdjustmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdjustmentController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/detail', [AdjustmentController::class, 'adjustmentDetail'])->name('detail');
    });
});
