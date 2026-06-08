<?php

namespace Modules\Core\Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\Department\Department;
use Modules\Core\Models\User\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $department = Department::first();

            $users = [
                [
                    'name' => 'Admin User',
                    'email' => 'admin@erp.com'
                ],
                [
                    'name' => 'Manager User',
                    'email' => 'manager@erp.com'
                ],
            ];

            foreach ($users as $user) {
                User::create([
                    'name'          => $user['name'],
                    'email'         => $user['email'],
                    'password'      => Hash::make('12345678'),
                    'department_id' => $department->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
