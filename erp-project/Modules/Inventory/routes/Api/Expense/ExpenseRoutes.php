<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\Expense\ExpenseController;

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('inventory/expense')->name('expense.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('/{id}', [ExpenseController::class, 'show'])->name('show');
        Route::put('/{id}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
    });
});
