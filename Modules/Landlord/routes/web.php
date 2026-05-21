<?php

use Illuminate\Support\Facades\Route;
use Modules\Landlord\Http\Controllers\LandlordController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('landlords', LandlordController::class)->names('landlord');
});
