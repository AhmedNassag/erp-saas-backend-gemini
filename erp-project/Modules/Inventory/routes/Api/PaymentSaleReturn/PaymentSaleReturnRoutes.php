<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\PaymentSaleReturn\PaymentSaleReturnController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/payment-sale-return')->name('payment-sale-return.')->group(function () {
        Route::get('/', [PaymentSaleReturnController::class, 'index'])->name('index');
        Route::post('/', [PaymentSaleReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [PaymentSaleReturnController::class, 'show'])->name('show');
        Route::put('/{id}', [PaymentSaleReturnController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentSaleReturnController::class, 'destroy'])->name('destroy');
    });
});
