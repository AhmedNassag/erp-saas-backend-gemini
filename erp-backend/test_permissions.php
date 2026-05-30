<?php
// test_permissions.php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing user permissions...\n";

// Connect to the tenant DB created in our last test
$dbName = 'erp_tenant_testcorp594';
config(['database.connections.tenant.database' => $dbName]);
DB::purge('tenant');
DB::reconnect('tenant');

$user = User::where('role', 'admin')->first();

if (!$user) {
    echo "No admin user found in $dbName\n";
    exit(1);
}

echo "Found User: {$user->email} (ID: {$user->id})\n";

// Show roles of the user
$roles = $user->roles()->get();
echo "User Roles in DB:\n";
foreach ($roles as $role) {
    echo "- Name: {$role->name}, Guard: {$role->guard_name}\n";
}

// Show permissions of the user's role
if ($roles->isNotEmpty()) {
    echo "Permissions of Admin Role in DB:\n";
    foreach ($roles->first()->permissions()->get() as $perm) {
        echo "  * {$perm->name} (Guard: {$perm->guard_name})\n";
    }
}

// Authenticate user under the 'tenant' guard
Auth::guard('tenant')->setUser($user);

// Verify check
echo "\nChecking permissions under active 'tenant' guard:\n";
try {
    $hasRole = $user->hasRole('Admin', 'tenant');
    echo "hasRole('Admin', 'tenant'): " . ($hasRole ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "hasRole('Admin', 'tenant') threw error: " . $e->getMessage() . "\n";
}

try {
    $hasRoleDefault = $user->hasRole('Admin');
    echo "hasRole('Admin') default guard: " . ($hasRoleDefault ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "hasRole('Admin') default guard threw error: " . $e->getMessage() . "\n";
}

try {
    $hasPerm = $user->hasPermissionTo('read-country', 'tenant');
    echo "hasPermissionTo('read-country', 'tenant'): " . ($hasPerm ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "hasPermissionTo('read-country', 'tenant') threw error: " . $e->getMessage() . "\n";
}

try {
    $hasPermDefault = $user->hasPermissionTo('read-country');
    echo "hasPermissionTo('read-country') default guard: " . ($hasPermDefault ? 'YES' : 'NO') . "\n";
} catch (\Exception $e) {
    echo "hasPermissionTo('read-country') default guard threw error: " . $e->getMessage() . "\n";
}
