<?php

namespace Modules\Inventory\Database\Seeders\Transfer;

use Illuminate\Database\Seeder;

class TransferDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['transfer' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            TransferSeeder::class,
        ]);
    }
}
