<?php

namespace Modules\Core\RoleAndPermission\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Traits\PermissionSeederTrait;

class RoleAndPermissionDatabaseSeeder extends Seeder
{
    use PermissionSeederTrait;

    public function run(): void
    {
        // 1️⃣ Seed only 'read' for 'permission'
        $this->createOrUpdatePermissions(
            ['permission' => 'Core'],
            ['read']
        );

        // 2️⃣ Seed all standard actions for 'role'
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $this->createOrUpdatePermissions(
            ['role' => 'Core'],
            $actions
        );
    }
}
