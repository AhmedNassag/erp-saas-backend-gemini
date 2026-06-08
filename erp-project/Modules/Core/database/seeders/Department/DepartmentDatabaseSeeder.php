<?php

namespace Modules\Core\Database\Seeders\Department;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\Department\DepartmentSeeder;

class DepartmentDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['department' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            DepartmentSeeder::class,
        ]);
    }
}
