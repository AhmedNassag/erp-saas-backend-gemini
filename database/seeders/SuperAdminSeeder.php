<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::updateOrCreate(
            ['email' => 'ahmednassag@gmail.com'],
            [
                'name'      => 'Super Admin',
                'email'     => 'ahmednassag@gmail.com',
                'mobile'    => '01016856433',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $this->command->info('SuperAdmin created: ahmednassag@gmail.com / password');
    }
}
