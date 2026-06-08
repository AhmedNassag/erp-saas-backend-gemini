<?php

namespace Modules\Core\Database\Seeders\Area;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\Area\AreaSeeder;

class AreaDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['area' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            AreaSeeder::class,
        ]);
    }
}
