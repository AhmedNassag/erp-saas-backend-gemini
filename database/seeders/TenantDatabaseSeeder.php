<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\CoreDatabaseSeeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant database.
     * Runs inside each tenant's isolated database after it's created.
     */
    public function run(): void
    {
        // Core data: roles, permissions, countries, cities, areas, branches
        $this->call([
            CoreDatabaseSeeder::class,
        ]);
    }
}
