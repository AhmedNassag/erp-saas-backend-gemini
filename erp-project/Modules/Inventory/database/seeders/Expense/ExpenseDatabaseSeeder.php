<?php

namespace Modules\Inventory\Database\Seeders\Expense;

use Illuminate\Database\Seeder;

class ExpenseDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['expense' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            ExpenseSeeder::class,
        ]);
    }
}
