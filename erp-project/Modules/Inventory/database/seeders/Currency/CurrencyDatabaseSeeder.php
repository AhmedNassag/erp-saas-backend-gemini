<?php

namespace Modules\Inventory\Database\Seeders\Currency;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Currency\CurrencySeeder;

class CurrencyDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['currency' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            CurrencySeeder::class,
        ]);
    }
}
