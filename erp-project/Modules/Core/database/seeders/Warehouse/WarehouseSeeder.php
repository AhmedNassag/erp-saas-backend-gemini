<?php

namespace Modules\Core\Database\Seeders\Warehouse;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Branch\Branch;
use Modules\Core\Models\Area\Area;
use Modules\Core\Models\Warehouse\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $branch = Branch::first();
            $area   = Area::first();

            $warehouses = [
                [
                    'name'    => 'Main Warehouse',
                    'code'    => 'WH-001',
                    'mobile'  => '01100000001',
                    'is_main' => true
                ],
                [
                    'name'    => 'Secondary Warehouse',
                    'code'    => 'WH-002',
                    'mobile'  => '01100000002',
                    'is_main' => false
                ],
            ];

            foreach ($warehouses as $warehouse) {
                Warehouse::create([
                    'name'      => $warehouse['name'],
                    'code'      => $warehouse['code'],
                    'mobile'    => $warehouse['mobile'],
                    'is_main'   => $warehouse['is_main'],
                    'branch_id' => $branch->id,
                    'area_id'   => $area->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
