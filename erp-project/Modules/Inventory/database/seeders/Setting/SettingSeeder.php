<?php

namespace Modules\Inventory\Database\Seeders\Setting;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Setting\Setting;
use Modules\Inventory\Models\Currency\Currency;
use Modules\Inventory\Models\Client\Client;
use Modules\Core\Models\Warehouse\Warehouse;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $currency  = Currency::first();
            $client    = Client::first();
            $warehouse = Warehouse::first();

            Setting::create([
                'companyName'   => 'My Company',
                'companyPhone'  => '01234567890',
                'companyAdress' => 'Main Street, Cairo',
                'developed_by'  => 'Ahmed Nassag',
                'footer'        => 'Ahmed Nassag - Ultimate Inventory With POS',
                'currency_id'   => $currency->id,
                'client_id'     => $client->id,
                'warehouse_id'  => $warehouse->id,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
