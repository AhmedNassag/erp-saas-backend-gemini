<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Tenant Subdomains Routes (ERP Space)
|--------------------------------------------------------------------------
|
| الملف الرئيسي هنا مخصص فقط وحصرياً لمنطقة الشركات والـ Tenants.
| موديول الـ Landlord شغال بشكل مستقل تماماً على الدومين الرئيسي (erp.test).
|
*/

$tenantDomain = '{tenant}.' . env('CENTRAL_DOMAIN', 'erp.test');

Route::domain($tenantDomain)->middleware(['tenant'])->group(function () {
    // ✅ لوحة دخول الشركة (SPA) — هتروح على صفحة تسجيل الدخول أو أي مسار من Vue Router
    Route::get('/login', function () { return view('app'); })->name('app.login');

    // ✅ SPA — كل مسارات الـ Vue هتتحكم عن طريق Vue Router (باستثناء api/*)
    Route::get('/{any?}', function () {
        return view('app');
    })->where('any', '^(?!api\/).*');
});