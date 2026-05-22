<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Api\AuthController;
use App\Http\Controllers\Tenant\Api\DashboardController;

$tenantDomain = '{tenant}.' . env('CENTRAL_DOMAIN', 'erp.test');

Route::domain($tenantDomain)->middleware(['tenant'])->group(function () {

    // 🔓 الـ Login مفتوح للعامة (أعطيناه اسم login هنا)
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // 🔒 الـ Dashboard مقفولة ومحمية بـ الـ Token
    Route::middleware(['auth:sanctum'])->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
    });

});