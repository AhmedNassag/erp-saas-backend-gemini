<?php

namespace Modules\Inventory\Database\Seeders\Purchase;

use Illuminate\Database\Seeder;

class PurchaseDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['purchase' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            PurchaseSeeder::class,
        ]);
    }
}
