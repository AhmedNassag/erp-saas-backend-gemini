<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\City\CityController;
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
    Route::prefix('core/city')->name('city.')->group(function () {
        Route::get('/', [CityController::class, 'index'])->name('index');
        Route::post('/', [CityController::class, 'store'])->name('store');
        Route::get('/{id}', [CityController::class, 'show'])->name('show');
        Route::put('/{id}', [CityController::class, 'update'])->name('update');
        Route::delete('/{id}', [CityController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [CityController::class, 'changeStatus'])->name('change-status');
    });
});
