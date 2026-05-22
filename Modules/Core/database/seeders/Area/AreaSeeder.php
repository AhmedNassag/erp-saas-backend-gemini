<?php

namespace Modules\Core\Branch\Database\Seeders\Area;

use Modules\Core\RoleAndPermission\App\Models\Permission;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = [ 'area' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
