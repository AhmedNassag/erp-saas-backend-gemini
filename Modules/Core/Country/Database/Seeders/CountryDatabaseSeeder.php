<?php

namespace Modules\Core\Country\Database\Seeders;

use Modules\Core\RoleAndPermission\App\Models\Permission;
use Illuminate\Database\Seeder;

class CountryDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['country' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);
    }
}
