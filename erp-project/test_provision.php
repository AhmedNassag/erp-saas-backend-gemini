<?php
// test_provision.php

use Modules\Landlord\Models\Tenant;
use Modules\Landlord\Listeners\CreateTenantDatabaseListener;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting test provision...\n";

// 1. Create a dummy tenant
$subdomain = 'testcorp' . rand(100, 999);
$domainName = $subdomain . '.erp.test';
$dbName = 'erp_tenant_' . $subdomain;

echo "Subdomain: $subdomain\n";
echo "Domain: $domainName\n";
echo "Database: $dbName\n";

// Get Gold Package
$package = DB::connection('landlord')->table('packages')->first();
if (!$package) {
    echo "No package found! Seeding landlord database...\n";
    Artisan::call('db:seed', ['--force' => true]);
    $package = DB::connection('landlord')->table('packages')->first();
}

$tenant = Tenant::create([
    'name'     => 'Test Corp',
    'domain'   => $domainName,
    'database' => $dbName,
    'package_id' => $package->id,
    'status'   => 'pending',
]);

echo "Tenant created in landlord DB. ID: {$tenant->id}\n";

$pending = [
    'tenant_id'       => $tenant->id,
    'admin_name'      => 'Test Admin',
    'admin_email'     => 'admin@' . $subdomain . '.com',
    'admin_password'  => '12345678',
    'subdomain'       => $subdomain,
];

try {
    echo "Running CreateTenantDatabaseListener...\n";
    $listener = new CreateTenantDatabaseListener();
    $listener->handle($tenant);
    echo "Database and migrations completed.\n";

    echo "Connecting to tenant database...\n";
    config(['database.connections.tenant.database' => $dbName]);
    DB::purge('tenant');
    DB::reconnect('tenant');

    echo "Inserting user...\n";
    if (DB::connection('tenant')->getSchemaBuilder()->hasTable('users')) {
        DB::connection('tenant')->table('users')->insert([
            'name'       => $pending['admin_name'],
            'email'      => $pending['admin_email'],
            'password'   => Hash::make($pending['admin_password']),
            'role'       => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "User inserted.\n";

        $userId = DB::connection('tenant')->table('users')->where('email', $pending['admin_email'])->value('id');
        $roleId = DB::connection('tenant')->table('roles')->where('name', 'Admin')->where('guard_name', 'sanctum')->value('id');
        
        echo "User ID: $userId, Role ID (sanctum): " . ($roleId ?? 'NOT FOUND') . "\n";
        
        if ($userId && $roleId) {
            DB::connection('tenant')->table('model_has_roles')->insert([
                'role_id'    => $roleId,
                'model_type' => 'App\Models\User',
                'model_id'   => $userId,
            ]);
            echo "Role assigned.\n";
        } else {
            echo "Failed to assign role. User ID or Role ID is empty.\n";
        }
    } else {
        echo "Error: Users table does not exist!\n";
    }

    $tenant->update(['status' => 'active']);
    echo "Tenant status updated to active. PROVISIONING SUCCESSFUL!\n";

} catch (\Exception $e) {
    echo "❌ Provisioning failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    $tenant->delete();
    echo "Tenant deleted from landlord DB.\n";
}
