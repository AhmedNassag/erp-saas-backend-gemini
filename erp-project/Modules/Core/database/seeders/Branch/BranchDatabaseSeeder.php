<?php

namespace Modules\Core\Database\Seeders\Branch;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\Branch\BranchSeeder;

class BranchDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['branch' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            BranchSeeder::class,
        ]);
    }
}
