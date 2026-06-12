<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Transfer\TransferController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/transfer')->name('transfer.')->group(function () {
        Route::get('/', [TransferController::class, 'index'])->name('index');
        Route::post('/', [TransferController::class, 'store'])->name('store');
        Route::get('/{id}', [TransferController::class, 'show'])->name('show');
        Route::put('/{id}', [TransferController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransferController::class, 'destroy'])->name('destroy');
    });
});
