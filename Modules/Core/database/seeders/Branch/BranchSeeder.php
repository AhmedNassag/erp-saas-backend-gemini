<?php

namespace Modules\Core\Branch\Database\Seeders\Branch;

use Modules\Core\RoleAndPermission\App\Models\Permission;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = [ 'branch' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
