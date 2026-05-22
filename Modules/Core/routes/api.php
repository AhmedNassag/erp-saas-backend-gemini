<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Core\Branch\App\Http\Controllers\Branch\BranchController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cores', CoreController::class)->names('core');

    // Route::prefix('core')->name('branch.')->group(function () {
    //     Route::apiResource('/branches', BranchController::class);
    // });
});

@include('Api/index.php');