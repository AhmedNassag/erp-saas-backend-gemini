<?php

namespace Modules\Landlord\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class CreateTenantDatabaseListener
{
    public function handle($tenant): void
    {
        $dbName = $tenant->database;

        if (!$dbName) {
            return;
        }

        // 1. إنشاء قاعدة البيانات الجديدة للعميل (حذف القديمة أولاً لضمان النظافة)
        DB::statement("DROP DATABASE IF EXISTS `{$dbName}`;");
        DB::statement("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        // 2. تغيير اتصال الـ tenant ديناميكياً
        config(['database.connections.tenant.database' => $dbName]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // 3. تشغيل كل tenant migrations من مجلد واحد
        //    (users + countries + cities + areas + branches + permissions + media)
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path'     => 'database/migrations/tenant',
            '--force'    => true,
        ]);

        // 4. تشغيل الـ Seeders الخاصة بالـ Tenant
        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class'    => \Database\Seeders\TenantDatabaseSeeder::class,
            '--force'    => true,
        ]);
    }
}