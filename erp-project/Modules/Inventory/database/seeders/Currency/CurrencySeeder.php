<?php

namespace Modules\Inventory\Database\Seeders\Currency;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Currency\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $currencies = [
                ['name' => 'Egyptian Pound', 'code' => 'EGP', 'symbol' => 'E£'],
                ['name' => 'US Dollar',      'code' => 'USD', 'symbol' => '$'],
                ['name' => 'Euro',           'code' => 'EUR', 'symbol' => '€'],
                ['name' => 'Saudi Riyal',    'code' => 'SAR', 'symbol' => '﷼'],
                ['name' => 'UAE Dirham',     'code' => 'AED', 'symbol' => 'د.إ'],
            ];

            foreach ($currencies as $currency) {
                Currency::create($currency);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
