<?php

namespace Modules\Core\Area\Database\Seeders;

use Modules\Core\RoleAndPermission\App\Models\Permission;
use Illuminate\Database\Seeder;

class AreaDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models = ['area' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
