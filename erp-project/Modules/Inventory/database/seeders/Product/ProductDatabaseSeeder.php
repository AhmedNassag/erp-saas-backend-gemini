<?php

namespace Modules\Inventory\Database\Seeders\Product;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Product\ProductSeeder;

class ProductDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['product' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
