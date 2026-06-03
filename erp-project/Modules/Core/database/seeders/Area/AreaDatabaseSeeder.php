<?php

namespace Modules\Core\Database\Seeders\Area;

use Modules\Core\Models\RoleAndPermission\Permission;
use Illuminate\Database\Seeder;

class AreaDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['area' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
