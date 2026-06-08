<?php

namespace Modules\Core\Database\Seeders\User;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\User\UserSeeder;

class UserDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus', 'profile'];
        $models  = ['user' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            // UserSeeder::class,
        ]);
    }
}
