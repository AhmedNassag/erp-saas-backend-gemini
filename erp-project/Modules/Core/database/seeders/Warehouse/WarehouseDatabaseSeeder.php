<?php

namespace Modules\Core\Database\Seeders\Warehouse;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\Warehouse\WarehouseSeeder;

class WarehouseDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['warehouse' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            WarehouseSeeder::class,
        ]);
    }
}
