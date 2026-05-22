<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant database.
     * This seeder runs inside each tenant's database after it's created.
     */
    public function run(): void
    {
        // Add tenant-specific seeders here as needed.
        // Example: default admin user, default settings, etc.
    }
}
