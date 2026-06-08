<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Branch\BranchController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
    Route::prefix('core/branch')->name('branch.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('/{id}', [BranchController::class, 'show'])->name('show');
        Route::put('/{id}', [BranchController::class, 'update'])->name('update');
        Route::delete('/{id}', [BranchController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [BranchController::class, 'changeStatus'])->name('change-status');
    });
});