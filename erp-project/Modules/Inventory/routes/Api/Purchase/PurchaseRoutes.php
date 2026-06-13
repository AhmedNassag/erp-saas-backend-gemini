<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Purchase\PurchaseController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/purchase')->name('purchase.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::post('/', [PurchaseController::class, 'store'])->name('store');
        Route::get('/{id}', [PurchaseController::class, 'show'])->name('show');
        Route::put('/{id}', [PurchaseController::class, 'update'])->name('update');
        Route::delete('/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/pdf', [PurchaseController::class, 'downloadPDF'])->name('pdf');
    });
});
