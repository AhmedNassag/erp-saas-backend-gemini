<?php

namespace Modules\Inventory\Database\Seeders\Category;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Category\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $main1 = Category::create(['name' => 'Electronics',    'code' => 2001, 'is_main' => true]);
            $main2 = Category::create(['name' => 'Furniture',      'code' => 2002, 'is_main' => true]);
            $main3 = Category::create(['name' => 'Office Supplies','code' => 2003, 'is_main' => true]);

            Category::create(['name' => 'Laptops',     'code' => 2004, 'is_main' => false, 'category_id' => $main1->id]);
            Category::create(['name' => 'Printers',    'code' => 2005, 'is_main' => false, 'category_id' => $main1->id]);
            Category::create(['name' => 'Desks',       'code' => 2006, 'is_main' => false, 'category_id' => $main2->id]);
            Category::create(['name' => 'Chairs',      'code' => 2007, 'is_main' => false, 'category_id' => $main2->id]);
            Category::create(['name' => 'Paper',       'code' => 2008, 'is_main' => false, 'category_id' => $main3->id]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
