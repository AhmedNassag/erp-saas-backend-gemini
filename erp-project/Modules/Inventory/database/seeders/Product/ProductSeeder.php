<?php

namespace Modules\Inventory\Database\Seeders\Product;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Product\Product;
use Modules\Inventory\Models\Category\Category;
use Modules\Inventory\Models\Brand\Brand;
use Modules\Inventory\Models\Unit\Unit;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $category = Category::first();
            $brand    = Brand::first();
            $unit     = Unit::whereNull('base_unit')->first();

            if (!$category || !$unit) {
                DB::rollBack();
                return;
            }

            $products = [
                [
                    'code'             => 'PROD-001',
                    'Type_barcode'     => 'CODE128',
                    'name'             => 'Sample Product 1',
                    'cost'             => 50,
                    'price'            => 100,
                    'category_id'      => $category->id,
                    'brand_id'         => $brand?->id,
                    'unit_id'          => $unit->id,
                    'unit_sale_id'     => $unit->id,
                    'unit_purchase_id' => $unit->id,
                    'TaxNet'           => 14,
                    'tax_method'       => '1',
                    'stock_alert'      => 5,
                ],
                [
                    'code'             => 'PROD-002',
                    'Type_barcode'     => 'CODE128',
                    'name'             => 'Sample Product 2',
                    'cost'             => 75,
                    'price'            => 150,
                    'category_id'      => $category->id,
                    'brand_id'         => $brand?->id,
                    'unit_id'          => $unit->id,
                    'unit_sale_id'     => $unit->id,
                    'unit_purchase_id' => $unit->id,
                    'TaxNet'           => 14,
                    'tax_method'       => '1',
                    'stock_alert'      => 10,
                ],
            ];

            foreach ($products as $data) {
                Product::create($data);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
