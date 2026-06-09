<?php

namespace Modules\Inventory\Database\Seeders\Setting;

use Illuminate\Database\Seeder;
use Modules\Inventory\Database\Seeders\Setting\SettingSeeder;

class SettingDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update'];
        $models  = ['setting' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            SettingSeeder::class,
        ]);
    }
}
