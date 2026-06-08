<?php

namespace Modules\Inventory\Database\Seeders\Category;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Category\CategorySeeder;

class CategoryDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['category' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            CategorySeeder::class,
        ]);
    }
}
