<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Area\AreaController;

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
    Route::prefix('core/area')->name('area.')->group(function () {
        Route::get('/', [AreaController::class, 'index'])->name('index');
        Route::post('/', [AreaController::class, 'store'])->name('store');
        Route::get('/{id}', [AreaController::class, 'show'])->name('show');
        Route::put('/{id}', [AreaController::class, 'update'])->name('update');
        Route::delete('/{id}', [AreaController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [AreaController::class, 'changeStatus'])->name('change-status');
    });
});

