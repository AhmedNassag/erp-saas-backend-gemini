<?php

namespace Modules\Inventory\Database\Seeders\Unit;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Unit\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $meter = Unit::create(['name' => 'Meter', 'code' => 'MTR', 'shortName' => 'm', 'operator' => '*', 'operator_value' => 1]);
            $kilogram = Unit::create(['name' => 'Kilogram', 'code' => 'KG', 'shortName' => 'kg', 'operator' => '*', 'operator_value' => 1]);
            $liter = Unit::create(['name' => 'Liter', 'code' => 'LTR', 'shortName' => 'L', 'operator' => '*', 'operator_value' => 1]);
            $piece = Unit::create(['name' => 'Piece', 'code' => 'PCS', 'shortName' => 'pcs', 'operator' => '*', 'operator_value' => 1]);

            Unit::create(['name' => 'Centimeter', 'code' => 'CM', 'shortName' => 'cm', 'base_unit' => $meter->id, 'operator' => '/', 'operator_value' => 100]);
            Unit::create(['name' => 'Gram', 'code' => 'G', 'shortName' => 'g', 'base_unit' => $kilogram->id, 'operator' => '/', 'operator_value' => 1000]);
            Unit::create(['name' => 'Milliliter', 'code' => 'ML', 'shortName' => 'ml', 'base_unit' => $liter->id, 'operator' => '/', 'operator_value' => 1000]);
            Unit::create(['name' => 'Dozen', 'code' => 'DZN', 'shortName' => 'dz', 'base_unit' => $piece->id, 'operator' => '*', 'operator_value' => 12]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
