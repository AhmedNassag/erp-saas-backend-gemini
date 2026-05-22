<?php

namespace Modules\Core\Branch\Database\Seeders\City;

use Modules\Core\RoleAndPermission\App\Models\Permission;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = [ 'city' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
