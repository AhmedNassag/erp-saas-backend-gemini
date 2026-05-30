<?php

namespace Modules\Core\Database\Seeders\City;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EgyptSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // ---------------- 1) Insert Egypt ----------------
            $country = DB::table('countries')->where('name', 'Egypt')->first();

            $country_id = $country ? $country->id : DB::table('countries')->insertGetId([
                'name' => 'Egypt',
            ]);

            // ---------------- 2) Cities ----------------
            $cities = [
                'Cairo',
                'Giza',
                'Alexandria',
            ];

            foreach ($cities as $city) {
                $name = $city;

                // Check existence first
                $exists = DB::table('cities')->where('country_id', $country_id)->where('name', $name)->first();

                if (!$exists) {
                    DB::table('cities')->insert([
                        'name'       => $name,
                        'country_id' => $country_id,
                    ]);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
