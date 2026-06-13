<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Sale\SaleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/sale')->name('sale.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/{id}', [SaleController::class, 'show'])->name('show');
        Route::put('/{id}', [SaleController::class, 'update'])->name('update');
        Route::delete('/{id}', [SaleController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/pdf', [SaleController::class, 'downloadPDF'])->name('pdf');
    });
});
