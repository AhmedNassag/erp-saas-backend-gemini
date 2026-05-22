<?php

namespace Modules\Core\City\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EgyptSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // ---------------- 1) Insert Egypt ----------------
            $country = DB::table('countries')
                ->where('name', json_encode(['ar' => 'مصر', 'en' => 'Egypt']))
                ->first();

            $country_id = $country ? $country->id : DB::table('countries')->insertGetId([
                'name' => json_encode(['ar' => 'مصر', 'en' => 'Egypt']),
            ]);

            // ---------------- 2) Cities ----------------
            $cities = [
                ['ar' => 'القاهرة', 'en' => 'Cairo'],
                ['ar' => 'الجيزة', 'en' => 'Giza'],
                ['ar' => 'الإسكندرية', 'en' => 'Alexandria'],
            ];

            foreach ($cities as $city) {
                $nameJson = json_encode(['ar' => $city['ar'], 'en' => $city['en']]);

                // Check existence first
                $exists = DB::table('cities')
                    ->where('country_id', $country_id)
                    ->where('name', $nameJson)
                    ->first();

                if (!$exists) {
                    DB::table('cities')->insert([
                        'name' => $nameJson,
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
