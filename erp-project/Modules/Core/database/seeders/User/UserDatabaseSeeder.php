<?php

namespace Modules\Core\Database\Seeders\User;

use Modules\Core\Models\RoleAndPermission\Permission;
use Illuminate\Database\Seeder;

class UserDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus', 'profile'];
        $models  = ['user' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
