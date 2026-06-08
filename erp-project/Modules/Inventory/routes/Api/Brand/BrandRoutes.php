<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Brand\BrandController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/brand')->name('brand.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::post('/', [BrandController::class, 'store'])->name('store');
        Route::get('/{id}', [BrandController::class, 'show'])->name('show');
        Route::put('/{id}', [BrandController::class, 'update'])->name('update');
        Route::delete('/{id}', [BrandController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [BrandController::class, 'changeStatus'])->name('change-status');
    });
});
