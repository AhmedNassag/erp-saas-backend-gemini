<?php

namespace Modules\Core\Database\Seeders\Department;

use Modules\Core\Models\RoleAndPermission\Permission;
use Illuminate\Database\Seeder;

class DepartmentDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['department' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
