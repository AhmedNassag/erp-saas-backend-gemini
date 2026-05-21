<?php

namespace Modules\Landlord\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class CreateTenantDatabaseListener
{
    /**
     * تشغيل معالجة إنشاء قاعدة البيانات للعميل.
     * باصينا الـ $tenant مباشرة بدل الـ Event عشان نضمن الكود 100%
     */
    public function handle($tenant): void
    {
        $dbName = $tenant->database;

        if (!$dbName) {
            return;
        }

        // 1. خلق قاعدة البيانات الجديدة للعميل لو مش موجودة
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        // 2. تغيير اتصال الـ tenant ديناميكياً ليشير للقاعدة الجديدة
        config(['database.connections.tenant.database' => $dbName]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // 3. تشغيل الـ Migrations الخاصة بالـ Tenants جوه قاعدة البيانات الجديدة
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant', 
            '--force' => true,
        ]);
    }
}