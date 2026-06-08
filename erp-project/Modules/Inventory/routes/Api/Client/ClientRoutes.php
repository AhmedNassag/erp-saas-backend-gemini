<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Client\ClientController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/client')->name('client.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{id}', [ClientController::class, 'show'])->name('show');
        Route::put('/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [ClientController::class, 'changeStatus'])->name('change-status');
    });
});
