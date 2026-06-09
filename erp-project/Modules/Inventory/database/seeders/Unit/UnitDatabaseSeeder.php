<?php

namespace Modules\Inventory\Database\Seeders\Unit;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Unit\UnitSeeder;

class UnitDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['unit' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            UnitSeeder::class,
        ]);
    }
}
