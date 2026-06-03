<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Database\Seeders\RoleAndPermission\RoleAndPermissionDatabaseSeeder;
use Modules\Core\Database\Seeders\Country\CountryDatabaseSeeder;
use Modules\Core\Database\Seeders\City\CityDatabaseSeeder;
use Modules\Core\Database\Seeders\Area\AreaDatabaseSeeder;
use Modules\Core\Database\Seeders\Branch\BranchDatabaseSeeder;
use Modules\Core\Database\Seeders\Department\DepartmentDatabaseSeeder;
use Modules\Core\Database\Seeders\User\UserDatabaseSeeder;
use Modules\Core\Models\RoleAndPermission\Role;
use Modules\Core\Models\RoleAndPermission\Permission;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionDatabaseSeeder::class,
            CountryDatabaseSeeder::class,
            CityDatabaseSeeder::class,
            AreaDatabaseSeeder::class,
            BranchDatabaseSeeder::class,
            DepartmentDatabaseSeeder::class,
            UserDatabaseSeeder::class,
        ]);

        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'tenant')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }
    }
}
