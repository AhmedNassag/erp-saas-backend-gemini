<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Api\AuthController as TenantAuthController;
use App\Http\Controllers\Tenant\Api\DashboardController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\AuthController as SuperAdminAuthController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\CmsController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\StatsController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\TenantController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\PackageController;
use Modules\Landlord\Http\Controllers\Public\PortfolioApiController;

// =============================================================================
// 🏰 Central Admin Routes (erp.test/api/admin/...)
// =============================================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // --- Public Auth Routes (no token required) ---
    Route::prefix('auth')->name('auth.')->group(function () {
        // Method 1: Email + Password
        Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('login');

        // Method 2: Email OTP
        Route::post('/email-otp/send',   [SuperAdminAuthController::class, 'sendEmailOtp'])->name('email-otp.send');
        Route::post('/email-otp/verify', [SuperAdminAuthController::class, 'verifyEmailOtp'])->name('email-otp.verify');

        // Method 3: Mobile OTP
        Route::post('/mobile-otp/send',   [SuperAdminAuthController::class, 'sendMobileOtp'])->name('mobile-otp.send');
        Route::post('/mobile-otp/verify', [SuperAdminAuthController::class, 'verifyMobileOtp'])->name('mobile-otp.verify');
    });

    // --- Protected Admin Routes (requires super_admin token) ---
    Route::middleware(['auth:super_admin'])->group(function () {
        Route::post('/auth/logout',     [SuperAdminAuthController::class, 'logout'])->name('auth.logout');
        Route::post('/auth/logout-all', [SuperAdminAuthController::class, 'logoutAll'])->name('auth.logout-all');
        Route::get('/auth/me',          [SuperAdminAuthController::class, 'me'])->name('auth.me');

        // Stats
        Route::get('/stats/overview', [StatsController::class, 'overview'])->name('stats.overview');
        Route::get('/stats/revenue',  [StatsController::class, 'revenue'])->name('stats.revenue');

        // Tenants
        Route::get('/tenants',              [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/{id}',         [TenantController::class, 'show'])->name('tenants.show');
        Route::patch('/tenants/{id}/suspend',  [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::patch('/tenants/{id}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
        Route::delete('/tenants/{id}',      [TenantController::class, 'destroy'])->name('tenants.destroy');

        // Packages
        Route::get('/packages',        [PackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/{id}',   [PackageController::class, 'show'])->name('packages.show');
        Route::post('/packages',       [PackageController::class, 'store'])->name('packages.store');
        Route::put('/packages/{id}',   [PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{id}',[PackageController::class, 'destroy'])->name('packages.destroy');

        // CMS
        Route::get('/cms/settings',          [CmsController::class, 'getSettings'])->name('cms.settings.get');
        Route::put('/cms/settings',          [CmsController::class, 'updateSettings'])->name('cms.settings.update');
        Route::get('/cms/hero',              [CmsController::class, 'getHero'])->name('cms.hero.get');
        Route::put('/cms/hero',              [CmsController::class, 'updateHero'])->name('cms.hero.update');
        Route::get('/cms/features',          [CmsController::class, 'getFeatures'])->name('cms.features.get');
        Route::put('/cms/features',          [CmsController::class, 'updateFeatures'])->name('cms.features.update');
        Route::get('/cms/testimonials',      [CmsController::class, 'getTestimonials'])->name('cms.testimonials.get');
        Route::put('/cms/testimonials',      [CmsController::class, 'updateTestimonials'])->name('cms.testimonials.update');
    });

});

// =============================================================================
// 🌐 Public Portfolio API (no auth required)
// =============================================================================
Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/packages',      [PortfolioApiController::class, 'packages'])->name('packages');
    Route::get('/settings',      [PortfolioApiController::class, 'settings'])->name('settings');
    Route::get('/features',      [PortfolioApiController::class, 'features'])->name('features');
    Route::get('/testimonials',  [PortfolioApiController::class, 'testimonials'])->name('testimonials');
    Route::post('/subscribe/{packageId}', [PortfolioApiController::class, 'subscribe'])->name('subscribe');
    Route::post('/contact',      [PortfolioApiController::class, 'contact'])->name('contact');
});

// =============================================================================
// 🏢 Tenant Routes ({subdomain}.erp.test/api/...)
// =============================================================================
$tenantDomain = '{tenant}.' . env('CENTRAL_DOMAIN', 'erp.test');

Route::domain($tenantDomain)->middleware(['tenant'])->group(function () {

    // 🔓 Public — Login
    Route::post('/login', [TenantAuthController::class, 'login'])->name('login');

    // 🔒 Protected — requires tenant user token
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::post('/logout',   [TenantAuthController::class, 'logout']);
    });

});