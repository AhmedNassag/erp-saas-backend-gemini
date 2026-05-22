<?php

namespace Modules\Core\City\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\RoleAndPermission\App\Models\Permission;
use App\Traits\PermissionSeederTrait;
use Modules\Core\City\Database\Seeders\EgyptSeeder;

class CityDatabaseSeeder extends Seeder
{
    use PermissionSeederTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions Seeder (keep as is)
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models = ['city' => 'Core'];

        $this->createOrUpdatePermissions($models, $actions);

        // ⭐ Run Egypt Seeder After Permissions
        $this->call([
            EgyptSeeder::class,
        ]);
    }
}
