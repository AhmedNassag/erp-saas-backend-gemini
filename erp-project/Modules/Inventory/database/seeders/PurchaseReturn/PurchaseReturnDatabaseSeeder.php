<?php

namespace Modules\Inventory\Database\Seeders\PurchaseReturn;

use Illuminate\Database\Seeder;

class PurchaseReturnDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['purchase-return' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            PurchaseReturnSeeder::class,
        ]);
    }
}
