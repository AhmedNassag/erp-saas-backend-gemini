<?php

namespace Modules\Landlord\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Landlord\Listeners\CreateTenantDatabaseListener;

class LandlordDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // تنظيف الجداول القديمة
        DB::connection('landlord')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('landlord')->table('tenants')->truncate();
        DB::connection('landlord')->table('packages')->truncate();
        DB::connection('landlord')->statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. إنشاء باقة تجريبية (الـ Gold Package)
        $packageId = DB::connection('landlord')->table('packages')->insertGetId([
            'name' => 'الباقة الذهبية',
            'slug' => 'gold-package',
            'price' => 299.00,
            'limit_users' => 50,
            'limit_tenants' => 1,
            'features' => json_encode(['accounting' => true, 'hr' => true, 'inventory' => true]),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('1. Package [Gold Package] created successfully.');

        // 2. إنشاء أول شركة عميل
        $this->command->info('2. Creating Tenant [Alpha Corporation]...');
        
        DB::connection('landlord')->table('tenants')->insert([
            'id' => 1,
            'name' => 'شركة ألفا للتجارة',
            'domain' => 'alpha.erp.test',
            'database' => 'erp_tenant_alpha', 
            'package_id' => $packageId,
            'subscription_ends_at' => now()->addYear(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // جلب الداتا اللي لسه مسجلينها ككائن (Object)
        $tenant = DB::connection('landlord')->table('tenants')->where('id', 1)->first();

        // 3. تشغيل الـ Automation يدوياً وبشكل مباشر ومضمون
        $this->command->info('3. Triggering database creation automation directly...');
        
        $listener = new CreateTenantDatabaseListener();
        $listener->handle($tenant);

        $this->command->info('4. Success! Check your MySQL Server right now.');
    }
}