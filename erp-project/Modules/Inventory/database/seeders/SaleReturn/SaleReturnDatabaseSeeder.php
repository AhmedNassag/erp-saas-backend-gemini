<?php

namespace Modules\Inventory\Database\Seeders\SaleReturn;

use Illuminate\Database\Seeder;

class SaleReturnDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['sale-return' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            SaleReturnSeeder::class,
        ]);
    }
}
