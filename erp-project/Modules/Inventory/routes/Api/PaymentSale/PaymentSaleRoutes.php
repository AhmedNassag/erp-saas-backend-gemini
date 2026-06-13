<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\PaymentSale\PaymentSaleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/payment-sale')->name('payment-sale.')->group(function () {
        Route::get('/', [PaymentSaleController::class, 'index'])->name('index');
        Route::post('/', [PaymentSaleController::class, 'store'])->name('store');
        Route::get('/{id}', [PaymentSaleController::class, 'show'])->name('show');
        Route::put('/{id}', [PaymentSaleController::class, 'update'])->name('update');
        Route::delete('/{id}', [PaymentSaleController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/pdf', [PaymentSaleController::class, 'downloadPDF'])->name('pdf');
    });
});
