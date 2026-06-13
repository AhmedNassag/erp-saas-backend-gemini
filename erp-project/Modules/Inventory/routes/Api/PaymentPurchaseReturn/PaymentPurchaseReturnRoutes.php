<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\PaymentPurchaseReturn\PaymentPurchaseReturnController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/payment-purchase-return')->name('payment-purchase-return.')->group(function () {
        Route::get('/', [PaymentPurchaseReturnController::class, 'index'])->name('index');
        Route::post('/', [PaymentPurchaseReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [PaymentPurchaseReturnController::class, 'show'])->name('show');
        Route::put('/{id}', [PaymentPurchaseReturnController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentPurchaseReturnController::class, 'destroy'])->name('destroy');
    });
});
