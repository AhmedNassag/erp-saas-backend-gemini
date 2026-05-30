<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Country\CountryController;


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
    Route::prefix('core/country')->name('country.')->group(function () {
        // Route::resource('/', CountryController::class);
        Route::get('/', [CountryController::class, 'index'])->name('index');
        Route::post('/', [CountryController::class, 'store'])->name('store');
        Route::get('/{id}', [CountryController::class, 'show'])->name('show');
        Route::put('/{id}', [CountryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CountryController::class, 'destroy'])->name('destroy');
        Route::post('/change-status/{id}', [CountryController::class, 'changeStatus'])->name('change-status');
    });
});