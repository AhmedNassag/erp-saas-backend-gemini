<?php

namespace Modules\Landlord\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Landlord\Listeners\CreateTenantDatabaseListener;
use Modules\Core\Models\RoleAndPermission\Role;

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

        // 4. إنشاء المستخدم الأول (Admin) في قاعدة العميل
        $this->command->info('4. Creating admin user for tenant...');

        config(['database.connections.tenant.database' => $tenant->database]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        if (DB::connection('tenant')->getSchemaBuilder()->hasTable('users')) {
            $userEmail = 'admin@alpha.com';

            DB::connection('tenant')->table('users')->updateOrInsert(
                ['email' => $userEmail],
                [
                    'name'       => 'Admin',
                    'password'   => Hash::make('12345678'),
                    'role'       => 'admin',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // تعيين صلاحية Admin
            $userId = DB::connection('tenant')->table('users')->where('email', $userEmail)->value('id');
            $roleId = DB::connection('tenant')->table('roles')->where('name', 'Admin')->where('guard_name', 'tenant')->value('id');

            if ($userId && $roleId) {
                DB::connection('tenant')->table('model_has_roles')->updateOrInsert(
                    ['model_id' => $userId, 'model_type' => 'App\Models\User', 'role_id' => $roleId],
                    ['role_id' => $roleId]
                );
            }
        }

        $this->command->info('5. Done! Tenant [alpha.erp.test:8000] ready — login with admin@alpha.com / 12345678');
    }
}