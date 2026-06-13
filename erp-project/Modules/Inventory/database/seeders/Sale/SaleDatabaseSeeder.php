<?php

namespace Modules\Inventory\Database\Seeders\Sale;

use Illuminate\Database\Seeder;

class SaleDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['sale' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            SaleSeeder::class,
        ]);
    }
}
