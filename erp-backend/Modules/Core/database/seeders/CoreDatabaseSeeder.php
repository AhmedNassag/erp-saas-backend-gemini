<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\RoleAndPermission\RoleAndPermissionDatabaseSeeder;
use Modules\Core\Database\Seeders\Country\CountryDatabaseSeeder;
use Modules\Core\Database\Seeders\City\CityDatabaseSeeder;
use Modules\Core\Database\Seeders\Area\AreaDatabaseSeeder;
use Modules\Core\Database\Seeders\Branch\BranchDatabaseSeeder;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionDatabaseSeeder::class,
            CountryDatabaseSeeder::class,
            CityDatabaseSeeder::class,
            AreaDatabaseSeeder::class,
            BranchDatabaseSeeder::class,
        ]);
    }
}
