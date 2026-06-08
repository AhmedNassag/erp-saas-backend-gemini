<?php

namespace Modules\Inventory\Database\Seeders\Brand;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Brand\BrandSeeder;

class BrandDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['brand' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            BrandSeeder::class,
        ]);
    }
}
