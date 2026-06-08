<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Category\CategoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/category')->name('category.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [CategoryController::class, 'changeStatus'])->name('change-status');
    });
});
