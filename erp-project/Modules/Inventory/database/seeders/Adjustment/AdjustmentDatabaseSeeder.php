<?php

namespace Modules\Inventory\Database\Seeders\Adjustment;

use Illuminate\Database\Seeder;

class AdjustmentDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['adjustment' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            AdjustmentSeeder::class,
        ]);
    }
}
