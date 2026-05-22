<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Area\App\Http\Controllers\AreaController;

Route::group([], function () {
    Route::resource('area', AreaController::class)->names('area');
});