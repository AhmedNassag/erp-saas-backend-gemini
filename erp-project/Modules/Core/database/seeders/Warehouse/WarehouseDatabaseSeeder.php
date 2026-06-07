<?php

namespace Modules\Core\Database\Seeders\Warehouse;

use Modules\Core\Models\RoleAndPermission\Permission;
use Illuminate\Database\Seeder;

class WarehouseDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['warehouse' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
