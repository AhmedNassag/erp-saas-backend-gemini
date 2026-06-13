<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\PaymentPurchase\PaymentPurchaseController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/payment-purchase')->name('payment-purchase.')->group(function () {
        Route::get('/', [PaymentPurchaseController::class, 'index'])->name('index');
        Route::post('/', [PaymentPurchaseController::class, 'store'])->name('store');
        Route::get('/{id}', [PaymentPurchaseController::class, 'show'])->name('show');
        Route::put('/{id}', [PaymentPurchaseController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentPurchaseController::class, 'destroy'])->name('destroy');
    });
});
