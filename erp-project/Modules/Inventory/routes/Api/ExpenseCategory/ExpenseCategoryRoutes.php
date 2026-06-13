<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\ExpenseCategory\ExpenseCategoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/expense-category')->name('expense-category.')->group(function () {
        Route::get('/', [ExpenseCategoryController::class, 'index'])->name('index');
        Route::post('/', [ExpenseCategoryController::class, 'store'])->name('store');
        Route::get('/{id}', [ExpenseCategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [ExpenseCategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [ExpenseCategoryController::class, 'changeStatus'])->name('change-status');
    });
});
