<?php

namespace Modules\Inventory\Database\Seeders\Provider;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Area\Area;
use Modules\Inventory\Models\Provider\Provider;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $area = Area::first();

            $providers = [
                [
                    'name'  => 'Global Supplies',
                    'code'  => 'PR_0001',
                    'phone' => '01110000001'
                ],
                [
                    'name'  => 'Egyptian Trading',
                    'code'  => 'PR_0002',
                    'phone' => '01110000002'
                ],
                [
                    'name'  => 'Prime Materials',
                    'code'  => 'PR_0003',
                    'phone' => '01110000003'
                ]
            ];

            foreach ($providers as $provider) {
                Provider::create([
                    'name'    => $provider['name'],
                    'code'    => $provider['code'],
                    'phone'   => $provider['phone'],
                    'area_id' => $area->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
