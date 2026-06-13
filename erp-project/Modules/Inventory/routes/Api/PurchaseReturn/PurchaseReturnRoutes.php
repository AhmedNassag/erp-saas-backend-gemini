<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\PurchaseReturn\PurchaseReturnController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/purchase-return')->name('purchase-return.')->group(function () {
        Route::get('/', [PurchaseReturnController::class, 'index'])->name('index');
        Route::post('/', [PurchaseReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [PurchaseReturnController::class, 'show'])->name('show');
        Route::put('/{id}', [PurchaseReturnController::class, 'update'])->name('update');
        Route::delete('/{id}', [PurchaseReturnController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/pdf', [PurchaseReturnController::class, 'downloadPDF'])->name('pdf');
    });
});
