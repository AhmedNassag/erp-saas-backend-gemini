<?php

use Illuminate\Support\Facades\Route;
use Modules\Landlord\Http\Controllers\Api\LanguageController;
use Modules\Landlord\Http\Controllers\Api\TranslationController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\AuthController as SuperAdminAuthController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\CmsController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\ModuleController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\StatsController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\TenantController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\PackageController;
use Modules\Landlord\Http\Controllers\SuperAdmin\Api\SubscriptionController;
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
        Route::post('/auth/logout',      [SuperAdminAuthController::class, 'logout'])->name('auth.logout');
        Route::post('/auth/logout-all',  [SuperAdminAuthController::class, 'logoutAll'])->name('auth.logout-all');
        Route::get('/auth/me',           [SuperAdminAuthController::class, 'me'])->name('auth.me');
        Route::put('/auth/profile',      [SuperAdminAuthController::class, 'updateProfile'])->name('auth.profile');
        Route::post('/auth/change-password', [SuperAdminAuthController::class, 'changePassword'])->name('auth.change-password');

        // Languages
        Route::get('/languages',              [LanguageController::class, 'adminIndex'])->name('languages.index');
        Route::post('/languages',             [LanguageController::class, 'store'])->name('languages.store');
        Route::put('/languages/{language}',   [LanguageController::class, 'update'])->name('languages.update');
        Route::delete('/languages/{language}',[LanguageController::class, 'destroy'])->name('languages.destroy');

        // Translations
        Route::get('/translations',                 [TranslationController::class, 'index'])->name('translations.index');
        Route::post('/translations',                [TranslationController::class, 'store'])->name('translations.store');
        Route::put('/translations/bulk',            [TranslationController::class, 'bulkUpdate'])->name('translations.bulk');
        Route::put('/translations/{languageLine}',  [TranslationController::class, 'update'])->name('translations.update');
        Route::delete('/translations/{languageLine}',[TranslationController::class, 'destroy'])->name('translations.destroy');

        // Stats
        Route::get('/stats/overview', [StatsController::class, 'overview'])->name('stats.overview');
        Route::get('/stats/revenue',  [StatsController::class, 'revenue'])->name('stats.revenue');

        // Tenants
        Route::get('/tenants',                 [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/{tenant}',        [TenantController::class, 'show'])->name('tenants.show');
        Route::patch('/tenants/{tenant}/suspend',  [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::patch('/tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
        Route::delete('/tenants/{tenant}',     [TenantController::class, 'destroy'])->name('tenants.destroy');

        // System Modules
        Route::get('/modules', [ModuleController::class, 'index'])->name('modules');

        // Packages
        Route::get('/packages',          [PackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/{package}',[PackageController::class, 'show'])->name('packages.show');
        Route::post('/packages',         [PackageController::class, 'store'])->name('packages.store');
        Route::put('/packages/{package}',[PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}',[PackageController::class, 'destroy'])->name('packages.destroy');

        // Subscriptions
        Route::get('/subscriptions',                    [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{subscription}',     [SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::patch('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::patch('/subscriptions/{subscription}/renew',  [SubscriptionController::class, 'renew'])->name('subscriptions.renew');

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
// 🌐 Public APIs (no auth required)
// =============================================================================

// Languages & Translations
Route::prefix('languages')->name('languages.')->group(function () {
    Route::get('/',                    [LanguageController::class, 'index'])->name('index');
    Route::get('/{code}',              [LanguageController::class, 'show'])->name('show');
    Route::get('/{code}/translations', [LanguageController::class, 'translations'])->name('translations');
    Route::get('/{code}/translations/{group}', [LanguageController::class, 'translationsByGroup'])->name('translations.group');
});

// Portfolio
Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::get('/packages',      [PortfolioApiController::class, 'packages'])->name('packages');
    Route::get('/settings',      [PortfolioApiController::class, 'settings'])->name('settings');
    Route::get('/features',      [PortfolioApiController::class, 'features'])->name('features');
    Route::get('/modules',       [PortfolioApiController::class, 'modules'])->name('modules');
    Route::get('/testimonials',  [PortfolioApiController::class, 'testimonials'])->name('testimonials');
    Route::post('/subscribe/{packageId}', [PortfolioApiController::class, 'subscribe'])->name('subscribe');
    Route::post('/contact',      [PortfolioApiController::class, 'contact'])->name('contact');
});
