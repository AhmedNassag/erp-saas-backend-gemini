<?php

namespace Modules\Inventory\Database\Seeders\Client;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Area\Area;
use Modules\Inventory\Models\Client\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $area = Area::first();

            $clients = [
                [
                    'name' => 'Ahmed Company',
                    'code' => 'CL-001',
                    'phone' => '01200000001'
                ],
                [
                    'name' => 'Sara Enterprise',
                    'code' => 'CL-002',
                    'phone' => '01200000002'
                ],
                [
                    'name' => 'Tech Solutions',
                    'code' => 'CL-003',
                    'phone' => '01200000003'
                ]
            ];

            foreach ($clients as $client) {
                Client::create([
                    'name'    => $client['name'],
                    'code'    => $client['code'],
                    'phone'   => $client['phone'],
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
