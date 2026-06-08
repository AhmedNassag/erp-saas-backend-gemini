<?php

namespace Modules\Inventory\Database\Seeders\Provider;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Provider\ProviderSeeder;

class ProviderDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['provider' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            ProviderSeeder::class,
        ]);
    }
}
