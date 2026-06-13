<?php

namespace Modules\Inventory\Database\Seeders\Brand;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Brand\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $brands = [
                [
                    'name' => 'Samsung',
                    'code' => 'BR_0001'
                ],
                [
                    'name' => 'Apple',
                    'code' => 'BR_0002'
                ],
                [
                    'name' => 'LG',
                    'code' => 'BR_0003'
                ],
                [
                    'name' => 'Sony',
                    'code' => 'BR_0004'
                ],
                [
                    'name' => 'HP',
                    'code' => 'BR_0005'
                ],
            ];

            foreach ($brands as $brand) {
                Brand::create($brand);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
