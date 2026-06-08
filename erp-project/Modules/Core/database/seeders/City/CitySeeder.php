<?php

namespace Modules\Core\Database\Seeders\City;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Country\Country;
use Modules\Core\Models\City\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // ---------------- 1) Get Country ----------------
            $country = Country::first();

            // ---------------- 2) Insert Cities ----------------
            $cities = [
                'Cairo',
                'Giza',
                'Alexandria',
            ];

            foreach ($cities as $city) {
                $name = $city;
                
                City::create([
                    'name'       => $name,
                    'country_id' => $country->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
