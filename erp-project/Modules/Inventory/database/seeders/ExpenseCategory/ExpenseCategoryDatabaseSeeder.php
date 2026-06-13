<?php

namespace Modules\Inventory\Database\Seeders\ExpenseCategory;

use Illuminate\Database\Seeder;

class ExpenseCategoryDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['expense-category' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            ExpenseCategorySeeder::class,
        ]);
    }
}
