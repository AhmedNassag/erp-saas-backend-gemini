<?php

namespace Modules\Core\Database\Seeders\Branch;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Area\Area;
use Modules\Core\Models\Branch\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $area = Area::first();

            $branches = [
                [
                    'name'                   => 'Main Branch',
                    'code'                   => 'BR_0001',
                    'commercialRegistration' => 'CR-001',
                    'taxCard'                => 'TX-001',
                    'mobile'                 => '01000000001'
                ],
                [
                    'name'                   => 'Second Branch',
                    'code'                   => 'BR_0002',
                    'commercialRegistration' => 'CR-002',
                    'taxCard'                => 'TX-002',
                    'mobile'                 => '01000000002'
                ],
            ];

            foreach ($branches as $branch) {
                Branch::create([
                    'name'                   => $branch['name'],
                    'code'                   => $branch['code'],
                    'commercialRegistration' => $branch['commercialRegistration'],
                    'taxCard'                => $branch['taxCard'],
                    'mobile'                 => $branch['mobile'],
                    'area_id'                => $area->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
