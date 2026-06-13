<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\SaleReturn\SaleReturnController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/sale-return')->name('sale-return.')->group(function () {
        Route::get('/', [SaleReturnController::class, 'index'])->name('index');
        Route::post('/', [SaleReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [SaleReturnController::class, 'show'])->name('show');
        Route::put('/{id}', [SaleReturnController::class, 'update'])->name('update');
        Route::delete('/{id}', [SaleReturnController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/pdf', [SaleReturnController::class, 'downloadPDF'])->name('pdf');
    });
});
