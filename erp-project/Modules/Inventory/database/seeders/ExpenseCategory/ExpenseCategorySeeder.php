<?php

namespace Modules\Inventory\Database\Seeders\ExpenseCategory;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\ExpenseCategory\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        ExpenseCategory::create([
            'name'        => 'General',
            'description' => 'General expenses',
        ]);

        ExpenseCategory::create([
            'name'        => 'Utilities',
            'description' => 'Electricity, water, gas',
        ]);

        ExpenseCategory::create([
            'name'        => 'Salaries',
            'description' => 'Employee salaries and wages',
        ]);
    }
}
