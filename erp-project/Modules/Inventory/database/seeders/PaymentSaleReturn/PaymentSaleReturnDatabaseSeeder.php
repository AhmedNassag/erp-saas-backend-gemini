<?php

namespace Modules\Inventory\Database\Seeders\PaymentSaleReturn;

use Illuminate\Database\Seeder;

class PaymentSaleReturnDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['payment-sale-return' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            PaymentSaleReturnSeeder::class,
        ]);
    }
}
