<?php

use Illuminate\Support\Facades\Route;
use Modules\Landlord\Http\Controllers\PortfolioController;
use Modules\Landlord\Http\Controllers\CentralAdminController;

/*
|--------------------------------------------------------------------------
| 🌐 Landlord Domain Central Routes (erp.test)
|--------------------------------------------------------------------------
| تغليف الرووتس بالدومين الرئيسي بيمنع تداخلها مع الساب دومينز بتاعة الشركات
*/
Route::domain(env('CENTRAL_DOMAIN', 'erp.test'))->group(function () {

    /* --- 1️⃣ Public Portfolio & SaaS Landing Page (المنطقة العامة) --- */
    Route::get('/', [PortfolioController::class, 'home'])->name('landlord.home');
    Route::get('/about', [PortfolioController::class, 'about'])->name('landlord.about');
    Route::get('/pricing', [PortfolioController::class, 'pricing'])->name('landlord.pricing');
    Route::get('/contact', [PortfolioController::class, 'contact'])->name('landlord.contact');

    // رحلة الاشتراك والدفع
    Route::get('/subscribe/{package_id}', [PortfolioController::class, 'subscribeForm'])->name('landlord.subscribe.form');
    Route::post('/subscribe/{package_id}/checkout', [PortfolioController::class, 'processCheckout'])->name('landlord.subscribe.checkout');
    Route::get('/payment/success', [PortfolioController::class, 'paymentSuccess'])->name('landlord.payment.success');


    /* --- 2️⃣ 🏰 Super Admin Central Dashboard (لوحة تحكم أصحاب المشروع) --- */
    // ملحوظة: شيلنا الـ auth مؤقتاً عشان تقدر تتصفحها وتشوف الـ Views بنفسك
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [CentralAdminController::class, 'index'])->name('landlord.admin.dashboard');
        // 💡 تعديل بسيط: تأكد إن اسم الكنترولر CentralAdminController زي الـ use فوق عشان ما يضربش مع الـ SuperAdmin الافتراضي
    });

});