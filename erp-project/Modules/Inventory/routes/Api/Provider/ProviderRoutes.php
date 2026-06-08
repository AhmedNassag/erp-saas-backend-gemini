<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Provider\ProviderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/provider')->name('provider.')->group(function () {
        Route::get('/', [ProviderController::class, 'index'])->name('index');
        Route::post('/', [ProviderController::class, 'store'])->name('store');
        Route::get('/{id}', [ProviderController::class, 'show'])->name('show');
        Route::put('/{id}', [ProviderController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProviderController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [ProviderController::class, 'changeStatus'])->name('change-status');
    });
});
