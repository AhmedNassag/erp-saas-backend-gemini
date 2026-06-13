<?php

namespace Modules\Inventory\Database\Seeders\PaymentPurchaseReturn;

use Illuminate\Database\Seeder;

class PaymentPurchaseReturnDatabaseSeeder extends Seeder
{
    use \App\Traits\PermissionSeederTrait;

    public function run(): void
    {
        $actions = ['read', 'create', 'show', 'update', 'delete'];
        $models  = ['payment-purchase-return' => 'Inventory'];

        $this->createOrUpdatePermissions($models, $actions);

        $this->call([
            PaymentPurchaseReturnSeeder::class,
        ]);
    }
}
