<?php

namespace Modules\Core\Database\Seeders\City;

use Illuminate\Database\Seeder;
use App\Traits\PermissionSeederTrait;
use Modules\Core\Models\RoleAndPermission\Permission;
use Modules\Core\Database\Seeders\City\EgyptSeeder;

class CityDatabaseSeeder extends Seeder
{
    use PermissionSeederTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions Seeder (keep as is)
        $actions = ['read', 'create', 'show', 'update', 'delete', 'changeStatus'];
        $models  = ['city' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);

        // ⭐ Run Egypt Seeder After Permissions
        $this->call([
            EgyptSeeder::class,
        ]);
    }
}
