<?php

use Illuminate\Support\Facades\Route;
use Modules\Landlord\Http\Controllers\PortfolioController;
use Modules\Landlord\Http\Controllers\PaymentController;
use Modules\Landlord\Http\Controllers\CentralAdminController;

/*
|--------------------------------------------------------------------------
| 🌐 Landlord Domain Central Routes (erp.test)
|--------------------------------------------------------------------------
*/
Route::domain(env('CENTRAL_DOMAIN', 'erp.test'))->group(function () {

    /* --- 1️⃣ Public Portfolio & SaaS Landing Page --- */
    Route::get('/', [PortfolioController::class, 'home'])->name('landlord.home');
    Route::get('/about', [PortfolioController::class, 'about'])->name('landlord.about');
    Route::get('/pricing', [PortfolioController::class, 'pricing'])->name('landlord.pricing');
    Route::get('/contact', [PortfolioController::class, 'contact'])->name('landlord.contact');

    // رحلة الاشتراك والدفع
    Route::get('/subscribe/{package_id}', [PortfolioController::class, 'subscribeForm'])->name('landlord.subscribe.form');
    Route::post('/subscribe/{package_id}/checkout', [PortfolioController::class, 'processCheckout'])->name('landlord.subscribe.checkout');

    // PayMob Callback — بعد الدفع، PayMob بيرجع المستخدم هنا
    Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'success'])->name('landlord.payment.callback');
    // صفحة النجاح والفشل
    Route::get('/payment/success', [PaymentController::class, 'showSuccess'])->name('landlord.payment.success-display');
    Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('landlord.payment.failed');

    /* --- 2️⃣ 🏰 Super Admin Central Dashboard --- */
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [CentralAdminController::class, 'index'])->name('landlord.admin.dashboard');
        Route::get('/tenants', [CentralAdminController::class, 'tenants'])->name('landlord.admin.tenants');
        Route::get('/packages', [CentralAdminController::class, 'packages'])->name('landlord.admin.packages');
        Route::get('/payments', [CentralAdminController::class, 'payments'])->name('landlord.admin.payments');
    });

});