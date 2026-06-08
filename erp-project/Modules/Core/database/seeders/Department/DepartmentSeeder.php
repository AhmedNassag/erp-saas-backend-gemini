<?php

namespace Modules\Core\Database\Seeders\Department;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Department\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $departments = [
                'Administration',
                'Sales',
                'HR',
            ];

            foreach ($departments as $department) {
                Department::create([
                    'name' => $department,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
