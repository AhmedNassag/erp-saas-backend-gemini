<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Landlord\Database\Seeders\LandlordDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // 1. Landlord data (packages + first demo tenant + creates tenant DB)
            LandlordDatabaseSeeder::class,

            // 2. Super Admin account (صاحب المشروع)
            SuperAdminSeeder::class,
        ]);
    }
}

