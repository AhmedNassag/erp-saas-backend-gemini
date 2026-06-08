<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Client\ClientDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Provider\ProviderDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Category\CategoryDatabaseSeeder;
use Modules\Inventory\Database\Seeders\Brand\BrandDatabaseSeeder;
use Modules\Core\Models\RoleAndPermission\Role;
use Modules\Core\Models\RoleAndPermission\Permission;

class InventoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ClientDatabaseSeeder::class,
            ProviderDatabaseSeeder::class,
            CategoryDatabaseSeeder::class,
            BrandDatabaseSeeder::class,
        ]);

        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'tenant')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }
    }
}
