<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CitySeeder::class,
            AreaSeeder::class,
            BranchSeeder::class,
        ]);
    }
}
