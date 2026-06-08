<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\CoreDatabaseSeeder;
use Modules\Inventory\Database\Seeders\InventoryDatabaseSeeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant database.
     * Runs inside each tenant's isolated database after it's created.
     */
    public function run(): void
    {
        $this->call([
            CoreDatabaseSeeder::class,
            InventoryDatabaseSeeder::class,
        ]);
    }
}
