<?php

namespace Modules\Inventory\Database\Seeders\Client;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Client\ClientSeeder;

class ClientDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['client' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            ClientSeeder::class,
        ]);
    }
}
