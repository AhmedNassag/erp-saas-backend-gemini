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

    // 1️⃣ فحص حالة اتصال الشركة والداتا بيز الديناميكية (ممتاز للتيتست)
    Route::get('/status', function () {
        $tenantDatabase = DB::connection('tenant')->getDatabaseName();
        return response()->json([
            'message' => 'Welcome to Tenant Space!',
            'tenant' => \Spatie\Multitenancy\Models\Tenant::current()->name,
            'database' => $tenantDatabase
        ]);
    });

    // 2️⃣ مسارات تسجيل الدخول الخاصة بموظفي وأدمن الشركة (خارج الـ Auth)
    // Route::get('/login', [App\Http\Controllers\Tenant\Auth\LoginController::class, 'showLoginForm'])->name('tenant.login');
    // Route::post('/login', [App\Http\Controllers\Tenant\Auth\LoginController::class, 'login']);
    // Route::post('/logout', [App\Http\Controllers\Tenant\Auth\LoginController::class, 'logout'])->name('tenant.logout');

    // 3️⃣ لوحة تحكم الـ ERP الداخلية للشركة (محمية بالـ Auth)
    Route::middleware(['auth'])->group(function () {
        
        // الصفحة الرئيسية للشركة بعد تسجيل الدخول مباشرة
        // Route::get('/', [App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('tenant.dashboard');

        /* 
         * 💡 مستقبلاً لما تكبر المشروع وتعمل موديولات الـ ERP (المخازن، الحسابات.. إلخ)
         * الرووتس بتاعتها هتنزل هنا عشان تكون محمية بالـ Tenant Middleware والـ Auth
         */
    });

});