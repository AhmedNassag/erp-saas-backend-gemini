<?php

namespace Modules\Core\Database\Seeders\Country;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Country\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // ---------------- 2) Insert Countries ----------------
            $countries = [
                'Egypt',
                'Saudi Arabia',
                'France',
            ];

            foreach ($countries as $country) {
                $name = $country;
                
                Country::create([
                    'name' => $name,
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
