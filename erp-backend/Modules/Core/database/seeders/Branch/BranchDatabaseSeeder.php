<?php

namespace Modules\Core\Database\Seeders\Branch;

use Modules\Core\Models\RoleAndPermission\Permission;
use Illuminate\Database\Seeder;

class BranchDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['branch' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
