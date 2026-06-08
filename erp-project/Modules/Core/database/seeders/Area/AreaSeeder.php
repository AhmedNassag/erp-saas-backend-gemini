<?php

namespace Modules\Core\Database\Seeders\Area;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\City\City;
use Modules\Core\Models\Area\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $city = City::first();

            $areas = [
                'Helwan',
                'Maadi',
                'Nasr City',
            ];

            foreach ($areas as $area) {
                Area::create([
                    'name'    => $area,
                    'city_id' => $city->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
